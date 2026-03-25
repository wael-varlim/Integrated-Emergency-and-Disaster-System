<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kolirt\Openstreetmap\Facade\Openstreetmap;
use Illuminate\Support\Facades\Http;

class testController extends Controller
{
    function mytest()
    {
        // $timeout=0;
        // do
        // {
        // $address = Openstreetmap::reverse(33.50576, 36.32183);
        // sleep(1);
        // $timeout++;
        // //$address ? $address = Openstreetmap::reverse(33.511567, 36.306655) : true;
        // }while(!$address && $timeout<5);


        $response = Http::withOptions(['verify' => false])
    ->withUserAgent('YourApp/1.0')
    ->get('https://nominatim.openstreetmap.org/reverse', [
        'format' => 'json',
        'lat' => 33.50576,
        'lon' => 36.32183,
        'accept-language' => 'ar'
    ]);
    $address = $response->json();


        dd($address);
        //return $address ? $address->address->display_name : 'address not found';
    }
}
