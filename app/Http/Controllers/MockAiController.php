<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MockAiController extends Controller
{
    public function analyze(Request $request)
    {
        // يمكنك لاحقاً استخدام $request->idea_text و $request->top_k
        // حالياً Mock ثابت للاختبار

        return response()->json([
            "predicted_category" => "Healthcare",
            "confidence" => 0.978,
            "is_ambiguous" => false,
            "top_k" => [
                [
                    "category" => "Healthcare",
                    "confidence" => 0.978
                ],
                [
                    "category" => "Professional Services",
                    "confidence" => 0.015
                ],
                [
                    "category" => "Corporate Services",
                    "confidence" => 0.007
                ]
            ]
        ]);
    }
}
