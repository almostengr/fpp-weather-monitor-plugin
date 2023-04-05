<?php

require_once('/home/fpp/media/plugins/fpp-weather-monitor-plugin/source/BaseApiService.php');

define("LOCALHOST_API", "http://127.0.0.1/api/");

interface FppApiServiceInterface
{
    public function getShowStatus();
    public function stopPlaylistGracefully();
}

final class FppApiService extends BaseApiService implements FppApiServiceInterface
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