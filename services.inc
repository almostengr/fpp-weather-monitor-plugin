<?php

include_once '/opt/fpp/www/common.php';

define("GET", "GET");
define("POST", "POST");

abstract class BaseService
{
    protected function callAPI(string $method, string $url, array $data, array $headers = false, string $userAgent = "")
    {
        // todo https://weichie.com/blog/curl-api-calls-with-php/
        $curl = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        if (!empty($userAgent)) {
            curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        }

        // EXECUTE:
        $result = curl_exec($curl);
        if (!$result) {
            die("Connection Failure");
        }
        curl_close($curl);
        return json_decode($result, false);
    }
}

interface IWeatherService
{
    public function getLatestObservations(): array; 
}

final class NwsApiService extends BaseService implements IWeatherService
{
    function getHeaders(): array
    {
        return array(
            "Content-Type" => "application/json",
        );
    }

    function userAgent(): string
    {
        // todo get user email address
        return "User-Agent: (myweatherapp.com, contact@myweatherapp.com)";
    }

    public function getStationIdFromGpsCoordinates(string $latitude, string $longitude): string
    {
        $pointsRoute = "https://api.weather.gov/points/" . $latitude . "," . $longitude; // 32.3546,-86.2629
        $pointResponse = $this->callAPI(GET, $pointsRoute, array(), $this->getHeaders(), $this->userAgent());
        $stationsResponse =
            $this->callAPI(GET, $pointResponse->properties->observationStations, array(), $this->getHeaders(), $this->userAgent());
        return $stationsResponse->features->properties->stationIdentifer;
    }

    public function getLatestObservations(): array
    {
        $route = "https://api.weather.gov/stations/KMGM/observations/latest";
        return $this->callAPI(GET, $route, array(), $this->getHeaders(), $this->userAgent());
    }
}

final class FppApiService extends BaseService
{
    public function getShowStatus()
    {

    }

    public function postStopPlaylistGracefully()
    {

    }
}