<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/FppApiService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/SettingService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/NwsWeatherApiService.php');

final class WeatherMonitorWorker
{
    private $settingRepository;
    private $settingService;
    private $fppApiService;
    private $nwsWeatherService;

    private $lastWeatherCheckTime = 0;
    private $lastFppStatusCheckTime = 0;
    private $lastAlertsCheckTime = 0;
    private $lastForecastCheckTime = 0;
    private $status = array();
    private $currentTime;

    private $gustThreshold;
    private $windThreshold;
    private $textDescriptions;


    public function __construct()
    {
        $this->settingRepository = new SettingRepository();
        $this->settingService = new SettingService($this->settingRepository);
        $this->fppApiService = new FppApiService();
        $this->nwsWeatherService = new NwsApiWeatherService($this->settingService);
    }

    public function updateCurrentTime()
    {
        $this->currentTime = time();
    }

    public function updateStationIdSettings(): void
    {
        if (($this->currentTime - $this->lastFppStatusCheckTime) < FPP_STATUS_CHECK_TIME) {
            return;
        }

        try {
            $stationId = $this->settingService->getSetting(NWS_WEATHER_STATION_ID);

            if ($stationId == "0000") {
                $pointsResponse = $this->nwsWeatherService->getPointsDetailsFromGpsCoordinates();

                $nwsStationId = $this->nwsWeatherService->getStationIdFromPointsResponse($pointsResponse);
                $this->settingService->createUpdateSetting(NWS_WEATHER_STATION_ID, $nwsStationId);

                $alertZone = $this->nwsWeatherService->getAlertZoneIdFromPointsResponse($pointsResponse);
                $this->settingService->createUpdateSetting(NWS_WEATHER_ALERT_ZONE, $alertZone);

                $this->gustThreshold = $this->settingService->getSetting(MAX_GUST_SPEED);
                $this->windThreshold = $this->settingService->getSetting(MAX_WIND_SPEED);
                $this->textDescriptions = $this->settingService->getSetting(WEATHER_DESCRIPTIONS);
            }

            $this->status = $this->fppApiService->getShowStatus();
            $this->lastFppStatusCheckTime = $this->currentTime;
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return;
        }
    }


    public function getWeatherObservations(): void
    {
        if ($this->status->status_name !== PLAYING && ($this->currentTime - $this->lastWeatherCheckTime) < OBSERVATION_CHECK_INTERVAL_TIME) {
            return;
        }

        try {
            $observation = $this->nwsWeatherService->getLatestObservations();

            if (
                $observation->getGustSpeed() >= $this->gustThreshold ||
                $observation->getWindSpeed() >= $this->windThreshold ||
                strpos(strtolower($this->textDescriptions), strtolower($observation->getDescription())) !== false
            ) {
                $this->fppApiService->runWeatherDelay();
                error_log("Stopping show due to weather condition(s) being met. " . print_r($observation));
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return;
        }

        $this->lastWeatherCheckTime = $this->currentTime;
    }


    public function getForecastObservation(): void
    {
        if ($this->status->status_name !== PLAYING && ($this->currentTime - $this->lastForecastCheckTime) < OBSERVATION_CHECK_INTERVAL_TIME) {
            return;
        }

        try {
            $forecast = $this->nwsWeatherService->getForecast();

            if (
                $forecast->getGustSpeed() >= $this->gustThreshold ||
                $forecast->getWindSpeed() >= $this->windThreshold ||
                strpos(strtolower($this->textDescriptions), strtolower($forecast->getDescription())) !== false
            ) {
                $this->fppApiService->runWeatherDelay();
                error_log("Stopping show due to weather forecast conditions being met. " . print_r($forecast));
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return;
        }

        $this->lastForecastCheckTime = $this->currentTime;
    }


    public function getWeatherAlerts(): void
    {
        if ($this->status->status_name != PLAYING || ($this->currentTime - $this->lastAlertsCheckTime) < NWS_ALERT_INTERVAL_TIME) {
            return;
        }

        try {
            $alerts = $this->nwsWeatherService->getLatestAlerts();

            $configuredAlerts = $this->settingService->getSetting(NWS_ALERT_TYPES);
            if (strpos(strtolower($configuredAlerts), strtolower($alerts)) !== false) {
                $this->fppApiService->runWeatherDelay();
                error_log("Stopping show due to alert condition(s) being met. " . print_r($alerts));
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return;
        }

        $this->lastAlertsCheckTime = $this->currentTime;
    }
}

$monitor = new WeatherMonitorWorker();
while (true) {
    $monitor->updateCurrentTime();
    $monitor->updateStationIdSettings();
    $monitor->getWeatherObservations();
    $monitor->getForecastObservation();
    $monitor->getWeatherAlerts();
}
