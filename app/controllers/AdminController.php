<?php
require_once __DIR__ . '/../../core/Session.php';

class AdminController {
    public function dashboard()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $adminModel   = new Admin();
        $bookingModel = new Booking();
        $feedbackModel= new Feedback();

        $adminId   = Session::get('admin_id');
        $admin     = $adminModel->findById($adminId);
        $topRooms  = $bookingModel->getTopRoomsByBooking(5);
        $bookings  = $bookingModel->getAll();
        $feedbacks = $feedbackModel->getAllWithRelations();

        require __DIR__ . '/../views/admin/dashboard.php';
    }
}