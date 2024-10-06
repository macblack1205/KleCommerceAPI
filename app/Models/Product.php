<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'image', 'title', 'slug', 'description', 'price'];

    protected $appends = ['image_url', 'average_rating'];

    protected static function booted()
    {
        static::creating(function ($product) {
            $baseSlug = Str::slug($product->title) . '-' . $product->price;
            $slug = $baseSlug;

            // Ensure the slug is unique
            $count = 1;
            while (self::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }

            $product->slug = $slug;
        });
    }

    public function getImageUrlAttribute()
    {
        // return env('APP_URL') 'storage/' .
        return $this->image ? url( $this->image) : null;
    }


    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }


    public function user() {
        return $this->belongsTo(User::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }
}
