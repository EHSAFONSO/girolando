<?php

// Configuração da API
$apiKey = '32b2e2d267e142c8a0c191810250602'; // Insira sua chave da Weather API
$baseUrl = 'http://api.weatherapi.com/v1/forecast.json';

// Mapeamento de condições climáticas para imagens
$weatherIcons = [
    "Clear" => "sun.png",
    "Partly cloudy" => "partly_cloudy.png",
    "Cloudy" => "cloudy.png",
    "Rain" => "rain.png",
    "Overcast" => "cloudy.png",
    "Patchy rain possible" => "rain_sun.png"
];

$daysTranslation = [
    "Mon" => "Segunda-feira",
    "Tue" => "Terça-feira",
    "Wed" => "Quarta-feira",
    "Thu" => "Quinta-feira",
    "Fri" => "Sexta-feira",
    "Sat" => "Sábado",
    "Sun" => "Domingo"
];

// Verifica se foi enviada uma cidade via GET
if (isset($_GET['city'])) {
    $city = urlencode($_GET['city']);
    $url = "$baseUrl?key=$apiKey&q=$city&days=7&aqi=no&alerts=no";
    
    // Inicializa cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Executa a requisição e captura a resposta
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Decodifica o JSON
    $weatherData = json_decode($response, true);
    $condition = $weatherData['current']['condition']['text'] ?? '';
    $iconFile = $weatherIcons[$condition] ?? "sun.png";
    $forecastDays = $weatherData['forecast']['forecastday'] ?? [];
} else {
    $weatherData = null;
    $forecastDays = [];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsão do Tempo</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div class="container">
        <div class="search-bar">
            <form method="GET" style="display: flex; width: 100%; justify-content: center;">
                <input type="text" name="city" placeholder="Digite a cidade" required>
                <button type="submit">🔍 Buscar</button>
            </form>
        </div>
        
        <?php if ($weatherData && isset($weatherData['current'])): ?>
            <div class="weather-main">
                <h1><?= htmlspecialchars($weatherData['location']['name']) ?></h1>
                <h4>Possibilidade de chuva: <?= intval($weatherData['current']['humidity']) ?>%</h4>
                <h1><?= intval($weatherData['current']['temp_c']) ?>°</h1>
                <img src="images/<?= $iconFile ?>" alt="Clima" class="weather-icon">
            </div>
            
            <div class="hourly-container">
                <h3>Previsão para hoje</h3>
                <div class="hourly">
                    <?php foreach ($forecastDays[0]['hour'] as $hour): ?>
                        <div>
                            <p><?= date('H:i', strtotime($hour['time'])) ?></p>
                            <img src="images/<?= $weatherIcons[$hour['condition']['text']] ?? 'sun.png' ?>" class="weather-icon">
                            <p><?= intval($hour['temp_c']) ?>°</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="daily-container">
                <h3>Previsão para os próximos dias</h3>
                <div class="daily">
                    <?php foreach ($forecastDays as $day): ?>
                        <div>
                            <p><?= $daysTranslation[date('D', strtotime($day['date']))] ?></p>
                            <img src="images/<?= $weatherIcons[$day['day']['condition']['text']] ?? 'sun.png' ?>" class="weather-icon">
                            <p>Mín: <?= intval($day['day']['mintemp_c']) ?>° Máx: <?= intval($day['day']['maxtemp_c']) ?>°</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
