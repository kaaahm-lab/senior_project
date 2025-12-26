<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CompetitorController extends Controller
{
    public function get(Request $request)
{
    $request->validate([
        'idea_id' => 'required|integer'
    ]);

    $idea = Idea::where('id', $request->idea_id)
                ->where('user_id', Auth::id())
                ->with('competitors')
                ->first();

    if (!$idea) {
        return response()->json([
            'status' => false,
            'message' => 'Idea not found or not yours'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'competitors' => $idea->competitors
    ]);
}

}
