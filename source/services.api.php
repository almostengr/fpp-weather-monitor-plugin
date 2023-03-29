<?php

include_once '/opt/fpp/www/common.php';

define("GET", "GET");
define("POST", "POST");
define("EMPTY_STRING", "");

abstract class BaseApiService
{
    protected function callAPI(string $method, string $url, array $data, array $headers = false, string $userAgent = EMPTY_STRING)
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

interface INwsWeatherService extends IWeatherService
{
    public function getStationIdFromGpsCoordinates(string $latitude, string $longitude);
}

final class NwsApiService extends BaseApiService implements INwsWeatherService
{
    private ISettingsFormService $settings;

    public function __construct(ISettingsFormService $settings)
    {
        $this->settings = $settings;
    }

    function getHeaders(): array
    {
        return array(
            "Content-Type" => "application/json",
        );
    }

    function userAgent(): string
    {
        // todo get user email address
        return "User-Agent: (myweatherapp.com, " . $this->settings->getEmailAddress() . ")";
    }

    public function getStationIdFromGpsCoordinates(): string
    {
        $latitude = ReadSettingFromFile("Latitude");
        $longitude = ReadSettingFromFile("Longitude");

        if (empty($latitude) || empty($longitude) || $latitude === false || $longitude === false) {
            // array_push($this->errors, "Longitude and latitude need to be set. Go to Content Setup > FPP Settings > System to set your location.");
            // todo display errur
            return EMPTY_STRING;
        }

        $pointsRoute = "https://api.weather.gov/points/" . $latitude . "," . $longitude; // 32.3546,-86.2629
        $pointResponse = $this->callAPI(GET, $pointsRoute, array(), $this->getHeaders(), $this->userAgent());
        $stationsResponse =
            $this->callAPI(GET, $pointResponse->properties->observationStations, array(), $this->getHeaders(), $this->userAgent());
        return $stationsResponse->features->properties->stationIdentifer;
    }

    public function getLatestObservations(): Observation
    {
        $route = "https://api.weather.gov/stations/KMGM/observations/latest";
        $response = $this->callAPI(GET, $route, array(), $this->getHeaders(), $this->userAgent());
        return Observation::CreateFromNwsApi(
            $response->properties->windSpeed,
            $response->properties->windGust,
            $response->properties->textDescription
        );
    }
}

final class FppApiService extends BaseApiService
{
    public function getShowStatus()
    {

    }

    public function postStopPlaylistGracefully()
    {

    }
}


final class Observation
{
    private float $windSpeedKmh;
    private float $gustSpeedKmh;
    private string $description;

    private function __construct(float $windSpeed, float $gustSpeed, string $description)
    {
        $this->windSpeedKmh = $windSpeed;
        $this->gustSpeedKmh = $gustSpeed;
        $this->description = $description;
    }

    public static function CreateFromNwsApi(float $windSpeed, float $gustSpeed, string $description)
    {
        return new Observation($windSpeed, $gustSpeed, $description);
    }

    public function getWindSpeed()
    {
        return $this->windSpeedKmh;
    }

    public function getGustSpeed()
    {
        return $this->gustSpeedKmh;
    }

    public function getDescription()
    {
        return $this->description;
    }
}