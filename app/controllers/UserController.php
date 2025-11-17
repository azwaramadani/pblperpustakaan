<?php
require_once __DIR__ . '/../../core/Session.php';

class UserController
{
    public function home()
    {
        # pastikan user login
        if (!Session::get('user_id')) {
            header("Location: ?route=Auth/login");
            exit;
        }

        $userModel  = new User();
        $roomModel  = new Room();

        $user_id = Session::get('user_id');
        $user    = $userModel->findById($user_id);

        $rooms   = $roomModel->getAll();

        # data yang dikirim ke view
        $data = [
            'user'  => $user,
            'rooms' => $rooms
        ];

        require __DIR__ . '/../views/user/home.php';
    }

    public function ruangan()
    {
        Session::checkUserLogin();

        # Ambil data user dari session
        $userModel = new User();
        $user = $userModel->findById(Session::get('user_id'));

        # Ambil semua ruangan
        $roomModel = new Room();
        $rooms = $roomModel->getAll();

        # Data dikirim ke view
        $data = [
            'user' => $user,
            'rooms' => $rooms
        ];

        require __DIR__ . '/../views/user/ruangan.php';
    }

    public function riwayat()
    {


    }
}
