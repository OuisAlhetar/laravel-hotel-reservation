<?php

namespace App\Observers;

use App\Models\Reservations;
use Illuminate\Support\Facades\Mail;

class ReservationObserver
{
    public function created(Reservations $reservation)
    {
        // Notify admin (or customer)
        Mail::raw('A new reservation has been created by: '. $reservation->customer_name, function ($message) {
            $message->to("ouis@gmail.com")->subject('New Reservation');
        });
    }


    public function updated(Reservations $reservation): void
    {
        // Notify admin (or customer)
        Mail::raw("the reservation for $reservation->customer_name been Updated \n". $reservation, function ($message) {
            $message->to('ouis@gmail.com')->subject("Reservation Updated");
        });
    }
}




// namespace App\Observers;

// use App\Models\Reservations;
// use Illuminate\Support\Facades\Mail;

// class ReservationObserver
// {
//     /**
//      * Handle the Reservations "created" event.
//      */
//     public function created(Reservations $reservation): void
//     {
//          // Notify admin (or customer)
//         Mail::raw('A new reservation has been created: ' . $reservation->customer_name, function ($message) {
//             $message->to('qasmalmhkmy@gmail.com')->subject('New Reservation');
//         });
//     }

//     /**
//      * Handle the Reservations "updated" event.
//      */
//     public function updated(Reservations $reservations): void
//     {
//         //
//     }

//     /**
//      * Handle the Reservations "deleted" event.
//      */
//     public function deleted(Reservations $reservations): void
//     {
//         //
//     }

//     /**
//      * Handle the Reservations "restored" event.
//      */
//     public function restored(Reservations $reservations): void
//     {
//         //
//     }

//     /**
//      * Handle the Reservations "force deleted" event.
//      */
//     public function forceDeleted(Reservations $reservations): void
//     {
//         //
//     }
// }