<?php


ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);


session_start();


/* IMPEDIR ENTRAR SIN INICIAR SESIÓN */


if (!isset($_SESSION["id_cuenta"])) {
    header("Location: login.php?error=sesion");
    exit;
}


require_once "conexion.php";


$mensaje = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /* DATOS DEL CLIENTE */


    $nombre = trim($_POST["nombre"] ?? "");
    $apellidos = trim($_POST["apellidos"] ?? "");
    $correo = strtolower(trim($_POST["correo"] ?? ""));
    $telefono = trim($_POST["telefono"] ?? "");
    $edad = trim($_POST["edad"] ?? "");


    /* DATOS DEL VEHÍCULO */


    $placa = strtoupper(trim($_POST["placa"] ?? ""));
    $color = trim($_POST["color"] ?? "");


    $lugar_estacionado = trim(
        $_POST["lugar_estacionado"] ?? ""
    );


    $modelo = trim($_POST["modelo"] ?? "");
    $hora_entrada = trim($_POST["hora_entrada"] ?? "");
$hora_salida = trim($_POST["hora_salida"] ?? "");


/* VALIDAR CAMPOS VACÍOS */


if (
    $nombre === "" ||
    $apellidos === "" ||
    $correo === "" ||
    $telefono === "" ||
    $edad === "" ||
    $placa === "" ||
    $color === "" ||
    $lugar_estacionado === "" ||
    $modelo === "" ||
    $hora_entrada === ""
) {


    $mensaje = "Completa todos los campos obligatorios.";


} elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {


    $mensaje = "Escribe un correo válido.";


} elseif (
    !is_numeric($edad) ||
    intval($edad) < 18 ||
    intval($edad) > 100
) {


    $mensaje = "Escribe una edad válida.";


} elseif (strtotime($hora_entrada) === false) {


    $mensaje = "La hora de entrada no es válida.";


} elseif (
    $hora_salida !== "" &&
    strtotime($hora_salida) === false
) {


    $mensaje = "La hora de salida no es válida.";
}


/* CONVERTIR HORAS PARA MYSQL */
    $hora_entrada_mysql = null;
    $hora_salida_mysql = null;


    if ($mensaje === "") {


        $hora_entrada_mysql = date(
            "Y-m-d H:i:s",
            strtotime($hora_entrada)
        );


        if ($hora_salida !== "") {


             $hora_salida_mysql = date(
                 "Y-m-d H:i:s",
                 strtotime($hora_salida)
             );


             if (
                    strtotime($hora_salida_mysql) <
                    strtotime($hora_entrada_mysql)
             ) {


                    $mensaje =
                        "La hora de salida no puede ser anterior a la
entrada.";
             }
        }
    }


    /* VALIDAR QUE EL CORREO NO ESTÉ REGISTRADO */


    if ($mensaje === "") {


        $validar_correo = $conexion->prepare(
            "SELECT id_cliente
            FROM clientes
            WHERE LOWER(TRIM(correo)) = LOWER(TRIM(?))
            LIMIT 1"
        );


        if (!$validar_correo) {


             $mensaje =
                 "Error al validar el correo: " .
                 $conexion->error;
    } else {


        $validar_correo->bind_param(
            "s",
            $correo
        );


        $validar_correo->execute();
        $validar_correo->store_result();


        if ($validar_correo->num_rows > 0) {


               $mensaje =
                   "Ya existe un cliente registrado con ese correo.";
        }


        $validar_correo->close();
    }
}


/* VALIDAR QUE LA PLACA NO ESTÉ ESTACIONADA */


if ($mensaje === "") {


    $validar_placa = $conexion->prepare(
        "SELECT id_vehiculo
        FROM vehiculos
        WHERE
            UPPER(
                REPLACE(
                    REPLACE(placa, '-', ''),
                    ' ',
                    ''
                )
            )
            =
            UPPER(
                REPLACE(
                    REPLACE(?, '-', ''),
                    ' ',
                    ''
                )
            )
        AND estado = 'Estacionado'
        LIMIT 1"
    );


    if (!$validar_placa) {


         $mensaje =
             "Error al validar la placa: " .
             $conexion->error;


    } else {


         $validar_placa->bind_param(
             "s",
             $placa
         );


         $validar_placa->execute();
         $validar_placa->store_result();


         if ($validar_placa->num_rows > 0) {


               $mensaje =
                   "Ya existe un vehículo estacionado con esa placa.";
         }


         $validar_placa->close();
    }
}


/* PROCESAR ARCHIVO DEL INE */


$ruta_ine = null;


if ($mensaje === "") {


    if (
        isset($_FILES["ine"]) &&
        $_FILES["ine"]["error"] === UPLOAD_ERR_OK
    ) {


         $nombre_original = $_FILES["ine"]["name"];
         $archivo_temporal = $_FILES["ine"]["tmp_name"];
         $tamano_archivo = $_FILES["ine"]["size"];


         $extension = strtolower(
             pathinfo(
           $nombre_original,
           PATHINFO_EXTENSION
       )
);


$extensiones_permitidas = [
    "jpg",
    "jpeg",
    "png",
    "pdf"
];


if (
       !in_array(
           $extension,
           $extensiones_permitidas,
           true
       )
) {


       $mensaje =
           "El INE debe ser JPG, JPEG, PNG o PDF.";


} elseif ($tamano_archivo > 5242880) {


       $mensaje =
           "El archivo del INE no debe pesar más de 5 MB.";


} else {


       $carpeta_uploads =
           __DIR__ .
           DIRECTORY_SEPARATOR .
           "uploads" .
           DIRECTORY_SEPARATOR;


       if (!is_dir($carpeta_uploads)) {


           if (
               !mkdir(
                   $carpeta_uploads,
                   0777,
                   true
               )
           ) {
                       $mensaje =
                           "No se pudo crear la carpeta uploads.";
                   }
               }


               if ($mensaje === "") {


                   $nombre_archivo =
                       "ine_" .
                       uniqid() .
                       "." .
                       $extension;


                   $ruta_completa =
                       $carpeta_uploads .
                       $nombre_archivo;


                   if (
                       move_uploaded_file(
                           $archivo_temporal,
                           $ruta_completa
                       )
                   ) {


                       $ruta_ine =
                           "uploads/" .
                           $nombre_archivo;


                   } else {


                       $mensaje =
                           "No se pudo guardar el archivo del INE.";
                   }
               }
        }


    } else {


        $mensaje =
            "Debes seleccionar un archivo de INE.";
    }
}


/* GUARDAR CLIENTE, VEHÍCULO Y TICKET */


if ($mensaje === "") {
$conexion->begin_transaction();


try {


   /* INSERTAR CLIENTE */


   $insertar_cliente = $conexion->prepare(
       "INSERT INTO clientes
       (
           ine,
           nombre,
           apellidos,
           correo,
           telefono,
           edad
       )
       VALUES (?, ?, ?, ?, ?, ?)"
   );


   if (!$insertar_cliente) {


        throw new Exception(
            "Error al preparar cliente: " .
            $conexion->error
        );
   }


   $edad_entero = intval($edad);


   $insertar_cliente->bind_param(
       "sssssi",
       $ruta_ine,
       $nombre,
       $apellidos,
       $correo,
       $telefono,
       $edad_entero
   );


   if (!$insertar_cliente->execute()) {


        throw new Exception(
            "Error al registrar cliente: " .
            $insertar_cliente->error
        );
}


$id_cliente = $conexion->insert_id;


$insertar_cliente->close();


/* INSERTAR VEHÍCULO */


$insertar_vehiculo = $conexion->prepare(
    "INSERT INTO vehiculos
    (
        id_cliente,
        placa,
        color,
        lugar_estacionado,
        modelo,
        hora_entrada,
        hora_salida,
        estado
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);


if (!$insertar_vehiculo) {


    throw new Exception(
        "Error al preparar vehículo: " .
        $conexion->error
    );
}


$estado = "Estacionado";


$insertar_vehiculo->bind_param(
    "isssssss",
    $id_cliente,
    $placa,
    $color,
    $lugar_estacionado,
    $modelo,
    $hora_entrada_mysql,
    $hora_salida_mysql,
    $estado
);


if (!$insertar_vehiculo->execute()) {
    throw new Exception(
        "Error al registrar vehículo: " .
        $insertar_vehiculo->error
    );
}


$id_vehiculo = $conexion->insert_id;


$insertar_vehiculo->close();


/* INSERTAR TICKET */


$insertar_ticket = $conexion->prepare(
    "INSERT INTO tickets
    (
        id_cliente,
        id_vehiculo
    )
    VALUES (?, ?)"
);


if (!$insertar_ticket) {


    throw new Exception(
        "Error al preparar ticket: " .
        $conexion->error
    );
}


$insertar_ticket->bind_param(
    "ii",
    $id_cliente,
    $id_vehiculo
);


if (!$insertar_ticket->execute()) {


    throw new Exception(
        "Error al crear ticket: " .
        $insertar_ticket->error
    );
}


$id_ticket = $conexion->insert_id;
             $insertar_ticket->close();


             /* CONFIRMAR LOS REGISTROS */


             $conexion->commit();


             header(
                 "Location: ticket.php?id_ticket=" .
                 $id_ticket
             );


             exit;


         } catch (Exception $error) {


             $conexion->rollback();


             /* BORRAR EL INE SI FALLÓ EL REGISTRO */


             if (
                    $ruta_ine !== null &&
                    file_exists(__DIR__ . "/" . $ruta_ine)
             ) {


                    unlink(__DIR__ . "/" . $ruta_ine);
             }


             $mensaje = $error->getMessage();
         }
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


   <title>Registro de usuario y vehículo</title>


   <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="accesibilidad.css">


</head>


<body>

<?php include "barra_accesibilidad.php"; ?>


<div class="registro-main">


   <h1 class="registro-titulo">
       REGISTRO DE USUARIO Y VEHÍCULO
   </h1>


   <?php if ($mensaje !== ""): ?>


          <div class="mensaje-formulario">


             <?php
             echo htmlspecialchars($mensaje);
             ?>


          </div>


   <?php endif; ?>


   <form
       method="POST"
       action="register_usuario.php"
       enctype="multipart/form-data"
       class="registro-formulario"
   >


          <div class="registro-grid">


             <!-- DATOS DEL CLIENTE -->


             <div class="registro-box">


                   <h2 class="registro-subtitulo">
                       REGISTRAR USUARIO
                   </h2>
<label class="label-hora">
    Subir INE
</label>


<input
    type="file"
    name="ine"
    class="input-archivo"
    accept=".jpg,.jpeg,.png,.pdf"
    required
>


<input
    type="text"
    name="nombre"
    class="input"
    placeholder="Nombre"
    value="<?php
    echo htmlspecialchars(
        $_POST["nombre"] ?? ""
    );
    ?>"
    required
>


<input
    type="text"
    name="apellidos"
    class="input"
    placeholder="Apellidos"
    value="<?php
    echo htmlspecialchars(
        $_POST["apellidos"] ?? ""
    );
    ?>"
    required
>


<input
    type="email"
    name="correo"
    class="input"
    placeholder="Correo"
    value="<?php
    echo htmlspecialchars(
        $_POST["correo"] ?? ""
         );
         ?>"
         required
    >


    <input
        type="tel"
        name="telefono"
        class="input"
        placeholder="Teléfono"
        value="<?php
        echo htmlspecialchars(
            $_POST["telefono"] ?? ""
        );
        ?>"
        required
    >


    <input
        type="number"
        name="edad"
        class="input"
        placeholder="Edad"
        min="18"
        max="100"
        value="<?php
        echo htmlspecialchars(
            $_POST["edad"] ?? ""
        );
        ?>"
        required
    >


</div>


<!-- IMAGEN CENTRAL -->


<div class="registro-img-box">


    <img
        src="img/vertical.jpg"
        alt="Valet parking"
        class="registro-img"
    >


</div>
<!-- DATOS DEL VEHÍCULO -->


<div class="registro-box">


    <h2 class="registro-subtitulo">
        REGISTRO DE VEHÍCULO
    </h2>


    <input
        type="text"
        name="placa"
        class="input"
        placeholder="Placa"
        maxlength="20"
        value="<?php
        echo htmlspecialchars(
            $_POST["placa"] ?? ""
        );
        ?>"
        required
    >


    <input
        type="text"
        name="color"
        class="input"
        placeholder="Color"
        value="<?php
        echo htmlspecialchars(
            $_POST["color"] ?? ""
        );
        ?>"
        required
    >


    <input
        type="text"
        name="lugar_estacionado"
        class="input"
        placeholder="Lugar donde fue estacionado"
        value="<?php
        echo htmlspecialchars(
            $_POST["lugar_estacionado"] ?? ""
        );
        ?>"
    required
>


<input
    type="text"
    name="modelo"
    class="input"
    placeholder="Modelo"
    value="<?php
    echo htmlspecialchars(
        $_POST["modelo"] ?? ""
    );
    ?>"
    required
>


<label class="label-hora">
    Hora de entrada
</label>


<input
    type="datetime-local"
    name="hora_entrada"
    class="input"
    value="<?php
    echo htmlspecialchars(
        $_POST["hora_entrada"] ?? ""
    );
    ?>"
    required
>


<label class="label-hora">
    Hora de salida
</label>


<input
    type="datetime-local"
    name="hora_salida"
    class="input"
    value="<?php
    echo htmlspecialchars(
        $_POST["hora_salida"] ?? ""
    );
    ?>"
>
              </div>


          </div>


          <div class="registro-botones">


              <a
                     href="panel.php"
                     class="btn-regresar"
              >
                     REGRESAR
              </a>


              <button
                  type="submit"
                  class="btn-guardar"
              >
                  GUARDAR Y GENERAR TICKET
              </button>


          </div>


    </form>


</div>


    <script src="accesibilidad.js"></script>

</body>


</html>

