<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Review;
use App\Models\User;
use App\Services\Uploader;
use Core\Controller;

class AdminController extends Controller
{
    // ------------------------------------------------------------------
    // Xác thực
    // ------------------------------------------------------------------
    public function loginForm(): void
    {
        if (admin_user()) {
            $this->redirect('/admin');
        }
        $this->render('admin/login', ['title' => 'Đăng nhập quản trị'], null);
    }

    public function login(): void
    {
        $this->verifyCsrf();

        // Giới hạn 5 lần sai / 15 phút cho mỗi phiên
        $attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'time' => time()];
        if (time() - $attempts['time'] > 900) {
            $attempts = ['count' => 0, 'time' => time()];
        }
        if ($attempts['count'] >= 5) {
            flash('error', 'Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau 15 phút.');
            $this->redirect('/admin/login');
        }

        $admin = (new Admin())->attempt($this->input('username'), (string) ($_POST['password'] ?? ''));
        if (!$admin) {
            $attempts['count']++;
            $_SESSION['login_attempts'] = $attempts;
            flash('error', 'Tên đăng nhập hoặc mật khẩu không đúng.');
            $this->redirect('/admin/login');
        }

        session_regenerate_id(true); // Chống session fixation
        unset($_SESSION['login_attempts']);
        $_SESSION['admin'] = $admin;
        $this->redirect('/admin');
    }

    public function logout(): void
    {
        unset($_SESSION['admin']);
        session_regenerate_id(true);
        $this->redirect('/admin/login');
    }

    // ------------------------------------------------------------------
    // Dashboard
    // ------------------------------------------------------------------
    public function dashboard(): void
    {
        $product = new Product();
        $quote   = new Quote();
        $order   = new Order();
        $user    = new User();
        $project = new Project();
        $review  = new Review();

        $this->render('admin/dashboard', [
            'title' => 'Tổng quan hệ thống',
            'stats' => [
                'products'   => $product->count('status = 1'),
                'low_stock'  => $product->count('stock < 50 AND status = 1'),
                'new_quotes' => $quote->count("status = 'new'"),
                'orders'     => $order->count(),
                'pending'    => $order->count("status = 'pending'"),
                'revenue'    => $order->revenue(),
                'users'      => $user->count(),
                'projects'   => $project->count('status = 1'),
                'reviews'    => $review->count(),
            ],
            'latestQuotes'  => $quote->paginate('', 1, 5)['items'],
            'latestOrders'  => $order->paginate('', 1, 5)['items'],
            'latestUsers'   => $user->paginate('', '', 1, 5)['items'],
            'latestReviews' => $review->adminPaginate('', null, 1, 5)['items'],
        ], 'admin');
    }

    // ------------------------------------------------------------------
    // Sản phẩm
    // ------------------------------------------------------------------
    public function products(): void
    {
        $keyword    = $this->input('q');
        $categoryId = $this->inputInt('category_id');
        $seriesId   = $this->inputInt('series_id');
        $page       = max(1, $this->inputInt('page', 1));

        $this->render('admin/products', [
            'title'       => 'Quản lý toàn bộ sản phẩm Panasonic',
            'keyword'     => $keyword,
            'categoryId'  => $categoryId,
            'seriesId'    => $seriesId,
            'categories'  => (new Category())->all('sort_order', 'ASC'),
            'seriesList'  => (new ProductSeries())->all('sort_order', 'ASC'),
            'result'      => (new Product())->adminList($keyword, $categoryId, $seriesId, $page, 20),
        ], 'admin');
    }

    public function createProduct(): void
    {
        $this->productForm(null);
    }

    public function editProduct(int|string $id): void
    {
        $id = (int) $id;
        $product = (new Product())->findWithRelations($id);
        if (!$product) {
            $this->abort(404);
        }
        $this->productForm($product);
    }

    public function storeProduct(): void
    {
        $this->verifyCsrf();
        $this->saveProduct(null);
    }

    public function updateProduct(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $product = (new Product())->find($id);
        if (!$product) {
            $this->abort(404);
        }
        $this->saveProduct($product);
    }

    public function deleteProduct(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $model = new Product();
        try {
            $model->delete($id);
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => 'Đã xoá vĩnh viễn sản phẩm khỏi cơ sở dữ liệu.']);
            }
            flash('success', 'Đã xoá sản phẩm.');
        } catch (\PDOException $e) {
            // Không xoá được do ràng buộc -> ẩn sản phẩm
            $model->update($id, ['status' => 0]);
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => 'Sản phẩm đã phát sinh dữ liệu liên quan nên được chuyển sang trạng thái ẩn.']);
            }
            flash('success', 'Sản phẩm đã phát sinh dữ liệu liên quan nên được chuyển sang trạng thái ẩn.');
        }
        $this->redirect('/admin/san-pham');
    }

    private function productForm(?array $product): void
    {
        $this->render('admin/product_form', [
            'title'      => $product ? 'Sửa: ' . $product['model_code'] : 'Thêm sản phẩm mới',
            'product'    => $product,
            'categories' => (new Category())->all('sort_order', 'ASC'),
            'seriesList' => (new ProductSeries())->all('sort_order', 'ASC'),
        ], 'admin');
    }

    private function saveProduct(?array $existing): void
    {
        $model = new Product();
        $id    = (int) ($existing['id'] ?? 0);

        $name  = mb_substr($this->input('name'), 0, 255);
        $slug  = str_slug($this->input('slug') ?: $name);
        $sku   = strtoupper(mb_substr($this->input('sku'), 0, 60));
        $model_code = strtoupper(mb_substr($this->input('model_code'), 0, 60));

        $errors = [];
        if (mb_strlen($name) < 3)                   { $errors[] = 'Tên sản phẩm tối thiểu 3 ký tự.'; }
        if ($model_code === '')                     { $errors[] = 'Vui lòng nhập mã Model Panasonic.'; }
        if ($sku === '')                            { $errors[] = 'Vui lòng nhập SKU.'; }
        if ($sku !== '' && $model->skuExists($sku, $id))     { $errors[] = 'SKU đã tồn tại.'; }
        if ($slug === '')                           { $errors[] = 'Slug không hợp lệ.'; }
        if ($this->inputInt('category_id') <= 0)    { $errors[] = 'Vui lòng chọn danh mục.'; }

        // Slug trùng -> tự thêm hậu tố
        $baseSlug = $slug;
        $i = 2;
        while ($slug !== '' && $model->slugExists($slug, $id)) {
            $slug = $baseSlug . '-' . $i++;
        }

        // Thông số kỹ thuật dạng cặp key/value
        $specs  = [];
        $keys   = (array) ($_POST['spec_key'] ?? []);
        $values = (array) ($_POST['spec_value'] ?? []);
        foreach ($keys as $idx => $key) {
            $key = trim((string) $key);
            $val = trim((string) ($values[$idx] ?? ''));
            if ($key !== '' && $val !== '') {
                $specs[mb_substr($key, 0, 80)] = mb_substr($val, 0, 255);
            }
        }

        $numOrNull = function (string $key): ?string {
            $v = str_replace(',', '.', $this->input($key));
            return is_numeric($v) ? (string) (float) $v : null;
        };

        $data = [
            'category_id'       => $this->inputInt('category_id'),
            'series_id'         => $this->inputInt('series_id') ?: null,
            'name'              => $name,
            'slug'              => $slug,
            'sku'               => $sku,
            'model_code'        => $model_code,
            'short_desc'        => mb_substr($this->input('short_desc'), 0, 500),
            'description'       => $this->input('description'),
            'price'             => max(0, $this->inputInt('price')),
            'wholesale_price'   => max(0, $this->inputInt('wholesale_price')),
            'wholesale_min_qty' => max(1, $this->inputInt('wholesale_min_qty', 10)),
            'stock'             => max(0, $this->inputInt('stock')),
            'unit'              => mb_substr($this->input('unit', 'Cái') ?: 'Cái', 0, 20),
            'specs'             => json_encode($specs, JSON_UNESCAPED_UNICODE),
            'rated_current'     => $numOrNull('rated_current'),
            'breaking_capacity' => $numOrNull('breaking_capacity'),
            'poles'             => $this->inputInt('poles') ?: null,
            'pdf_catalog_url'   => mb_substr($this->input('pdf_catalog_url'), 0, 255) ?: null,
            'is_featured'       => isset($_POST['is_featured']) ? 1 : 0,
            'status'            => isset($_POST['status']) ? 1 : 0,
        ];

        if ($data['wholesale_price'] > $data['price'] && $data['price'] > 0) {
            $errors[] = 'Giá công trình không được lớn hơn giá bán lẻ.';
        }

        // Xử lý link Thumbnail trực tiếp nếu có
        $thumbnailUrl = trim($this->input('thumbnail_url'));
        if ($thumbnailUrl !== '') {
            $data['thumbnail'] = $thumbnailUrl;
        }

        // Xử lý Gallery hiện có và URLs mới
        $existingGallery = [];
        if (isset($_POST['existing_gallery']) && is_array($_POST['existing_gallery'])) {
            $existingGallery = array_values(array_filter($_POST['existing_gallery'], fn($g) => !empty(trim((string)$g))));
        } elseif ($existing && !empty($existing['gallery'])) {
            $existingGallery = is_array($existing['gallery']) ? $existing['gallery'] : (json_decode((string)$existing['gallery'], true) ?: []);
        }

        $galleryUrlsInput = trim($this->input('gallery_urls'));
        if ($galleryUrlsInput !== '') {
            $urls = preg_split('/[\r\n]+/', $galleryUrlsInput);
            foreach ($urls as $u) {
                $u = trim($u);
                if ($u !== '' && !in_array($u, $existingGallery)) {
                    $existingGallery[] = $u;
                }
            }
        }

        // Upload ảnh đại diện, file Gallery nhiều ảnh & PDF Catalogue
        if (!$errors) {
            try {
                if (($_FILES['thumbnail']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    $img = Uploader::store($_FILES['thumbnail'], PUBLIC_PATH . '/uploads/products', ['jpg', 'jpeg', 'png', 'webp', 'svg'], (int) config('image_max_size'), 'p-');
                    $data['thumbnail'] = 'uploads/products/' . $img['path'];
                    if (!in_array($data['thumbnail'], $existingGallery)) {
                        array_unshift($existingGallery, $data['thumbnail']);
                    }
                }

                // Upload nhiều ảnh gallery
                if (!empty($_FILES['gallery_files']['name']) && is_array($_FILES['gallery_files']['name'])) {
                    $fileCount = count($_FILES['gallery_files']['name']);
                    for ($f = 0; $f < $fileCount; $f++) {
                        if (($_FILES['gallery_files']['error'][$f] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                            $singleFile = [
                                'name'     => $_FILES['gallery_files']['name'][$f],
                                'type'     => $_FILES['gallery_files']['type'][$f],
                                'tmp_name' => $_FILES['gallery_files']['tmp_name'][$f],
                                'error'    => $_FILES['gallery_files']['error'][$f],
                                'size'     => $_FILES['gallery_files']['size'][$f],
                            ];
                            $up = Uploader::store($singleFile, PUBLIC_PATH . '/uploads/products', ['jpg', 'jpeg', 'png', 'webp', 'svg'], (int) config('image_max_size'), 'p-gal-');
                            $galPath = 'uploads/products/' . $up['path'];
                            if (!in_array($galPath, $existingGallery)) {
                                $existingGallery[] = $galPath;
                            }
                        }
                    }
                }

                if (($_FILES['pdf_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    $pdf = Uploader::store($_FILES['pdf_file'], PUBLIC_PATH . '/uploads/catalogs', ['pdf'], 20 * 1024 * 1024, 'catalog-');
                    $data['pdf_catalog_url'] = 'uploads/catalogs/' . $pdf['path'];
                }
            } catch (\RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!empty($data['thumbnail']) && !in_array($data['thumbnail'], $existingGallery)) {
            array_unshift($existingGallery, $data['thumbnail']);
        }
        $data['gallery'] = json_encode(array_values(array_unique(array_slice($existingGallery, 0, 15))), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($errors) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => implode(' ', $errors)], 422);
            }
            flash('error', implode(' ', $errors));
            set_old($_POST);
            $this->redirect($existing ? '/admin/san-pham/sua/' . $id : '/admin/san-pham/them');
        }

        if ($existing) {
            $model->update($id, $data);
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => 'Đã cập nhật sản phẩm ' . $model_code, 'id' => $id, 'redirect' => url('/admin/san-pham')]);
            }
            flash('success', 'Đã cập nhật sản phẩm ' . $model_code);
        } else {
            $id = $model->create($data);
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => 'Đã thêm sản phẩm ' . $model_code, 'id' => $id, 'redirect' => url('/admin/san-pham')]);
            }
            flash('success', 'Đã thêm sản phẩm ' . $model_code);
        }
        clear_old();
        $this->redirect('/admin/san-pham/sua/' . $id);
    }

    // ------------------------------------------------------------------
    // Yêu cầu báo giá
    // ------------------------------------------------------------------
    public function quotes(): void
    {
        $status = $this->input('status');
        $this->render('admin/quotes', [
            'title'    => 'Yêu cầu báo giá công trình',
            'status'   => $status,
            'result'   => (new Quote())->paginate($status, max(1, $this->inputInt('page', 1))),
            'statuses' => Quote::STATUSES,
            'types'    => Quote::CUSTOMER_TYPES,
        ], 'admin');
    }

    public function updateQuoteStatus(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $status = $this->input('status');
        $updated = (new Quote())->updateStatus($id, $status);
        if ($this->isAjax()) {
            $this->json([
                'success' => $updated,
                'message' => $updated ? "Đã cập nhật yêu cầu #{$id}" : 'Trạng thái không hợp lệ'
            ]);
        }
        $updated
            ? flash('success', "Đã cập nhật yêu cầu #{$id}")
            : flash('error', 'Trạng thái không hợp lệ');
        $this->redirect('/admin/bao-gia');
    }

    /**
     * Tải file BOM (lưu ngoài public, chỉ admin truy cập được).
     */
    public function downloadBom(int|string $id): void
    {
        $id = (int) $id;
        $quote = (new Quote())->find($id);
        if (!$quote || !$quote['file_bom_path']) {
            $this->abort(404, 'Không có file đính kèm.');
        }
        $path = realpath(STORAGE_PATH . '/boms/' . basename($quote['file_bom_path']));
        $root = realpath(STORAGE_PATH . '/boms');
        if (!$path || !$root || !str_starts_with($path, $root) || !is_file($path)) {
            $this->abort(404, 'File không tồn tại.');
        }

        $downloadName = preg_replace('/[^\w\.\-]+/u', '_', $quote['file_original'] ?: basename($path));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . filesize($path));
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }

    // ------------------------------------------------------------------
    // Đơn hàng
    // ------------------------------------------------------------------
    public function orders(): void
    {
        $status = $this->input('status');
        $this->render('admin/orders', [
            'title'    => 'Quản lý đơn hàng',
            'status'   => $status,
            'result'   => (new Order())->paginate($status, max(1, $this->inputInt('page', 1))),
            'statuses' => Order::STATUSES,
        ], 'admin');
    }

    public function orderDetail(int|string $id): void
    {
        $id = (int) $id;
        $model = new Order();
        $order = $model->find($id);
        if (!$order) {
            $this->abort(404);
        }
        $this->render('admin/order_detail', [
            'title'    => 'Đơn hàng #' . $order['order_code'],
            'order'    => $order,
            'items'    => $model->items($id),
            'statuses' => Order::STATUSES,
        ], 'admin');
    }

    public function updateOrderStatus(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $status = $this->input('status');
        if (isset(Order::STATUSES[$status])) {
            (new Order())->update($id, ['status' => $status]);
            if ($this->isAjax()) {
                $this->json([
                    'success' => true,
                    'status'  => $status,
                    'message' => 'Đã cập nhật trạng thái đơn hàng: ' . Order::STATUSES[$status]
                ]);
            }
            flash('success', 'Đã cập nhật trạng thái đơn hàng.');
        } else {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Trạng thái không hợp lệ'], 422);
            }
        }
        $this->redirect('/admin/don-hang/' . $id);
    }

    // ------------------------------------------------------------------
    // Quản lý Thành viên & Khách hàng
    // ------------------------------------------------------------------
    public function users(): void
    {
        $keyword = $this->input('q');
        $provider = $this->input('provider');
        $page = max(1, $this->inputInt('page', 1));
        $this->render('admin/users', [
            'title'    => 'Quản lý thành viên & Khách hàng',
            'keyword'  => $keyword,
            'provider' => $provider,
            'result'   => (new User())->paginate($keyword, $provider, $page, 15),
        ], 'admin');
    }

    public function toggleUserStatus(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $userModel = new User();
        $user = $userModel->find($id);
        if ($user) {
            $newStatus = ($user['status'] === 'active') ? 'banned' : 'active';
            $userModel->update($id, ['status' => $newStatus]);
            if ($this->isAjax()) {
                $this->json([
                    'success' => true,
                    'status'  => $newStatus,
                    'message' => 'Đã chuyển trạng thái tài khoản sang: ' . $newStatus
                ]);
            }
            flash('success', 'Đã chuyển trạng thái tài khoản: ' . $user['name'] . ' sang: ' . $newStatus);
        } else {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Tài khoản không tồn tại'], 404);
            }
        }
        $this->redirect('/admin/thanh-vien');
    }

    // ------------------------------------------------------------------
    // Quản lý Dự án Công trình (TP.HCM & Long An)
    // ------------------------------------------------------------------
    public function projects(): void
    {
        $keyword = $this->input('q');
        $page = max(1, $this->inputInt('page', 1));
        $this->render('admin/projects', [
            'title'   => 'Quản lý dự án công trình (TP.HCM & Long An)',
            'keyword' => $keyword,
            'result'  => (new Project())->paginate($keyword, $page, 10),
        ], 'admin');
    }

    public function createProject(): void
    {
        $this->render('admin/project_form', [
            'title'   => 'Thêm dự án công trình mới',
            'project' => null,
        ], 'admin');
    }

    public function storeProject(): void
    {
        $this->verifyCsrf();
        $this->saveProject(null);
    }

    public function editProject(int|string $id): void
    {
        $id = (int) $id;
        $project = (new Project())->find($id);
        if (!$project) {
            $this->abort(404);
        }
        $this->render('admin/project_form', [
            'title'   => 'Sửa dự án: ' . $project['title'],
            'project' => $project,
        ], 'admin');
    }

    public function updateProject(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $project = (new Project())->find($id);
        if (!$project) {
            $this->abort(404);
        }
        $this->saveProject($project);
    }

    public function deleteProject(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        (new Project())->delete($id);
        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'Đã xóa dự án thành công!']);
        }
        flash('success', 'Đã xóa dự án thành công.');
        $this->redirect('/admin/du-an');
    }

    private function saveProject(?array $existing): void
    {
        $model = new Project();
        $id = (int) ($existing['id'] ?? 0);

        $title = mb_substr($this->input('title'), 0, 255);
        $slug = str_slug($this->input('slug') ?: $title);

        $data = [
            'title'           => $title,
            'slug'            => $slug,
            'client_name'     => mb_substr($this->input('client_name'), 0, 150),
            'location'        => mb_substr($this->input('location'), 0, 255),
            'scale'           => mb_substr($this->input('scale'), 0, 150),
            'category'        => mb_substr($this->input('category'), 0, 100),
            'supplied_items'  => $this->input('supplied_items'),
            'description'     => $this->input('description'),
            'completion_year' => $this->inputInt('completion_year') ?: date('Y'),
            'sort_order'      => $this->inputInt('sort_order', 10),
            'is_featured'     => isset($_POST['is_featured']) ? 1 : 0,
            'status'          => isset($_POST['status']) ? 1 : 0,
        ];

        // Upload ảnh đại diện nếu có
        if (($_FILES['thumbnail']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            try {
                $img = Uploader::store($_FILES['thumbnail'], PUBLIC_PATH . '/uploads/projects', ['jpg', 'jpeg', 'png', 'webp', 'svg'], 5 * 1024 * 1024, 'proj-');
                $data['thumbnail'] = 'uploads/projects/' . $img['path'];
            } catch (\Exception $e) {
                if ($this->isAjax()) {
                    $this->json(['success' => false, 'message' => 'Lỗi upload ảnh: ' . $e->getMessage()], 422);
                }
                flash('error', 'Lỗi upload ảnh: ' . $e->getMessage());
                $this->redirect($existing ? '/admin/du-an/sua/' . $id : '/admin/du-an/them');
            }
        }

        if ($existing) {
            $model->update($id, $data);
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => 'Đã cập nhật dự án thành công.', 'redirect' => url('/admin/du-an')]);
            }
            flash('success', 'Đã cập nhật dự án thành công.');
        } else {
            if (empty($data['thumbnail'])) {
                $data['thumbnail'] = 'uploads/projects/project_hcm_apartment.jpg';
            }
            $model->create($data);
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => 'Đã thêm dự án mới thành công.', 'redirect' => url('/admin/du-an')]);
            }
            flash('success', 'Đã thêm dự án mới thành công.');
        }

        $this->redirect('/admin/du-an');
    }

    // ------------------------------------------------------------------
    // Quản lý Đánh giá Sản phẩm
    // ------------------------------------------------------------------
    public function reviews(): void
    {
        $keyword = $this->input('q');
        $rating = $this->inputInt('rating') ?: null;
        $page = max(1, $this->inputInt('page', 1));
        $this->render('admin/reviews', [
            'title'   => 'Quản lý đánh giá sản phẩm',
            'keyword' => $keyword,
            'rating'  => $rating,
            'result'  => (new Review())->adminPaginate($keyword, $rating, $page, 15),
        ], 'admin');
    }

    public function replyReview(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $model = new Review();
        $rev = $model->find($id);
        if (!$rev) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Không tìm thấy đánh giá cần phản hồi.'], 404);
            }
            flash('error', 'Đánh giá không tồn tại.');
            $this->redirect('/admin/danh-gia');
        }

        $reply = trim((string) ($_POST['reply'] ?? ''));
        $model->replyReview($id, $reply);

        if ($this->isAjax()) {
            $this->json([
                'success'  => true,
                'message'  => $reply !== '' ? 'Đã lưu phản hồi của Quản trị viên thành công.' : 'Đã gỡ bỏ phản hồi của đánh giá này.',
                'reply'    => $reply,
                'reply_at' => $reply !== '' ? date('d/m/Y H:i') : null
            ]);
        }

        flash('success', $reply !== '' ? 'Đã lưu phản hồi thành công.' : 'Đã gỡ bỏ phản hồi.');
        $this->redirect('/admin/danh-gia');
    }

    public function toggleReviewStatus(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $model = new Review();
        $rev = $model->find($id);
        if ($rev) {
            $newStatus = ((int)$rev['status'] === 1) ? 0 : 1;
            $model->update($id, ['status' => $newStatus]);
            // Tính lại sao trung bình
            $stats = $model->getStats((int)$rev['product_id']);
            (new Product())->update((int)$rev['product_id'], [
                'rating_avg'    => $stats['avg'],
                'reviews_count' => $stats['total']
            ]);
            if ($this->isAjax()) {
                $this->json([
                    'success' => true,
                    'status'  => $newStatus,
                    'message' => $newStatus === 1 ? 'Đã duyệt hiển thị đánh giá lên trang sản phẩm.' : 'Đã ẩn đánh giá khỏi trang sản phẩm.'
                ]);
            }
            flash('success', 'Đã thay đổi trạng thái hiển thị đánh giá.');
        } else {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Đánh giá không tồn tại'], 404);
            }
        }
        $this->redirect('/admin/danh-gia');
    }

    public function deleteReview(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $model = new Review();
        $rev = $model->find($id);
        if ($rev) {
            $productId = (int)$rev['product_id'];
            $model->delete($id);
            $stats = $model->getStats($productId);
            (new Product())->update($productId, [
                'rating_avg'    => $stats['avg'],
                'reviews_count' => $stats['total']
            ]);
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => 'Đã xoá vĩnh viễn đánh giá khỏi hệ thống.']);
            }
            flash('success', 'Đã xoá đánh giá khỏi hệ thống.');
        } else {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Đánh giá không tồn tại.'], 404);
            }
        }
        $this->redirect('/admin/danh-gia');
    }

    // ------------------------------------------------------------------
    // Danh mục sản phẩm
    // ------------------------------------------------------------------
    public function categories(): void
    {
        $db = \Core\Database::getInstance();
        $categories = $db->fetchAll(
            "SELECT c.*, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             GROUP BY c.id
             ORDER BY c.sort_order ASC, c.name ASC"
        );

        $this->render('admin/categories', [
            'title'      => 'Quản lý danh mục thiết bị điện',
            'categories' => $categories,
        ], 'admin');
    }

    public function createCategory(): void
    {
        $this->render('admin/category_form', [
            'title'    => 'Thêm danh mục thiết bị mới',
            'category' => null,
        ], 'admin');
    }

    public function storeCategory(): void
    {
        $this->verifyCsrf();
        $name = $this->input('name');
        if (!$name) {
            flash('error', 'Vui lòng nhập tên danh mục.');
            $this->redirect('/admin/danh-muc/them');
        }

        $slug = $this->input('slug') ?: slugify($name);
        $icon = $this->input('icon') ?: 'grid';
        $description = $this->input('description');
        $sortOrder = $this->inputInt('sort_order', 0);
        $status = isset($_POST['status']) ? 1 : 0;

        (new Category())->create([
            'name'        => $name,
            'slug'        => $slug,
            'icon'        => $icon,
            'description' => $description,
            'sort_order'  => $sortOrder,
            'status'      => $status,
        ]);

        flash('success', 'Đã thêm danh mục thiết bị thành công!');
        $this->redirect('/admin/danh-muc');
    }

    public function editCategory(int|string $id): void
    {
        $category = (new Category())->find((int) $id);
        if (!$category) {
            $this->abort(404);
        }

        $this->render('admin/category_form', [
            'title'    => 'Chỉnh sửa danh mục: ' . $category['name'],
            'category' => $category,
        ], 'admin');
    }

    public function updateCategory(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $model = new Category();
        $cat = $model->find($id);
        if (!$cat) {
            $this->abort(404);
        }

        $name = $this->input('name');
        if (!$name) {
            flash('error', 'Vui lòng nhập tên danh mục.');
            $this->redirect('/admin/danh-muc/sua/' . $id);
        }

        $slug = $this->input('slug') ?: slugify($name);
        $icon = $this->input('icon') ?: 'grid';
        $description = $this->input('description');
        $sortOrder = $this->inputInt('sort_order', 0);
        $status = isset($_POST['status']) ? 1 : 0;

        $model->update($id, [
            'name'        => $name,
            'slug'        => $slug,
            'icon'        => $icon,
            'description' => $description,
            'sort_order'  => $sortOrder,
            'status'      => $status,
        ]);

        flash('success', 'Đã cập nhật danh mục thành công!');
        $this->redirect('/admin/danh-muc');
    }

    public function toggleCategoryStatus(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $model = new Category();
        $cat = $model->find($id);
        if ($cat) {
            $newStatus = $cat['status'] ? 0 : 1;
            $model->update($id, ['status' => $newStatus]);
            flash('success', 'Đã thay đổi trạng thái danh mục: ' . ($newStatus ? 'Đang hoạt động' : 'Tạm ẩn'));
        }
        $this->redirect('/admin/danh-muc');
    }

    public function deleteCategory(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $db = \Core\Database::getInstance();
        $productCount = (int) $db->fetchColumn('SELECT COUNT(*) FROM products WHERE category_id = :cid', ['cid' => $id]);
        if ($productCount > 0) {
            flash('error', "Không thể xóa danh mục này vì đang có {$productCount} sản phẩm trực thuộc!");
            $this->redirect('/admin/danh-muc');
        }

        (new Category())->delete($id);
        flash('success', 'Đã xóa danh mục thành công!');
        $this->redirect('/admin/danh-muc');
    }

    // ------------------------------------------------------------------
    // Dòng Series sản phẩm
    // ------------------------------------------------------------------
    public function series(): void
    {
        $db = \Core\Database::getInstance();
        $series = $db->fetchAll(
            "SELECT s.*, COUNT(p.id) AS product_count
             FROM product_series s
             LEFT JOIN products p ON p.series_id = s.id
             GROUP BY s.id
             ORDER BY s.sort_order ASC, s.name ASC"
        );

        $this->render('admin/series', [
            'title'  => 'Quản lý dòng Series thiết bị Panasonic',
            'series' => $series,
        ], 'admin');
    }

    public function createSeries(): void
    {
        $this->render('admin/series_form', [
            'title'  => 'Thêm dòng Series mới',
            'series' => null,
        ], 'admin');
    }

    public function storeSeries(): void
    {
        $this->verifyCsrf();
        $name = $this->input('name');
        if (!$name) {
            flash('error', 'Vui lòng nhập tên dòng series.');
            $this->redirect('/admin/dong-series/them');
        }

        $slug = $this->input('slug') ?: slugify($name);
        $tagline = $this->input('tagline');
        $color = $this->input('color') ?: '#004098';
        $catalogUrl = $this->input('catalog_url');
        $sortOrder = $this->inputInt('sort_order', 0);
        $status = isset($_POST['status']) ? 1 : 0;

        (new ProductSeries())->create([
            'name'        => $name,
            'slug'        => $slug,
            'tagline'     => $tagline,
            'color'       => $color,
            'catalog_url' => $catalogUrl,
            'sort_order'  => $sortOrder,
            'status'      => $status,
        ]);

        flash('success', 'Đã thêm dòng Series thành công!');
        $this->redirect('/admin/dong-series');
    }

    public function editSeries(int|string $id): void
    {
        $series = (new ProductSeries())->find((int) $id);
        if (!$series) {
            $this->abort(404);
        }

        $this->render('admin/series_form', [
            'title'  => 'Chỉnh sửa dòng Series: ' . $series['name'],
            'series' => $series,
        ], 'admin');
    }

    public function updateSeries(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $model = new ProductSeries();
        $s = $model->find($id);
        if (!$s) {
            $this->abort(404);
        }

        $name = $this->input('name');
        if (!$name) {
            flash('error', 'Vui lòng nhập tên dòng series.');
            $this->redirect('/admin/dong-series/sua/' . $id);
        }

        $slug = $this->input('slug') ?: slugify($name);
        $tagline = $this->input('tagline');
        $color = $this->input('color') ?: '#004098';
        $catalogUrl = $this->input('catalog_url');
        $sortOrder = $this->inputInt('sort_order', 0);
        $status = isset($_POST['status']) ? 1 : 0;

        $model->update($id, [
            'name'        => $name,
            'slug'        => $slug,
            'tagline'     => $tagline,
            'color'       => $color,
            'catalog_url' => $catalogUrl,
            'sort_order'  => $sortOrder,
            'status'      => $status,
        ]);

        flash('success', 'Đã cập nhật dòng Series thành công!');
        $this->redirect('/admin/dong-series');
    }

    public function toggleSeriesStatus(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $model = new ProductSeries();
        $s = $model->find($id);
        if ($s) {
            $newStatus = $s['status'] ? 0 : 1;
            $model->update($id, ['status' => $newStatus]);
            flash('success', 'Đã cập nhật trạng thái dòng Series!');
        }
        $this->redirect('/admin/dong-series');
    }

    public function deleteSeries(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $db = \Core\Database::getInstance();
        $productCount = (int) $db->fetchColumn('SELECT COUNT(*) FROM products WHERE series_id = :sid', ['sid' => $id]);
        if ($productCount > 0) {
            flash('error', "Không thể xóa dòng Series này vì đang có {$productCount} sản phẩm trực thuộc!");
            $this->redirect('/admin/dong-series');
        }

        (new ProductSeries())->delete($id);
        flash('success', 'Đã xóa dòng Series thành công!');
        $this->redirect('/admin/dong-series');
    }

    // ------------------------------------------------------------------
    // Thống kê & Báo cáo Doanh nghiệp
    // ------------------------------------------------------------------
    public function analytics(): void
    {
        $db = \Core\Database::getInstance();

        // 1. Tổng hợp doanh thu theo các mốc
        $totalRevenue = (float) $db->fetchColumn("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled'");
        $monthlyRevenue = (float) $db->fetchColumn("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
        $todayRevenue = (float) $db->fetchColumn("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled' AND DATE(created_at) = CURRENT_DATE()");
        $totalOrdersCount = (int) $db->fetchColumn("SELECT COUNT(*) FROM orders");
        $completedOrdersCount = (int) $db->fetchColumn("SELECT COUNT(*) FROM orders WHERE status = 'completed'");
        $avgOrderValue = $completedOrdersCount > 0 ? (int) ($totalRevenue / $completedOrdersCount) : 0;

        // 2. Thống kê doanh thu 6 tháng gần nhất
        $monthlyStats = $db->fetchAll(
            "SELECT 
                DATE_FORMAT(created_at, '%m/%Y') AS month_label,
                COUNT(id) AS order_count,
                COALESCE(SUM(total), 0) AS revenue
             FROM orders 
             WHERE status != 'cancelled' AND created_at >= (NOW() - INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%m/%Y'), YEAR(created_at), MONTH(created_at)
             ORDER BY YEAR(created_at) ASC, MONTH(created_at) ASC"
        );

        // 3. Cơ cấu đơn hàng theo trạng thái
        $statusBreakdown = $db->fetchAll(
            "SELECT status, COUNT(*) AS count, COALESCE(SUM(total), 0) AS sum_total
             FROM orders 
             GROUP BY status"
        );

        // 4. Cơ cấu phương thức thanh toán
        $paymentMethods = $db->fetchAll(
            "SELECT payment_method, COUNT(*) AS count, COALESCE(SUM(total), 0) AS sum_total
             FROM orders
             GROUP BY payment_method"
        );

        // 5. Top 10 sản phẩm bán chạy nhất
        $topProducts = $db->fetchAll(
            "SELECT p.id, p.name, p.sku, p.thumbnail, p.price,
                    SUM(oi.quantity) AS total_qty,
                    SUM(oi.line_total) AS total_sales
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             JOIN orders o ON o.id = oi.order_id
             WHERE o.status != 'cancelled'
             GROUP BY p.id, p.name, p.sku, p.thumbnail, p.price
             ORDER BY total_sales DESC
             LIMIT 10"
        );

        // 6. Cảnh báo tồn kho thấp (< 20)
        $lowStockProducts = $db->fetchAll(
            "SELECT id, name, sku, stock, price, thumbnail 
             FROM products 
             WHERE status = 1 AND stock < 20 
             ORDER BY stock ASC 
             LIMIT 10"
        );

        $this->render('admin/analytics', [
            'title'                => 'Báo cáo doanh thu & Thống kê kinh doanh',
            'totalRevenue'         => $totalRevenue,
            'monthlyRevenue'       => $monthlyRevenue,
            'todayRevenue'         => $todayRevenue,
            'totalOrdersCount'     => $totalOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'avgOrderValue'        => $avgOrderValue,
            'monthlyStats'         => $monthlyStats,
            'statusBreakdown'      => $statusBreakdown,
            'paymentMethods'       => $paymentMethods,
            'topProducts'          => $topProducts,
            'lowStockProducts'     => $lowStockProducts,
        ], 'admin');
    }

    // ------------------------------------------------------------------
    // In Phiếu Giao Hàng & Hóa Đơn Bán Hàng
    // ------------------------------------------------------------------
    public function printOrder(int|string $id): void
    {
        $id = (int) $id;
        $orderModel = new Order();
        $order = $orderModel->find($id);
        if (!$order) {
            $this->abort(404, 'Không tìm thấy đơn hàng cần in');
        }

        $items = $orderModel->items($id);

        $this->render('admin/order_invoice', [
            'title' => 'Phiếu Xuất Kho & Hóa Đơn #' . $order['order_code'],
            'order' => $order,
            'items' => $items,
        ], null);
    }

    // ------------------------------------------------------------------
    // Quản lý Tin nhắn liên hệ từ khách hàng
    // ------------------------------------------------------------------
    public function contacts(): void
    {
        $status = $this->input('status');
        $q = $this->input('q');
        $page = max(1, $this->inputInt('page', 1));
        $contactModel = new \App\Models\Contact();

        $result = $contactModel->paginate($status, $q, $page, 20);

        $this->render('admin/contacts', [
            'title'    => 'Hộp thư liên hệ & Yêu cầu tư vấn',
            'status'   => $status,
            'q'        => $q,
            'result'   => $result,
            'statuses' => \App\Models\Contact::STATUSES,
        ], 'admin');
    }

    public function updateContactStatus(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $status = $this->input('status');
        $note = $this->input('note');

        (new \App\Models\Contact())->updateStatus($id, $status, $note);

        flash('success', 'Đã cập nhật trạng thái liên hệ thành công!');
        $this->redirect('/admin/lien-he');
    }

    public function deleteContact(int|string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        (new \App\Models\Contact())->delete($id);

        flash('success', 'Đã xóa tin nhắn liên hệ.');
        $this->redirect('/admin/lien-he');
    }

    // ------------------------------------------------------------------
    // Cài đặt Hệ thống Website
    // ------------------------------------------------------------------
    public function settings(): void
    {
        $this->render('admin/settings', [
            'title' => 'Cài đặt hệ thống website & Thông tin liên hệ',
        ], 'admin');
    }

    public function updateSettings(): void
    {
        $this->verifyCsrf();
        $configPath = ROOT_PATH . '/config/app.php';
        $currentConfig = require $configPath;

        $currentConfig['name']             = $this->input('name', $currentConfig['name'] ?? '');
        $currentConfig['company']          = $this->input('company', $currentConfig['company'] ?? '');
        $currentConfig['tagline']          = $this->input('tagline', $currentConfig['tagline'] ?? '');
        $currentConfig['hotline']          = $this->input('hotline', $currentConfig['hotline'] ?? '');
        $currentConfig['hotline_sales']    = $this->input('hotline_sales', $currentConfig['hotline_sales'] ?? '');
        $currentConfig['zalo']             = $this->input('zalo', $currentConfig['zalo'] ?? '');
        $currentConfig['email']            = $this->input('email', $currentConfig['email'] ?? '');
        $currentConfig['address']          = $this->input('address', $currentConfig['address'] ?? '');
        $currentConfig['working_hours']    = $this->input('working_hours', $currentConfig['working_hours'] ?? '');
        $currentConfig['tax_code']         = $this->input('tax_code', $currentConfig['tax_code'] ?? '');
        $currentConfig['free_ship_from']   = $this->inputInt('free_ship_from', (int)($currentConfig['free_ship_from'] ?? 2000000));
        $currentConfig['google_client_id'] = $this->input('google_client_id', $currentConfig['google_client_id'] ?? '');
        $currentConfig['facebook_app_id']  = $this->input('facebook_app_id', $currentConfig['facebook_app_id'] ?? '');

        $export = "<?php\n/**\n * Cấu hình ứng dụng - Đồng bộ từ Admin.\n */\nreturn " . var_export($currentConfig, true) . ";\n";
        file_put_contents($configPath, $export);

        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'Đã lưu cấu hình hệ thống website thành công!']);
        }

        flash('success', 'Đã lưu cấu hình hệ thống website thành công!');
        $this->redirect('/admin/cai-dat');
    }
}
