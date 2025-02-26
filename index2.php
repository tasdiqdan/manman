<?php
session_start();
require 'vendor/autoload.php'; // TCPDF yoki FPDI uchun

use Smalot\PdfParser\Parser;

function extractQuestionsFromPDF($filePath) {
    $parser = new Parser();
    $pdf = $parser->parseFile($filePath);
    $text = $pdf->getText();
    
    $lines = explode("\n", $text);
    $questions = [];
    for ($i = 0; $i < count($lines); $i++) {
        if (strpos($lines[$i], '?') !== false) {
            $answers = array_slice($lines, $i + 1, 4);
            shuffle($answers);
            $correctAnswer = array_filter($answers, fn($a) => strpos($a, '*') !== false);
            $correctAnswer = reset($correctAnswer);
            $correctAnswer = str_replace('*', '', $correctAnswer);
            
            $questions[] = [
                'question' => trim($lines[$i]),
                'answers' => array_map('trim', $answers),
                'correct' => trim($correctAnswer)
            ];
            $i += 4;
        }
    }
    return $questions;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category'])) {
    $category = $_POST['category'];
    $pdfPath = [
            "Rentgenologiya.pdf" => "https://raw.githubusercontent.com/Foydalanuvchi/Repository/main/pdfs/Rentgenologiya.pdf",
    "matematika.pdf" => "https://raw.githubusercontent.com/Foydalanuvchi/Repository/main/pdfs/matematika.pdf",
    "fizika.pdf" => "https://raw.githubusercontent.com/Foydalanuvchi/Repository/main/pdfs/fizika.pdf",
    "kimyo.pdf" => "https://raw.githubusercontent.com/Foydalanuvchi/Repository/main/pdfs/kimyo.pdf"
];

if (!isset($pdfUrls[$category])) {
    die("Noto‘g‘ri toifa tanlandi.");
}
    }
    
    $questions = extractQuestionsFromPDF($pdfPath);
    shuffle($questions);
    $_SESSION['questions'] = array_slice($questions, 0, 50);
    $_SESSION['current'] = 0;
    $_SESSION['score'] = 0;
    header("Location: test.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Tizimi</title>
</head>
<body>
    <h2>Test Tizimi</h2>
    <form method="POST" action="">
        <label for="category">Toifani tanlang:</label>
        <select name="category">
            <option value="Rentgenologiya.pdf">Rentgenologiya</option>
            <option value="matematika.pdf">Matematika</option>
            <option value="fizika.pdf">Fizika</option>
            <option value="kimyo.pdf">Kimyo</option>
        </select>
        <button type="submit">Boshlash</button>
    </form>
</body>
</html>
