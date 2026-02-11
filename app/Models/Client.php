<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'document_number', 'phone', 'email', 'address', 'notes'];

    // Relación: Un cliente tiene muchas órdenes
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}