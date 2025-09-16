<?php
// Configuración de sucursales
$sucursales = array(
    'central' => array('name' => 'Central', 'column' => 'L', 'icon' => 'fas fa-building'),
    'dc' => array('name' => 'DC', 'column' => 'M', 'icon' => 'fas fa-store'),
    'roma' => array('name' => 'Roma', 'column' => 'N', 'icon' => 'fas fa-map-marker-alt'),
    'roveda' => array('name' => 'Roveda', 'column' => 'O', 'icon' => 'fas fa-shopping-bag'),
    'marz' => array('name' => 'Marz', 'column' => 'P', 'icon' => 'fas fa-star'),
    'darra' => array('name' => 'Darra', 'column' => 'Q', 'icon' => 'fas fa-heart'),
    'bada' => array('name' => 'Bada', 'column' => 'R', 'icon' => 'fas fa-leaf'),
    'sanl' => array('name' => 'San L', 'column' => 'S', 'icon' => 'fas fa-sun'),
    'da' => array('name' => 'DA', 'column' => 'T', 'icon' => 'fas fa-home'),
    'da1' => array('name' => 'DA1', 'column' => 'U', 'icon' => 'fas fa-plus'),
    'da2' => array('name' => 'DA2', 'column' => 'V', 'icon' => 'fas fa-plus-circle'),
    'ecomm' => array('name' => 'Ecomm', 'column' => 'W', 'icon' => 'fas fa-laptop'),
    'colon' => array('name' => 'Colon', 'column' => 'X', 'icon' => 'fas fa-crown'),
    'qm' => array('name' => 'QM', 'column' => 'Y', 'icon' => 'fas fa-diamond')
);

// Obtener ID de sucursal
$sucursalId = isset($_GET['id']) ? $_GET['id'] : '';
if (!array_key_exists($sucursalId, $sucursales)) {
    header('Location: index.php');
    exit;
}

$sucursal = $sucursales[$sucursalId];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones - <?php echo $sucursal['name']; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sucursal.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <div class="last-update" id="lastUpdate">
                    <i class="fas fa-sync-alt"></i> Actualizando...
                </div>
            </nav>
            <h1><i class="<?php echo $sucursal['icon']; ?>"></i> <?php echo $sucursal['name']; ?></h1>
            <p class="subtitle">Promociones activas disponibles</p>
        </div>
    </header>
    <main class="main">
        <div class="container">
            <div class="filters">
                <div class="filter-group">
                    <label for="diaFilter">Filtrar por día:</label>
                    <select id="diaFilter" class="filter-select">
                        <option value="">Todos los días</option>
                        <option value="lunes">Lunes</option>
                        <option value="martes">Martes</option>
                        <option value="miercoles">Miércoles</option>
                        <option value="jueves">Jueves</option>
                        <option value="viernes">Viernes</option>
                        <option value="sabados">Sábados</option>
                        <option value="domingo">Domingo</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="rubroFilter">Filtrar por rubro:</label>
                    <select id="rubroFilter" class="filter-select">
                        <option value="">Todos los rubros</option>
                        <option value="farmacia">Farmacia</option>
                        <option value="perfumeria">Perfumería</option>
                        <option value="ambos">Farmacia y Perfumería</option>
                    </select>
                </div>
            </div>
            
            <div class="promociones-container" id="promocionesContainer">
                <div class="loading"><div class="spinner"></div></div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Sistema de Promociones. Datos actualizados automáticamente cada 5 minutos.</p>
        </div>
    </footer>

    <script>
        const sucursalData = {
            id: '<?php echo $sucursalId; ?>',
            name: '<?php echo $sucursal['name']; ?>',
            column: '<?php echo $sucursal['column']; ?>',
            icon: '<?php echo $sucursal['icon']; ?>'
        };
    </script>
    <script src="js/sucursal.js"></script>
</body>
</html>