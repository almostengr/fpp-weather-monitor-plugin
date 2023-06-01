<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/BaseApiService.php');

define("NWS_API", "https://api.weather.gov/");

interface NwsWeatherApiServiceInterface
{
    public function getPointsDetailsFromGpsCoordinates();
    public function getStationIdFromPointsResponse($pointsResponse): string;
    public function getAlertZoneIdFromPointsResponse($pointsResponse): string;
    public function getForecast(): ObservationModel;
    public function getLatestObservations(): ObservationModel;
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

        $pointsRoute = NWS_API . "points/" . $latitude . "," . $longitude;
        return $this->callAPI(GET, $pointsRoute, array(), $this->getHeaders(), $this->userAgent());
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

    public function getLatestObservations(): ObservationModel
    {
        $route = NWS_API . "stations/" . $this->settingService->getSetting(NWS_WEATHER_STATION_ID) . "/observations/latest";
        $response = $this->callAPI(GET, $route, array(), $this->getHeaders(), $this->userAgent());
        return ObservationModel::CreateFromNwsApi(
            (float) $response->properties->windSpeed->value,
            (float) $response->properties->windGust->value,
            $response->properties->textDescription
        );
    }

    public function getLatestAlerts()
    {
        $route = NWS_API . "alerts/active/zone/" . $this->settingService->getSetting(NWS_WEATHER_ALERT_ZONE);
        return $this->callAPI(GET, $route, array(), $this->getHeaders(), $this->userAgent());
    }

    public function getForecast(): ObservationModel
    {
        $route = NWS_API . "/zones/forecast/" . $this->settingService->getSetting(NWS_WEATHER_ALERT_ZONE) . "/observations";
        $response = $this->callAPI(GET, $route, array(), $this->getHeaders(), $this->userAgent());

        foreach ($response->features as $feature) {
            $stationIdFound = strpos($feature->id, $this->settingService->getSetting(NWS_WEATHER_STATION_ID));
            if ($stationIdFound === false) {
                continue;
            }

            return ObservationModel::CreateFromNwsApi(
                $feature->properties->windSpeed->value,
                $feature->properties->windGust->value,
                $feature->properties->textDescription
            );
        }

        throw new Exception("Unable to get weather station information");
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

