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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(255, 255, 255);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 150vh;
            margin: 0;
            text-align: center;
        }
        .container {
            width: 100%;
            max-width: 900px;
            background:rgb(0, 18, 43);
            padding: 20px;
            box-shadow: 0 4px 10px #0948A2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 40px;
        }
        .weather-main, .forecast-container, .hourly-container, .daily-container {
            background:rgb(0, 18, 43);
            padding: 20px;
            border-radius: 20px;
            margin: 0;
            width: 100%;
        }
        .forecast, .hourly, .daily {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            gap: 10px;
            padding-bottom: 10px;
        }
        .forecast div, .hourly div, .daily div {
            text-align: center;
            flex: 0 0 auto;
            min-width: 120px;
            background: #e3e3e3;
            padding: 10px;
            border-radius: 20px;
            box-shadow: 2px 2px 5px #0948A2;
        }
        .weather-icon {
            width: 50px;
            height: auto;
        }
        h1, h2, h3, h4{
            color: #FFFFFF;
        }
        ::-webkit-scrollbar {
            width: 5px; /* Largura da scrollbar */
            height: 20px;
        }

        ::-webkit-scrollbar-track {
            background: #1e1e2f; /* Cor do fundo */
            border-radius: 30px;
        }

        ::-webkit-scrollbar-thumb {
            background: #4a90e2; /* Cor da alça */
            border-radius: 30px;
            transition: background 0.3s;
        }

        ::-webkit-scrollbar-thumb:hover {
            background:rgb(0, 48, 114); /* Cor ao passar o mouse */
        }
        input {
        background-color: #e3edf7;
        padding: 16px 32px;
        border: none;
        display: block;
        font-family: 'Orbitron', sans-serif;
        font-weight: 600;
        color: #a9b8c9;
        -webkit-appearance: none;
        transition: all 240ms ease-out;
        width: 100%;
        
        &::placeholder {
            color: #6d7f8f;
        }
        
        &:focus {
            outline: none;
            color: #6d7f8f;
            background-color: lighten(#e3edf7, 3%);
        }
        };

        :root {
        --border-radius: 10px;
}
        
        .InputContainer {
        --top-shadow: inset 1px 1px 3px #c5d4e3, inset 2px 2px 6px #c5d4e3;
        --bottom-shadow: inset -2px -2px 4px rgba(255,255,255, .7);
        border-radius: 30px;
        
        position: relative;
        border-radius: var(--border-radius);
        overflow: hidden;
        
        &:before,
        &:after {
            left: 0;
            top: 0;
            display: block;
            content: "";
            pointer-events: none;
            width: 100%;
            height: 100%;
            position: absolute;
        }
        
        &:before {
            box-shadow: var(--bottom-shadow);
        }
        
        &:after {
            box-shadow: var(--top-shadow);
        }
        }
    </style>
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
