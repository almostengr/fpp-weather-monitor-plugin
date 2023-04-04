<?php

// require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/WeatherApiService.php');
// require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/FppApiService.php');
// require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/SettingService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/MonitorService.php');

// $weatherService = new NwsApiWeatherService();
// $fppApiService = new FppApiService();
// $settingService = new SettingService();
$monitorService = new MonitorService();
$lastWeatherCheckTime = 0;
$lastFppStatusCheckTime = 0;
$lastAlertsCheckTime = 0;
$status = array();

while (true) {
    $currentTime = time();
    
    if (($currentTime - $lastFppStatusCheckTime) >= FPP_STATUS_CHECK_TIME) {
        $status = $monitorService->getFppStatus();        
        $lastFppStatusCheckTime = $currentTime;
    } // end getting FPP status
    
    if ($status->status_name == PLAYING && ($currentTime - $lastWeatherCheckTime) >= OBSERVATION_CHECK_INTERVAL_TIME) {
        $monitorService->getAndCompareWeatherObservation();
        $lastWeatherCheckTime = $currentTime;
    } // end getting weather observation

    if ($status->status_name == PLAYING && ($currentTime - $lastAlertsCheckTime) >= NWS_ALERT_INTERVAL_TIME)
    {
        $monitorService->getAndCompareAlerts();
        $lastAlertsCheckTime = $currentTime;
    } // end getting weather alerts
}