<?php

require_once "conexion.php";

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $apellidos = trim($_POST["apellidos"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $contrasena_usuario = $_POST["contrasena"] ?? "";

    if (
        $nombre === "" ||
        $apellidos === "" ||
        $telefono === "" ||
        $correo === "" ||
        $contrasena_usuario === ""
    ) {
        $mensaje = "Completa todos los campos.";
        $tipo_mensaje = "error";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Escribe un correo válido.";
        $tipo_mensaje = "error";
    } elseif (strlen($contrasena_usuario) < 8) {
        $mensaje = "La contraseña debe tener mínimo 8 caracteres.";
        $tipo_mensaje = "error";
    } else {

        /* Revisar si el correo ya existe */
        $consulta = $conexion->prepare(
            "SELECT id_cuenta FROM cuentas WHERE correo = ?"
        );

        $consulta->bind_param("s", $correo);
        $consulta->execute();
        $consulta->store_result();

        if ($consulta->num_rows > 0) {

            $mensaje = "Ese correo ya está registrado.";
            $tipo_mensaje = "error";

        } else {

            $ruta_licencia = null;

            /* Procesar la licencia */
            if (
                isset($_FILES["licencia"]) &&
                $_FILES["licencia"]["error"] !== UPLOAD_ERR_NO_FILE
            ) {

                if ($_FILES["licencia"]["error"] !== UPLOAD_ERR_OK) {

                    $mensaje = "Ocurrió un error al subir la licencia.";
                    $tipo_mensaje = "error";

                } else {

                    $tipos_permitidos = [
                        "image/jpeg" => "jpg",
                        "image/png" => "png",
                        "application/pdf" => "pdf"
                    ];

                    $tipo_archivo = mime_content_type(
                        $_FILES["licencia"]["tmp_name"]
                    );

                    if (!isset($tipos_permitidos[$tipo_archivo])) {

                        $mensaje = "La licencia debe ser JPG, PNG o PDF.";
                        $tipo_mensaje = "error";

                    } elseif ($_FILES["licencia"]["size"] > 5 * 1024 * 1024) {

                        $mensaje = "La licencia no debe pesar más de 5 MB.";
                        $tipo_mensaje = "error";

                    } else {

                        $extension = $tipos_permitidos[$tipo_archivo];

                        $nombre_archivo =
                            "licencia_" .
                            bin2hex(random_bytes(8)) .
                            "." .
                            $extension;

                        $carpeta_destino = __DIR__ . "/uploads/";

                        if (!is_dir($carpeta_destino)) {
                            mkdir($carpeta_destino, 0755, true);
                        }

                        $ruta_completa =
                            $carpeta_destino .
                            $nombre_archivo;

                        if (
                            move_uploaded_file(
                                $_FILES["licencia"]["tmp_name"],
                                $ruta_completa
                            )
                        ) {
                            $ruta_licencia =
                                "uploads/" .
                                $nombre_archivo;
                        } else {
                            $mensaje =
                                "No se pudo guardar la licencia.";
                            $tipo_mensaje = "error";
                        }
                    }
                }
            }

            /* Guardar la cuenta */
            if ($mensaje === "") {

                $contrasena_hash = password_hash(
                    $contrasena_usuario,
                    PASSWORD_DEFAULT
                );

                $insertar = $conexion->prepare(
                    "INSERT INTO cuentas
                    (
                        nombre,
                        apellidos,
                        telefono,
                        correo,
                        contrasena,
                        licencia
                    )
                    VALUES (?, ?, ?, ?, ?, ?)"
                );

                $insertar->bind_param(
                    "ssssss",
                    $nombre,
                    $apellidos,
                    $telefono,
                    $correo,
                    $contrasena_hash,
                    $ruta_licencia
                );

                if ($insertar->execute()) {

                    header("Location: login.php?registro=exitoso");
                    exit;

                } else {

                    $mensaje = "No se pudo crear la cuenta.";
                    $tipo_mensaje = "error";
                }

                $insertar->close();
            }
        }

        $consulta->close();
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Crear Cuenta</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="register-contenedor">

    <div class="register-card">

        <h1 class="register-titulo">
            CREACIÓN DE CUENTA
        </h1>

        <?php if ($mensaje !== ""): ?>

            <div class="mensaje-formulario <?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>

        <?php endif; ?>

        <form
            method="POST"
            enctype="multipart/form-data"
            class="register-formulario"
        >

            <input
                type="text"
                name="nombre"
                class="register-input"
                placeholder="Nombre"
                value="<?php echo htmlspecialchars($_POST["nombre"] ?? ""); ?>"
                required
            >

            <input
                type="text"
                name="apellidos"
                class="register-input"
                placeholder="Apellidos"
                value="<?php echo htmlspecialchars($_POST["apellidos"] ?? ""); ?>"
                required
            >

            <input
                type="tel"
                name="telefono"
                class="register-input"
                placeholder="Teléfono"
                value="<?php echo htmlspecialchars($_POST["telefono"] ?? ""); ?>"
                required
            >

            <input
                type="email"
                name="correo"
                class="register-input"
                placeholder="Correo"
                value="<?php echo htmlspecialchars($_POST["correo"] ?? ""); ?>"
                required
            >

            <input
                type="password"
                name="contrasena"
                class="register-input"
                placeholder="Contraseña"
                minlength="8"
                required
            >

            <label class="file-label">
                Subir licencia de conducir
            </label>

            <input
                type="file"
                name="licencia"
                class="register-file"
                accept=".jpg,.jpeg,.png,.pdf"
            >

            <button
                type="submit"
                class="btn-register"
            >
                SIGUIENTE
            </button>

        </form>

    </div>

</div>

</body>

</html>