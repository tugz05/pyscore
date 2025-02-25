<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PythonEvaluationService;
use App\Models\Output;
use App\Models\Activity; // Import the Activity model to fetch instructions

class PythonEvaluationController extends Controller {
    protected $evaluationService;

    public function __construct(PythonEvaluationService $evaluationService) {
        $this->evaluationService = $evaluationService;
    }

    public function evaluate(Request $request) {
        // Validate required fields
        $request->validate([
            'python_code' => 'required|string',
            'user_id' => 'required|integer',
            'activity_id' => 'required|integer',
            'section_id' => 'required|integer',
        ]);

        // Retrieve instruction from database
        $activity = Activity::find($request->activity_id);
        $instruction = $activity ? $activity->instruction : 'Evaluate the code based on correctness, efficiency, and best practices.';
        $assigned_score = $activity->points;
        // Evaluate the Python code with instructions
        $evaluation = $this->evaluationService->evaluatePythonCode(
            $request->python_code,
            $instruction,
            $request->user_id,
            $request->activity_id,
            $request->section_id,
            $assigned_score
        );

        return response()->json([
            'message' => 'Python code evaluated successfully.',
        ]);
    }

    public function getEvaluations() {
        // Retrieve all evaluations
        return response()->json(Output::all());
    }
    public function checkSubmission(Request $request)
{
    $submission = Output::where('user_id', $request->user_id)
                            ->where('activity_id', $request->activity_id)
                            ->first();
    $assigned_score = Activity::find($request->activity_id)->points;

    if ($submission) {
        $formattedFeedback = nl2br(e($submission->feedback));

        // Convert **bold** text to <strong>bold</strong>
        $formattedFeedback = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $formattedFeedback);

        // Convert *italic* text to <em>italic</em>
        $formattedFeedback = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $formattedFeedback);

        // Convert markdown-style bullet points (- item) into proper <ul><li>...</li></ul>
        $formattedFeedback = preg_replace('/- (.*?)<br>/', '<li>$1</li>', $formattedFeedback);
        $formattedFeedback = "<ul>" . $formattedFeedback . "</ul>"; // Wrap in <ul>
        return response()->json([
            'submitted' => true,
            'score' => $submission->score,
            'assigned_score' => $assigned_score,
            'feedback' => $formattedFeedback,
            'python_code' => $submission->code
        ]);
    } else {
        return response()->json(['submitted' => false]);
    }
}
}
?>
