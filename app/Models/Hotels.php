<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotels extends Model
{
    use HasFactory;

    protected $table = "hotels";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'province',
        'country',
        'rating',
        'image_url'
    ];

    public function rooms()
    {
        return $this->hasMany(Rooms::class, 'hotel_id');
    }
}