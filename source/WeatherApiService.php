<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/BaseApiService.php');

interface WeatherApiServiceInterface
{
    public function getLatestObservations(): ObservationModel;
}

interface NwsWeatherApiServiceInterface extends WeatherApiServiceInterface
{
    // public function getStationIdFromGpsCoordinates(): string;
    public function getPointsDetailsFromGpsCoordinates();
    public function getStationIdFromPointsResponse($pointsResponse): string;
    public function getAlertZoneIdFromPointsResponse($pointsResponse): string;
}

final class NwsApiWeatherService extends BaseApiService implements NwsWeatherApiServiceInterface
{
    private $settingService;

    public function __construct(SettingServiceInterface $settingServiceInterface)
    {
        $this->settingService = $settingServiceInterface;
    }

    private function userAgent(): string
    {
        // return "User-Agent: (FalconPiPlayer, " . ReadSettingFromFile(EMAIL_ADDRESS_SETTING, WM_PLUGIN_NAME) . ")";
        return "User-Agent: (FalconPiPlayer, " . $this->settingService->getSetting(EMAIL_ADDRESS_SETTING) . ")";
    }

    public function getPointsDetailsFromGpsCoordinates()
    {
        $latitude = ReadSettingFromFile("Latitude");
        $longitude = ReadSettingFromFile("Longitude");
        
        if (empty($latitude) || empty($longitude) || $latitude === false || $longitude === false) {
            $errorMsg = "Longitude and latitude need to be set. Go to Content Setup > FPP Settings > System to set your location.";
            error_log($errorMsg);
            return $errorMsg;
        }
        
        $latitude = bcdiv($latitude, 1, 4);
        $longitude = bcdiv($longitude, 1, 4);

        $pointsRoute = "https://api.weather.gov/points/" . $latitude . "," . $longitude;
        return $this->callAPI(GET, $pointsRoute, array(), $this->getHeaders(), $this->userAgent());
        // $result = $this->callAPI(GET, $pointsRoute, array(), $this->getHeaders(), $this->userAgent());
        // return $result;
    }


    public function getStationIdFromPointsResponse($pointsResponse): string
    {
        $stationsResponse =
            $this->callAPI(GET, $pointsResponse->properties->observationStations, array(), $this->getHeaders(), $this->userAgent(), true);
        return $stationsResponse['features']['0']['properties']['stationIdentifier'];
    }

    public function getAlertZoneIdFromPointsResponse($pointsResponse): string
    {
        $forecastZoneResponse = $this->callAPI(GET, $pointsResponse->properties->forecastZone, array(), $this->getHeaders(), $this->userAgent());
        return $forecastZoneResponse->properties->id;
    }

    // public function getAlertZoneFromGpsCoordinates(): string
    // {
    //     $pointResponse = $this->getPointsDetailsFromGpsCoordinates();
    //     $forecastZoneResponse = $this->callAPI(GET, $pointResponse->properties->forecastZone, array(), $this->getHeaders(), $this->userAgent());
    //     return $forecastZoneResponse->properties->id;
    // }

    // public function getStationIdFromGpsCoordinates(): string
    // {
    //     $pointResponse = $this->getPointsDetailsFromGpsCoordinates();
    //     $stationsResponse =
    //         $this->callAPI(GET, $pointResponse->properties->observationStations, array(), $this->getHeaders(), $this->userAgent(), true);
    //     return $stationsResponse['features']['0']['properties']['stationIdentifier'];
    // }

    public function getLatestObservations(): ObservationModel
    {
        // $route = "https://api.weather.gov/stations/" . ReadSettingFromFile(NWS_WEATHER_STATION_ID, WM_PLUGIN_NAME) . "/observations/latest";
        $route = "https://api.weather.gov/stations/" . $this->settingService->getSetting(NWS_WEATHER_STATION_ID) . "/observations/latest";
        $response = $this->callAPI(GET, $route, array(), $this->getHeaders(), $this->userAgent());
        return ObservationModel::CreateFromNwsApi(
            (float) $response->properties->windSpeed->value,
            (float) $response->properties->windGust->value,
            $response->properties->textDescription
        );
    }

    public function getLatestAlerts()
    {
        // $route = "https://api.weather.gov/alerts/active/zone/" . ReadSettingFromFile(NWS_WEATHER_ALERT_ZONE, WM_PLUGIN_NAME);
        $route = "https://api.weather.gov/alerts/active/zone/" . $this->settingService->getSetting(NWS_WEATHER_ALERT_ZONE);
        return $this->callAPI(GET, $route, array(), $this->getHeaders(), $this->userAgent());
        // $response = $this->callAPI(GET, $route, array(), $this->getHeaders(), $this->userAgent());
        // return $response;
    }
}

final class ObservationModel
{
    private $windSpeedKmh;
    private $gustSpeedKmh;
    private $description;

    private function __construct(float $windSpeed, float $gustSpeed, string $description)
    {
        $this->windSpeedKmh = $windSpeed;
        $this->gustSpeedKmh = $gustSpeed;
        $this->description = $description;
    }

    public static function CreateFromNwsApi(float $windSpeed, float $gustSpeed, string $description)
    {
        return new ObservationModel($windSpeed, $gustSpeed, $description);
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