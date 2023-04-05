<?php

// require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/WeatherApiService.php');
// require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/FppApiService.php');
// require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/SettingService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/MonitorService.php');

// $weatherService = new NwsApiWeatherService();
// $fppApiService = new FppApiService();
// $settingService = new SettingService();
$settingRepository = new $settingRepository();
$settingService = new SettingService($settingRepository);
$fppApiService = new FppApiService();
$weatherApiService = new NwsApiWeatherService($settingService);
// $monitorService = new MonitorService();
$monitorService = new MonitorService($settingService, $fppApiService, $weatherApiService);

$lastWeatherCheckTime = 0;
$lastFppStatusCheckTime = 0;
$lastAlertsCheckTime = 0;
$status = array();

while (true) {
    $currentTime = time();

    if (($currentTime - $lastFppStatusCheckTime) >= FPP_STATUS_CHECK_TIME) {
        $monitorService->updateStationIdSettings();

        $status = $monitorService->getFppStatus();
        $lastFppStatusCheckTime = $currentTime;
    } // end getting FPP status

    if ($status->status_name == PLAYING && ($currentTime - $lastWeatherCheckTime) >= OBSERVATION_CHECK_INTERVAL_TIME) {
        $monitorService->getWeatherObservations();
        $lastWeatherCheckTime = $currentTime;
    } // end getting weather observation

    if ($status->status_name == PLAYING && ($currentTime - $lastAlertsCheckTime) >= NWS_ALERT_INTERVAL_TIME) {
        $monitorService->getWeatherAlerts();
        $lastAlertsCheckTime = $currentTime;
    } // end getting weather alerts
}