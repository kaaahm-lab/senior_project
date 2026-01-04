<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MockCompetitionAiController extends Controller
{
    public function analyze(Request $request)
    {
  

        return response()->json([
            "idea" => [
                "original_text" => $request->idea_text,
                "normalized_industry" => $request->industry_hint ?? "Unknown",
                "target_country" => $request->target_country ?? null,
                "target_city" => $request->target_city ?? null,
            ],

            "competitors_summary" => [
                "total_competitors_found" => 37,
                "competition_level" => "high",
                "avg_company_size" => "51-200",
                "top_regions" => [
                    ["region" => "Dubai", "count" => 20],
                    ["region" => "Abu Dhabi", "count" => 9],
                ],
            ],

            "competitors" => [
                [
                    "name" => "ABC Telemedicine Clinic",
                    "industry" => "Healthcare",
                    "region" => "Dubai",
                    "country" => "UAE",
                    "size" => "51-200",
                    "website" => "https://example.com",
                    "similarity_score" => 0.91,
                ],
                [
                    "name" => "HealthNow Platform",
                    "industry" => "Healthcare",
                    "region" => "Dubai",
                    "country" => "UAE",
                    "size" => "11-50",
                    "website" => "https://healthnow.test",
                    "similarity_score" => 0.86,
                ],
            ],

            "clusters" => [
                [
                    "cluster_id" => 0,
                    "label" => "Telemedicine Platforms",
                    "keywords" => [
                        "online consultation",
                        "video call",
                        "booking"
                    ],
                    "competitors_count" => 18,
                ],
                [
                    "cluster_id" => 1,
                    "label" => "Clinic Management Systems",
                    "keywords" => [
                        "appointments",
                        "medical records",
                        "billing"
                    ],
                    "competitors_count" => 11,
                ],
            ],

            "swot" => [
                "strengths" => [
                    "High demand for digital healthcare services",
                    "Convenience for patients and doctors",
                ],
                "weaknesses" => [
                    "Strong existing competitors",
                    "Regulatory and licensing requirements",
                ],
                "opportunities" => [
                    "Government support for smart health in UAE",
                    "Increasing adoption of telemedicine",
                ],
                "threats" => [
                    "Entry of large global health platforms",
                    "Possible regulatory changes",
                ],
            ],

            "metadata" => [
                "model_version" => "competition_v1.0",
                "mock" => true,
            ],
        ]);
    }
}
