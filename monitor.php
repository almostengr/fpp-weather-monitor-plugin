<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/FppApiService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/SettingService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/WeatherApiService.php');


$settingRepository = new $settingRepository();
$settingService = new SettingService($settingRepository);
$fppApiService = new FppApiService($settingService);
$weatherApiService = new NwsApiWeatherService($settingService);

$lastWeatherCheckTime = 0;
$lastFppStatusCheckTime = 0;
$lastAlertsCheckTime = 0;
$lastForecastCheckTime = 0;
$status = array();


function updateStationIdSettings()
{
    global $currentTime;
    global $lastFppStatusCheckTime;
    global $settingService;
    global $weatherApiService;
    global $status;
    global $fppApiService;

    if (($currentTime - $lastFppStatusCheckTime) < FPP_STATUS_CHECK_TIME) {
        return;
    }

    try {
        $stationId = $settingService->getSetting(NWS_WEATHER_STATION_ID);

        if ($stationId == "0000") {
            $pointsResponse = $weatherApiService->getPointsDetailsFromGpsCoordinates();

            $nwsStationId = $weatherApiService->getStationIdFromPointsResponse($pointsResponse);
            $settingService->createUpdateSetting(NWS_WEATHER_STATION_ID, $nwsStationId);

            $alertZone = $weatherApiService->getAlertZoneIdFromPointsResponse($pointsResponse);
            $settingService->createUpdateSetting(NWS_WEATHER_ALERT_ZONE, $alertZone);
        }

        $status = $fppApiService->getShowStatus();
        $lastFppStatusCheckTime = $currentTime;
    } catch (Exception $exception) {
        error_log($exception->getMessage());
        return;
    }
}


function getWeatherObservations()
{
    global $currentTime;
    global $lastWeatherCheckTime;
    global $settingService;
    global $weatherApiService;
    global $fppApiService;
    global $status;

    if ($status->status_name == PLAYING && ($currentTime - $lastWeatherCheckTime) >= OBSERVATION_CHECK_INTERVAL_TIME) {
        try {
            $observation = $weatherApiService->getLatestObservations();
            $gustThreshold = $settingService->getSetting(MAX_GUST_SPEED);
            $windThreshold = $settingService->getSetting(MAX_WIND_SPEED);
            $textDescriptions = $settingService->getSetting(WEATHER_DESCRIPTIONS);

            if (
                $observation->getGustSpeed() >= $gustThreshold ||
                $observation->getWindSpeed() >= $windThreshold ||
                strpos(strtolower($textDescriptions), strtolower($observation->getDescription())) !== false
            ) {
                $fppApiService->runWeatherDelay();
                error_log("Stopping show due to weather condition(s) being met. " . print_r($observation));
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return;
        }

        $lastWeatherCheckTime = $currentTime;
    }
}


function getForecastObservation()
{
    global $currentTime;
    global $lastForecastCheckTime;
    global $settingService;
    global $weatherApiService;
    global $fppApiService;
    global $status;

    if ($status->status_name == PLAYING && ($currentTime - $lastForecastCheckTime) >= OBSERVATION_CHECK_INTERVAL_TIME) {

        $gustThreshold = $settingService->getSetting(MAX_GUST_SPEED);
        $windThreshold = $settingService->getSetting(MAX_WIND_SPEED);
        $textDescriptions = $settingService->getSetting(WEATHER_DESCRIPTIONS);

        try {
            $forecast = $weatherApiService->getForecast();

            if (
                $forecast->getGustSpeed() >= $gustThreshold ||
                $forecast->getWindSpeed() >= $windThreshold ||
                strpos(strtolower($textDescriptions), strtolower($forecast->getDescription())) !== false
            ) {
                $fppApiService->runWeatherDelay();
                error_log("Stopping show due to weather forecast conditions being met. " . print_r($forecast));
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return;
        }
        $lastForecastCheckTime = $currentTime;
    }
}


function getWeatherAlerts()
{
    global $currentTime;
    global $lastAlertsCheckTime;
    global $settingService;
    global $weatherApiService;
    global $fppApiService;
    global $status;

    if ($status->status_name != PLAYING || ($currentTime - $lastAlertsCheckTime) < NWS_ALERT_INTERVAL_TIME) {
        return;
    }

    try {
        $alerts = $weatherApiService->getLatestAlerts();

        $configuredAlerts = $settingService->getSetting(NWS_ALERT_TYPES);
        if (strpos(strtolower($configuredAlerts), strtolower($alerts)) !== false) {
            $fppApiService->runWeatherDelay();
            error_log("Stopping show due to alert condition(s) being met. " . print_r($alerts));
        }
    } catch (Exception $exception) {
        error_log($exception->getMessage());
        return;
    }

    $lastAlertsCheckTime = $currentTime;
}


while (true) {
    $currentTime = time();

    updateStationIdSettings();
    getWeatherObservations();
    getForecastObservation();
    getWeatherAlerts();
}
