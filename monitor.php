<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/WeatherApiService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/FppApiService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/SettingService.php');

$weatherService = new NwsApiWeatherService();
$fppApiService = new FppApiService();
$settingService = new SettingService();
$lastWeatherCheckTime = 0;
$lastFppStatusCheckTime = 0;
$lastAlertsCheckTime = 0;
$status = array();

while (true) {
    $currentTime = time();
    
    if (($currentTime - $lastFppStatusCheckTime) >= FPP_STATUS_CHECK_TIME) {
        try {
            $status = $fppApiService->getShowStatus();
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            continue;
        }
        
        $lastFppStatusCheckTime = $currentTime;
    } // end getting FPP status
    
    if ($status->status_name == PLAYING && ($currentTime - $lastWeatherCheckTime) >= MONITOR_DELAY_TIME) {
        $observation = array();
        try {
            $observation = $weatherService->getLatestObservations();
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            continue;
        }

        $lastWeatherCheckTime = $currentTime;
        $gustThreshold = $settingService->getSetting(MAX_GUST_SPEED);
        $windThreshold = $settingService->getSetting(MAX_WIND_SPEED);
        $textDescriptions = $settingService->getSetting(WEATHER_DESCRIPTIONS);

        if (
            $observation->getGustSpeed() >= $gustThreshold ||
            $observation->getWindSpeed() >= $windThreshold ||
            strpos(strtolower($textDescriptions), strtolower($observation->getDescription())) !== false
        ) {
            $fppApiService->stopPlaylistGracefully();
            error_log("Stopping show due to weather condition(s) being met. " . print_r($observation));
            // todo send notification when show is stopped
        }
    } // end getting weather observation

    if ($status->status_name == PLAYING && ($currentTime - $lastAlertsCheckTime) >= NWS_ALERT_INTERVAL_TIME)
    {
        $alerts = array();

        try {
            $alerts = $weatherService->getLatestAlerts();  // call alerts api
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            continue;
        }

        $lastAlertsCheckTime = $currentTime;
        // compare existing alerts to configured alerts

        // if matches are found, then gracefully stop the show
    } // end getting weather alerts
}