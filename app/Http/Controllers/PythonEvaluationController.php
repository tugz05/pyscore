<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PythonEvaluationService;
use App\Models\Output;

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

        // Evaluate the Python code
        $evaluation = $this->evaluationService->evaluatePythonCode(
            $request->python_code,
            $request->user_id,
            $request->activity_id,
            $request->section_id
        );

        return response()->json([
            'message' => 'Python code evaluated successfully.',
            'data' => $evaluation
        ]);
    }

    public function getEvaluations() {
        // Retrieve all evaluations
        return response()->json(Output::all());
    }
}
?>
