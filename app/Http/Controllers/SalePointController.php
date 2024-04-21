<?php

namespace App\Http\Controllers;

use App\Models\Salepoint;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SalePointController extends Controller
{
        private Salepoint $salesPoints;

        public function __construct(Salepoint $salesPoints){
            $this->salesPoints = $salesPoints;
        }

        public function retrieve() : RedirectResponse {
            //get sales points from remote server
            $salesPoints = $this->salesPoints->getSalesPoints();

            //update database records
            if($salesPoints) {
                $this->salesPoints->updateSalesPoints($salesPoints);
                return redirect(route('index'))->with('success', 'Data has been successfully received from remote API  adn saved to database!');
            }

            return redirect(route('index'))->with('error', 'Anything was wrong! Please check a link or try again later!');

        }


}
