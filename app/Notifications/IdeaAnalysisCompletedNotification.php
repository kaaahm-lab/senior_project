<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class IdeaAnalysisCompletedNotification extends Notification
{
    protected $idea;

    public function __construct($idea)
    {
        $this->idea = $idea;
    }

    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function toFirebase($notifiable)
    {
        return [
            'title' => 'تحليل الفكرة جاهز ✅',
            'body'  => 'تم الانتهاء من تحليل فكرتك: ' . $this->idea->title,
            'data'  => [
                'idea_id' => $this->idea->id,
            ],
        ];
    }
}
