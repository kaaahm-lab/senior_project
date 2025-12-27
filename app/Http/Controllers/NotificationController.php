<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Routing\Controller;
use App\Services\FirebaseService;

class NotificationController extends Controller
{

public function sendIdeaAnalysisNotification($userId, $ideaTitle)
{
    $user = User::find($userId);

    if (!$user || !$user->fcm_token) {
        return response()->json(['status' => false, 'message' => 'No FCM token found']);
    }

    $firebase = new FirebaseService();
    $firebase->sendNotification(
        $user->fcm_token,
        'Idea Analysis Completed',
        "Your idea '$ideaTitle' has been analyzed",
        ['idea_title' => $ideaTitle]
    );

    return response()->json(['status' => true, 'message' => 'Notification sent']);
}
}
