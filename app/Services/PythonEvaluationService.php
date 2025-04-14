<?php
namespace App\Services;

use App\Models\Output;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonEvaluationService {
    protected $apiKey;

    public function __construct() {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function evaluatePythonCode($code, $instruction, $userId, $activityId, $sectionId, $assigned_score, $time_consumed) {
        // Decode the Base64-encoded Python code
        $decodedCode = base64_decode($code);

        // Construct a structured prompt for Gemini
        $prompt = "Instruction: $instruction\n\n"
                . "Time consumed: $time_consumed seconds\n\n"
                . "Evaluate the following Python code based on the given instruction. Provide a score (0-$assigned_score) and a short feedback. Note that if the code is not in python automatically zero. \n\n"
                . "### Evaluation Criteria:\n"
                . "- **Adherence to the assigned problems (35%)**: Assesses the extent to which the solution aligns with the specific requirements and constraints outlined in the problem instruction.\n"
                . "- **Program Execution (30%)**: Evaluates whether the program runs successfully without errors and produces the expected output. It also considers the clarity and quality of the code construction.\n"
                . "- **Correctness (25%)**: Checks the program's accuracy and logical correctness, ensuring it produces correct results for various inputs.\n"
                . "- **Time Efficiency (10%)**: Assesses the efficiency of the program's algorithm and implementation, ensuring tasks complete within a reasonable timeframe.\n\n"
                . "### Python Code:\n"
                . "```python\n$decodedCode\n```\n\n"
                . "Please provide your response in the following format:\n"
                . "**Score:** (value between 0-$assigned_score)\n"
                . "**Feedback:** (brief feedback on performance)";

        try {
            // Send request to Gemini API
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$this->apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            // Check for API error
            if (!$response->successful()) {
                Log::error('Python Evaluation API request failed.', ['response' => $response->body()]);
                return [
                    'score' => 0,
                    'feedback' => 'API request failed. Please try again later.',
                ];
            }

            $result = $response->json();

            // Extract text feedback
            if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                Log::error('Python Evaluation API returned an unexpected response.', ['response' => $result]);
                return [
                    'score' => 0,
                    'feedback' => 'No valid response received. Please try again.',
                ];
            }

            $feedback = $result['candidates'][0]['content']['parts'][0]['text'];

            // Extract score using regex
            preg_match('/\*\*Score:\*\*\s*(\d+)/i', $feedback, $matches);
            $score = isset($matches[1]) ? min(intval($matches[1]), $assigned_score) : 0;

            // Clean feedback (remove score line)
            $cleanedFeedback = trim(str_replace("**Score:** $score", '', $feedback));

            // Save to database
            return Output::create([
                'user_id' => $userId,
                'activity_id' => $activityId,
                'section_id' => $sectionId,
                'code' => $decodedCode,
                'score' => $score,
                'feedback' => $cleanedFeedback,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in PythonEvaluationService:', ['message' => $e->getMessage()]);
            return [
                'score' => 0,
                'feedback' => 'An error occurred during evaluation. Please try again.',
            ];
        }
    }
}
