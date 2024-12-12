<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservations extends Model
{
    use HasFactory;

    protected $table = "reservations";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'hotel_id',
        'room_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'check_in',
        'check_out',
        'status'
    ];

    // [Relationships]
    
    // Reservation belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Reservation belongs to a hotel (optional depending on your database structure)
    public function hotel()
    {
        return $this->belongsTo(Hotels::class);
    }

    // Reservation belongs to a room (optional)
    public function room()
    {
        return $this->belongsTo(Rooms::class);
    }
}