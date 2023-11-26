<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Query\ReverseQuery;

class Home extends BaseController
{
    public function index()
    {
        $clientIP = $this->request->getIPAddress();
// Use a service like ipinfo.io
$response = file_get_contents("http://ipinfo.io/{$clientIP}/geo");
$locationData = json_decode($response, true);

        $data=[
            'title' => "acasa",
            'location' => $locationData,
        ];
        return view('websitehome/home', $data);
    }
    public function restaurants()
    {
        $clientIP = $this->request->getIPAddress();
// Use a service like ipinfo.io
$response = file_get_contents("http://ipinfo.io/{$clientIP}/geo");
$locationData = json_decode($response, true);

        $data=[
            'title' => "restaurante",
            'location' => $locationData,
        ];
        return view('websitehome/restaurants', $data);
    }
    public function getLocationData()
{
    $json = $this->request->getJSON();
    $latitude = $json->latitude;
    $longitude = $json->longitude;

    // Use a geocoding service to convert lat, long to a human-readable address
    // For example, using Google Maps Geocoding API
    $location = $this->convertLatLongToAddress($latitude, $longitude);

    return $this->response->setJSON(['location' => $location]);
}
private function convertLatLongToAddress($lat, $lon)
{
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}";

    $opts = [
        "http" => [
            "header" => "User-Agent: YourApp\r\n"
        ]
    ];

    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);
    $data = json_decode($response, true);

    if (isset($data['address'])) {
        $address = $data['address'];
        // The city might be in different fields depending on the location
        if (isset($address['city'])) {
            return $address['city'];
        } elseif (isset($address['town'])) {
            return $address['town'];
        } elseif (isset($address['village'])) {
            return $address['village'];
        } elseif (isset($address['county'])) {
            return $address['county'];
        }
    }

    return "Unknown City";
}


}
