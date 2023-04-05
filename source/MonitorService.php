<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/WeatherApiService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/FppApiService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/SettingService.php');

interface MonitorServiceInterface
{
    public function updateStationIdSettings();
    public function getWeatherObservations();
    public function getFppStatus();
    public function getWeatherAlerts();
}

final class MonitorService extends BaseApiService implements MonitorServiceInterface
{
    private $fppApiService;
    private $weatherService;
    private $settingService;

    public function __construct(
        SettingServiceInterface $settingServiceInterface,
        FppApiServiceInterface $fppApiServiceInterface,
        NwsWeatherApiServiceInterface $nwsWeatherApiServiceInterface
    ) {
        $this->fppApiService = $fppApiServiceInterface;
        $this->weatherService = $nwsWeatherApiServiceInterface;
        $this->settingService = $settingServiceInterface;
    }

    public function updateStationIdSettings()
    {
        $stationId = $this->settingService->getSetting(NWS_WEATHER_STATION_ID);

        if ($stationId == "0000") {
            $pointsResponse = $this->weatherService->getPointsDetailsFromGpsCoordinates();

            // $nwsStationId = $this->weatherService->getStationIdFromGpsCoordinates();
            $nwsStationId = $this->weatherService->getStationIdFromPointsResponse($pointsResponse);
            $this->settingService->createUpdateSetting(NWS_WEATHER_STATION_ID, $nwsStationId);

            $alertZone = $this->weatherService->getAlertZoneIdFromPointsResponse($pointsResponse);
            $this->settingService->createUpdateSetting(NWS_WEATHER_ALERT_ZONE, $alertZone);
        }
    }

    public function getFppStatus()
    {
        try {
            return $this->fppApiService->getShowStatus();
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return;
        }
    }

    public function getWeatherObservations()
    {
        try {
            $observation = $this->weatherService->getLatestObservations();

            $gustThreshold = $this->settingService->getSetting(MAX_GUST_SPEED);
            $windThreshold = $this->settingService->getSetting(MAX_WIND_SPEED);
            $textDescriptions = $this->settingService->getSetting(WEATHER_DESCRIPTIONS);

            if (
                $observation->getGustSpeed() >= $gustThreshold ||
                $observation->getWindSpeed() >= $windThreshold ||
                strpos(strtolower($textDescriptions), strtolower($observation->getDescription())) !== false
            ) {
                $this->fppApiService->stopPlaylistGracefully();
                error_log("Stopping show due to weather condition(s) being met. " . print_r($observation));
                // todo send notification when show is stopped
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return;
        }
    }

    public function getWeatherAlerts()
    {
        try {
            $alerts = $this->weatherService->getLatestAlerts(); // call alerts api

            // compare existing alerts to configured alerts

            // if matches are found, then gracefully stop the show
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return;
        }
    }

}