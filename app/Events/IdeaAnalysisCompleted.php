<?php

namespace App\Events;

use App\Models\Idea;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class IdeaAnalysisCompleted implements ShouldBroadcast
{
    use SerializesModels;

    public $idea;

    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
    }

    /**
     * القناة الخاصة بالمستخدم
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->idea->user_id);
    }

    /**
     * اسم الحدث في Pusher
     */
    public function broadcastAs()
    {
        return 'idea.analysis.completed';
    }

    /**
     * البيانات المرسلة
     */
    public function broadcastWith()
    {
        return [
            'idea_id' => $this->idea->id,
            'title'   => $this->idea->title,
            'message' => 'تم الانتهاء من تحليل فكرتك بنجاح',
        ];
    }
}
