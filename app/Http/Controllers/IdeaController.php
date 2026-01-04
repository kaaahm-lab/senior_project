<?php

namespace App\Http\Controllers;
use App\Events\IdeaCreated;
use App\Models\Idea;
use App\Models\Competitor;
use App\Models\Recommendation;
use App\Models\AnalysisReport;
use App\Models\FinancialEstimation;
use App\Models\SwotAnalysis;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class IdeaController extends Controller
{
    /**
     * إنشاء فكرة جديدة + إرسالها للـ AI
     */
   public function store(Request $request)
{
    $request->validate([
        'title'           => 'required|string|max:255',
        'description'     => 'required|string',

    ]);

    // استخدام Transaction لضمان سلامة البيانات
    return DB::transaction(function () use ($request) {

        // 1) حفظ الفكرة
        $idea = Idea::create([
            'user_id'         => Auth::id(),
            'title'           => $request->title,
            'description'     => $request->description,
            'status'          => 'processing',
        ]);

        try {
            // 2) إرسال الفكرة للـ AI
            $aiResponse = Http::timeout(10)->post('http://127.0.0.1:8001/api/mock-ai',
             [
                'idea_text' => $idea->description,
                'top_k' => 3
            ]);

            if (!$aiResponse->successful()) {
                throw new \Exception("AI API error");
            }

            $ai = $aiResponse->json();

            // 3) حفظ التقرير
            $report = AnalysisReport::create([
                'idea_id'            => $idea->id,
                'predicted_category' => $ai['predicted_category'],
                'confidence'         => $ai['confidence'],
                'is_ambiguous'       => $ai['is_ambiguous'],
                'top_k'              => $ai['top_k'],
            ]);

            $idea->update(['status' => 'done']);

            return response()->json([
                'status'  => true,
                'message' => 'Idea analyzed successfully',
                'data'    => [
                    'idea' => $idea,
                    'report' => $report
                ]
            ]);

        } catch (\Exception $e) {
            $idea->update(['status' => 'failed']);
            return response()->json([
                'status' => false,
                'message' => "Analysis failed: " . $e->getMessage(),
            ], 500);
        }
    });
}


public function runCompetitionAnalysis(Request $request)
{
    return DB::transaction(function () use ($request) {

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ جلب الفكرة والتأكد من الملكية
        |--------------------------------------------------------------------------
        */

        $idea = Idea::where('id', $request->idea_id)
                    ->where('user_id', Auth::id())
                    ->first();

        if (!$idea) {
            return response()->json([
                'status' => false,
                'message' => 'Idea not found or unauthorized'
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ جلب نتيجة Model 1 (Idea Classification)
        |--------------------------------------------------------------------------
        */



        $analysis = AnalysisReport::where('idea_id', $idea->id)->latest()->first();

        if (!$analysis) {
            throw new \Exception('Idea classification not found (no analysis record)');
        }

        if (empty($analysis->predicted_category)) {
            throw new \Exception('Idea classification found but predicted_category is empty');
        }


        /*
        |--------------------------------------------------------------------------
        | 3️⃣ إرسال Request إلى AI Model 2
        |--------------------------------------------------------------------------
        */

        $response = Http::timeout(10)->post(
            'http://127.0.0.1:8001/api/mock-ai/competition',
            [
                'idea_text'       => $idea->description,
                'industry_hint'   => $analysis->predicted_category,
                'target_country'  => $request->target_country,
                'target_city'     => $request->target_city,
                'max_competitors' => $request->max_competitors ?? 10,
                'max_clusters'    => $request->max_clusters ?? 4,
            ]
        );

        if (!$response->successful()) {
            throw new \Exception('Competition AI API failed');
        }

        $data = $response->json();

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ تخزين المنافسين
        |--------------------------------------------------------------------------
        */

        foreach ($data['competitors'] as $comp) {
            Competitor::create([
                'idea_id'          => $idea->id,
                'name'             => $comp['name'],
                'industry'         => $comp['industry'],
                'region'           => $comp['region'],
                'country'          => $comp['country'],
                'company_size'     => $comp['size'],
                'website'          => $comp['website'] ?? null,
                'similarity_score' => $comp['similarity_score'],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ تخزين SWOT Analysis
        |--------------------------------------------------------------------------
        */

        SwotAnalysis::create([
            'idea_id'       => $idea->id,
            'strengths'     => $data['swot']['strengths'],
            'weaknesses'    => $data['swot']['weaknesses'],
            'opportunities' => $data['swot']['opportunities'],
            'threats'       => $data['swot']['threats'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 6️⃣ تحديث حالة الفكرة
        |--------------------------------------------------------------------------
        */

        $idea->update(['status' => 'done']);

        /*
        |--------------------------------------------------------------------------
        | 7️⃣ إرسال إشعار
        |--------------------------------------------------------------------------
        */

        event(new \App\Events\IdeaAnalysisCompleted($idea));

        /*
        |--------------------------------------------------------------------------
        | 🔚 Response
        |--------------------------------------------------------------------------
        */

                        return response()->json([
                    'status' => true,

                    
                    'idea' => $data['idea'],
                    'competitors_summary' => $data['competitors_summary'],
                    'competitors' => $data['competitors'],
                    'clusters' => $data['clusters'],
                    'swot' => $data['swot'],
                    'metadata' => $data['metadata'] ?? null
                ]);

    });
}







    /**
     * جميع أفكار المستخدم
     */
    public function myIdeas()
    {
        $ideas = Idea::where('user_id', Auth::id())
            ->with(['competitors', 'recommendations', 'financialEstimation', 'report'])
            ->get();

        return response()->json([
            'status' => true,
            'ideas' => $ideas,
        ]);
    }

    /**
     * عرض فكرة واحدة
     */
    public function show($id)
    {
        $idea = Idea::where('id', $id)
            ->with(['competitors', 'recommendations', 'financialEstimation', 'report'])
            ->first();

        if (!$idea) {
            return response()->json([
                'status' => false,
                'message' => 'Idea not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'idea' => $idea,
        ]);
    }

    /**
     * تحديث حالة الفكرة (للمشرف)
     */

    public function update(Request $request)
{
    $request->validate([
        'idea_id'          => 'required|integer',
        'title'            => 'required|string|max:255',
        'description'      => 'required|string',
        'industry'         => 'nullable|string',
        'target_audience'  => 'nullable|string',
    ]);

    // 1) اجلب الفكرة وتأكد أنها للمستخدم نفسه
    $idea = Idea::where('id', $request->idea_id)
                ->where('user_id', Auth::id())
                ->first();

    if (!$idea) {
        return response()->json([
            'status' => false,
            'message' => 'Idea not found or unauthorized'
        ], 404);
    }

    // 2) تحديث بيانات الفكرة
    $idea->update([
        'title'            => $request->title,
        'description'      => $request->description,
        'industry'         => $request->industry,
        'target_audience'  => $request->target_audience,
        'status'           => 'pending',  // نرجعها pending
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Idea updated successfully',
        'idea' => $idea
    ]);
}
public function reanalyze(Request $request)
{
    $request->validate([
        'idea_id' => 'required|integer'
    ]);

    // 1) تأكد أن الفكرة تخص المستخدم
    $idea = Idea::where('id', $request->idea_id)
                ->where('user_id', Auth::id())
                ->first();

    if (!$idea) {
        return response()->json([
            'status' => false,
            'message' => 'Idea not found or unauthorized',
        ], 404);
    }

    // 2) غيّر الحالة إلى processing
    $idea->update(['status' => 'processing']);

    // 3) احذف البيانات القديمة
    $idea->competitors()->delete();
    $idea->recommendations()->delete();
    $idea->financialEstimation()->delete();
    $idea->report()->delete();

    // 4) أرسل الطلب للذكاء الاصطناعي (نفس الاستدعاء السابق)
    try {
        $aiResponse = Http::post('http://127.0.0.1:8001/api/mock-ai', [
            'title'       => $idea->title,
            'description' => $idea->description,
        ]);

        if (!$aiResponse->successful()) {
            $idea->update(['status' => 'failed']);
            return response()->json([
                'status' => false,
                'message' => 'AI server did not respond',
            ], 500);
        }

        $ai = $aiResponse->json()['data'];

    } catch (\Exception $e) {
        $idea->update(['status' => 'failed']);
        return response()->json([
            'status' => false,
            'message' => "AI error: " . $e->getMessage(),
        ], 500);
    }

    // 5) احفظ التوصيات الجديدة
    foreach ($ai['recommendations'] as $rec) {
        Recommendation::create([
            'idea_id' => $idea->id,
            'recommendation_text' => $rec,
        ]);
    }

    // 6) احفظ المنافسين الجدد
    foreach ($ai['competitors'] as $comp) {
        Competitor::create([
            'idea_id' => $idea->id,
            'name' => $comp['name'],
            'description' => $comp['description'],
        ]);
    }

    // 7) حفظ التحليل المالي الجديد
    FinancialEstimation::create([
        'idea_id'           => $idea->id,
        'estimated_cost'    => $ai['financial_estimation']['estimated_cost'],
        'estimated_revenue' => $ai['financial_estimation']['expected_revenue'],
        'roi'               => $ai['financial_estimation']['roi'],
    ]);

    // 8) تقرير التحليل الجديد
    AnalysisReport::create([
        'idea_id'    => $idea->id,
        'strengths'  => $ai['strengths'],
        'weaknesses' => $ai['weaknesses'],
        'pdf_path'   => 'none',
        'report_type'  => 'full',
        'storage_disk' => 'local',
    ]);

    // 9) تحديث حالة الفكرة
    $idea->update(['status' => 'done']);


}
public function delete(Request $request)
{
    $request->validate([
        'idea_id' => 'required|integer',
    ]);

    // 1) جلب الفكرة والتأكد أنها لنفس المستخدم
    $idea = Idea::where('id', $request->idea_id)
                ->where('user_id', Auth::id())
                ->first();

    if (!$idea) {
        return response()->json([
            'status' => false,
            'message' => 'Idea not found or unauthorized',
        ], 404);
    }

    // 2) حذف الفكرة (وسيتم حذف كل البيانات المرتبطة بسبب onDelete Cascade)
    $idea->delete();

    return response()->json([
        'status' => true,
        'message' => 'Idea deleted successfully',
    ]);
}


}
