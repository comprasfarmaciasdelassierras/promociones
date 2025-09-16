<?php
// Archivo de prueba para verificar la conexión con Google Sheets
header('Content-Type: text/html; charset=utf-8');

// URL del Google Sheet (formato CSV)
$sheetUrl = 'https://docs.google.com/spreadsheets/d/1rNa4PO0FLPsJw9YLIH8OC-Z3ZNplQqoWHujeXWaCAuQ/export?format=csv&gid=0';

// Configurar cURL para compatibilidad con versiones antiguas de PHP
function getGoogleSheetData($url) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return array(
        'data' => $data,
        'httpCode' => $httpCode,
        'error' => $error
    );
}

echo "<h1>Prueba de Conexión con Google Sheets</h1>";
echo "<hr>";

try {
    echo "<h2>🔄 Obteniendo datos...</h2>";
    
    $result = getGoogleSheetData($sheetUrl);
    
    if ($result['error']) {
        throw new Exception('Error cURL: ' . $result['error']);
    }
    
    if ($result['httpCode'] !== 200) {
        throw new Exception('HTTP Error: ' . $result['httpCode']);
    }
    
    $csvData = $result['data'];
    
    if ($csvData === false || empty($csvData)) {
        throw new Exception('No se pudieron obtener datos');
    }
    
    echo "<h2>✅ Conexión exitosa</h2>";
    echo "<p><strong>HTTP Code:</strong> " . $result['httpCode'] . "</p>";
    echo "<p><strong>Tamaño de datos:</strong> " . number_format(strlen($csvData)) . " bytes</p>";
    
    // Parsear CSV
    $lines = explode("\n", $csvData);
    $totalLines = count($lines);
    echo "<p><strong>Total de líneas:</strong> $totalLines</p>";
    
    // Mostrar las primeras 5 filas
    echo "<h2>📋 Primeras filas de datos:</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";

    for ($i = 0; $i < min(5, $totalLines); $i++) {
        $trimmed = trim($lines[$i]);
		if (!empty($trimmed)) {
			$row = str_getcsv($lines[$i]);
            echo "<tr>";
            foreach ($row as $j => $cell) {
                if ($j > 25) break; // Limitar columnas mostradas
                echo "<td style='padding: 5px; border: 1px solid #ccc;'>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";
        }
    }
    echo "</table>";
    
    // Analizar sucursales (columnas L a Y)
    echo "<h2>🏪 Análisis de Sucursales:</h2>";
    
    $sucursales = array(
        'L' => 'Central', 'M' => 'DC', 'N' => 'Roma', 'O' => 'Roveda', 
        'P' => 'Marz', 'Q' => 'Darra', 'R' => 'Bada', 'S' => 'San L',
        'T' => 'DA', 'U' => 'DA1', 'V' => 'DA2', 'W' => 'Ecomm', 
        'X' => 'Colon', 'Y' => 'QM'
    );
    
    $promociones_por_sucursal = array();
    
// Contar promociones (filas 3-20, excluyendo 18 y 19)
for ($row_index = 2; $row_index <= 19; $row_index++) {
    // Saltar filas 18 y 19 (índices 17 y 18)
    if ($row_index === 17 || $row_index === 18) continue;
    
    $trimmed = trim($lines[$row_index]);
    if (isset($lines[$row_index]) && !empty($trimmed)) {
        $row = str_getcsv($lines[$row_index]);
        
        foreach ($sucursales as $col_letter => $sucursal_name) {
            $col_index = ord($col_letter) - ord('A'); // L=11, M=12, etc.
            
            if (!isset($promociones_por_sucursal[$sucursal_name])) {
                $promociones_por_sucursal[$sucursal_name] = 0;
            }
            
            if (isset($row[$col_index]) && 
                strtolower(trim($row[$col_index])) === 'x') {
                $promociones_por_sucursal[$sucursal_name]++;
            }
        }
    }
}
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th style='padding: 10px;'>Sucursal</th><th style='padding: 10px;'>Promociones Activas</th></tr>";
    
    foreach ($promociones_por_sucursal as $sucursal => $count) {
        echo "<tr>";
        echo "<td style='padding: 8px;'><strong>$sucursal</strong></td>";
        echo "<td style='padding: 8px; text-align: center;'>$count</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error en la conexión</h2>";
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    
    echo "<h3>🔧 Información de diagnóstico:</h3>";
    echo "<ul>";
    echo "<li><strong>PHP Version:</strong> " . PHP_VERSION . "</li>";
    echo "<li><strong>cURL habilitado:</strong> " . (function_exists('curl_init') ? 'Sí' : 'No') . "</li>";
    echo "<li><strong>OpenSSL habilitado:</strong> " . (extension_loaded('openssl') ? 'Sí' : 'No') . "</li>";
    echo "<li><strong>URL de prueba:</strong> " . $sheetUrl . "</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Volver al sistema</a></p>";
?>
