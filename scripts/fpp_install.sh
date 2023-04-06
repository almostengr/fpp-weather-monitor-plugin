#!/bin/bash

# fpp-weather-monitor-plugin install script

if [ -f /home/fpp/media/config/plugin.fpp_weather_monitor ]
    echo "Moving configuration file"
    mv /home/fpp/media/config/plugin.fpp_weather_monitor /home/fpp/media/config/plugin.fpp_weather_monitor_plugin
fi

echo "Please set the plugin configuration from the Status/Control menu."