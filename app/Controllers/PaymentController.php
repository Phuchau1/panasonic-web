<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Order;
use App\Services\Mailer;
use App\Services\MomoService;
use App\Services\VnPayService;
use Core\Controller;

final class PaymentController extends Controller
{
    /**
     * Xử lý kết quả trả về từ Cổng VNPAY sau khi khách thực hiện thanh toán
     * GET /thanh-toan/vnpay-return
     */
    public function vnpayReturn(): void
    {
        $vnpayService = new VnPayService();
        $res = $vnpayService->verifyReturn($_GET);

        $orderCode = $res['order_code'];
        $orderModel = new Order();
        $order = $orderModel->findByCode($orderCode);

        if (!$order) {
            flash('error', 'Không tìm thấy thông tin đơn hàng #' . $orderCode);
            $this->redirect('/');
            return;
        }

        if ($res['success']) {
            $orderModel->updatePaymentStatus($orderCode, 'paid');
            Mailer::dispatchAsync($orderCode);
            flash('success', 'Thanh toán đơn hàng #' . $orderCode . ' qua VNPAY thành công!');
        } else {
            flash('error', $res['message'] ?: 'Giao dịch VNPAY không thành công hoặc đã bị hủy.');
        }

        $this->redirect('/dat-hang-thanh-cong/' . $orderCode);
    }

    /**
     * Xử lý kết quả trả về từ Cổng MoMo sau khi khách thực hiện thanh toán
     * GET /thanh-toan/momo-return
     */
    public function momoReturn(): void
    {
        $momoService = new MomoService();
        $res = $momoService->verifyReturn($_GET);

        $orderCode = $res['order_code'];
        $orderModel = new Order();
        $order = $orderModel->findByCode($orderCode);

        if (!$order) {
            flash('error', 'Không tìm thấy thông tin đơn hàng #' . $orderCode);
            $this->redirect('/');
            return;
        }

        if ($res['success']) {
            $orderModel->updatePaymentStatus($orderCode, 'paid');
            Mailer::dispatchAsync($orderCode);
            flash('success', 'Thanh toán đơn hàng #' . $orderCode . ' qua Ví MoMo thành công!');
        } else {
            flash('error', $res['message'] ?: 'Giao dịch MoMo không thành công hoặc đã bị hủy.');
        }

        $this->redirect('/dat-hang-thanh-cong/' . $orderCode);
    }

    /**
     * Màn hình Sandbox Simulator cho MoMo khi kiểm thử nội bộ
     * GET /thanh-toan/momo-simulator
     */
    public function momoSimulator(): void
    {
        $orderCode = trim((string)($this->input('order_code') ?? ''));
        $orderModel = new Order();
        $order = $orderModel->findByCode($orderCode);

        if (!$order) {
            $this->abort(404, 'Không tìm thấy đơn hàng cần thanh toán.');
        }

        $this->render('payment/momo_simulator', [
            'title' => 'Cổng Thanh Toán MoMo Sandbox - ' . $orderCode,
            'order' => $order,
        ]);
    }
}
