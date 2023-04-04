<?php

// require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/BaseService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/WeatherApiService.php');

final class MonitorService extends BaseApiService
{
    private $fppApiService;
    private $weatherService;
    private $settingService;

    public function __construct()
    {
        $this->fppApiService = new FppApiService();
        $this->weatherService = new NwsApiWeatherService();
        $this->settingService = new SettingService();
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

    public function getAndCompareWeatherObservation()
    {
        $observation = array();
        
        try {
            $observation = $this->weatherService->getLatestObservations();
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return;
        }

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
    }

    public function getAndCompareAlerts()
    {
        $alerts = array();

        try {
            $alerts = $this->weatherService->getLatestAlerts();  // call alerts api
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            return;
        }

        // compare existing alerts to configured alerts

        // if matches are found, then gracefully stop the show
    }

}