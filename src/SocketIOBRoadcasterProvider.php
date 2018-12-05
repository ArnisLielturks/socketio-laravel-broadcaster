<?php

namespace ArnisLielturks\SocketIOBroadcaster;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Broadcasting\BroadcastManager;
use ArnisLielturks\SocketIOBroadcaster\SocketIOBroadcaster;
use Log;

class SocketIOBRoadcasterProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(BroadcastManager $broadcastManager)
    {
        $broadcastManager->extend('socketio', function ($app, array $config) {
            return new SocketIOBroadcaster(config('socketio'));
        });
    }
}
