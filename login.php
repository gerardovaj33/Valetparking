<?php

session_start();

require_once "conexion.php";

$mensaje = "";

if (isset($_GET["registro"]) && $_GET["registro"] === "exitoso") {

    $mensaje = "Cuenta creada correctamente. Ya puedes iniciar sesión.";

}

if (isset($_GET["error"]) && $_GET["error"] === "sesion") {

    $mensaje = "Debes iniciar sesión para entrar al panel.";

}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["correo"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";

    if ($correo === "" || $contrasena === "") {

        $mensaje = "Completa todos los campos.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "Escribe un correo válido.";

    } else {

        $consulta = $conexion->prepare(
            "SELECT id_cuenta, nombre, apellidos, correo, contrasena
             FROM cuentas
             WHERE correo = ?
             LIMIT 1"
        );

        if (!$consulta) {

            die("Error en la consulta: " . $conexion->error);

        }

        $consulta->bind_param("s", $correo);
        $consulta->execute();
        $consulta->store_result();

        if ($consulta->num_rows === 1) {

            $consulta->bind_result(
                $id_cuenta,
                $nombre,
                $apellidos,
                $correo_guardado,
                $contrasena_guardada
            );

            $consulta->fetch();

            if (password_verify($contrasena, $contrasena_guardada)) {

                session_regenerate_id(true);

                $_SESSION["id_cuenta"] = $id_cuenta;
                $_SESSION["nombre"] = $nombre;
                $_SESSION["apellidos"] = $apellidos;
                $_SESSION["correo"] = $correo_guardado;

                header("Location: panel.php");
                exit;

            } else {

                $mensaje = "Correo o contraseña incorrectos.";

            }

        } else {

            $mensaje = "Correo o contraseña incorrectos.";

        }

        $consulta->close();

    }

}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Inicio de sesión</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="accesibilidad.css">

</head>

<body>

    <?php include "barra_accesibilidad.php"; ?>

    <div class="login-contenedor">

        <div class="login-card">

            <h1 class="login-titulo">
                INICIO DE SESIÓN
            </h1>

            <?php if ($mensaje !== ""): ?>

                <div class="mensaje-formulario">

                    <?php echo htmlspecialchars($mensaje); ?>

                </div>

            <?php endif; ?>

            <form
                method="POST"
                action="login.php"
                class="login-formulario"
            >

                <input
                    type="email"
                    name="correo"
                    class="login-input"
                    placeholder="Correo"
                    value="<?php echo htmlspecialchars($_POST["correo"] ?? ""); ?>"
                    required
                >

                <input
                    type="password"
                    name="contrasena"
                    class="login-input"
                    placeholder="Contraseña"
                    required
                >

                <button
                    type="submit"
                    class="btn-login"
                >
                    INICIAR SESIÓN
                </button>

            </form>

            <p class="login-texto">
                ¿No tienes cuenta?
            </p>

            <a
                href="register.php"
                class="btn-crear-cuenta"
            >
                CREAR CUENTA
            </a>

        </div>

    </div>

    <script src="accesibilidad.js"></script>

</body>

</html>