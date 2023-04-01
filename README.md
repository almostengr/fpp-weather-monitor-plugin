# FPP Weather Monitor

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

## User Guide

Project documentation and user guide can be viewed in the [User Guide](./user_guide/) folder.

## Donations

Enjoy using this plugin? Consider giving a donation to support the development efforts.