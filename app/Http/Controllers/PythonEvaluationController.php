<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PythonEvaluationService;
use App\Models\Output;
use App\Models\Activity;
use Illuminate\Support\Facades\Log;

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

        // Retrieve activity details
        $activity = Activity::find($request->activity_id);
        if (!$activity) {
            return response()->json(['error' => 'Activity not found.'], 404);
        }

        // Retrieve existing submission
        $output = Output::where('activity_id', $request->activity_id)
                        ->where('user_id', $request->user_id)
                        ->first();

        // Calculate time consumed if submission exists
        if ($output) {
            $total_seconds = $activity->created_at->diffInSeconds($output->created_at);
            $minutes = floor($total_seconds / 60);
            $seconds = $total_seconds % 60;
            $time_consumed = sprintf("%d minutes %d seconds", $minutes, $seconds);
        } else {
            $time_consumed = "N/A"; // If output is missing
        }

        // Get instruction and assigned score
        $instruction = $activity->instruction ?? 'Evaluate the code based on correctness, efficiency, and best practices.';
        $assigned_score = $activity->points ?? 100;

        // Evaluate the Python code
        $evaluation = $this->evaluationService->evaluatePythonCode(
            $request->python_code,
            $instruction,
            $request->user_id,
            $request->activity_id,
            $request->section_id,
            $assigned_score,
            $time_consumed
        );

        if (!$evaluation) {
            return response()->json(['error' => 'Evaluation failed. Please try again.'], 500);
        }
        $submitted = Activity::where('id', $request->activity_id)->first(); // Ensure correct ID is used

        if ($submitted) {
            $submitted->update(['is_submitted' => true]);
        } else {
            return response()->json(['error' => 'Activity not found'], 404);
        }


        return response()->json([
            'message' => 'Python code evaluated successfully.',
            'evaluation' => $evaluation
        ]);
    }

    public function getEvaluations() {
        // Retrieve all evaluations
        return response()->json(Output::all());
    }

    public function checkSubmission(Request $request) {
        // Validate required fields
        $request->validate([
            'user_id' => 'required|integer',
            'activity_id' => 'required|integer',
        ]);

        $activity = Activity::find($request->activity_id);
        if (!$activity) {
            return response()->json(['error' => 'Activity not found.'], 404);
        }

        $submission = Output::where('user_id', $request->user_id)
                            ->where('activity_id', $request->activity_id)
                            ->first();

        if (!$submission) {
            return response()->json(['submitted' => false]);
        }

        $assigned_score = $activity->points ?? 100;

        // Convert feedback to HTML format
        $formattedFeedback = nl2br(e($submission->feedback));

        // Convert markdown **bold** to <strong>bold</strong>
        $formattedFeedback = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $formattedFeedback);

        // Convert markdown *italic* to <em>italic</em>
        $formattedFeedback = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $formattedFeedback);

        // Convert markdown bullet points (- item) to <ul><li>...</li></ul>
        if (preg_match('/- (.*?)<br>/', $formattedFeedback)) {
            $formattedFeedback = preg_replace('/- (.*?)<br>/', '<li>$1</li>', $formattedFeedback);
            $formattedFeedback = "<ul>" . $formattedFeedback . "</ul>";
        }

        return response()->json([
            'submitted' => true,
            'score' => $submission->score,
            'assigned_score' => $assigned_score,
            'feedback' => $formattedFeedback,
            'python_code' => $submission->code
        ]);
    }
}
?>
