<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MockAiController extends Controller
{
    public function analyze(Request $request)
    {
        return response()->json([
            "success" => true,
            "message" => "Mock AI response returned successfully",
            "data" => [
                "strengths" => "Your idea has strong market potential",
                "weaknesses" => "High competition in this field",

                "recommendations" => [
                    "Improve your marketing strategy",
                    "Target specific niche customers",
                    "Focus on differentiation"
                ],

                "competitors" => [
                    [
                        "name" => "Competitor A",
                        "description" => "Large established company"
                    ],
                    [
                        "name" => "Competitor B",
                        "description" => "Growing startup in the same sector"
                    ]
                ],

                "financial_estimation" => [
                    "estimated_cost" => 1500,
                    "expected_revenue" => 5000,
                    "roi" => 233.33
                ],

                "report" => "This is a full mock AI analysis for testing."
            ]
        ]);
    }
}
