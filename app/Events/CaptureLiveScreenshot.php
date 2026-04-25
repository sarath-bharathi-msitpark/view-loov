<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;

//use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\Channel;

//use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CaptureLiveScreenshot implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    public $userId;
    /**
     * @var
     */
    public $incident;

    /**
     * @param int $userId
     * @param $incident
     */
    public function __construct(int $userId, $incident)
    {
        $this->userId = $userId;
        $this->incident = $incident;
    }

    public function broadcastOn()
    {
        $channelPrefix = config('broadcasting.broadcast_channel_prefix', 'screenshot-request-loov-v2');
//        return new PrivateChannel("{$channelPrefix}-{$this->userId}");
        return new Channel("{$channelPrefix}-{$this->userId}");
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'CaptureLiveScreenshot';
    }

    /**
     * @return array
     */
    public function broadcastWith()
    {
        $data = [
            'user_id' => $this->userId,
            'incident' => $this->incident,
            'requested_date_and_time' => $this->incident->requested_date_and_time,
        ];

//        Log::info('Broadcasting CaptureLiveScreenshot event data', $data);

        return $data;
    }
}
