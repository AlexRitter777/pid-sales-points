<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Salepoint extends Model
{

    protected $keyType = 'string';

    public $timestamps = false;

    protected  $fillable = ['id', 'type', 'name', 'address', 'openingHours', 'lat', 'lon', 'services', 'payMethods', 'link', 'remarks'];


    /**
     * Retrieve an array of sales points from a remote API endpoint.
     * Returns an empty array in case of an unsuccessful HTTP request or if the response does not contain an array.
     * @return array
     */
    public function getSalesPoints(): array {

        $response = Http::get('https://data.pid.cz/pointsOfSale/json/pointsOfSale.json');

        if ($response->successful()) {

            $data = $response->json();
            return is_array($data) ? $data : [];

        }

        return [];

    }


    /**
     * Clears the current data from the table and stores new data from the provided array.
     *
     * @param array $salesPoints
     */
    public function updateSalesPoints (array $salesPoints) : void  {

        static::truncate();

        DB::transaction(function() use ($salesPoints){

            foreach ($salesPoints as $salesPoint) {

                static::create([
                    'id' => $salesPoint['id'],
                    'type' => $salesPoint['type'],
                    'name' => $salesPoint['name'],
                    'address' => $salesPoint['address'],
                    'openingHours' => json_encode($salesPoint['openingHours']),
                    'lat' => $salesPoint['lat'],
                    'lon' => $salesPoint['lon'],
                    'services' => $salesPoint['services'],
                    'payMethods' => $salesPoint['payMethods'],
                    'link' => $salesPoint['link'] ?? '',
                    'remarks' => $salesPoint['remarks'] ?? ''

                ]);

            }


        });


    }


    /**
     * Parses open hours and open days from a collection of IDs and their open hours,
     * and returns only the IDs of currently open sales points.
     *
     * @param Collection $openingDaysAndHours  where each key is a sales point ID and each value is a JSON string of opening hours and days.
     * @return array
     */
    public function isOpen(Collection $openingDaysAndHours) : array {

        $openedPoints = [];

        //Decode JSON strings into PHP objects.
        foreach ($openingDaysAndHours as $id =>$onePointDaysAndHours) {
            $openingDaysAndHours[$id] = json_decode($onePointDaysAndHours);
        }

        // Find opened sales points and put them into array
        foreach ($openingDaysAndHours as $id =>$onePointDaysAndHours) {

            if(empty($onePointDaysAndHours)) continue;

            foreach ($onePointDaysAndHours as $daysAndHoursIntervals) {

                $currentDayOfWeek = (Carbon::now()->dayOfWeek + 6) % 7;

                 if($currentDayOfWeek >= $daysAndHoursIntervals->from && $currentDayOfWeek <= $daysAndHoursIntervals->to) {

                     $hoursIntervals = explode(',', $daysAndHoursIntervals->hours);

                     foreach ($hoursIntervals as $hours) {

                         $hour = explode('-', $hours);
                         $openTime = Carbon::createFromFormat('H:i', $hour[0]);
                         $closeTime = Carbon::createFromFormat('H:i', $hour[1]);
                         $currentTime = Carbon::now();

                         if($currentTime->gte($openTime) && $currentTime->lte($closeTime)){

                             $openedPoints[] = $id;

                             continue 2;

                         }

                     }

                 }

            }

        }


        return $openedPoints;

    }



    /*
     * Instead Dump or DD
     *
     */
    public function debug($arr) {
        echo '<pre>' . print_r($arr, true) . '</pre>';
    }



}
