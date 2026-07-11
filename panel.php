<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="panel">

        <!-- TÍTULO -->
        <h1 class="panel-titulo">PANEL DE CONTROL</h1>

        <!-- PARTE SUPERIOR -->
        <div class="panel-arriba">

            <!-- DATOS -->
            <div class="panel-datos">

                <div class="dato">
                    👤 Número de clientes: 0
                </div>

                <div class="dato">
                    🚗 Número de coches: 0
                </div>

                <div class="dato">
                    ✅ Coches entregados: 0
                </div>

            </div>

            <!-- IMAGEN -->
            <img src="img/panel.jpg" class="panel-img" alt="Panel de Control">

        </div>

        <!-- TÍTULO -->
        <h1 class="panel-titulo">¿QUÉ HAREMOS HOY?</h1>

        <!-- PARTE INFERIOR -->
        <div class="panel-abajo">

            <!-- REGISTRAR USUARIO -->
            <div class="panel-box">

                <p>
                    Registrar usuario y vehículo
                </p>

                <a href="register_usuario.php">
                    <button class="btn-panel">
                        SIGUIENTE
                    </button>
                </a>

            </div>

            <!-- IMPRIMIR TICKET -->
            <div class="panel-box">

                <p>
                    Imprimir ticket
                </p>

                <a href="ticket.php">
                    <button class="btn-panel">
                        IMPRIMIR
                    </button>
                </a>

            </div>

        </div>

    </div>

</body>

</html>