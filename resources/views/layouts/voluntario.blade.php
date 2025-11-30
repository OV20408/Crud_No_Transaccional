<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Evaluación de Voluntario') - GEVOPI</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <style>
        .main-sidebar {
            background-color: #343a40 !important;
        }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            background-color: #007bff;
        }
        .brand-link {
            border-bottom: 1px solid #4b545c;
        }
        .content-wrapper {
            background-color: #f4f6f9;
        }
    </style>
    
    @yield('css')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link">
                    <i class="fas fa-user"></i> {{ $voluntario->nombres ?? 'Voluntario' }}
                </span>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="#" class="brand-link">
            <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="GEVOPI Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">GEVOPI</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    
                    <!-- Evaluación Voluntarios -->
                    <li class="nav-item">
                        <a href="{{ url('/evaluacion-voluntario/' . ($token ?? '')) }}" class="nav-link active">
                            <i class="nav-icon fas fa-clipboard-check"></i>
                            <p>Evaluación Voluntarios</p>
                        </a>
                    </li>
                    
                    <!-- Cerrar Sesión -->
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="cerrarSesionVoluntario(event)">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Cerrar Sesión</p>
                        </a>
                    </li>
                    
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('header', 'Evaluación de Voluntario')</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>&copy; {{ date('Y') }} <a href="#">GEVOPI</a>.</strong>
        Sistema de Gestión de Voluntarios
    </footer>

</div>

<!-- Modal de Agradecimiento -->
<div class="modal fade" id="modalAgradecimiento" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0 text-white" style="background-color: #353b41;">
                <h5 class="modal-title w-100">Gracias por tu tiempo</h5>
            </div>
            <div class="modal-body py-4">
                <i class="fas fa-check-circle" style="font-size: 70px; color: #353b41;"></i>
                <h4 class="mb-3 mt-3">Gracias por completar tu evaluación</h4>
                <p class="text-muted">Tu participación es muy importante para nosotros.<br>Serás redirigido en unos segundos...</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <a href="https://www.google.com" class="btn btn-lg text-white" style="background-color: #353b41;">
                    <i class="fas fa-check"></i> Continuar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
function cerrarSesionVoluntario(e) {
    e.preventDefault();
    $('#modalAgradecimiento').modal('show');
    
    // Redirigir después de 3 segundos
    setTimeout(function() {
        window.location.href = 'https://www.google.com';
    }, 3000);
}
</script>

@yield('js')

</body>
</html>
