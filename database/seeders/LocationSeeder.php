<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            // Villages on Isle of Arran
            ['name' => 'Brodick', 'latitude' => 55.5766, 'longitude' => -5.1486, 'altitude' => 10, 'type' => 'Village'],
            ['name' => 'Lamlash', 'latitude' => 55.5333, 'longitude' => -5.1333, 'altitude' => 5, 'type' => 'Village'],
            ['name' => 'Lochranza', 'latitude' => 55.7056, 'longitude' => -5.2958, 'altitude' => 15, 'type' => 'Village'],
            ['name' => 'Blackwaterfoot', 'latitude' => 55.5011, 'longitude' => -5.3336, 'altitude' => 5, 'type' => 'Village'],
            ['name' => 'Whiting Bay', 'latitude' => 55.4919, 'longitude' => -5.0958, 'altitude' => 10, 'type' => 'Village'],
            ['name' => 'Kildonan', 'latitude' => 55.4333, 'longitude' => -5.1167, 'altitude' => 5, 'type' => 'Village'],
            ['name' => 'Shiskine', 'latitude' => 55.5167, 'longitude' => -5.3167, 'altitude' => 20, 'type' => 'Village'],
            ['name' => 'Corrie', 'latitude' => 55.6458, 'longitude' => -5.1417, 'altitude' => 10, 'type' => 'Village'],
            ['name' => 'Machrie', 'latitude' => 55.5500, 'longitude' => -5.3333, 'altitude' => 15, 'type' => 'Village'],
            ['name' => 'Pirnmill', 'latitude' => 55.6500, 'longitude' => -5.3833, 'altitude' => 10, 'type' => 'Village'],
            ['name' => 'Sannox', 'latitude' => 55.6667, 'longitude' => -5.1667, 'altitude' => 5, 'type' => 'Village'],
            ['name' => 'Catacol', 'latitude' => 55.6833, 'longitude' => -5.3167, 'altitude' => 10, 'type' => 'Village'],
            ['name' => 'Dougarie', 'latitude' => 55.6167, 'longitude' => -5.3667, 'altitude' => 15, 'type' => 'Village'],
            ['name' => 'Kilmory', 'latitude' => 55.4500, 'longitude' => -5.2333, 'altitude' => 20, 'type' => 'Village'],

            // Hill (Summit)
            ['name' => 'Goatfell', 'latitude' => 55.6258, 'longitude' => -5.1917, 'altitude' => 874, 'type' => 'Hill'],
            ['name' => 'Sail Chalmadale', 'latitude' => 55.6097, 'longitude' => -5.3135, 'altitude' => 490, 'type' => 'Hill'],
            ['name' => 'Coire Fhionn Lochan', 'latitude' => 55.6663, 'longitude' => -5.37332, 'altitude' => 326, 'type' => 'Hill'],
            ['name' => 'Cioch na h\'Oighe', 'latitude' => 55.6477, 'longitude' => -5.1813, 'altitude' => 660, 'type' => 'Hill'],
            ['name' => 'Cìr Mhòr', 'latitude' => 55.6391, 'longitude' => -5.2225, 'altitude' => 799, 'type' => 'Hill'],
            ['name' => 'Beinn Tarsuinn', 'latitude' => 55.6558, 'longitude' => -5.2908, 'altitude' => 556, 'type' => 'Hill'],
            ['name' => 'Caisteal Abhail', 'latitude' => 55.6498, 'longitude' => -5.2293, 'altitude' => 859, 'type' => 'Hill'],

            // Mainland Villages
            ['name' => 'Troon', 'latitude' => 55.5436, 'longitude' => -4.6636, 'altitude' => 5, 'type' => 'Village'],
            ['name' => 'Ardrossan', 'latitude' => 55.6392, 'longitude' => -4.8119, 'altitude' => 10, 'type' => 'Village'],

            // Marine Forecast Locations
            ['name' => 'Brodick Bay', 'latitude' => 55.5766, 'longitude' => -5.1486, 'altitude' => 0, 'type' => 'Marine'], 
            ['name' => 'Sannox Bay', 'latitude' => 55.6667, 'longitude' => -5.1667, 'altitude' => 0, 'type' => 'Marine'],
            ['name' => 'Lamlash Bay', 'latitude' => 55.5333, 'longitude' => -5.1333, 'altitude' => 0, 'type' => 'Marine'],
            ['name' => 'Kildonan', 'latitude' => 55.4333, 'longitude' => -5.1167, 'altitude' => 0, 'type' => 'Marine'], // Marine forecast for Kildonan coast
            ['name' => 'Blackwaterfoot Beach', 'latitude' => 55.5011, 'longitude' => -5.3336, 'altitude' => 0, 'type' => 'Marine'],
            ['name' => 'Lochranza', 'latitude' => 55.7056, 'longitude' => -5.2958, 'altitude' => 0, 'type' => 'Marine'],
            ['name' => 'Troon Harbour', 'latitude' => 55.5436, 'longitude' => -4.6636, 'altitude' => 0, 'type' => 'Marine'],
            ['name' => 'Ardrossan Ardrossan', 'latitude' => 55.6392, 'longitude' => -4.8119, 'altitude' => 0, 'type' => 'Marine'],
            ['name' => 'Claonaig Port', 'latitude' => 55.7667, 'longitude' => -5.3833, 'altitude' => 0, 'type' => 'Marine'] // Added
        ];

        DB::table('locations')->insert($locations);
    }
}