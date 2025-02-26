<?php
session_start();
require 'vendor/autoload.php'; // TCPDF yoki FPDI uchun
use Smalot\PdfParser\Parser;

function extractQuestionsFromPDF($pdfUrl) {
    $pdfContent = file_get_contents($pdfUrl);
    if (!$pdfContent) {
        die("PDF yuklanmadi yoki noto‘g‘ri format");
    }
    $parser = new Parser();
    $pdf = $parser->parseContent($pdfContent);
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

$pdfUrls = [
    "Rentgenologiya.pdf" => "https://raw.githubusercontent.com/Foydalanuvchi/Repository/main/pdfs/Rentgenologiya.pdf",
    "matematika.pdf" => "https://raw.githubusercontent.com/Foydalanuvchi/Repository/main/pdfs/matematika.pdf",
    "fizika.pdf" => "https://raw.githubusercontent.com/Foydalanuvchi/Repository/main/pdfs/fizika.pdf",
    "kimyo.pdf" => "https://raw.githubusercontent.com/Foydalanuvchi/Repository/main/pdfs/kimyo.pdf"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category'])) {
    $category = $_POST['category'];
    if (!isset($pdfUrls[$category])) {
        die("Noto‘g‘ri toifa tanlandi.");
    }
    
    $_SESSION['questions'] = extractQuestionsFromPDF($pdfUrls[$category]);
    shuffle($_SESSION['questions']);
    $_SESSION['questions'] = array_slice($_SESSION['questions'], 0, 50);
    $_SESSION['current'] = 0;
    $_SESSION['score'] = 0;
    header("Location: ?test");
    exit;
}

if (isset($_GET['test'])) {
    if (!isset($_SESSION['questions'])) {
        header("Location: ?");
        exit;
    }
    $current = $_SESSION['current'];
    $questions = $_SESSION['questions'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selectedAnswer = $_POST['answer'] ?? '';
        if ($selectedAnswer === $questions[$current]['correct']) {
            $_SESSION['score']++;
        }
        $_SESSION['current']++;
        if ($_SESSION['current'] >= count($questions)) {
            header("Location: ?result");
            exit;
        }
    }
    $question = $questions[$_SESSION['current']];
    ?>
    <!DOCTYPE html>
    <html lang="uz">
    <head><meta charset="UTF-8"><title>Test</title></head>
    <body>
        <h2><?= $_SESSION['current'] + 1 ?>. <?= htmlspecialchars($question['question']) ?></h2>
        <form method="POST">
            <?php foreach ($question['answers'] as $answer): ?>
                <input type="radio" name="answer" value="<?= htmlspecialchars($answer) ?>" required> <?= htmlspecialchars($answer) ?><br>
            <?php endforeach; ?>
            <button type="submit">Keyingi</button>
        </form>
    </body>
    </html>
    <?php
    exit;
}

if (isset($_GET['result'])) {
    if (!isset($_SESSION['score'])) {
        header("Location: ?");
        exit;
    }
    $score = $_SESSION['score'];
    $total = count($_SESSION['questions']);
    session_destroy();
    ?>
    <!DOCTYPE html>
    <html lang="uz">
    <head><meta charset="UTF-8"><title>Natija</title></head>
    <body>
        <h2>Test yakunlandi!</h2>
        <p>Sizning natijangiz: <?= $score ?> / <?= $total ?></p>
        <a href="?">Qayta boshlash</a>
    </body>
    </html>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Test Tizimi</title>
</head>
<body>
    <h2>Test Tizimi</h2>
    <form method="POST">
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
