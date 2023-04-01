<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/WeatherService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/FppApiService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/SettingService.php');

$weatherService = new NwsApiWeatherService();
$fppApiService = new FppApiService();
$settingService = new SettingService();
$lastWeatherCheckTime = 0;
$lastFppStatusCheckTime = 0;
$status = array();

while (true) {
    $currentTime = time();

    if (($lastFppStatusCheckTime - $currentTime) >= FPP_STATUS_CHECK_TIME) {
        try {
            $status = $fppApiService->getShowStatus();
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            continue;
        }

        $lastFppStatusCheckTime = $currentTime;
    } // end getting FPP status

    if (($status->status_name == "playing" && $lastWeatherCheckTime - $currentTime) >= MONITOR_DELAY_TIME) {
        $observation;

        try {
            $observation = $weatherService->getLatestObservations();
            syslog(LOG_INFO, print_r($observation)); // todo log the reported conditions
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            continue;
        }

        $lastWeatherCheckTimeSeconds = $currentTime;
        $gustThreshold = $settingService->getSetting(MAX_GUST_SPEED);
        $windThreshold = $settingService->getSetting(MAX_WIND_SPEED);
        $textDescriptions = $settingService->getSetting(WEATHER_DESCRIPTIONS);

        if (
            $observation->getGustSpeed() >= $gustThreshold ||
            $observation->getWindSpeed() >= $windThreshold ||
            str_contains(strtolower($textDescriptions), strtolower($observation->getDescription()))
        ) {
            $fppApiService->stopPlaylistGracefully();
            syslog(LOG_INFO, "Stopping show due to weather"); // todo send notification when show is stopped
        }
    } // end getting weather observation
    
}