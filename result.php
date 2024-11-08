<?php
header('Content-Type: text/html; charset=utf-8');

$text = $_SERVER["REQUEST_METHOD"] === "POST" ? trim($_POST['text'] ?? '') : '';
$text = $text !== '' ? $text : null;

function analyzeText($text)
{
    if (is_null($text)) {
        return null;
    }

    // Инициализация переменных для хранения результатов анализа
    $charCount = mb_strlen($text, 'UTF-8');
    $letterCount = $lowerCount = $upperCount = $punctuationCount = $digitCount = $wordCount = 0;
    $charFrequency = [];
    $wordFrequency = [];

    // Разбиение текста на слова
    $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($words as $word) {
        $normalizedWord = mb_strtolower($word, 'UTF-8');
        $wordFrequency[$normalizedWord] = ($wordFrequency[$normalizedWord] ?? 0) + 1;
        $wordCount++;
    }

    // Подсчет символов и определение их характеристик
    for ($i = 0; $i < $charCount; $i++) {
        $char = mb_substr($text, $i, 1, 'UTF-8');
        if (preg_match('/[a-zа-яё]/iu', $char)) {
            $letterCount++;
            $lowerCount += preg_match('/[a-zа-яё]/u', $char) ? 1 : 0;
            $upperCount += preg_match('/[A-ZА-ЯЁ]/u', $char) ? 1 : 0;
        } elseif (preg_match('/[0-9]/', $char)) {
            $digitCount++;
        } elseif (preg_match('/[.,!?;:\'\"-]/u', $char)) {
            $punctuationCount++;
        }

        $lowerChar = mb_strtolower($char, 'UTF-8');
        $charFrequency[$lowerChar] = ($charFrequency[$lowerChar] ?? 0) + 1;
    }

    return [
        'text' => $text,
        'charCount' => $charCount,
        'letterCount' => $letterCount,
        'lowerCount' => $lowerCount,
        'upperCount' => $upperCount,
        'punctuationCount' => $punctuationCount,
        'digitCount' => $digitCount,
        'wordCount' => $wordCount,
        'charFrequency' => $charFrequency,
        'wordFrequency' => $wordFrequency
    ];
}

$result = analyzeText($text);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты анализа</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Результаты анализа текста</h1>

    <?php if ($result): ?>
        <div class="text-output" style="color: red; font-style: italic;"><?= $result['text']; ?></div>

        <h2>Информация о тексте</h2>
        <table>
            <tr><th>Количество символов</th><td><?= $result['charCount']; ?></td></tr>
            <tr><th>Количество букв</th><td><?= $result['letterCount']; ?></td></tr>
            <tr><th>Количество строчных букв</th><td><?= $result['lowerCount']; ?></td></tr>
            <tr><th>Количество заглавных букв</th><td><?= $result['upperCount']; ?></td></tr>
            <tr><th>Количество знаков препинания</th><td><?= $result['punctuationCount']; ?></td></tr>
            <tr><th>Количество цифр</th><td><?= $result['digitCount']; ?></td></tr>
            <tr><th>Количество слов</th><td><?= $result['wordCount']; ?></td></tr>
        </table>

        <h2>Частота символов</h2>
        <table>
            <tr><th>Символ</th><th>Количество</th></tr>
            <?php foreach ($result['charFrequency'] as $char => $count): ?>
                <tr><td><?= $char; ?></td><td><?= $count; ?></td></tr>
            <?php endforeach; ?>
        </table>

        <h2>Частота слов</h2>
        <table>
            <tr><th>Слово</th><th>Количество</th></tr>
            <?php foreach ($result['wordFrequency'] as $word => $count): ?>
                <tr><td><?= $word; ?></td><td><?= $count; ?></td></tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <h2>Нет текста для анализа</h2>
    <?php endif; ?>

    <a href="index.html" class="button-link">Другой анализ</a>
</body>
</html>
