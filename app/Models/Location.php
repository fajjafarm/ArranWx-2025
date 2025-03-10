<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'altitude',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'altitude' => 'float',
        'type' => 'string', // Enum: Village, Marine, Hill
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Get the weather type as an enum-like value.
     *
     * @return string
     */
    public function getTypeAttribute($value)
    {
        return ucfirst($value); // Capitalize for display (Village, Marine, Hill)
    }
}