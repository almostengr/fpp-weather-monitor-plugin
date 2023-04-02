# Plugin Settings

Go to Status/Control > Weather Monitor (under Plugins subheading) to modify the settings for this plugin.

## NWS Weather Station ID

If you know the weather station ID that is closest to your show, can enter it. Entering "0000",
will have the system automatically populate the station ID for you. Station ID is dependent upon the
GPS coordinates of your show entered in the FPP Settings (Status/Control > FPP Settings > System tab).

## Email Address

The NWS API does not use API keys or tokens. Instead they use the User Agent and email address as the
way to uniquely identify your requests. If a security event were to occur, you will be notified by the NWS
at the email address that you provide.

## Weather Text Descriptions

If there are certain weather conditions that you want the show to be stopped for, such as 
rain, thunderstorms, cloudy, etc, you may
enter these in this field. Each condition should be separated by semicolons.

### Descriptions

Below are the most common text descriptions that can be used with this setting.

* Clear
* Cloudy
* Fog
* Mostly Cloudy
* Partly Cloudy
* Rain
* Snow
* Sunny
* Thunderstorms

## Max Wind Speed (MPH/KMH)

Enter the wind speed, that if exceeded, the show will be stopped by the monitor. The units for this field, 
are dependent upon the Temperature display setting under Status/Control > FPP Settings > UI tab.

## Max Gust Speed (MPH/KMH)

Enter the wind gust speed, that if exceeded, the show will be stopped by the monitor. The units for this field, 
are dependent upon the Temperature display setting under Status/Control > FPP Settings > UI tab.

