<?php
session_start();
if (!isset($_SESSION['score'])) {
    header("Location: index.php");
    exit;
}
$score = $_SESSION['score'];
$total = count($_SESSION['questions']);
session_destroy();
?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Natija</title>
</head>
<body>
    <h2>Test yakunlandi!</h2>
    <p>Sizning natijangiz: <?= $score ?> / <?= $total ?></p>
    <a href="index.php">Qayta boshlash</a>
</body>
</html>
