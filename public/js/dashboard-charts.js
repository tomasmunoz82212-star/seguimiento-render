// =============================================
// DASHBOARD CHARTS - Inicialización de gráficos
// =============================================

(function() {
    'use strict';

    let charts = {};

    window.chartsInstances = {};

    function destroyCharts() {
        Object.values(charts).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
        charts = {};
        window.chartsInstances = {};
    }

    function initCharts() {
        destroyCharts();

        // 1. Gráfico: Reportes por tipo (doughnut)
        const ctxTipos = document.getElementById('chartTipos')?.getContext('2d');
        if (ctxTipos && window.tipoAcademico !== undefined) {
            charts.tipos = new Chart(ctxTipos, {
                type: 'doughnut',
                data: {
                    labels: ['Académico', 'Asistencia', 'Comportamiento'],
                    datasets: [{
                        data: [window.tipoAcademico, window.tipoAsistencia, window.tipoComportamiento],
                        backgroundColor: ['#1565C0', '#E65100', '#6A1B9A'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10 } }
                    }
                }
            });
            window.chartsInstances.tipos = charts.tipos;
        }

        // 2. Gráfico: Reportes por carrera (barra horizontal)
        const ctxCarreras = document.getElementById('chartCarreras')?.getContext('2d');
        if (ctxCarreras && window.porCarrera && Object.keys(window.porCarrera).length > 0) {
            const labels = Object.keys(window.porCarrera);
            const data = Object.values(window.porCarrera);
            
            charts.carreras = new Chart(ctxCarreras, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Reportes',
                        data: data,
                        backgroundColor: '#2D7D32',
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { 
                            beginAtZero: true, 
                            grid: { color: '#F0F1F4' }, 
                            ticks: { 
                                stepSize: 1,      // 👈 Números enteros
                                precision: 0      // 👈 Sin decimales
                            } 
                        },
                        y: { 
                            grid: { display: false }, 
                            ticks: { font: { size: 12 } }
                        }
                    }
                }
            });
            window.chartsInstances.carreras = charts.carreras;
        }

        // 3. Gráfico: Reportes por semestre (barras verticales)
        const ctxSemestres = document.getElementById('chartSemestres')?.getContext('2d');
        if (ctxSemestres && window.reportesPorSemestre) {
            const maxSemestre = 10;
            const labels = [];
            const data = [];
            
            for (let i = 1; i <= maxSemestre; i++) {
                labels.push(`${i}° Semestre`);
                data.push(window.reportesPorSemestre[i] || 0);
            }
            
            const colores = data.map((val, idx) => {
                const semestre = idx + 1;
                if (semestre >= 8) return '#C62828';
                if (semestre >= 5) return '#E65100';
                return '#2D7D32';
            });
            
            charts.semestres = new Chart(ctxSemestres, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Reportes',
                        data: data,
                        backgroundColor: colores,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: { callbacks: { label: (ctx) => `${ctx.raw} reporte(s)` } }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#F0F1F4' }, 
                            ticks: { 
                                stepSize: 1,      // 👈 Números enteros
                                precision: 0      // 👈 Sin decimales
                            } 
                        },
                        x: { 
                            grid: { display: false }, 
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        }

        // 4. Gráfico: Top materias (barra horizontal)
        const ctxMaterias = document.getElementById('chartMaterias')?.getContext('2d');
        if (ctxMaterias && window.reportesPorMateria && window.reportesPorMateria.length > 0) {
            const labels = window.reportesPorMateria.map(m => `${m.nombre} (${m.programa})`);
            const data = window.reportesPorMateria.map(m => m.total);
            
            charts.materias = new Chart(ctxMaterias, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Reportes',
                        data: data,
                        backgroundColor: '#1B5E20',
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: { callbacks: { label: (ctx) => `${ctx.raw} reporte(s)` } }
                    },
                    scales: {
                        x: { 
                            beginAtZero: true, 
                            grid: { color: '#F0F1F4' }, 
                            ticks: { 
                                stepSize: 1,      // 👈 Números enteros
                                precision: 0      // 👈 Sin decimales
                            } 
                        },
                        y: { 
                            grid: { display: false }, 
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });
            window.chartsInstances.materias = charts.materias;
        }

        console.log('📊 Gráficos inicializados');
    }

    window.refreshDashboardCharts = function() {
        console.log('🔄 Refrescando gráficos del dashboard...');
        
        window.tipoAcademico = parseInt(document.getElementById('data-tipo-academico')?.value) || 0;
        window.tipoAsistencia = parseInt(document.getElementById('data-tipo-asistencia')?.value) || 0;
        window.tipoComportamiento = parseInt(document.getElementById('data-tipo-comportamiento')?.value) || 0;
        
        window.porCarrera = {};
        document.querySelectorAll('[data-carrera-nombre]').forEach(el => {
            const nombre = el.dataset.carreraNombre;
            const total = parseInt(el.dataset.carreraTotal) || 0;
            if (nombre) window.porCarrera[nombre] = total;
        });
        
        window.reportesPorSemestre = {};
        document.querySelectorAll('[data-semestre]').forEach(el => {
            const semestre = parseInt(el.dataset.semestre);
            const total = parseInt(el.dataset.semestreTotal) || 0;
            if (semestre) window.reportesPorSemestre[semestre] = total;
        });
        
        window.reportesPorMateria = [];
        document.querySelectorAll('[data-materia-nombre]').forEach(el => {
            window.reportesPorMateria.push({
                nombre: el.dataset.materiaNombre,
                programa: el.dataset.materiaPrograma,
                total: parseInt(el.dataset.materiaTotal) || 0
            });
        });
        
        initCharts();
    };

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initCharts, 100);
    });

    window.addEventListener('content-updated', function() {
        console.log('📡 Evento content-updated recibido');
        window.refreshDashboardCharts();
    });
})();