<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesPointsRequest;
use App\Models\Salepoint;
use Illuminate\Http\JsonResponse;


class SalesPointsApiController extends Controller
{

    public function index(SalesPointsRequest $request) : JsonResponse {


        if($request->has('opened')) {

            // Retrieve a collection of sales points in the format 'id' => 'openingHours'
            $openingDaysAndHours = Salepoint::pluck('openingHours', 'id');

            // Get array of IDs for sales points that are currently open
            $salesPoint = new Salepoint();
            $openedPointsIds = $salesPoint->isOpen($openingDaysAndHours);

            // Convert the "opened" request string parameter to a boolean (true or false)
            $isOpen = filter_var($request->opened, FILTER_VALIDATE_BOOLEAN);

            // Fetch data from the database based on whether the points are open or not
            $salesPoints = $isOpen ? Salepoint::whereIn('id', $openedPointsIds)->get() : Salepoint::whereNotIn('id', $openedPointsIds)->get();

        } else {

            // Fetch all sales points if no specific "opened" filter is provided
            $salesPoints = Salepoint::all();

        }
        // Remove empty fields and decode JSON for the 'openingHours'
        foreach ($salesPoints as $salesPoint){

            if(empty($salesPoint->link)) unset($salesPoint->link);
            if(empty($salesPoint->remarks)) unset($salesPoint->remarks);
            $salesPoint->openingHours = json_decode($salesPoint->openingHours);

        }

        return response()->json($salesPoints, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


    }

}
