# HƯỚNG DẪN CÀI ĐẶT & VẬN HÀNH WEBSITE PHÂN PHỐI THIẾT BỊ ĐIỆN PANASONIC

Hệ thống được phát triển theo kiến trúc **PHP thuần OOP MVC Clean Architecture**, **MySQL PDO Prepared Statements**, không sử dụng framework cồng kềnh, tốc độ xử lý tức thì, bảo mật cao và tối ưu 100% Mobile Responsive.

---

## 1. CẤU TRÚC THƯ MỤC DỰ ÁN
```
panasonic/
├── app/
│   ├── Controllers/
│   │   ├── HomeController.php      # Trang chủ, Hero slider, Tra cứu MCB
│   │   ├── ProductController.php   # Lọc kỹ thuật (kA, Ampe, Poles), Series, Detail, Live Search
│   │   ├── QuoteController.php     # Tiếp nhận file BOM công trình, Tải Catalogue 2026
│   │   ├── CartController.php      # Giỏ hàng tự động áp giá sỉ / dự toán công trình
│   │   └── AdminController.php     # Quản trị hệ thống, CRUD sản phẩm, BOM, đơn hàng
│   ├── Models/
│   │   ├── BaseModel.php           # CRUD cốt lõi, Prepared Statements, chống Mass-assignment
│   │   ├── Product.php             # Lọc đa chiều, Facets, Live Search theo Model Code
│   │   ├── Category.php            # Danh mục thiết bị
│   │   ├── ProductSeries.php       # Series (Wide, Halumie, Gen-X, Moderva, Full Color, Minerva)
│   │   ├── Quote.php               # Yêu cầu báo giá công trình & chống spam IP
│   │   ├── Order.php               # Đơn hàng, quản lý kho trong Transaction (FOR UPDATE)
│   │   └── Admin.php               # Quản trị viên, xác thực Bcrypt & Session Fixation Shield
│   ├── Services/
│   │   ├── Cart.php                # Tính toán giỏ hàng & quy tắc giá bán buôn
│   │   └── Uploader.php            # Upload an toàn (kiểm tra MIME thật finfo, chặn SVG script)
│   └── Views/
│       ├── layouts/                # header.php, navbar.php, footer.php
│       ├── partials/               # product_card.php
│       ├── home/                   # index.php (Hero, Danh mục, Tra cứu MCB, Kéo thả BOM)
│       ├── products/               # list.php (Bộ lọc kỹ thuật), detail.php (Bảng specs)
│       ├── quotes/                 # request_form.php, catalogue.php
│       ├── cart/                   # index.php, success.php
│       ├── errors/                 # 404.php
│       └── admin/                  # login.php, dashboard.php, products.php, product_form.php, quotes.php, orders.php, order_detail.php
├── config/
│   ├── database.php                # Kết nối PDO MySQL
│   └── app.php                     # Cấu hình chung, hotline, hạn mức upload
├── core/
│   ├── Database.php                # PDO Singleton chuẩn, Transaction wrapper
│   ├── Router.php                  # Định tuyến Regex, Clean URL, Middleware
│   ├── Controller.php              # Render View, JSON, CSRF, Redirect
│   └── helpers.php                 # Hàm toàn cục e(), money(), url(), icon() SVG inline
├── public/
│   ├── index.php                   # Front Controller
│   ├── .htaccess                   # Rewrite Clean URL & Cache Expire Headers
│   ├── css/
│   │   ├── main.css                # Style Panasonic Blue #004098, Silver/Slate, 3D Tilt Card
│   │   └── responsive.css          # Mobile Drawer, Floating Action Buttons
│   ├── js/
│   │   ├── app.js                  # Drawer, Slider, Tính Ampe MCB, Drag & Drop BOM
│   │   ├── quicksearch.js          # Live Search AJAX theo mã Model (WEV, BBD...)
│   │   └── cart.js                 # Giỏ hàng AJAX tự cập nhật giá sỉ
│   └── uploads/
│       ├── products/               # Ảnh vector sản phẩm (Moderva, Wide, MCB, Quạt trần, LED...)
│       └── catalogs/               # File PDF Catalogue & Datasheet
├── storage/
│   ├── .htaccess                   # Chặn toàn bộ truy cập HTTP trực tiếp
│   └── boms/                       # Nơi lưu file dự toán công trình của khách
├── database.sql                    # Schema InnoDB utf8mb4 & dữ liệu thực tế
└── .htaccess                       # Điều hướng root vào /public
```

---

## 2. HƯỚNG DẪN CÀI ĐẶT TRÊN XAMPP / WAMP

### Bước 1: Đặt mã nguồn
- Đặt thư mục dự án `panasonic` vào thư mục web:
  - Trên XAMPP: `C:\xampp\htdocs\panasonic` (hoặc ổ `E:\HTML\panasonic` nếu cấu hình VirtualHost).

### Bước 2: Nhập Cơ sở Dữ liệu
1. Mở trình quản lý MySQL (phpMyAdmin hoặc MySQL CLI).
2. Tạo cơ sở dữ liệu `panasonic_shop` (nếu chưa có).
3. Import file `database.sql`:
   ```bash
   mysql -u root -p panasonic_shop --default-character-set=utf8mb4 < database.sql
   ```
*(File `database.sql` đã tạo sẵn bảng và chèn đầy đủ dữ liệu thực tế: Công tắc Moderva Xám Ánh Kim, Wide Series WEV5001SW, WEV1582SW, MCB 2P 16A 6kA BBD2161CNV, Quạt trần 5 cánh F-60UFN, Downlight 9W).*

### Bước 3: Cấu hình kết nối DB (nếu cần)
- Chỉnh sửa file [config/database.php](file:///config/database.php):
  ```php
  return [
      'host'     => '127.0.0.1',
      'port'     => '3306',
      'database' => 'panasonic_shop',
      'username' => 'root',
      'password' => '', // Mật khẩu root MySQL của bạn (XAMPP mặc định để trống)
  ];
  ```

### Bước 4: Chạy Website
- Truy cập qua trình duyệt:
  - Nếu qua Apache XAMPP: `http://localhost/panasonic/` (nhờ file `.htaccess` gốc, hệ thống tự nhận diện đường dẫn sạch).
  - Nếu dùng PHP CLI Server:
    ```bash
    php -S localhost:8000 -t public public/index.php
    ```
    Truy cập: `http://localhost:8000`

---

## 3. THÔNG TIN ĐĂNG NHẬP TRANG QUẢN TRỊ (ADMIN)
- Đường dẫn: `http://localhost/panasonic/admin/login` (hoặc `http://localhost:8000/admin/login`)
- **Tài khoản:** `admin`
- **Mật khẩu:** `Admin@123`
*(Bảo mật: Mật khẩu mã hóa chuẩn `password_hash()` BCRYPT, có cơ chế throttle chống Brute-force 5 lần/15 phút và chống Session Fixation).*

---

## 4. CÁC TÍNH NĂNG ĐẶC THÙ NGÀNH ĐIỆN ĐÃ TÍCH HỢP
1. **Live Search theo Model Panasonic:**
   - Gõ "WEV", "BBD", "F-60", "Moderva" trên thanh tìm kiếm (hoặc nhấn phím `/`), kết quả hiển thị dạng Dropdown kèm giá, ảnh và số lượng tồn kho.
2. **Bộ lọc kỹ thuật điện chuyên sâu:**
   - Lọc theo Dòng cắt ngắn mạch `kA` (4.5kA, 6.0kA, 10kA).
   - Lọc theo Dòng điện định mức `Ampe` (10A, 16A, 20A, 32A...).
   - Lọc theo Số cực `Poles` (1P, 2P, 3P, 4P).
   - Lọc theo Series (Wide, Halumie, Gen-X, Moderva, Full Color, Minerva).
3. **Công cụ tra cứu MCB/RCBO theo tải Ampe thực tế (Trang chủ):**
   - Tự động tính dòng làm việc $I_b = \frac{P}{U \cdot \cos\varphi}$, dòng chọn cầu dao $I_n \ge 1.25 \times I_b$, đề xuất tiết diện dây đồng và hiển thị link sản phẩm Panasonic tương thích.
4. **Hệ thống giỏ hàng tự động áp giá công trình:**
   - Khi số lượng đặt mua $\ge$ ngưỡng bán buôn (`wholesale_min_qty`), hệ thống tự chuyển từ giá lẻ sang giá công trình (chiết khấu 15% - 30%).
5. **Tiếp nhận file dự toán công trình (BOM):**
   - Hỗ trợ kéo thả file Excel, PDF, DWG (CAD), ZIP lên đến 10MB; lưu trữ ngoài thư mục web công khai (`storage/boms`), bảo vệ thông tin thầu tuyệt đối.
