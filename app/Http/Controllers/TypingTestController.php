<?php

namespace App\Http\Controllers;

use App\Models\TestResult;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypingTestController extends Controller
{
    public function index()
    {
        $randomText = $this->generateRandomText();
        return view('typing-test', compact('randomText'));
    }

    private function generateRandomText()
    {
        $texts = [
            "The quick brown fox jumps over the lazy dog.",
            "Laravel is a PHP framework designed for web artisans.",
            "Typing fast is all about muscle memory.",
        ];
        return $texts[array_rand($texts)];
    }

    public function calculateResult(Request $request)
    {
        $typedText = $request->input('typedText');
        $originalText = $request->input('originalText');
        $timeTaken = $request->input('timeTaken'); // waktu dalam detik

        // Menghitung WPM dan akurasi
        $wpm = $this->calculateWPM($typedText, $timeTaken);
        $accuracy = $this->calculateAccuracy($typedText, $originalText);
        $correctChars = $this->calculateCorrectChars($typedText, $originalText);
        $incorrectChars = strlen($typedText) - $correctChars;

        // Simpan hasil ke database jika pengguna login
        if (Auth::check()) {
            TestResult::create([
                'user_id' => Auth::id(),
                'wpm' => round($wpm),
                'accuracy' => $accuracy,
                'correct_chars' => $correctChars,
                'incorrect_chars' => $incorrectChars,
                'time_taken' => $timeTaken,
            ]);
        }

        return response()->json([
            'wpm' => round($wpm),
            'accuracy' => $accuracy,
            'correctChars' => $correctChars,
            'incorrectChars' => $incorrectChars,
        ]);
    }

    private function calculateWPM($typedText, $timeTaken)
    {
        $words = str_word_count($typedText);
        $minutes = $timeTaken / 60;
        return $minutes > 0 ? $words / $minutes : 0;
    }

    private function calculateAccuracy($typedText, $originalText)
    {
        $correctChars = $this->calculateCorrectChars($typedText, $originalText);
        return ($correctChars / strlen($originalText)) * 100;
    }

    private function calculateCorrectChars($typedText, $originalText)
    {
        $correctChars = 0;
        $length = min(strlen($typedText), strlen($originalText));

        for ($i = 0; $i < $length; $i++) {
            if ($typedText[$i] === $originalText[$i]) {
                $correctChars++;
            }
        }

        return $correctChars;
    }

    public function leaderboard()
{
        $results = TestResult::with('user')
            ->orderBy('wpm', 'desc')
            ->take(10)
            ->get();

        return view('leaderboard', compact('results'));
    }

    public function randomText()
    {
        $randomText = $this->generateRandomText(); // Menggunakan metode yang sudah ada
        return response()->json(['randomText' => $randomText]);
    }


}
