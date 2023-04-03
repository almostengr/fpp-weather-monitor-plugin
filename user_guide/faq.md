# Frequently Asked Questions (FAQs)

### How does this plugin work?

When your show is playing, it will periodically pull the latest weather observations for the specified
location from the NWS API.
If one or more of the configured values or thresholds are exceeded, the show will be stopped gracefully.
The plugin will not make calls to the NWS API when the show is idle or paused.

## Known Limitations

* The weather stations that are used by the NWS report their data approximately once per hour.
For that reason, this plugin is designed to poll the NWS API once every 15 minutes. This plugin cannot get real
time data nor does it utilize forecasted weather conditions.
* This project is designed to use data from the National Weather Service. This means that users outside of 
the United States, will not be able to use this plugin.

## I do not want to use an email address with this plugin. What are my other options?

Per the <a href="https://www.weather.gov/documentation/services-web-api" target='_blank'>NWS documentation</a>,
a user agent is required to identify your application as they do not use API keys or tokens. For more 
information about the NWS API authentication methods, you can visit their website.

## I would like to have another feature added to this plugin. How do I request that? 

See the [Report Issue](./report_issue.md) page of this guide.

## I have questions about something that is not in this guide. 

Create an issue on the 
[project repository](https://github.com/almostengr/fpp-weather-monitor-plugin/issues) with your question.
Answers are typically provided within 72 hours.

## What is the release schedule for this plugin?

New feature releases will be released at most once month. Bug fixes, depending on the severity, 
may be patched and released independently of the monthly release schedule. Check the repository page or 
the Plugin Manager in your FPP instance to see if new release is available for download.

## What's with the version number? It looks like a date.

It is a date. This project utilizes CalVer (calendar versioning) for releases. Thus the number that you 
see is the year.month.day that the release was created. 

It is much simplier to do release numbers when the number is the current date. In comparision, when doing 
semantic versioning, a determination has to be made whether the changes are major or minor. It is 
possible that multiple minor changes being release, could fit the criteria for a major change. 
To keep things simple, it was decided to have the version number be based on the date that the 
release occurred. This is similar to how Ubuntu does version numbers.
