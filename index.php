<?php
// 1. Esto carga todas las librerías de Composer
require 'vendor/autoload.php'; 

function is_text($text, $min, $max) {
    $len = strlen($text);
    return ($len >= $min && $len <= $max);
}

$user_data = ['testua' => '', 'hizkuntza' => ''];
$errors = ['testua' => '', 'hizkuntza' => ''];
$mezua = '';
$itzulpena = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_data['testua']    = $_POST['testua'] ?? '';
    $user_data['hizkuntza'] = $_POST['hizkuntza'] ?? '';

    $errors['testua']    = !empty($user_data['testua']) ? '' : 'Testu bat Sartu behar duzu';
    $errors['hizkuntza'] = !empty($user_data['hizkuntza']) ? '' : 'Hizkuntza bat aukeratu behar duzu';

    $invalid = implode($errors); 

    if ($invalid) {
        $mezua = '<h3>Mesedez, zuzendu akatsak</h3>';
    } else {
        $mezua = 'Datuak ondo daude. Itzultzen...';
        try {
            $yourApiKey = getenv('OPENAI_API_KEY'); 
            
            // 2. Ahora ya funcionará porque hemos hecho el require del autoload
            $client = OpenAI::client($yourApiKey);

            $prompt = "Actúa como traductor. Traduce estrictamente el siguiente texto al idioma " . $user_data['hizkuntza'] . ". Texto a traducir: " . $user_data['testua'];

            $response = $client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un traductor preciso.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $itzulpena = $response->choices[0]->message->content;

        } catch (Exception $e) {
            $mezua = "Errorea APIarekin: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Testuen itzultzailea</title>
    <style>
        th, td { border: 1px solid black; }
        td { padding: 10px; }
        h1, div { text-align: center; }
        .container { display: flex; justify-content: center; }
    </style>
</head>
<body>
    <div>
        <h1>itzultzapena</h1>
        <?php echo $mezua ?>
        <?php if($itzulpena): ?>
            <div style="background: #e1ffe1; padding: 10px; margin: 10px auto; width: 50%;">
                <strong>Itzulpena:</strong> <?php echo htmlspecialchars($itzulpena); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="container">
        <form action="index.php" method="POST">
            <table>
                <tr>
                    <td><strong>Testua</strong></td>
                    <td><textarea name="testua"><?php echo htmlspecialchars($user_data['testua']); ?></textarea></td>
                </tr>
                <tr>
                    <td><input type="radio" name="hizkuntza" value="Euskera"></td>
                    <td>Euskera</td>
                </tr>
                <tr>
                    <td><input type="radio" name="hizkuntza" value="Castellano"></td>
                    <td>Castellano</td>
                </tr>
                <tr>
                    <td><input type="radio" name="hizkuntza" value="Inglés"></td>
                    <td>Inglés</td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit">Itzuli</button></td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>