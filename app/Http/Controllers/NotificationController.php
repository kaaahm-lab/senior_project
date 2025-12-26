<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{
   public function saveToken(Request $request)
{
    $request->validate([
        'fcm_token' => 'required|string',
    ]);

    $user = User::findOrFail(Auth::id());

    $user->update([
        'fcm_token' => $request->fcm_token,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'FCM token saved successfully',
    ]);
}
}
