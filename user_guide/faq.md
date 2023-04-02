# Frequently Asked Questions (FAQs)

### How does this plugin work?

When your show is playing, it will periodically pull the latest weather observations for the specified
location from the NWS API.
If one or more of the configured values or thresholds are exceeded, the show will be stopped gracefully.
The plugin will not make calls to the NWS API when the show is idle or paused.

## I do not want ot use an email address with this plugin. What are my other options?

Per the <a href="https://www.weather.gov/documentation/services-web-api" target='_blank'>NWS documentation</a>,
a user agent is required to identify your application as they do not use API keys or tokens. For more 
information about the NWS API authentication methods, you can visit their website.

## I would like to have another feature added to this plugin. How do I request that? 

See the [Report Issue](./report_issue.md) page of this guide.

## I have questions about something that is not in this guide. 

Create an issue on the 
[project repository](https://github.com/almostengr/fpp-weather-monitor-plugin/issues) with your question.
Answers are typically provided within 72 hours.

## Known Limitations

* The weather stations that are used by the NWS report their data approximately once per hour.
For that reason, this plugin is designed to poll the NWS API once every 15 minutes. This plugin cannot get real
time data nor does it utilize forecasted weather conditions.
* This project is designed to use data from the National Weather Service. This means that users outside of 
the United States, will not be able to use this plugin.
