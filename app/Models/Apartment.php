<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Apartment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'governorate_id',
        'city_id',
        'address',
        'title',
        'area',
        'description',
        'daily_price',
        'image1',
        'image2',
        'image3',
        'image4',
        'image5',
    ];

    /**
     * Get the user that owns the apartment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the governorate that owns the apartment.
     */
    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    /**
     * Get the city that owns the apartment.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
