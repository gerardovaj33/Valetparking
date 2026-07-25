<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Valet Parking</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="accesibilidad.css">

</head>

<body>

    <!-- BARRA SUPERIOR -->

    <header class="navbar">

        <div class="logo">
            🚗 VALET PARKING
        </div>

        <nav class="navbar-opciones">

            <a href="accesibilidad.php" class="accesibilidad-enlace">
                Accesibilidad
            </a>

            <button
                type="button"
                class="btn-vista"
                id="toggleDaltonico"
                aria-label="Cambiar vista para personas con daltonismo"
            >
                Vista para daltónicos
            </button>

        </nav>

    </header>

    <!-- CONTENIDO PRINCIPAL -->

    <main class="contenedor">

        <section class="hero">

            <h1>
                BIENVENIDO
            </h1>

            <p>
                Tu vehículo en las mejores manos.
            </p>

        </section>

        <!-- CUADRÍCULA PRINCIPAL -->

        <section class="grid">

            <!-- IMAGEN -->

            <div class="card-imagen">

                <img
                    src="img/valet.jpg"
                    class="img1"
                    alt="Servicio de valet parking"
                >

            </div>

            <!-- DESCRIPCIÓN -->

            <div class="descripcion">

                <h2>
                    ¿Quiénes somos?
                </h2>

                <br>

                Somos un servicio de valet parking dedicado a brindar
                comodidad, rapidez y seguridad a nuestros clientes.

                <br><br>

                Recibimos tu vehículo, lo estacionamos y lo devolvemos
                cuando lo necesites.

            </div>

            <!-- INFORMACIÓN -->

            <div class="card-inferior">

                <h3>
                    ¿Por qué elegirnos?
                </h3>

                <br>

                🚗 Estaciona fácil y sin estrés.

                <br><br>

                🔑 Tu auto seguro mientras disfrutas tu tiempo.

            </div>

            <!-- IMAGEN -->

            <div class="card-imagen">

                <img
                    src="img/autos.jpg"
                    class="img2"
                    alt="Automóviles estacionados"
                >

            </div>

        </section>

        <!-- BOTÓN DE INICIO DE SESIÓN -->

        <div class="boton-centro">

            <a
                href="login.php"
                class="btn"
            >
                INICIAR SESIÓN
            </a>

        </div>

        <!-- ESTADÍSTICAS -->

        <section class="estadisticas">

            <div class="estadistica">

                <h2>
                    +5,000
                </h2>

                <p>
                    Vehículos atendidos
                </p>

            </div>

            <div class="estadistica">

                <h2>
                    24/7
                </h2>

                <p>
                    Servicio continuo
                </p>

            </div>

            <div class="estadistica">

                <h2>
                    100%
                </h2>

                <p>
                    Seguridad
                </p>

            </div>

        </section>

    </main>

    <!-- PIE DE PÁGINA -->

    <footer>

        © 2026 VALET PARKING |
        Todos los derechos reservados.

    </footer>

    <script src="accesibilidad.js"></script>

</body>

</html>