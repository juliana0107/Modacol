<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Modacol</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #f8f9fa;
      --white: #fff;
      --primary: #1d3557;
      --accent: #e63946;
      --text: #333;
      --shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }


    a {
      text-decoration: none;
      border: 1px solid transparent;
      color: black;
      padding: 10px 18px;
      border-radius: 5px;
      font-size: 0.95rem;
      background: white;
      transition: background 0.3s ease, color 0.3s ease;
    }


    a:hover {
      background: black;
      color: white;
    }


    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
    }


    header {
      background: var(--primary);
      color: white;
      padding: 30px 20px;
      text-align: center;
      position: relative;
    }


    .nav-container {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      padding: 10px 20px;
      background-color: var(--primary);
    }


    .nav-container a {
      background: transparent;
      color: white;
      border: 1px solid white;
    }


    .nav-container a:hover {
      background: white;
      color: var(--primary);
    }


    header h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
    }


    header p {
      font-size: 1.1rem;
      opacity: 0.9;
    }


    main {
      max-width: 900px;
      margin: 50px auto;
      padding: 0 20px;
    }


    .section {
      background: var(--white);
      padding: 30px;
      border-radius: 10px;
      box-shadow: var(--shadow);
      margin-bottom: 30px;
    }


    .section h2 {
      color: var(--primary);
      margin-bottom: 15px;
      font-size: 1.4rem;
    }


    .section p {
      line-height: 1.7;
      font-size: 1rem;
      margin-bottom: 15px;
    }


    .quick-links ul {
      list-style: none;
      padding: 0;
    }


    .quick-links ul li {
      margin: 10px 0;
      padding-left: 20px;
      position: relative;
    }


    .quick-links ul li::before {
      content: "✔";
      position: absolute;
      left: 0;
      color: var(--accent);
      font-weight: bold;
    }


    footer {
      text-align: center;
      padding: 20px;
      background: var(--primary);
      color: white;
      margin-top: 40px;
    }


    @media (max-width: 600px) {
      header h1 {
        font-size: 2rem;
      }


      .nav-container {
        flex-direction: column;
        align-items: flex-end;
        padding-right: 10px;
        gap: 5px;
      }
    }
  </style>
</head>
<body>


  {{-- Encabezado con login a la derecha --}}
  <header>
    <div class="nav-container">
      @if (Route::has('login'))
          @auth
              <a href="{{ url('/dashboard') }}">Dashboard</a>
          @else
              <a href="{{ route('login') }}">Log in</a>
              @if (Route::has('register'))
                  <a href="{{ route('register') }}">Register</a>
              @endif
          @endauth
      @endif
    </div>


    <h1>Bienvenidos Modacol</h1>
    <p>Comprometidos con la calidad, el equipo y el crecimiento constante</p>
  </header>


  {{-- Contenido principal --}}
  <main>
    <div class="section">
      <h2>¿Quiénes somos?</h2>
      <p>
        Somos una empresa dedicada a la fabricación y comercialización de prendas de vestir, construida sobre los valores de calidad, compromiso y trabajo en equipo. Creemos que cada proceso, cada costura y cada esfuerzo humano cuenta.
      </p>
      <p>
        Nuestra misión es entregar productos que reflejen dedicación, creatividad y excelencia, manteniendo siempre un precio justo para nuestros clientes y una cultura laboral digna y profesional para nuestros colaboradores.
      </p>
    </div>


    <div class="section">
      <h2>Nuestra visión interna</h2>
      <p>
        Esta plataforma ha sido creada como un punto de encuentro digital para nuestros trabajadores. Aquí encontrarás información útil, accesos a herramientas de trabajo, y un espacio para mantenernos conectados, informados y organizados.
      </p>
      <p>
        Esperamos que cada integrante del equipo pueda encontrar aquí no solo recursos, sino también motivación para seguir creciendo y aportando con pasión a lo que hacemos día a día.
      </p>
    </div>


    <div class="section">
      <h2>Mensaje para el equipo</h2>
      <p>
        "Cada puntada, cada entrega, cada detalle cuenta. Somos un equipo que transforma esfuerzo en resultados, talento en diseño, y compromiso en calidad. Gracias por ser parte de esta gran familia."
      </p>
    </div>
  </main>


  <footer>
    © 2025 Empresa Modacol – Plataforma Interna
  </footer>


</body>
</html>





