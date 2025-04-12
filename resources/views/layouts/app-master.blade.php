<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'App')</title>
    <!--Libreria para el Js de Excel-->
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <!-- Cargar FullCalendar 5.x con los plugins necesarios -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.11.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@5.11.0/main.min.js"></script>
    <!--JQuery-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Asegúrate de incluir los archivos necesarios de Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Estilo de DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <!-- JS de DataTables -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <link rel="icon" type="image/png" href="{!! asset('/icons/tms_logo.jpg')!!}">
    @vite([
        'resources/sass/app.scss',
        'resources/js/app.js'
        ])

    <style>
      .dataTables_filter {
          display: none;
      }

      .gradient-text {
        background: linear-gradient(135deg, #1e4877, rgb(13, 82, 200));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
      }
      .navbar{
        background: linear-gradient(135deg, #1e4877,rgb(13, 82, 200));
        height: 80px;
        margin: 20px;
        border-radius: 16px;
        padding: 0.5rem;
      }
      .fixed-padding {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%; /* Para que ocupe todo el ancho */
        z-index: 999; /* Asegura que esté sobre otros elementos */
        background-color: white; /* Fondo blanco para no mostrar contenido detrás */
      }
      .navbar-brand{
        font-weight: 500;
        /*color: #1e4877;*/
        color: white;
        font-size: 24px;
        transition: 0.3s color;
      }
      .navbar-brand:hover, .navbar-brand:active{
        color: white;
      }
      .login-button{
        background-color: white;
        color: #1e4877;
        font-size: 14px;
        padding: 8px 20px;
        border-radius: 50px;
        text-decoration: none;
        transition: 0.3s background-color;
      }
      .login-button:hover{
        background-color: #c3c3c3;
      }
      .navbar-toggler{
        border: none;
        font-size: 1.25rem;
      }
      .navbar-toggler:focus, .btn-close:focus{
        box-shadow: none;
        outline: none;
      }
      .nav-link{
        color: white;
        /*color: #1e4877; */
        font-weight: 500;
        position: relative;
      }
      .dropdown-item{
        color: white;
        position: relative;
      }
      .nav-link:hover, .nav-link:active, .dropdown-item:hover, .dropdown-item:active{
        /*color: #000;*/
        color: white;
      }

      @media (max-width: 990px){
        .offcanvas-bodyy{
          background-color: #1e4877;
        }
      }
      @media (min-width: 576px) {
        .modal-dialogg {
          max-width: 300px;
          margin-right: 1.2rem;
          margin-left: auto;
          margin-top: 1.2rem;
        }
      }

      @media (max-width: 576px) {
        .modal-dialogg {
          max-width: 215px;
          margin-right: 1.2rem;
          margin-left: auto;
          margin-top: 1.2rem;
        }
      }

      @media (min-width: 630px){
        .offcanvas.offcanvas-end{
          width: 50%;
        }
      }
      @media (max-width: 630px){
        .offcanvas.offcanvas-end{
          width: 85%;
        }
      }
      
      @media (min-width: 991px) {
        .nav-link::before, .dropdown-item::before{
          content: "";
          position: absolute;
          bottom: 0;
          left: 50%;
          transform: translateX(-50%);
          width: 0;
          height: 0.5px;
          background-color:white;
          /*background-color: #1e4877; */
          visibility: hidden;
          transition: 0.3s ease-in-out;
        }
        li{
          text-align: center; /* Centra el texto horizontalmente */
          /*width: 100%; /* Asegura que ocupen todo el ancho disponible */
          display: flex; /* Utiliza flexbox para centrado */
          justify-content: center; /* Centra horizontalmente el contenido */
          align-items: center; /* Centra verticalmente el contenido */
        }
        .offcanvas-body{
          background-color: transparent;
        }
        .nav-link:hover::before, .nav-link.active::before{
          width: 100%;
          visibility: visible;
        }
        .dropdown-item:hover::before, .dropdown-item.active::before{
          width: 80%;
          visibility: visible;
        }
      }
      .hero-section{
        background: white;
        background-size: cover;
        width: 100%;
      }
      .hero-section .container{
        height: 100vh;
        z-index: 1;
        position: relative;

      }
      .hero-section h1{
        font-size: 1.5em;
      }
      .non{
        cursor: default;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select:none;
        user-select:none;
      }
      .dropdown-item:hover{
        background-color: unset;
      }
      .is-valid {
      border-color: #198754;
      }
      .select2.is-invalid .select2-selection {
        border-color: red !important;
        box-shadow: 0 0 5px rgba(255, 0, 0, 0.5) !important;  /* Sombra roja */
      }
      .select2-selection.is-invalid {
          border-color: red !important;
          box-shadow: 0 0 5px rgba(255, 0, 0, 0.5) !important;  /* Sombra roja */
      }
      .is-invalid {
        border-color: red !important;
        box-shadow: 0 0 5px rgba(255, 0, 0, 0.5) !important;  /* Sombra roja */
      }
      .invalid-feedback {
          color: #dc3545;
          display: block;
      }
      .error-message {
          font-size: 0.875em;
          color: #dc3545;
      }
      /*.select2-search__field:focus {
          outline: none !important;
          box-shadow: none !important;
          border-color: transparent !important;
      }*/


      /* Eliminar bordes de la tabla y las celdas */
  .table_style {
    width: 100%;
    overflow-y: auto; /* Habilita el scroll vertical */
    overflow-x: auto; /* Habilita el desplazamiento horizontal */
    max-height: 520px; /* Define un alto máximo para habilitar el scroll vertical */
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  td {
    padding: 15px !important;
    text-align: left;
    /*border: none;*/
  }

  th {
    padding: 10px;
    text-align: left;
    font-size: 15px;
    background-color: #f8f9fa;
    font-weight: bold;
    white-space: nowrap; /* Evita que el texto se envuelva */
    border-bottom: 2px solid #000;

    /* Fija el encabezado al desplazarse verticalmente */
    position: sticky;
    top: 0; /* Fija el encabezado en la parte superior */
    z-index: 1; /* Asegura que el encabezado esté sobre las celdas */
  }

  td {
    font-size: 14px;
    white-space: nowrap; /* Evita que el texto se envuelva */
  }
  /* Estilo para pantallas menores o iguales a 768px */
  @media screen and (max-width: 768px) {
    th {
      font-size: 14px; /* Encabezados más pequeños */
    }
    
    td {
      font-size: 13px; /* Celdas más pequeñas */
    }
  }
  /* Cambiar fondo y texto */
  
.tooltip-inner {
    background-color: rgb(13, 82, 200);;  /* Fondo azul */
    color: #fff;  /* Texto blanco */
    font-size: 14px;  /* Tamaño de fuente */
    border-radius: 5px;  /* Bordes redondeados */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}
/* Cambiar el color de la flecha */
.tooltip-arrow::before {
    border-top-color:rgb(13, 82, 200) !important;  /* Cambiar color de la flecha */
    border-right-color: transparent;
    border-bottom-color: transparent;
    border-left-color: transparent;
}
/* Animación de aparición */
.tooltip.bs-tooltip-top .tooltip-inner {
    transition: opacity 0.4s ease;
}
.centered-form {
    max-width: 1050px; /* Limita el ancho máximo a 600px */
    margin: 0 auto; /* Centra el formulario horizontalmente */
    padding: 20px; /* Añade un poco de espacio interno */
}


/*Estilos de Christian*/
.offcanvasUpdateStatus {
  background-color: #212529;
  color: white;
  width: 300px;
}
.offcanvasUpdateStatus .form-label {
  color: #f8f9fa;
}
.offcanvasDetails {
  background-color: #212529;
  color: white;
  width: 300px;
}
.ooffcanvasDetails .form-label {
  color: #f8f9fa;
}

/* Asegúrate de que el select2 y el input tengan el mismo estilo */
.select2-container--default .select2-selection--single {
    height: calc(2.25rem + 2px);  /* Igual que los inputs */
    border-radius: 0.375rem; /* Asegura bordes redondeados */
    font-size: 1rem; /* Igual al tamaño de los inputs */
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 2.25rem; /* Asegura que el texto esté centrado verticalmente */
}
/* Asegura que el borde del select2 coincida con el borde del input */
.select2-container--default .select2-selection--single {
    border: 1px solid #dee2e6;  /* Color del borde, igual al del input .form-control */
    border-radius: 0.375rem;    /* Bordes redondeados, como el input */
}

/* Cuando el select2 está en foco, cambia el borde a azul (como el input) */
.select2-container--default .select2-selection--single:focus-within {
    border-color: #80bdff;      /* Color del borde azul al estar enfocado */
    box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25); /* Sombra azul para el enfoque */
}
/* Centrar la flecha dentro del select2 */
.select2-container--default .select2-selection--single .select2-selection__arrow {
    top: 50%;                /* Centra la flecha verticalmente */
    right: 10px;             /* Ajusta la posición horizontal */
    transform: translateY(-50%); /* Centra la flecha correctamente */
}
/* Eliminar el borde negro del campo de búsqueda cuando está enfocado */
.select2-container--default .select2-search__field:focus {
    border-color: transparent !important;   /* Eliminar el borde */
    box-shadow: none !important;        /* Eliminar cualquier sombra aplicada */
    border: 
}
.select2-container--default .select2-search--dropdown .select2-search__field {
    /*border: 1px !important; */
    /*border-color: #dee2e6 !important;*/
}
.select2-container--default .select2-selection--single .select2-selection__clear {
    right: 15px; /* Mueve la "X" más a la izquierda */
}

.select2-container {
    width: 100% !important; 
}
.select2newcfs + .select2-container .select2-selection {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}
.list-group-item{
  border-left: unset;
  border-right: unset;
}

.list-group-item:first-child {
    border-top-left-radius: unset;
    border-top-right-radius: unset;
    border-top:unset;
}
.list-group-item:last-child {
    border-bottom-left-radius: unset;
    border-bottom-right-radius: unset;
    border-bottom:unset;
}
.acordionsmaster.accordion-button:not(.collapsed) {
    color: inherit !important; /* Heredar color de texto */
    background-color: transparent !important; /* Fondo transparente */
    box-shadow: none !important; /* Eliminar sombra */
    --bs-accordion-active-bg: transparent !important; /* Sobrescribir la variable de fondo */
}


    </style>
</head>
<body>

      @include('layouts.partials.navbar')
      <div style="margin-top: 120px;">
        @yield('content')
      </div>
      
      <!-- Aquí se cargarán los scripts específicos de cada página -->
      @yield('scripts')

</body>
</html>

