<?php

include_once '/opt/fpp/www/common.php';

define("WM_PLUGIN_NAME", "weather_monitor");
define("EMAIL_ADDRESS_SETTING", "emailAddressSetting");
define("NWS_WEATHER_STATION_ID", "nwsWeatherStationId");
define("WEATHER_DESCRIPTIONS", "weatherDescriptions");
define("MAX_WIND_SPEED", "maxWindSpeedKmH");
define("MAX_GUST_SPEED", "maxGustSpeedKmH");


function isTemperatureInF(): bool
{
    return ReadSettingFromFile("temperatureInF") == 0 ? false : true;
}

function convertKmhToMph($speedKmh): float
{
    return $speedKmh / 1.609344;
}

function convertMphToKmh($speedMph): float
{
    return $speedMph * 1.609344;
}

function saveSettings(string $nwsStationId, string $email, string $textDescription, string $windSpeed, string $gustSpeed)
{
    $errors = array();
    $this->setWeatherStationId($nwsStationId);
    $this->setEmailAddress($email);
    $this->setWeatherTextDescriptions($textDescription);
    $this->setMaxWindSpeed($windSpeed);
    $this->setMaxGustSpeed($gustSpeed);
}

function createUpdateEmailAddressSetting(string $emailAddress): void
{
    $isValid = filter_var($emailAddress, FILTER_VALIDATE_EMAIL);

    if ($isValid) {
        WriteSettingToFile(EMAIL_ADDRESS_SETTING, WM_PLUGIN_NAME);
        return;
    }

    array_push($this->errors, "Invalid email address");
}

function getEmailAddressSetting(): string
{
    $email = ReadSettingFromFile(EMAIL_ADDRESS_SETTING, WM_PLUGIN_NAME);
    return $email === false ? EMPTY_STRING : $email;
}

function createUpdateWeatherStationIdSetting(string $stationId): void
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

function getWeatherStationIdSetting(): string
{
    $stationId = ReadSettingFromFileSetting(NWS_WEATHER_STATION_ID, WM_PLUGIN_NAME);
    return $stationId === false ? EMPTY_STRING : $stationId;
}

function createUpdateWeatherTextDescriptionsSetting(string $descriptions): void
{
    WriteSettingToFile(WEATHER_DESCRIPTIONS, strtolower($descriptions), WM_PLUGIN_NAME);
}

function getWeatherTextDescriptionsSetting(): string
{
    $descriptions = ReadSettingFromFile(WEATHER_DESCRIPTIONS, WM_PLUGIN_NAME);
    return $descriptions === false ? EMPTY_STRING : $descriptions;
}

function createUpdateMaxWindSpeedSetting(string $windSpeedString): void
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

function getMaxWindSpeedSetting(): float
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

function getMaxGustSpeedSetting(): float
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

function createUpdateMaxGustSpeedSetting(string $gustSpeedString): void
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

function getSpeedUnitText(): string
{
    if ($this->isTemperatureInF()) {
        return " (MPH)";
    }
    return " (KMH)";
}
