# socketio-laravel-broadcaster

This package allows to send Laravel events directly to the socket.io server

### Installation
1. Install the package via composer:
 ```sh
composer require arnislielturks/socketio-laravel-broadcaster
```

2. Register the provider in config/app.php
 ```php
// 'providers' => [
    ...
    
    ArnisLielturks\SocketIOBroadcaster\SocketIOBRoadcasterProvider::class
// ];
```

3. Add configuration file (config/socketio.php) with the following content. This should point to the socket.io service
```php
return [
    'server' => 'http://127.0.0.1:3000'
];
```

4. Change the broadcast driver to socketio in (config/broadcasting.php
```php
default' => env('BROADCAST_DRIVER', 'socketio'),
```
OR set this value in .env file

```php
BROADCAST_DRIVER=socketio
```

5. Update config/broadcasting.php
```
'connections' => [
        ...
        
        'socketio' => [
            'driver' => 'socketio'
        ]

    ],
```

6. Create event which will send out the broadcast via Socket.io service

```php
class TestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    //All public attributes will be sent with the message
    public $id;
    public $event = 'test_event';

    public function __construct()
    {
        $this->id = 123;
    }

    public function broadcastOn()
    {
        //List of channels where this event should be sent
        return ['/test_event'];
    }

}
```

7. Send out the event via Controller
```php
class TestController extends Controller
{
    public function test() {
        event(new TestEvent());
        return view('main');
    }
}
```

8. Outgoing message to socketio will look like this:
```php
{ 
  id: 123,
  event: 'test_event'
}
```

That's it!