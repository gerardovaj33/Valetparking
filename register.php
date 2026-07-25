<?php


require_once "conexion.php";


$mensaje = "";


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
} elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $mensaje = "Escribe un correo válido.";
} elseif (strlen($contrasena_usuario) < 8) {
    $mensaje = "La contraseña debe tener mínimo 8 caracteres.";
} else {


      $revisar = $conexion->prepare(
          "SELECT id_cuenta FROM cuentas WHERE correo = ?"
      );


      $revisar->bind_param("s", $correo);
      $revisar->execute();
      $revisar->store_result();


      if ($revisar->num_rows > 0) {


          $mensaje = "Ese correo ya está registrado.";


      } else {


          $ruta_licencia = null;


          if (
                 isset($_FILES["licencia"]) &&
                 $_FILES["licencia"]["error"] === UPLOAD_ERR_OK
          ) {


                 $extension = strtolower(
                     pathinfo(
                         $_FILES["licencia"]["name"],
                         PATHINFO_EXTENSION
                     )
                 );


                 $permitidas = ["jpg", "jpeg", "png", "pdf"];


                 if (!in_array($extension, $permitidas, true)) {


                    $mensaje = "La licencia debe ser JPG, PNG o PDF.";
    } elseif ($_FILES["licencia"]["size"] > 5242880) {


        $mensaje = "El archivo no debe pesar más de 5 MB.";


    } else {


        $nombre_archivo =
            "licencia_" .
            uniqid() .
            "." .
            $extension;


        $ruta_licencia =
            "uploads/" .
            $nombre_archivo;


        $destino =
            __DIR__ .
            "/" .
            $ruta_licencia;


        if (
            !move_uploaded_file(
                $_FILES["licencia"]["tmp_name"],
                $destino
            )
        ) {
            $mensaje = "No se pudo guardar la licencia.";
            $ruta_licencia = null;
        }
    }
}


if ($mensaje === "") {


    $contrasena_cifrada = password_hash(
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
                       $contrasena_cifrada,
                       $ruta_licencia
                   );


                   if ($insertar->execute()) {


                        header("Location: login.php?registro=exitoso");
                        exit;


                   } else {


                        $mensaje = "No se pudo crear la cuenta.";
                   }


                   $insertar->close();
             }
         }


         $revisar->close();
     }
}


?>


<!DOCTYPE html>
<html lang="es">


<head>


     <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">


    <title>Crear cuenta</title>


    <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="accesibilidad.css">


</head>


<body>

<?php include "barra_accesibilidad.php"; ?>


<div class="register-contenedor">


    <div class="register-card">


          <h1 class="register-titulo">
              CREACIÓN DE CUENTA
          </h1>


          <?php if ($mensaje !== ""): ?>


             <div class="mensaje-formulario">
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
                 value="<?php echo htmlspecialchars($_POST["nombre"] ??
""); ?>"
                  required
             >


             <input
                 type="text"
               name="apellidos"
               class="register-input"
               placeholder="Apellidos"
               value="<?php echo htmlspecialchars($_POST["apellidos"] ??
""); ?>"
               required
           >


           <input
               type="tel"
               name="telefono"
               class="register-input"
               placeholder="Teléfono"
               value="<?php echo htmlspecialchars($_POST["telefono"] ??
""); ?>"
               required
           >


           <input
               type="email"
               name="correo"
               class="register-input"
               placeholder="Correo"
               value="<?php echo htmlspecialchars($_POST["correo"] ??
""); ?>"
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
               Subir licencia
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


    <script src="accesibilidad.js"></script>

</body>


</html>

