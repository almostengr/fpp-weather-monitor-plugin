<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/BaseService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/WeatherService.php');

interface SettingServiceInterface
{
    public function getSetting(string $key);
    public function createUpdateSetting(string $key, string $value);
}

final class SettingService extends BaseService implements SettingServiceInterface
{
    public function getSetting(string $key)
    {
        $value = ReadSettingFromFile($key, WM_PLUGIN_NAME);

        switch ($key) {
            case MAX_GUST_SPEED:
            case MAX_WIND_SPEED:
                $value = (float) $value;
                if ($this->isTemperatureInF()) {
                    return $this->convertKmhToMph($value);
                }
                return $value;

            default:
                return $value;
        }
    }

    public function createUpdateSetting(string $key, string $value)
    {
        switch ($key) {
            case EMAIL_ADDRESS_SETTING:
                $isValid = filter_var($value, FILTER_VALIDATE_EMAIL);
                if ($isValid === false) {
                    return "Email address is invalid.";
                }
                break;

            case NWS_WEATHER_STATION_ID:
                if (!empty($value) && $value != "0000") {
                    break;
                }

                $nwsApi = new NwsApiWeatherService();
                $value = $nwsApi->getStationIdFromGpsCoordinates();
                break;

            case NWS_WEATHER_ALERT_ZONE:
                if (!empty($value)) {
                    return;
                }

                $nwsApi = new NwsApiWeatherService();
                $value = $nwsApi->getAlertZoneFromGpsCoordinates();
                break;

            case MAX_GUST_SPEED:
                $valueFloat = (float) $value;
                if ($valueFloat < 1) {
                    return "Gust speed cannot be less than or equal to zero (0)";
                }

                if ($this->isTemperatureInF()) {
                    $value = $this->convertMphToKmh($valueFloat);
                }
                break;

            case MAX_WIND_SPEED:
                $valueFloat = (float) $value;

                if ($valueFloat < 1) {
                    return "Wind speed cannot be less than or equal to zero (0)";
                }

                if ($this->isTemperatureInF()) {
                    $value = $this->convertMphToKmh($valueFloat);
                }
                break;
        }

        WriteSettingToFile($key, $value, WM_PLUGIN_NAME);
        return true;
    }
}
