<?php

include_once '/opt/fpp/www/common.php';

define("WM_PLUGIN_NAME", "weather_monitor");
define("EMAIL_ADDRESS_SETTING", "emailAddressSetting");
define("NWS_WEATHER_STATION_ID", "nwsWeatherStationId");
define("WEATHER_DESCRIPTIONS", "weatherDescriptions");
define("MAX_WIND_SPEED", "maxWindSpeedKmH");
define("MAX_GUST_SPEED", "maxGustSpeedKmH");


final class SettingsService
{
    public 
}

interface ISettingsFormService
{
    public function getErrors(): array;
    public function saveSettings(string $nwsStationId, string $email, string $textDescription, string $windSpeed, string $gustSpeed);
    public function getEmailAddress(): string;
    public function getWeatherStationId(): string;
    public function getWeatherTextDescriptions(): string;
    public function getMaxWindSpeed(): float;
    public function getMaxGustSpeed(): float;
    public function getSpeedUnitText(): string;
}

final class SettingsFormService implements ISettingsFormService
{
    private array $errors;

    public function __construct()
    {
        $this->errors = array();
    }

    private function isTemperatureInF(): bool
    {
        return ReadSettingFromFile("temperatureInF") == 0 ? false : true;
    }

    private function convertKmhToMph($speedKmh): float
    {
        return $speedKmh / 1.609344;
    }

    private function convertMphToKmh($speedMph): float
    {
        return $speedMph * 1.609344;
    }

    public function saveSettings(string $nwsStationId, string $email, string $textDescription, string $windSpeed, string $gustSpeed)
    {
        $this->setWeatherStationId($nwsStationId);
        $this->setEmailAddress($email);
        $this->setWeatherTextDescriptions($textDescription);
        $this->setMaxWindSpeed($windSpeed);
        $this->setMaxGustSpeed($gustSpeed);
    }

    private function setEmailAddress(string $emailAddress): void
    {
        $isValid = filter_var($emailAddress, FILTER_VALIDATE_EMAIL);

        if ($isValid) {
            WriteSettingToFile(EMAIL_ADDRESS_SETTING, WM_PLUGIN_NAME);
            return;
        }

        array_push($this->errors, "Invalid email address");
    }

    public function getEmailAddress(): string
    {
        $email = ReadSettingFromFile(EMAIL_ADDRESS_SETTING, WM_PLUGIN_NAME);
        return $email === false ? EMPTY_STRING : $email;
    }

    private function setWeatherStationId(string $stationId): void
    {
        $existingStationId = ReadSettingFromFile(NWS_WEATHER_STATION_ID, WM_PLUGIN_NAME);

        if ($existingStationId == $stationId) {
            return;
        }

        if (empty($stationId)) {
            array_push($this->errors, "Station ID cannot be empty");
        }

        // if (empty($latitude) || empty($longitude) || $latitude === false || $longitude === false) {
        //     array_push($this->errors, "Longitude and latitude need to be set. Go to Content Setup > FPP Settings > System to set your location.");
        // }

        // $nwsService = new NwsApiService();
        // $stationId = $nwsService->getStationIdFromGpsCoordinates($latitude, $longitude);

        WriteSettingToFile(NWS_WEATHER_STATION_ID, $stationId, WM_PLUGIN_NAME);
    }

    public function getWeatherStationId(): string
    {
        $stationId = ReadSettingFromFile(NWS_WEATHER_STATION_ID, WM_PLUGIN_NAME);
        return $stationId === false ? EMPTY_STRING : $stationId;
    }

    private function setWeatherTextDescriptions(string $descriptions): void
    {
        WriteSettingToFile(WEATHER_DESCRIPTIONS, strtolower($descriptions), WM_PLUGIN_NAME);
    }

    public function getWeatherTextDescriptions(): string
    {
        $descriptions = ReadSettingFromFile(WEATHER_DESCRIPTIONS, WM_PLUGIN_NAME);
        return $descriptions === false ? EMPTY_STRING : $descriptions;
    }

    private function setMaxWindSpeed(string $windSpeedString): void
    {
        $windSpeed = (float) $windSpeedString;

        if ($windSpeed < 1) {
            array_push($this->errors, "Wind speed cannot be less than or equal to zero (0)");
        }

        if ($this->isTemperatureInF()) {
            $windSpeed = $this->convertKmhToMph($windSpeed);
        }

        WriteSettingToFile(MAX_WIND_SPEED, $windSpeed, WM_PLUGIN_NAME);
    }

    public function getMaxWindSpeed(): float
    {
        $windSpeed = ReadSettingFromFile(MAX_WIND_SPEED, WM_PLUGIN_NAME);

        if ($windSpeed === false) {
            return 999;
        }

        if ($this->isTemperatureInF()) {
            return $this->convertKmhToMph($windSpeed);
        }

        return $windSpeed;
    }

    public function getMaxGustSpeed(): float
    {
        $maxGustSpeed = ReadSettingFromFile(MAX_GUST_SPEED, WM_PLUGIN_NAME);

        if ($maxGustSpeed === false) {
            return 999;
        }

        if ($this->isTemperatureInF()) {
            return $this->convertKmhToMph($maxGustSpeed);
        }

        return $maxGustSpeed;
    }

    private function setMaxGustSpeed(string $gustSpeedString): void
    {
        $gustSpeed = (float) $gustSpeedString;

        if ($gustSpeed < 1) {
            array_push($this->errors, "Gust speed cannot be less than or equal to zero (0).");
        }

        if ($this->isTemperatureInF()) {
            $gustSpeed = $this->convertMphToKmh($gustSpeed);
        }

        WriteSettingToFile(MAX_GUST_SPEED, $gustSpeed, WM_PLUGIN_NAME);
    }

    public function getSpeedUnitText(): string
    {
        if ($this->isTemperatureInF()) {
            return " (MPH)";
        }
        return " (KMH)";
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

}

