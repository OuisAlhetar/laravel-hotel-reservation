<?php

namespace App\Factories;

use App\Models\Hotels;
use App\Models\Rooms;
use App\Models\Reservations;

class ModelFactory
{
    public static function create($model, $data)
    {
        switch ($model) {
            case 'hotel':
                return Hotels::create($data);
            case 'room':
                return Rooms::create($data);
            case 'reservation':
                return Reservations::create($data);
            default:
                throw new \Exception("Unknown model type");
        }
    }
}