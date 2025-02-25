<?php
namespace App\Services;

use App\Models\Output;
use Illuminate\Support\Facades\Http;

class PythonEvaluationService {
    protected $apiKey;

    public function __construct() {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function evaluatePythonCode($code, $userId, $activityId, $sectionId) {
        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$this->apiKey}", [
            'contents' => [
                ['parts' => [['text' => "Evaluate the following Python code and provide a score (0-100) along with feedback:\n\n$code"]]]
            ]
        ]);

        $result = $response->json();

        // Handle possible missing response
        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'score' => 0,
                'feedback' => 'No feedback received.',
            ];
        }

        $feedback = $result['candidates'][0]['content']['parts'][0]['text'];

        // Extract score using regex
        preg_match('/Score:\s*(\d+)/', $feedback, $matches);
        $score = isset($matches[1]) ? floatval($matches[1]) : 0;

        // Save to database
        return Output::create([
            'user_id' => $userId,
            'activity_id' => $activityId,
            'section_id' => $sectionId,
            'code' => $code, // Assuming you want to store the code
            'score' => $score,
            'feedback' => $feedback,
        ]);
    }
}
?>
