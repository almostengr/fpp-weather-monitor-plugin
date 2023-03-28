<?php

include_once '/opt/fpp/www/common.php';

define("GET", "GET");
define("POST", "POST");
define("EMPTY_STRING", "");

define("WM_PLUGIN_NAME", "weather_monitor");
define("EMAIL_ADDRESS_SETTING", "emailAddressSetting");
define("NWS_WEATHER_STATION_ID", "nwsWeatherStationId");
define("WEATHER_DESCRIPTIONS", "weatherDescriptions");
define("MAX_WIND_SPEED", "maxWindSpeedKmH");
define("MAX_GUST_SPEED", "maxGustSpeedKmH");

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

final class NwsApiService extends BaseApiService implements IWeatherService
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

final class FppApiService extends BaseApiService
{
    public function getShowStatus()
    {

    }

    public function postStopPlaylistGracefully()
    {

    }
}

final class SettingsFormService
{
    private function isTemperatureInF(): bool
    {
        return ReadSettingFromFile("temperatureInF") == 0 ? false : true;
    }

    private function convertKmhToMph($speedKmh)
    {
        return $speedKmh / 1.609344;
    }

    private function convertMphToKmh($speedMph)
    {
        return $speedMph * 1.609344;
    }

    public function saveEmailAddress(string $emailAddress)
    {
        $isValid = filter_var($emailAddress, FILTER_VALIDATE_EMAIL);

        if ($isValid) {
            WriteSettingToFile(EMAIL_ADDRESS_SETTING, WM_PLUGIN_NAME);
            return EMPTY_STRING;
        }

        return "Invald email address";
    }

    public function getEmailAddress(): string
    {
        // return ReadSettingFromFile(EMAIL_ADDRESS_SETTING, WM_PLUGIN_NAME);
        $email = ReadSettingFromFile(EMAIL_ADDRESS_SETTING, WM_PLUGIN_NAME);
        return $email === false ? EMPTY_STRING : $email;
    }

    public function saveWeatherStationId(string $stationId): string
    {
        $existingStationId = ReadSettingFromFile(NWS_WEATHER_STATION_ID, WM_PLUGIN_NAME);

        if ($existingStationId == $stationId) {
            return EMPTY_STRING;
        }

        if (empty($stationId)) {
            return "Station ID cannot be empty";
        }

        $latitude = ReadSettingFromFile("Latitude");
        $longitude = ReadSettingFromFile("Longitude");

        if (empty($latitude) || empty($longitude)) {
            return "Longitude and latitude need to be set. Go to FPP Settings > System to set your shows location.";
        }

        $nwsService = new NwsApiService();
        $stationId = $nwsService->getStationIdFromGpsCoordinates($latitude, $longitude);

        WriteSettingToFile(NWS_WEATHER_STATION_ID, $stationId, WM_PLUGIN_NAME);
        return EMPTY_STRING;
    }

    public function getWeatherStationId(): string
    {
        $stationId = ReadSettingFromFile(NWS_WEATHER_STATION_ID, WM_PLUGIN_NAME);
        // return ReadSettingFromFile(NWS_WEATHER_STATION_ID, WM_PLUGIN_NAME);
        return $stationId === false ? EMPTY_STRING : $stationId;
    }

    public function saveWeatherTextDescriptions(string $descriptions): string
    {
        WriteSettingToFile(WEATHER_DESCRIPTIONS, strtolower($descriptions), WM_PLUGIN_NAME);
        return EMPTY_STRING;
    }

    public function getWeatherTextDescriptions(): string
    {
        // return ReadSettingFromFile(WEATHER_DESCRIPTIONS, WM_PLUGIN_NAME);
        $descriptions = ReadSettingFromFile(WEATHER_DESCRIPTIONS, WM_PLUGIN_NAME);
        return $descriptions === false ? EMPTY_STRING : $descriptions;
    }

    public function saveMaxWindSpeed(string $windSpeedString): string
    {
        $windSpeed = (float) $windSpeedString;

        if ($windSpeed < 1) {
            return "Wind speed cannot be less than or equal to zero (0)";
        }

        if ($this->isTemperatureInF()) {
            $windSpeed = $this->convertKmhToMph($windSpeed);
        }

        WriteSettingToFile(MAX_WIND_SPEED, $windSpeed, WM_PLUGIN_NAME);
        return EMPTY_STRING;
    }

    public function getMaxWindSpeed(): float
    {
        $windSpeed = ReadSettingFromFile(MAX_WIND_SPEED, WM_PLUGIN_NAME);

        if ($windSpeed === false) {
            return 999;
        }

        if ($this->isTemperatureInF()) {
            return $this->convertKmhToMph($windSpeed);
        }

        return $windSpeed;
    }

    public function getMaxGustSpeed(): float
    {
        $maxGustSpeed = ReadSettingFromFile(MAX_GUST_SPEED, WM_PLUGIN_NAME);

        if ($maxGustSpeed === false)
        {
            return 999;
        }

        if ($this->isTemperatureInF()) {
            return $this->convertKmhToMph($maxGustSpeed);
        }

        return $maxGustSpeed;
    }

    public function saveMaxGustSpeed(string $gustSpeedString): string
    {
        $gustSpeed = (float) $gustSpeedString;
        
        if ($gustSpeed < 1) {
            return "Gust speed canont be less than or equal to zero (0).";
        }

        if ($this->isTemperatureInF()) {
            $gustSpeed = $this->convertMphToKmh($gustSpeed);
        }

        WriteSettingToFile(MAX_GUST_SPEED, $gustSpeed, WM_PLUGIN_NAME);
        return EMPTY_STRING;
    }

}