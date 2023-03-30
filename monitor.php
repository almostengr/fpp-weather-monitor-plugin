<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/WeatherService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/FppApiService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/SettingService.php');

$weatherService = new NwsApiWeatherService();
$fppApiService = new FppApiService();
$settingService = new SettingService();
$lastWeatherCheckTimeSeconds = 0;

while (true) {
    $currentTime = time();
    if (($lastWeatherCheckTimeSeconds + MONITOR_DELAY_SECONDS) > $currentTime) {
        continue;
    }

    $status = $fppApiService->getShowStatus();
    if ($status->status_name == "idle" || $status->status_name == "paused") {
        continue;
    }

    $observation = $weatherService->getLatestObservations();
    $gustThreshold = $settingService->getSetting(MAX_GUST_SPEED);
    $windThreshold = $settingService->getSetting(MAX_WIND_SPEED);
    $textDescriptions = $settingService->getSetting(WEATHER_DESCRIPTIONS);

    // todo log the reported conditions

    if (
        $observation->getGustSpeed() >= $gustThreshold ||
        $observation->getWindSpeed() >= $windThreshold ||
        str_contains(strtolower($textDescriptions), strtolower($observation->getDescription()))
    ) {
        $fppApiService->stopPlaylistGracefully();
        // todo send notification when show is stopped
    }

    $lastWeatherCheckTimeSeconds = $currentTime;
}