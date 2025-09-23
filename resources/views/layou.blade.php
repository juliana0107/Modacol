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
    
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: #fff;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        .main-content {
            padding: 20px;
        }
        .table-actions {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4 text-center text-white">
                    <h5>Modacol</h5>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                  <!--   <a class="nav-link active" href="{{ route('usuarios.index') }}">
                        <i class="fas fa-users me-2"></i> Usuarios
                    </a>
                    <a class="nav-link" href="{{ route('clientes.index') }}">
                        <i class="fas fa-address-book me-2"></i> Clientes
                    </a>
                    <a class="nav-link" href="{{ route('productos.index') }}">
                        <i class="fas fa-box me-2"></i> Productos
                    </a>
                    <a class="nav-link" href="{{ route('ventas.index') }}">
                        <i class="fas fa-shopping-cart me-2"></i> Ventas
                    </a>
                    <a class="nav-link" href="{{ route('proveedores.index') }}">
                        <i class="fas fa-shopping-cart me-2"></i> Proveedores
                    </a>
                    <a class="nav-link" href="{{ route('compras.index') }}">
                        <i class="fas fa-shopping-cart me-2"></i> Compras
                    </a> -->
                </nav>
            </div>
            
            <!-- Main content -->
            <div class="col-md-10 main-content">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>@yield('title')</h1>
                    <div>
                        <span class="me-3">Hola, {{ auth()->user()->Nombre }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-sign-out-alt me-1"></i> Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="card shadow">
                    <div class="card-body">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>