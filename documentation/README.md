# Weather Monitor Plugin Documentation


## Table of Contents

* [Pre-Configuration](#pre-configuration)
* [Initial Configuration](#initial-configuration)

## Pre-Configuration

This plugin uses other configuration settings to function properly. Please verify that each of the settings 
below is correct before performing the [Initial Configuration](#initial-configuration).

* Go to Content Setup > FPP Settings > System and confirm that you have entered the Latitude and Logitude for your location.
* Go to Content Setup > FPP Settings > UI and confrim that the "Temperature display units" setting is set correctly to your preference.

## Initial Configuration

* Go to Content Setup > Weather Monitor (under Plugins).

### Weather Station ID

This  field will populate after saving with the nearest weather station ID. 

### Email Address

Enter your email address. This will be used to uniquely identify you to the NWS API and for you to be contacted if there is a security event. 

### Weather Text Descriptions

If there are certain weather conditions that you want the show to be stopped for, such as rain, thunderstorms, etc, you may 
enter these in this field. Each condition should be separated by commas.

### Max Wind Speed

Enter the wind speed that you would want your show to be shut down. This feature is useful for those that have 
inflatables or props that can be below away. 

### Max Gust Speed

Enter the wind gust speed that you would want your show to be shut down. This feature is useful for those that have 
inflatables or props that can be below away. 
