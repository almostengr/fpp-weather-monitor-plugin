# FPP Weather Monitor


## Table of Contents

* [Purpose](#purpose)
* [Pre-Installation](#pre-installation)
* [Installation](#installation)
* [Plugin Settings](#plugin-settings)
* [Donations](#donations)
* [Feature Requests and Bug Reports](#feature-requests-and-bug-reports)
* [References](#references)



## Purpose

Falcon Pi Player (FPP) Weather Monitor is a plugin that can stop your show based upon weather
conditions.
Some light show hosts do not run their show during rain or inclement weather because
of the adverse effects it can have on the light show equipment. This includes, but it not
limited to electrical shorts, power outages, and props being blown away.

### How It Works

When your show is playing, it will periodically pull the latest weather observations for the specified 
location from the NWS API.
If one or more of the configured values or thresholds are exceeded, the show will be stopped gracefully.
The plugin will not make calls to the NWS API when the show is idle or paused.

### Known Limitations

* The weather stations that are used by the NWS report their data approximately once per hour.
For that reason, this plugin is designed to poll the NWS API twice per hour. This plugin cannot get real
time data nor does it utilize forecasted weather conditions.



## Pre-Installation

Before installing the plugin, be sure that you have completed the following tasks.

* Set the Latitude and Longitude for your location on the Status/Control > FPP Settings > System tab.
* Confirm the "Temperature display units" setting is set correctly to your preference on the Status/Control > FPP Settings > UI tab.
* Your show has access to the internet.



## Installation

To install this plugin

* Copy and paste the following URL on the Plugin Manger page (Content Setup > Plugin Manager).
https://raw.githubusercontent.com/almostengr/fpp-weather-monitor-plugin/main/pluginInfo.json
* Click Get Plugin Info button
* The plugin will show in the Available Plugins list.
* Click the Install button next to the plugin name.



## Plugin Settings

Go to Status/Control > Weather Monitor (under Plugins) to modify the settings for this plugin.

### Weather Station ID

If you know the weather station ID that is closest to your show, can enter it. Leaving this field blank,
will have the system automatically populate the station ID for you. Station ID is dependent upon the
GPS coordinates of your show entered in the FPP Settings (Status/Control > FPP Settings > System tab).

### Email Address

The NWS API does not use API keys or tokens. Instead they use the User Agent and email address as the 
way to uniquely identify your requests. If a security event were to occur, you will be notified by the NWS
at the email address that you provide.

### Weather Text Descriptions

If there are certain weather conditions that you want the show to be stopped for, such as rain, thunderstorms, etc, you may
enter these in this field. Each condition should be separated by commas or semicolons.

### Max Wind Speed

Enter the wind speed, that if exceeded, the show will be stopped by the monitor.

### Max Gust Speed

Enter the wind gust speed, that if exceeded, the show will be stopped by the monitor.



## Donate

Enjoy using this plugin? Consider giving a donation to support the development efforts.



## Feature Requests and Bug Reports

To request a feature or report a bug, file an issue on the project repository at
https://github.com/almostengr/fpp-weather-monitor-plugin/issues



## References

Parts of the code were referenced from various websites. Sources are cited below.

* https://weichie.com/blog/curl-api-calls-with-php/
