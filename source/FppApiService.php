<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/BaseApiService.php');

define("LOCALHOST_API", "http://127.0.0.1/api/");

final class FppApiService extends BaseApiService
{
    public function getShowStatus()
    {
        $route = LOCALHOST_API . "fppd/status";
        return $this->callAPI(GET, $route);
    }

    public function stopPlaylistGracefully()
    {
        $route = LOCALHOST_API . "playlists/stopgracefully";
        return $this->callAPI(GET, $route);
    }
}