<?php
namespace App\Services;

use App\Models\Output;
use Illuminate\Support\Facades\Http;

class PythonEvaluationService {
    protected $apiKey;

    public function __construct() {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function evaluatePythonCode($code, $instruction, $userId, $activityId, $sectionId,$assigned_score) {
        // Construct a more precise and structured prompt
        $prompt = "Instruction: $instruction\n\n"
                . "Evaluate the following Python code based on the given instruction. Provide a score (0-$assigned_score) and detailed feedback covering correctness, efficiency, and best coding practices.\n\n"
                . "```python\n$code\n```";

        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$this->apiKey}", [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ]);
        $result = $response->json();

        // Handle possible missing response
        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'score' => 0,
                'feedback' => 'No feedback received. Please try again.',
            ];
        }

        $feedback = $result['candidates'][0]['content']['parts'][0]['text'];

        // Extract score using regex
        preg_match('/Score:\s*(\d+)/', $feedback, $matches);
        $score = isset($matches[1]) ? floatval($matches[1]) : 0;

        // Save evaluation to database
        return Output::create([
            'user_id' => $userId,
            'activity_id' => $activityId,
            'section_id' => $sectionId,
            'code' => $code,
            'score' => $score,
            'feedback' => $feedback,
        ]);
    }
}
?>
