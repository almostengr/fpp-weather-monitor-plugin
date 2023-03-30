<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/BaseApiService.php');

define("LOCALHOST_API", "http://127.0.0.1/api/");

final class FppApiService extends BaseApiService
{
    public function getShowStatus()
    {
        // return json_decode(file_get_contents(LOCALHOST_API . "fppd/status"));
        $route = LOCALHOST_API . "fppd/status";
        return $this->callAPI(GET, $route);
    }

    public function stopPlaylistGracefully()
    {
        // return json_decode(file_get_contents(LOCALHOST_API . "playlists/stopgracefully"));
        $route = LOCALHOST_API . "playlists/stopgracefully";
        return $this->callAPI(GET, $route);
    }
}