<?php
session_start();
if (!isset($_SESSION['questions'])) {
    header("Location: index.php");
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
        header("Location: result.php");
        exit;
    }
}
$question = $questions[$_SESSION['current']];
?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Savollari</title>
</head>
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
