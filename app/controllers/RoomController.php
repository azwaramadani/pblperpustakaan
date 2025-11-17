<?php 
require_once __DIR__ . '/../../core/Session.php';

class RoomController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function index() {
        $roomModel = new Room($this->db);
        $rooms = $roomModel->getAll();

        //supaya bisa tampil di view
        include __DIR__ . '/../views/user/ruangan.php';
    }
}
?>
