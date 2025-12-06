<?php
require_once __DIR__ . '/../../core/Session.php';

class FeedbackController
{
    //buat user liat feedback mereka
    public function form($bookingId)
    {
        Session::checkUserLogin();
        Session::preventCache();

        $bookingModel = new Booking();
        $userModel    = new User();
        $feedbackModel = new Feedback();

        $userId  = Session::get('user_id');
        $booking = $bookingModel->findByIdAndUser($bookingId, $userId); //mastiin bisa kasih feedback kalau sudah booking

        if (!$booking) {
            http_response_code(404);
            exit('Booking tidak ditemukan.');
        }

        // Cek apakah sudah ada feedback
        $existingFeedback = $feedbackModel->findByBooking($bookingId, $userId);

        $data = [
            'user' => $userModel->findById($userId),
            'booking' => $booking,
            'feedback' => $existingFeedback
        ];

        require __DIR__ . '/../views/user/feedback_form.php';
    }

    // Simpan feedback
    public function store()
    {
        Session::checkUserLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=User/riwayat');
            exit;
        }

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $rating    = trim($_POST['rating'] ?? '');
        // terima nama field komentar atau isi_feedback (fallback)
        $komentar  = trim($_POST['komentar'] ?? ($_POST['isi_feedback'] ?? ''));

        if (!$bookingId || $rating === '' || $komentar === '') {
            Session::set('flash_error', 'Isi rating dan feedback.');
            header('Location: ?route=Feedback/form/' . $bookingId);
            exit;
        }

        $bookingModel  = new Booking();
        $feedbackModel = new Feedback();

        $userId  = Session::get('user_id');
        $booking = $bookingModel->findByIdAndUser($bookingId, $userId);
        if (!$booking) {
            http_response_code(404);
            exit('Booking tidak ditemukan.');
        }

        if ($feedbackModel->findByBooking($bookingId, $userId)) {
            Session::set('flash_error', 'Feedback sudah pernah dikirim.');
            header('Location: ?route=User/riwayat');
            exit;
        }

        $payload = [
            'booking_id' => $bookingId,
            'user_id'    => $userId,
            'room_id'    => $booking['room_id'],
            'puas'       => (strtolower($rating) === 'puas') ? 1 : 0,
            'komentar'   => $komentar
        ];

        $feedbackModel->create($payload);

        Session::set('flash_success', 'Feedback terkirim. Terima kasih!');
        header('Location: ?route=User/riwayat');
        exit;
    }

}
