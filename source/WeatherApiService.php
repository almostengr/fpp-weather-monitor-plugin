<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/BaseApiService.php');

interface WeatherServiceInterface
{
    public function getLatestObservations(): ObservationModel;
}

interface NwsWeatherServiceInterface extends WeatherServiceInterface
{
    public function getStationIdFromGpsCoordinates(): string;
}

final class NwsApiWeatherService extends BaseApiService implements NwsWeatherServiceInterface
{
    private function userAgent(): string
    {
        return "User-Agent: (FalconPiPlayer, " . ReadSettingFromFile(EMAIL_ADDRESS_SETTING, WM_PLUGIN_NAME) . ")";
    }

    private function getPointsDetailsFromGpsCoordinates()
    {
        $latitude = ReadSettingFromFile("Latitude");
        $longitude = ReadSettingFromFile("Longitude");

        if (empty($latitude) || empty($longitude) || $latitude === false || $longitude === false) {
            return "Longitude and latitude need to be set. Go to Content Setup > FPP Settings > System to set your location.";
        }

        $pointsRoute = "https://api.weather.gov/points/" . $latitude . "," . $longitude;
        $result = $this->callAPI(GET, $pointsRoute, array(), $this->getHeaders(), $this->userAgent());
        return $result;
    }

    // todo for future release
    // public function getAlertZoneFromGpsCoordinates(): string
    // {
    //     $pointResponse = $this->getPointsDetailsFromGpsCoordinates();
    //     $forecastZoneResponse = $this->callAPI(GET, $pointResponse->properties->forecastZone, array(), $this->getHeaders(), $this->userAgent());
    //     return $forecastZoneResponse->properties->id;
    // }

    public function getStationIdFromGpsCoordinates(): string
    {
        $pointResponse = $this->getPointsDetailsFromGpsCoordinates();
        $stationsResponse =
            $this->callAPI(GET, $pointResponse->properties->observationStations, array(), $this->getHeaders(), $this->userAgent(), true);
        return $stationsResponse['features']['0']['properties']['stationIdentifier'];
    }

    public function getLatestObservations(): ObservationModel
    {
        $route = "https://api.weather.gov/stations/" . ReadSettingFromFile(NWS_WEATHER_STATION_ID, WM_PLUGIN_NAME) . "/observations/latest";
        $response = $this->callAPI(GET, $route, array(), $this->getHeaders(), $this->userAgent());
        return ObservationModel::CreateFromNwsApi(
            (float) $response->properties->windSpeed->value,
            (float) $response->properties->windGust->value,
            $response->properties->textDescription
        );
    }

// todo for future release
// public function getLatestAlerts()
// {
//     $route = "https://api.weather.gov/alerts/active/zone/" . ReadSettingFromFile(NWS_WEATHER_ALERT_ZONE, WM_PLUGIN_NAME);
//     $response = $this->callAPI(GET, $route, array(), $this->getHeaders(), $this->userAgent());
//     return $response;
// }
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
