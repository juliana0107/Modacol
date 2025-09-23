@extends('layou')

@section('content')

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modacol - Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #5E5DF0;
            --secondary-color: #8045DD;
            --accent-color: #FF6B6B;
            --background-color: #f8f9fc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 4px 15px rgba(94, 93, 240, 0.3);
            padding: 0.8rem 1.5rem;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }

        .user-info {
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-logout {
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: var(--transition);
        }

        .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Header */
        .dashboard-header {
            padding: 2rem 0 1rem;
            margin-bottom: 1.5rem;
        }

        .dashboard-header h1 {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .dashboard-header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Cards de Resumen */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background-color: var(--card-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-users::before {
            background-color: var(--primary-color);
        }

        .card-sales::before {
            background-color: var(--success-color);
        }

        .card-growth::before {
            background-color: var(--warning-color);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .card-users .card-icon {
            background-color: rgba(94, 93, 240, 0.1);
            color: var(--primary-color);
        }

        .card-sales .card-icon {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .card-growth .card-icon {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .card-title {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .card-trend {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .trend-up {
            color: var(--success-color);
        }

        .trend-down {
            color: var(--danger-color);
        }

        /* Gráficos */
        .charts-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background-color: var(--card-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .chart-card.wide {
            grid-column: 1 / -1;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .chart-actions {
            display: flex;
            gap: 0.5rem;
        }

        .chart-actions button {
            background-color: transparent;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.35rem 0.75rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .chart-actions button:hover {
            background-color: #f1f5f9;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .wide .chart-container {
            height: 350px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .summary-cards {
                grid-template-columns: 1fr;
            }
            
            .dashboard-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="dashboard-header">
            <h1>Panel de Control</h1>
            <p>Vista general de estadísticas y métricas de tu negocio</p>
        </div>

        <!-- Cards de Resumen -->
        <div class="summary-cards">
            <div class="summary-card card-users">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="card-title">Usuarios Totales</h3>
                <div class="card-value">1,234</div>
                <div class="card-trend trend-up">
                    <i class="fas fa-arrow-up"></i>
                    <span>+5.2% vs mes anterior</span>
                </div>
            </div>

            <div class="summary-card card-sales">
                <div class="card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="card-title">Ventas Totales</h3>
                <div class="card-value">$45,678</div>
                <div class="card-trend trend-up">
                    <i class="fas fa-arrow-up"></i>
                    <span>+12.3% vs mes anterior</span>
                </div>
            </div>

            <div class="summary-card card-growth">
                <div class="card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="card-title">Crecimiento</h3>
                <div class="card-value">23.4%</div>
                <div class="card-trend trend-down">
                    <i class="fas fa-arrow-down"></i>
                    <span>-2.1% vs mes anterior</span>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="charts-container">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Ventas Mensuales</h3>
                    <div class="chart-actions">
                        <button>2024</button>
                        <button><i class="fas fa-download"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Distribución de Usuarios</h3>
                    <div class="chart-actions">
                        <button><i class="fas fa-download"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="usersChart"></canvas>
                </div>
            </div>

            <div class="chart-card wide">
                <div class="chart-header">
                    <h3 class="chart-title">Tendencias Anuales</h3>
                    <div class="chart-actions">
                        <button>2023-2024</button>
                        <button><i class="fas fa-download"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Datos simulados para los gráficos
        const mockData = {
            sales: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                data: [12500, 15000, 17500, 16000, 19000, 22000, 24500, 21000, 23000, 25000, 27000, 30000]
            },
            users: {
                labels: ['Nuevos', 'Recurrentes', 'Inactivos', 'Premium'],
                data: [30, 45, 15, 10],
                colors: ['#5E5DF0', '#8045DD', '#FF6B6B', '#10b981']
            },
            trends: {
                labels: ['Q1', 'Q2', 'Q3', 'Q4'],
                datasets: [
                    {
                        label: '2023',
                        data: [65, 75, 70, 80],
                        color: '#5E5DF0'
                    },
                    {
                        label: '2024',
                        data: [70, 85, 80, 90],
                        color: '#8045DD'
                    }
                ]
            }
        };

        // Inicializar gráficos cuando el DOM esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Ventas (Línea)
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: mockData.sales.labels,
                    datasets: [{
                        label: 'Ventas Mensuales',
                        data: mockData.sales.data,
                        borderColor: '#5E5DF0',
                        backgroundColor: 'rgba(94, 93, 240, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#5E5DF0',
                        pointBorderColor: '#fff',
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Gráfico de Usuarios (Doughnut)
            const usersCtx = document.getElementById('usersChart').getContext('2d');
            new Chart(usersCtx, {
                type: 'doughnut',
                data: {
                    labels: mockData.users.labels,
                    datasets: [{
                        data: mockData.users.data,
                        backgroundColor: mockData.users.colors,
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    cutout: '70%'
                }
            });

            // Gráfico de Tendencias (Barras)
            const trendsCtx = document.getElementById('trendsChart').getContext('2d');
            new Chart(trendsCtx, {
                type: 'bar',
                data: {
                    labels: mockData.trends.labels,
                    datasets: mockData.trends.datasets.map((dataset, index) => ({
                        label: dataset.label,
                        data: dataset.data,
                        backgroundColor: dataset.color,
                        borderRadius: 6,
                        barPercentage: 0.7
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
@endsection