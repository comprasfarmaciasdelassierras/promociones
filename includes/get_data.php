<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

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
    
    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return false;
    }
    
    return $data;
}

try {
    // Obtener datos del sheet
    $csvData = getGoogleSheetData($sheetUrl);
    
    if ($csvData === false) {
        throw new Exception('Error al obtener datos del Google Sheet');
    }
    
    // Parsear CSV
    $lines = explode("\n", $csvData);
    $result = array();
    
    foreach ($lines as $line) {
        if (!empty(trim($line))) {
            // Usar str_getcsv para parsear correctamente el CSV
            $row = str_getcsv($line);
            $result[] = $row;
        }
    }
    
    // Validar que tenemos datos
    if (empty($result)) {
        throw new Exception('No se pudieron obtener datos válidos');
    }
    
    // Devolver datos como JSON
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // En caso de error, devolver array vacío
    http_response_code(500);
    echo json_encode(array(
        'error' => true,
        'message' => $e->getMessage(),
        'data' => array()
    ), JSON_UNESCAPED_UNICODE);
}
?>
