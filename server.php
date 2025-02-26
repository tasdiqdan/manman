<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST["category"];
    
    // PDF faylni o'qish va savollarni tanlash
    $pdfFile = $category;
    $questions = extractQuestionsFromPDF($pdfFile);
    
    // 50 ta tasodifiy savol tanlash
    shuffle($questions);
    $selectedQuestions = array_slice($questions, 0, 50);
    
    function extractQuestionsFromPDF($file) {
        // PDF dan matn chiqarish uchun `pdftotext` ishlatiladi
        $output = shell_exec("pdftotext $file -");
        $lines = explode("\n", $output);
        $questions = [];
        
        for ($i = 0; $i < count($lines); $i++) {
            if (strpos($lines[$i], '?') !== false) {
                $answers = [$lines[$i+1], $lines[$i+2], $lines[$i+3], $lines[$i+4]];
                shuffle($answers);
                $questions[] = [
                    "question" => $lines[$i],
                    "answers" => $answers,
                    "correct" => trim(str_replace('*', '', $lines[$i+1]))
                ];
                $i += 4;
            }
        }
        return $questions;
    }
    ?>

    <!DOCTYPE html>
    <html lang="uz">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Test Savollari</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; padding: 20px; }
            .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); width: 500px; margin: auto; }
            .question { font-size: 18px; margin: 20px 0; }
        </style>
    </head>
    <body>
    <div class="container">
        <h2>Test Savollari</h2>
        <form method="POST" action="results.php">
            <?php foreach ($selectedQuestions as $index => $q) { ?>
                <p class="question"><?php echo ($index + 1) . ". " . $q["question"]; ?></p>
                <?php foreach ($q["answers"] as $answer) { ?>
                    <input type="radio" name="q<?php echo $index; ?>" value="<?php echo $answer; ?>"> <?php echo $answer; ?><br>
                <?php } ?>
            <?php } ?>
            <br>
            <button type="submit">Testni yakunlash</button>
        </form>
    </div>
    </body>
    </html>
    <?php
} else {
    echo "Noto‘g‘ri so‘rov!";
}
?>
