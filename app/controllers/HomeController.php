<?php

class HomeController
{
    public function index()
    {
        require __DIR__ . '/../views/home/index.php';
    }

    public function landingPageTopRooms()
    {
        $bookingmodel = new Booking();
        $toprooms = $bookingmodel->getTopRoomsByBooking(3);

        require __DIR__ . '/../views/home/index.php';
    }
}
