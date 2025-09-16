<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Sistema de Promociones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            color: white;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 30px;
            backdrop-filter: blur(10px);
        }
        h1 { text-align: center; margin-bottom: 30px; }
        .tool-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        .status-ok { color: #4ade80; font-weight: bold; }
        .status-error { color: #ef4444; font-weight: bold; }
        .info-box {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            padding: 15px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Panel de Administración</h1>
        
        <div class="tool-section">
            <h2>📊 Estado del Sistema</h2>
            <div class="info-box">
                <strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?><br>
                <strong>PHP:</strong> <?php echo PHP_VERSION; ?><br>
                <strong>cURL:</strong> <?php echo function_exists('curl_init') ? '<span class="status-ok">✅ Habilitado</span>' : '<span class="status-error">❌ Deshabilitado</span>'; ?><br>
                <strong>OpenSSL:</strong> <?php echo extension_loaded('openssl') ? '<span class="status-ok">✅ Habilitado</span>' : '<span class="status-error">❌ Deshabilitado</span>'; ?><br>
                <strong>Última modificación:</strong> <?php echo date('d/m/Y H:i:s', filemtime(__FILE__)); ?>
            </div>
        </div>
        
        <div class="tool-section">
            <h2>🔍 Herramientas de Diagnóstico</h2>
            <a href="test_connection.php" class="btn">🌐 Probar Conexión Google Sheets</a>
            <a href="includes/get_data.php" class="btn" target="_blank">📋 Ver Datos JSON Raw</a>
            <button onclick="testAjax()" class="btn">⚡ Probar AJAX</button>
        </div>
        
        <div class="tool-section">
            <h2>🏪 Navegación</h2>
            <a href="index.php" class="btn">🏠 Ir al Sistema Principal</a>
            <a href="sucursal.php?id=central" class="btn">🏢 Ver Sucursal Central</a>
            <a href="sucursal.php?id=dc" class="btn">🏪 Ver Sucursal DC</a>
        </div>
        
        <div class="tool-section">
            <h2>📁 Archivos del Sistema</h2>
            <div class="info-box">
                <?php
                $files = array(
					'index.php' => 'Página principal',
					'sucursal.php' => 'Página de sucursal',
					'css/style.css' => 'Estilos principales',
					'css/sucursal.css' => 'Estilos de sucursal',
					'js/script.js' => 'JavaScript principal', 
					'js/sucursal.js' => 'JavaScript de sucursal',
					'includes/get_data.php' => 'API de datos'
				);
                
                foreach ($files as $file => $desc) {
                    $exists = file_exists($file);
                    $status = $exists ? '<span class="status-ok">✅</span>' : '<span class="status-error">❌</span>';
                    $size = $exists ? ' (' . number_format(filesize($file)) . ' bytes)' : '';
                    echo "<strong>$desc:</strong> $status $file$size<br>";
                }
                ?>
            </div>
        </div>
        
        <div class="tool-section">
            <h2>⚙️ Configuración Rápida</h2>
            <div class="info-box">
                <strong>URL Google Sheets:</strong><br>
                <code style="font-size: 0.8em; word-break: break-all;">
                https://docs.google.com/spreadsheets/d/1rNa4PO0FLPsJw9YLIH8OC-Z3ZNplQqoWHujeXWaCAuQ/export?format=csv&gid=0
                </code><br><br>
                
                <strong>Actualización automática:</strong> Cada 5 minutos<br>
                <strong>Sucursales configuradas:</strong> 14 sucursales<br>
                <strong>Promociones válidas:</strong> Filas 3-17 y 20 (excluyendo 18-19)
            </div>
        </div>
        
        <div class="tool-section">
            <h2>📝 Log de Prueba</h2>
            <textarea id="logArea" readonly style="width: 100%; height: 150px; background: rgba(0,0,0,0.3); color: white; border: none; border-radius: 4px; padding: 10px; font-family: monospace;"></textarea>
        </div>
    </div>

    <script>
        function log(message) {
            const logArea = document.getElementById('logArea');
            const timestamp = new Date().toLocaleTimeString();
            logArea.value += `[${timestamp}] ${message}\n`;
            logArea.scrollTop = logArea.scrollHeight;
        }
        
        async function testAjax() {
            log('🔄 Iniciando prueba AJAX...');
            
            try {
                const response = await fetch('includes/get_data.php');
                log(`📡 Respuesta HTTP: ${response.status}`);
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.error) {
                        log(`❌ Error en datos: ${data.message}`);
                    } else {
                        log(`✅ Datos obtenidos correctamente: ${data.length} filas`);
                        
                        // Contar promociones para Central como ejemplo
                        let count = 0;
                        for (let i = 2; i <= 19; i++) {
                            if (i === 17 || i === 18) continue; // Excluir filas 18-19
                            if (data[i] && data[i][11] && data[i][11].toString().trim().toLowerCase() === 'x') {
                                count++;
                            }
                        }
                        log(`📊 Promociones para Central: ${count}`);
                    }
                } else {
                    log(`❌ Error HTTP: ${response.status}`);
                }
            } catch (error) {
                log(`💥 Error: ${error.message}`);
            }
        }
        
        // Log inicial
        log('🚀 Panel de administración cargado');
        log('💡 Usa "Probar AJAX" para verificar la conexión con Google Sheets');
    </script>
</body>
</html>