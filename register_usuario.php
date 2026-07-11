<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario y Vehículo</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="registro-main">

        <h1 class="registro-titulo">
            REGISTRO DE USUARIO Y VEHÍCULO
        </h1>

        <div class="registro-grid">

            <!-- LADO IZQUIERDO -->
            <div class="registro-box">

                <h2>REGISTRAR USUARIO</h2>

                <input
                    type="file"
                    class="input"
                    accept="image/*">

                <input
                    type="text"
                    class="input"
                    placeholder="Nombre">

                <input
                    type="text"
                    class="input"
                    placeholder="Apellidos">

                <input
                    type="email"
                    class="input"
                    placeholder="Correo">

                <input
                    type="tel"
                    class="input"
                    placeholder="Teléfono">

                <input
                    type="number"
                    class="input"
                    placeholder="Edad">

            </div>

            <!-- IMAGEN CENTRAL -->
            <div class="registro-img-box">

                <img
                    src="img/vertical.jpg"
                    class="registro-img"
                    alt="Servicio de valet parking">

            </div>

            <!-- LADO DERECHO -->
            <div class="registro-box">

                <h2>REGISTRO DE VEHÍCULO</h2>

                <input
                    type="text"
                    class="input"
                    placeholder="Placa">

                <input
                    type="text"
                    class="input"
                    placeholder="Color">

                <input
                    type="text"
                    class="input"
                    placeholder="Lugar donde fue estacionado">

                <input
                    type="text"
                    class="input"
                    placeholder="Modelo">

                <input
                    type="time"
                    class="input">

                <input
                    type="time"
                    class="input">

            </div>

        </div>

        <div class="registro-btn-box">

            <a href="panel.php" class="btn-registro-final">
                GUARDAR Y SALIR
            </a>

        </div>

    </div>

</body>

</html>