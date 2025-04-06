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

    public function evaluatePythonCode($code, $instruction, $userId, $activityId, $sectionId, $assigned_score) {
        // Updated prompt using the new criteria
        $prompt = "Instruction: $instruction\n\n"
                . "Evaluate the following Python code based on the given Instruction and criteria below. Provide a score (0-$assigned_score) and a short feedback.\n\n"
                . "### Evaluation Criteria:\n\n"
                . "**READABILITY**\n"
                . "POOR (1)\n- The code is poorly organized and very difficult to read.\n"
                . "BAD (2)\n- The code is readable only by someone who knows what it is supposed to be doing.\n"
                . "FAIR (3)\n- The code is fairly easy to read.\n"
                . "GOOD (4)\n- Code is easy to read just some minor mistakes.\n"
                . "EXCELLENT (5)\n- The code is exceptionally well organized and very easy to follow.\n\n"

                . "**EFFICIENCY**\n"
                . "POOR (1)\n- The code is huge and appears to be patched together.\n"
                . "BAD (2)\n- The code is brute force and unnecessarily long.\n"
                . "FAIR (3)\n- The code is fairly efficient without sacrificing readability and understanding.\n"
                . "GOOD (4)\n- The code is efficient without sacrificing readability and understanding.\n"
                . "EXCELLENT (5)\n- The code is extremely efficient without sacrificing readability and understanding.\n\n"

                . "**REUSABILITY**\n"
                . "POOR (1)\n- The code is not organized for reusability.\n"
                . "BAD (2)\n- Some parts of the code could be reused in other programs.\n"
                . "FAIR (3)\n- Most of the code could be reused in other programs.\n"
                . "GOOD (4)\n- The code could be reused in other programs.\n"
                . "EXCELLENT (5)\n- The code could be reused as a whole, or each routine could be reused.\n\n"

                . "**SPECIFICATIONS**\n"
                . "POOR (1)\n- The program is producing incorrect results.\n"
                . "BAD (2)\n- The program produces correct results but does not display them correctly.\n"
                . "FAIR (3)\n- The program produces correct results but does not display all of it correctly.\n"
                . "GOOD (4)\n- The program works and produces the correct results and displays them correctly. It also meets most of the other specifications.\n"
                . "EXCELLENT (5)\n- The program works and meets all the specifications.\n\n"

                . "**FORMATTING**\n"
                . "POOR (1)\n- Code is poorly formatted.\n"
                . "BAD (2)\n- Code is formatted but shows lack of work.\n"
                . "FAIR (3)\n- Code is fairly formatted for program.\n"
                . "GOOD (4)\n- Code is formatted and is easily to read for program.\n"
                . "EXCELLENT (5)\n- Code is formatted and is perfect with program.\n\n"

                . "### How to calculate the final score:\n"
                . "Each criterion is scored from 1 to 5. Add the scores for all 5 criteria to get a total out of 25.\n"
                . "Then calculate the final score out of $assigned_score using this formula:\n"
                . "(Total criteria score / 25) * $assigned_score\n"
                . "Round down to the nearest whole number.\n\n"

                . "### Python Code:\n"
                . "```python\n$code\n```\n\n"
                . "Please provide your response in the following format:\n"
                . "**Score:** (0-$assigned_score)\n"
                . "(brief and concise feedback)";

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$this->apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if (!$response->successful()) {
                Log::error('Python Evaluation API request failed.', ['response' => $response->body()]);
                return [
                    'score' => 0,
                    'feedback' => 'API request failed. Please try again later.',
                ];
            }

            $result = $response->json();

            if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                Log::error('Python Evaluation API returned an unexpected response.', ['response' => $result]);
                return [
                    'score' => 0,
                    'feedback' => 'No valid response received. Please try again.',
                ];
            }

            $feedback = $result['candidates'][0]['content']['parts'][0]['text'];

            preg_match('/\*\*Score:\*\*\s*(\d+)/i', $feedback, $matches);
            $score = isset($matches[1]) ? min(intval($matches[1]), $assigned_score) : 0;

            return Output::create([
                'user_id' => $userId,
                'activity_id' => $activityId,
                'section_id' => $sectionId,
                'code' => $code,
                'score' => $score,
                'feedback' => trim(str_replace("**Score:** $score", '', $feedback)),
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
?>
