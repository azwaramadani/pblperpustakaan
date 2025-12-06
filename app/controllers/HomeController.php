<?php

class HomeController
{
    public function index()
    {
        $bookingmodel = new Booking();
        $toprooms = $bookingmodel->getTopRoomsbyBooking(3);

        require __DIR__ . '/../views/home/index.php';
    }

}
