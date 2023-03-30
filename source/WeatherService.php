<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/BaseApiService.php');

interface WeatherServiceInterface
{
    public function getLatestObservations(): ObservationModel;
}

interface NwsWeatherServiceInterface extends WeatherServiceInterface
{
    public function getStationIdFromGpsCoordinates(string $latitude, string $longitude): string;
}

final class NwsApiWeatherService extends BaseApiService implements NwsWeatherServiceInterface
{
    private function userAgent(): string
    {
        return "User-Agent: (FalconPiPlayer, " . ReadSettingFromFile(EMAIL_ADDRESS_SETTING, WM_PLUGIN_NAME) . ")";
    }

    public function getStationIdFromGpsCoordinates(): string
    {
        $latitude = ReadSettingFromFile("Latitude");
        $longitude = ReadSettingFromFile("Longitude");

        if (empty($latitude) || empty($longitude) || $latitude === false || $longitude === false) {
            return "Longitude and latitude need to be set. Go to Content Setup > FPP Settings > System to set your location.";
        }

        $pointsRoute = "https://api.weather.gov/points/" . $latitude . "," . $longitude;
        $pointResponse = $this->callAPI(GET, $pointsRoute, array(), $this->getHeaders(), $this->userAgent());
        $stationsResponse =
            $this->callAPI(GET, $pointResponse->properties->observationStations, array(), $this->getHeaders(), $this->userAgent());
        return $stationsResponse->features->properties->stationIdentifer;
    }

    public function getLatestObservations(): ObservationModel
    {
        $route = "https://api.weather.gov/stations/" . ReadSettingFromFile(NWS_WEATHER_STATION_ID, WM_PLUGIN_NAME) . "/observations/latest";
        $response = $this->callAPI(GET, $route, array(), $this->getHeaders(), $this->userAgent());
        return ObservationModel::CreateFromNwsApi(
            $response->properties->windSpeed,
            $response->properties->windGust,
            $response->properties->textDescription
        );
    }
}

final class ObservationModel
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