<?php
namespace App\Helpers;

class WeatherHelper
{
    public static function degreesToCardinal($degrees)
    {
        if ($degrees === null) {
            return 'N/A';
        }
        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
        $index = round($degrees / 22.5) % 16;
        return $directions[$index];
    }
}