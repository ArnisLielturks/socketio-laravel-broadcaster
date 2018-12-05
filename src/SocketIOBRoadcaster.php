<?php

namespace ArnisLielturks\SocketIOBroadcaster;

use ElephantIO\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use ElephantIO\Engine\SocketIO\Version2X;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SocketIOBroadcaster extends Broadcaster
{
    /**
     * The Redis instance.
     *
     * @var \Illuminate\Contracts\Redis\Factory
     */
    protected $config;

    /**
     * Create a new broadcaster instance.
     *
     * @param  \Illuminate\Contracts\Redis\Factory  $redis
     * @param  string  $connection
     * @return void
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function auth($request)
    {
        return true;
        if (Str::startsWith($request->channel_name, ['private-', 'presence-']) &&
            ! $request->user()) {
            throw new HttpException(403);
        }

        $channelName = Str::startsWith($request->channel_name, 'private-')
            ? Str::replaceFirst('private-', '', $request->channel_name)
            : Str::replaceFirst('presence-', '', $request->channel_name);

        return parent::verifyUserCanAccessChannel(
            $request, $channelName
        );
    }

    /**
     * Return the valid authentication response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $result
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result)
    {
        if (is_bool($result)) {
            return json_encode($result);
        }

        return json_encode(['channel_data' => [
            'user_id' => $request->user()->getAuthIdentifier(),
            'user_info' => $result,
        ]]);
    }

    /**
     * Broadcast the given event.
     *
     * @param  array  $channels
     * @param  string  $event
     * @param  array  $payload
     * @return void
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        try {
            $url = $this->config['server'];
            $client = new Client(new Version2X( $url, [
                'headers' => [
                ]
            ]));
            $client->initialize();
            foreach ($channels as $channel) {
                $client->emit($channel, $payload);
            }
            $client->close();
        } catch(\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
