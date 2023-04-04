<?php

// require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/BaseService.php');
require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/WeatherApiService.php');

interface SettingServiceInterface
{
    public function getSetting(string $key);
    public function createUpdateSetting(string $key, string $value);
}

// final class SettingService extends BaseService implements SettingServiceInterface
final class SettingService extends BaseService implements SettingServiceInterface
{
    private $repository;
    private $weatherService;

    public function __construct(SettingRepostioryInterface $repository, NwsWeatherServiceInterface $weatherService)
    {
        $this->repository = $repository;
        $this->weatherService = $weatherService;
    }


    public function getSetting(string $key)
    {
        $this->repository->getSEtting($key);

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
        $value = trim($value);
        switch ($key) {
            case EMAIL_ADDRESS_SETTING:
                $isValid = filter_var($value, FILTER_VALIDATE_EMAIL);
                if ($isValid === false || empty($value)) {
                    return "Email Address is invalid.";
                }
                break;

            case NWS_WEATHER_STATION_ID:
                if (empty($value)) {
                    return "Weather Station ID is required.";
                }
                break;

            case MAX_GUST_SPEED:
                $valueFloat = (float) $value;
                if ($valueFloat < 1) {
                    return "Wind Gust Speed cannot be less than or equal to zero.";
                }

                if ($this->isTemperatureInF()) {
                    $value = $this->convertMphToKmh($valueFloat);
                }
                break;

            case MAX_WIND_SPEED:
                $valueFloat = (float) $value;

                if ($valueFloat < 1) {
                    return "Wind Speed cannot be less than or equal to zero.";
                }

                if ($this->isTemperatureInF()) {
                    $value = $this->convertMphToKmh($valueFloat);
                }
                break;
        }

        $this->repository->createUpdateSetting($key, $value);
        return true;
    }
}


interface SettingRepostioryInterface
{
    public function getSetting(string $key) : string;
    public function createUpdateSetting(string $key, string $value) : void;
}

final class SettingRepository implements SettingRepostioryInterface
{
    public function getSetting(string $value) : string
    {
        $value = ReadSettingFromFile($key, WM_PLUGIN_NAME);
        return str_replace("_", " ", $value);
    }

    public function createUpdateSetting(string $key, string $value) : void
    {
        $value = str_replace(" ", "_", $value);
        WriteSettingToFile($key, $value, WM_PLUGIN_NAME);
    }
}