<?php

require_once('/opt/fpp/www/common.php');

define("GET", "GET");
define("POST", "POST");
define("PLAYING", "playing");
define("EMPTY_STRING", "");
define("EMAIL_ADDRESS_SETTING", "emailAddressSetting");
define("NWS_WEATHER_STATION_ID", "nwsWeatherStationId");
define("NWS_WEATHER_ALERT_ZONE", "nwsWeatherAlertZone");
define("WEATHER_DESCRIPTIONS", "weatherDescriptions");
define("MAX_WIND_SPEED", "maxWindSpeedKmH");
define("MAX_GUST_SPEED", "maxGustSpeedKmH");
define("OBSERVATION_CHECK_INTERVAL_TIME", 900); // 15 minutes
define("FPP_STATUS_CHECK_TIME", 15); // 15 seconds
define("NWS_ALERT_INTERVAL_TIME", 300); // 5 minutes
define("NWS_ALERT_TYPES", "nwsAlertTypes");
define("STOP_GRACEFULLY", "stopGracefully");

abstract class BaseService
{
    public function getSpeedUnitText(): string
    {
        if ($this->isTemperatureInF()) {
            return " (MPH)";
        }
        return " (KMH)";
    }

    protected function isTemperatureInF(): bool
    {
        return ReadSettingFromFile("temperatureInF") == 1;
    }

    protected function convertKmhToMph($speedKmh): float
    {
        return $speedKmh / 1.609344;
    }

    protected function convertMphToKmh($speedMph): float
    {
        return $speedMph * 1.609344;
    }
}