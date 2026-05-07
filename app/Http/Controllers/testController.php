<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kolirt\Openstreetmap\Facade\Openstreetmap;
use Illuminate\Support\Facades\Http;

class testController extends Controller
{

    function randomFloat($min, $max) 
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }


    function mytest(Request $request)
    {
        //32.944, 35.876
        //34.089, 37.095



        // $timeout=0;
        // do
        // {
        // $address = Openstreetmap::reverse(33.50576, 36.32183);
        // sleep(1);
        // $timeout++;
        // //$address ? $address = Openstreetmap::reverse(33.511567, 36.306655) : true;
        // }while(!$address && $timeout<5);


        //---------------------------------------------------------------------
        $fullData='';
        for ($i=0; $i < 40; $i++) { 
    
        
            $response = Http::withOptions(['verify' => false])
                ->withUserAgent('YourApp/1.0')
                ->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $this->randomFloat(32.944, 34.089),
                'lon' => $this->randomFloat(34.089, 37.095),
                'accept-language' => 'en'
            ]);

            $address = $response->json();

            if (!$address || !isset($address['display_name'])) 
                {
                    continue;
                }
            $address = json_decode($response->body(), false);

            //dd($address);
            $fullData =$fullData . ' @@@@ ' . $address->display_name;

            sleep(1);
        }

        return $fullData;
   
    }






}
