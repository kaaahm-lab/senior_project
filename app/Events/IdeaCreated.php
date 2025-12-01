<?php

namespace App\Events;

use App\Models\Idea;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class IdeaCreated implements ShouldBroadcast
{
    use SerializesModels;

    public $idea;

    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function broadcastOn()
    {
        return new Channel('ideas');
    }

    public function broadcastAs()
    {
        return 'idea.created';
    }
}
