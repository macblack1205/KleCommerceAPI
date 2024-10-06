<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'coupon', 'discount_percentage'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
