// Variables globales
let promocionesData = [];
let promocionesFiltradas = [];
let lastUpdateTime = null;

// Mapeo de columnas (A=0, B=1, etc.)
const COLUMNS = {
    DIAS: 1,        // B
    PROMOCION: 2,   // C  
    DESCUENTO: 3,   // D
    TOPE: 4,        // E
    TARJETAS: 5,    // F
    TERMINAL: 6,    // G
    TECLA: 7,       // H
    LIQUIDACION: 8, // I
    NO_APLICA: 9,   // J
    RUBRO: 10,      // K
    VIGENCIA: 11,   // L
    // Las sucursales empiezan en L=11
    L: 11, M: 12, N: 13, O: 14, P: 15, Q: 16, R: 17, 
    S: 18, T: 19, U: 20, V: 21, W: 22, X: 23, Y: 24
};

// Inicializar aplicación
document.addEventListener('DOMContentLoaded', function() {
    loadPromocionesData();
    setupFilters();
    
    // Actualizar datos cada 5 minutos
    setInterval(loadPromocionesData, 300000);
});

// Cargar datos de Google Sheets
async function loadPromocionesData() {
    try {
        showLoadingState();
        
        const response = await fetch('includes/get_data.php');
        if (response.ok) {
            promocionesData = await response.json();
            
            // Verificar si hay error en la respuesta
            if (promocionesData.error) {
                throw new Error(promocionesData.message);
            }
            
            filtrarPromociones();
            lastUpdateTime = new Date();
            updateLastUpdateTime();
            console.log('Datos actualizados:', lastUpdateTime);
        } else {
            throw new Error('Error en la respuesta del servidor');
        }
    } catch (error) {
        console.error('Error cargando datos:', error);
        showErrorState(error.message);
    }
}// Filtrar promociones para la sucursal actual
function filtrarPromociones() {
    const sucursalColumn = COLUMNS[sucursalData.column];
    promocionesFiltradas = [];
    
    // Filtrar filas 3-20, excluyendo 18 y 19 para algunas sucursales
    for (let i = 2; i <= 19; i++) { // índices 2-19 = filas 3-20
        const row = promocionesData[i];
        if (!row) continue;
        
        // Excluir filas 18 y 19 (índices 17 y 18) para todas las sucursales
        if (i === 17 || i === 18) {
            continue;
        }
        
        // Verificar si la promoción aplica para esta sucursal
        if (row[sucursalColumn] && row[sucursalColumn].toString().trim().toLowerCase() === 'x') {
            promocionesFiltradas.push({
                index: i,
                dias: row[COLUMNS.DIAS] || '',
                promocion: row[COLUMNS.PROMOCION] || '',
                descuento: row[COLUMNS.DESCUENTO] || '',
                tope: row[COLUMNS.TOPE] || '',
                tarjetas: row[COLUMNS.TARJETAS] || '',
                terminal: row[COLUMNS.TERMINAL] || '',
                tecla: row[COLUMNS.TECLA] || '',
                liquidacion: row[COLUMNS.LIQUIDACION] || '',
                noAplica: row[COLUMNS.NO_APLICA] || '',
                rubro: row[COLUMNS.RUBRO] || '',
                vigencia: row[COLUMNS.VIGENCIA] || ''
            });
        }
    }
    
    aplicarFiltros();
}

// Aplicar filtros de día y rubro
function aplicarFiltros() {
    const diaFilter = document.getElementById('diaFilter').value.toLowerCase();
    const rubroFilter = document.getElementById('rubroFilter').value.toLowerCase();
    
    let promocionesMostrar = promocionesFiltradas.filter(promo => {
        let pasaDiaFilter = true;
        let pasaRubroFilter = true;
        
        // Filtro por día
        if (diaFilter && diaFilter !== '') {
            const diasPromo = promo.dias.toLowerCase();
            pasaDiaFilter = diasPromo.includes(diaFilter);
        }
        
        // Filtro por rubro
        if (rubroFilter && rubroFilter !== '') {
            const rubroPromo = promo.rubro.toLowerCase();
            if (rubroFilter === 'farmacia') {
                pasaRubroFilter = rubroPromo.includes('farmacia');
            } else if (rubroFilter === 'perfumeria') {
                pasaRubroFilter = rubroPromo.includes('perfumeria') && !rubroPromo.includes('farmacia');
            } else if (rubroFilter === 'ambos') {
                pasaRubroFilter = rubroPromo.includes('farmacia') && rubroPromo.includes('perfumeria');
            }
        }
        
        return pasaDiaFilter && pasaRubroFilter;
    });
    
    renderPromociones(promocionesMostrar);
}// Renderizar promociones
function renderPromociones(promociones) {
    const container = document.getElementById('promocionesContainer');
    
    if (promociones.length === 0) {
        container.innerHTML = `
            <div class="no-promociones">
                <div class="no-promociones-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>No hay promociones disponibles</h3>
                <p>No se encontraron promociones que coincidan con los filtros seleccionados para esta sucursal.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = promociones.map((promo, index) => `
        <div class="promocion-card" data-index="${index}">
            <div class="promocion-header">
                <div class="promocion-badge">${getDiasIcon(promo.dias)}</div>
                <div class="promocion-vigencia">
                    <i class="fas fa-calendar-alt"></i>
                    Hasta ${promo.vigencia}
                </div>
            </div>
            
            <div class="promocion-content">
                <h3 class="promocion-titulo">${promo.promocion}</h3>
                
                <div class="promocion-details">
                    <div class="detail-item descuento-highlight">
                        <i class="fas fa-percentage"></i>
                        <strong>Descuento: ${promo.descuento}</strong>
                    </div>
                    
                    ${promo.tope ? `
                        <div class="detail-item">
                            <i class="fas fa-dollar-sign"></i>
                            <span>Tope: ${promo.tope}</span>
                        </div>
                    ` : ''}
                    
                    <div class="detail-item">
                        <i class="fas fa-credit-card"></i>
                        <span>Tarjetas: ${promo.tarjetas}</span>
                    </div>
                    
                    ${promo.terminal ? `
                        <div class="detail-item">
                            <i class="fas fa-desktop"></i>
                            <span>Terminal: ${promo.terminal}</span>
                        </div>
                    ` : ''}
                    
                    ${promo.tecla ? `
                        <div class="detail-item">
                            <i class="fas fa-keyboard"></i>
                            <span>Tecla: ${promo.tecla}</span>
                        </div>
                    ` : ''}
                    
                    <div class="detail-item">
                        <i class="fas fa-tags"></i>
                        <span>Rubro: ${promo.rubro}</span>
                    </div>
                    
                    ${promo.liquidacion ? `
                        <div class="detail-item">
                            <i class="fas fa-file-invoice"></i>
                            <span>Liquidación: ${promo.liquidacion}</span>
                        </div>
                    ` : ''}
                </div>
                
                ${promo.noAplica ? `
                    <div class="promocion-restricciones">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>${promo.noAplica}</span>
                    </div>
                ` : ''}
                
                <div class="promocion-dias">
                    <strong>Días que aplica: ${promo.dias}</strong>
                </div>
            </div>
        </div>
    `).join('');
}// Funciones auxiliares
function getDiasIcon(dias) {
    const diasLower = dias.toLowerCase();
    
    if (diasLower.includes('lunes')) return '<i class="fas fa-calendar-week"></i> L';
    if (diasLower.includes('martes')) return '<i class="fas fa-calendar-week"></i> M';
    if (diasLower.includes('miercoles')) return '<i class="fas fa-calendar-week"></i> X';
    if (diasLower.includes('jueves')) return '<i class="fas fa-calendar-week"></i> J';
    if (diasLower.includes('viernes')) return '<i class="fas fa-calendar-week"></i> V';
    if (diasLower.includes('sabado')) return '<i class="fas fa-calendar-week"></i> S';
    if (diasLower.includes('domingo')) return '<i class="fas fa-calendar-week"></i> D';
    if (diasLower.includes('todas')) return '<i class="fas fa-calendar"></i> Todos';
    
    return '<i class="fas fa-calendar-day"></i>';
}

function setupFilters() {
    const diaFilter = document.getElementById('diaFilter');
    const rubroFilter = document.getElementById('rubroFilter');
    
    diaFilter.addEventListener('change', aplicarFiltros);
    rubroFilter.addEventListener('change', aplicarFiltros);
}

function showLoadingState() {
    const container = document.getElementById('promocionesContainer');
    container.innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            <p>Cargando promociones...</p>
        </div>
    `;
}

function showErrorState(message) {
    const container = document.getElementById('promocionesContainer');
    container.innerHTML = `
        <div class="error-state">
            <div class="error-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3>Error al cargar datos</h3>
            <p>${message}</p>
            <button onclick="loadPromocionesData()" class="retry-btn">
                <i class="fas fa-refresh"></i> Reintentar
            </button>
        </div>
    `;
}

function updateLastUpdateTime() {
    const updateElement = document.getElementById('lastUpdate');
    if (updateElement && lastUpdateTime) {
        updateElement.innerHTML = `
            <i class="fas fa-sync-alt"></i> 
            Última actualización: ${lastUpdateTime.toLocaleTimeString('es-AR')}
        `;
    }
}

// Función para formatear fecha
function formatDate(date) {
    return date.toLocaleDateString('es-AR') + ' ' + date.toLocaleTimeString('es-AR');
}