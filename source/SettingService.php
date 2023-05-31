<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/BaseService.php');

interface SettingServiceInterface
{
    public function getSetting(string $key);
    public function createUpdateSetting(string $key, string $value);
    public function getAlertTypes(): array;
}

final class SettingService extends BaseService implements SettingServiceInterface
{
    private $repository;

    public function __construct(SettingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAlertTypes(): array
    {
        return array(
            "Blizzard Warning",
            "Coastal Flood Advisory",
            "Coastal Flood Warning",
            "Coastal Flood Watch",
            "Dense Fog Advisory",
            "Excessive Heat Warning",
            "Excessive Heat Watch",
            "Extreme Wind Warning",
            "Fire Weather Watch",
            "Flash Flood Warning",
            "Flash Flood Watch",
            "Flood Warning",
            "Flood Watch",
            "Freeze Warning",
            "Freeze Watch",
            "Frost Advisory",
            "Heat Advisory",
            "High Wind Warning",
            "High Wind Watch",
            "Hurricane Warning",
            "Hurricane Watch",
            "Ice Storm Warning",
            "Red Flag Warning",
            "River Flood Warning",
            "River Flood Watch",
            "Severe Thunderstorm Warning",
            "Severe Thunderstorm Watch",
            "Tornado Warning",
            "Tornado Watch",
            "Tropical Storm Warning",
            "Tropical Storm Watch",
            "Wind Advisory",
            "Wind Chill Advisory",
            "Wind Chill Warning",
            "Winter Storm Warning",
            "Winter Weather Advisory",
        );
    }

    public function getSetting(string $key)
    {
        $value = $this->repository->getSetting($key);

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


interface SettingRepositoryInterface
{
    public function getSetting(string $key): string;
    public function createUpdateSetting(string $key, string $value): void;
}

final class SettingRepository implements SettingRepositoryInterface
{
    public function getSetting(string $key): string
    {
        $value = ReadSettingFromFile($key, WM_PLUGIN_NAME);
        return str_replace("_", " ", $value);
    }

    public function createUpdateSetting(string $key, string $value): void
    {
        $value = str_replace(" ", "_", $value);
        WriteSettingToFile($key, $value, WM_PLUGIN_NAME);
    }
}