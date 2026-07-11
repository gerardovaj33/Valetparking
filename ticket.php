<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Generación de Ticket</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="ticket-main">

        <h1 class="ticket-titulo">
            GENERACIÓN DE TICKET
        </h1>

        <div class="ticket-grid">

            <!-- IMAGEN IZQUIERDA -->
            <img
                src="img/ticket1.jpg"
                class="ticket-img"
                alt="Ticket de valet parking">

            <!-- CAMPOS CENTRALES -->
            <div class="ticket-center">

                <input
                    type="text"
                    class="input"
                    placeholder="ID de usuario">

                <input
                    type="text"
                    class="input"
                    placeholder="ID de vehículo">

            </div>

            <!-- IMAGEN DERECHA -->
            <img
                src="img/ticket2.jpg"
                class="ticket-img"
                alt="Servicio de estacionamiento">

        </div>

        <div class="ticket-btn-box">

            <a href="panel.php" class="btn-ticket">
                IMPRIMIR
            </a>

        </div>

    </div>

</body>

</html>