// Configuración de sucursales
const sucursales = [
    { id: 'central', name: 'Central', icon: 'fas fa-building', column: 'L' },
    { id: 'dc', name: 'DC', icon: 'fas fa-store', column: 'M' },
    { id: 'roma', name: 'Roma', icon: 'fas fa-map-marker-alt', column: 'N' },
    { id: 'roveda', name: 'Roveda', icon: 'fas fa-shopping-bag', column: 'O' },
    { id: 'marz', name: 'Marz', icon: 'fas fa-star', column: 'P' },
    { id: 'darra', name: 'Darra', icon: 'fas fa-heart', column: 'Q' },
    { id: 'bada', name: 'Bada', icon: 'fas fa-leaf', column: 'R' },
    { id: 'sanl', name: 'San L', icon: 'fas fa-sun', column: 'S' },
    { id: 'da', name: 'DA', icon: 'fas fa-home', column: 'T' },
    { id: 'da1', name: 'DA1', icon: 'fas fa-plus', column: 'U' },
    { id: 'da2', name: 'DA2', icon: 'fas fa-plus-circle', column: 'V' },
    { id: 'ecomm', name: 'Ecomm', icon: 'fas fa-laptop', column: 'W' },
    { id: 'colon', name: 'Colon', icon: 'fas fa-crown', column: 'X' },
    { id: 'qm', name: 'QM', icon: 'fas fa-diamond', column: 'Y' }
];

// Variables globales
let promocionesData = [];
let lastUpdateTime = null;

// Inicializar aplicación
document.addEventListener('DOMContentLoaded', function() {
    renderSucursales();
    loadPromocionesData();
    
    // Actualizar datos cada 5 minutos
    setInterval(loadPromocionesData, 300000);
});

// Renderizar grid de sucursales
function renderSucursales() {
    const grid = document.getElementById('sucursalesGrid');
    grid.innerHTML = '<div class="loading"><div class="spinner"></div></div>';
    
    setTimeout(() => {
        grid.innerHTML = sucursales.map(sucursal => `
            <div class="sucursal-card" onclick="openSucursal('${sucursal.id}')">
                <div class="sucursal-status"></div>
                <div class="sucursal-icon">
                    <i class="${sucursal.icon}"></i>
                </div>
                <h3 class="sucursal-name">${sucursal.name}</h3>
                <p class="sucursal-count" id="count-${sucursal.id}">Cargando...</p>
            </div>
        `).join('');
        
        updatePromoCounts();
    }, 1000);
}

// Cargar datos de Google Sheets
async function loadPromocionesData() {
    try {
        const response = await fetch('includes/get_data.php');
        if (response.ok) {
            promocionesData = await response.json();
            updatePromoCounts();
            lastUpdateTime = new Date();
            console.log('Datos actualizados:', lastUpdateTime);
        }
    } catch (error) {
        console.error('Error cargando datos:', error);
    }
}
// Actualizar contadores de promociones por sucursal
function updatePromoCounts() {
    sucursales.forEach(sucursal => {
        const count = countPromocionesParaSucursal(sucursal.id);
        const countElement = document.getElementById(`count-${sucursal.id}`);
        if (countElement) {
            countElement.textContent = `${count} promociones activas`;
        }
    });
}

// Contar promociones activas para una sucursal
function countPromocionesParaSucursal(sucursalId) {
    if (!promocionesData || !promocionesData.length) return 0;
    
    const sucursal = sucursales.find(s => s.id === sucursalId);
    if (!sucursal) return 0;
    
    let count = 0;
    promocionesData.forEach((row, index) => {
        // Saltar filas 18 y 19 para Central (índices 17 y 18)
        if (sucursalId === 'central' && (index === 17 || index === 18)) return;
        
        // Solo contar filas 3-20 (índices 2-19)
        if (index >= 2 && index <= 19) {
            const columnIndex = getColumnIndex(sucursal.column);
            if (row[columnIndex] && row[columnIndex].trim() === 'x') {
                count++;
            }
        }
    });
    
    return count;
}

// Obtener índice de columna desde letra
function getColumnIndex(letter) {
    return letter.charCodeAt(0) - 65; // A=0, B=1, C=2, etc.
}

// Abrir página de sucursal
function openSucursal(sucursalId) {
    window.location.href = `sucursal.php?id=${sucursalId}`;
}

// Función auxiliar para formatear fecha
function formatDate(date) {
    return date.toLocaleDateString('es-AR') + ' ' + date.toLocaleTimeString('es-AR');
}

// Función para mostrar estado de conexión
function showConnectionStatus(online = true) {
    const statusElements = document.querySelectorAll('.sucursal-status');
    statusElements.forEach(el => {
        el.style.background = online ? '#4ade80' : '#f87171';
    });
}