<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modacol - @yield('title', 'Sistema de Gestión')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/Cruds.css') }}">
    
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--secondary-color) 0%, #1a2530 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            font-weight: 600;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        
        .brand-section {
            background-color: rgba(0,0,0,0.2);
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .brand-section h5 {
            color: #fff;
            font-weight: 600;
            margin: 0;
        }
        
        .main-content {
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .header-bar {
            background: linear-gradient(90deg, var(--primary-color) 0%, #2980b9 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .table-actions {
            white-space: nowrap;
        }
        
        .btn-logout {
            background-color: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
        }
        
        .btn-logout:hover {
            background-color: rgba(255,255,255,0.3);
            color: white;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: linear-gradient(90deg, var(--primary-color) 0%, #2980b9 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="brand-section">
                    <h5><i class="fas fa-tshirt me-2"></i>Modacol</h5>
                </div>
                <nav class="nav flex-column p-3">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" 
                       href="{{ route('usuarios.index') }}">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                    <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" 
                       href="{{ route('clientes.index') }}">
                        <i class="fas fa-address-book"></i> Clientes
                    </a>
                    <a class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" 
                       href="{{ route('productos.index') }}">
                        <i class="fas fa-box"></i> Productos
                    </a>
                    <a class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}" 
                       href="{{ route('ventas.index') }}">
                        <i class="fas fa-cash-register"></i> Ventas
                    </a>
                    <a class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}" 
                       href="{{ route('proveedores.index') }}">
                        <i class="fas fa-truck-loading"></i> Proveedores
                    </a>
                    <a class="nav-link {{ request()->routeIs('compras.*') ? 'active' : '' }}" 
                       href="{{ route('compras.index') }}">
                        <i class="fas fa-shopping-cart"></i> Compras
                    </a>
                    <a class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}" 
                       href="{{ route('categorias.index') }}">
                        <i class="fas fa-tags"></i> Categorías
                    </a>
                    <a class="nav-link {{ request()->routeIs('flujoCaja.*') ? 'active' : '' }}" 
                       href="{{ route('flujoCaja.index') }}">
                        <i class="fas fa-chart-line"></i> Flujo de Caja
                    </a>
                     <!-- @if(auth()->user()->role_id == 1)
                        <a class="nav-link" href="{{ route('usuarios.index') }}">
                            <i class="fas fa-users me-2"></i> Usuarios
                        </a>
                        <a class="nav-link" href="{{ route('proveedores.index') }}">
                            <i class="fas fa-shopping-cart me-2"></i> Proveedores
                        </a>
                        <a class="nav-link" href="{{ route('compras.index') }}">
                            <i class="fas fa-shopping-cart me-2"></i> Compras
                        </a>
                        <a class="nav-link" href="{{ route('categorias.index') }}">
                            <i class="fas fa-cogs me-2"></i> Categorías
                        </a>
                    @endif

                    @if(auth()->user()->role_id == 2)
                        <a class="nav-link" href="{{ route('clientes.index') }}">
                            <i class="fas fa-address-book me-2"></i> Clientes
                        </a>
                        <a class="nav-link" href="{{ route('productos.index') }}">
                            <i class="fas fa-box me-2"></i> Productos
                        </a>
                        <a class="nav-link" href="{{ route('ventas.index') }}">
                            <i class="fas fa-shopping-cart me-2"></i> Ventas
                        </a>
                    @endif

                    @if(auth()->user()->role_id == 3)
                <a class="nav-link" href="{{ route('ventas.index') }}">
                    <i class="fas fa-shopping-cart me-2"></i> Ventas
                </a>
                <a class="nav-link" href="{{ route('compras.index') }}">
                    <i class="fas fa-shopping-cart me-2"></i> Compras
                </a>
                <a class="nav-link" href="{{ route('categorias.index') }}">
                    <i class="fas fa-cogs me-2"></i> Categorías
                </a>
                <a class="nav-link" href="{{ route('flujoCaja.index') }}">
                    <i class="fas fa-cogs me-2"></i> Flujo de Caja
                </a>
            @endif -->


                </nav>
            </div>
            
            <!-- Main content -->
            <div class="col-md-10 main-content">
                <!-- Header -->
                <div class="header-bar">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h4 mb-0"><i class="fas fa-@yield('icon', 'home') me-2"></i>@yield('title', 'Dashboard')</h1>
                        <div class="d-flex align-items-center">
                            <span class="me-3">
                                <i class="fas fa-user-circle me-1"></i>
                                ¡Hola, {{ auth()->user()->Nombre ?? 'Usuario' }}
                            </span>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-logout btn-sm">
                                    <i class="fas fa-sign-out-alt me-1"></i> Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="card">
                    <div class="card-body">
                        <!-- Mensajes de sesión -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts adicionales -->
    @yield('scripts')
</body>
</html>