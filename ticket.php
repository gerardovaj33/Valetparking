<?php


session_start();


/* IMPEDIR ENTRAR SIN INICIAR SESIÓN */


if (!isset($_SESSION["id_cuenta"])) {
    header("Location: login.php?error=sesion");
    exit;
}


require_once "conexion.php";


/* ZONA HORARIA */


date_default_timezone_set("America/Mexico_City");


/* DATOS DEL EMPLEADO QUE INICIÓ SESIÓN */


$nombre_empleado = trim(
    ($_SESSION["nombre"] ?? "Usuario") .
    " " .
    ($_SESSION["apellidos"] ?? "")
);


$mensaje = "";
$ticket = null;


/* OBTENER EL ID DEL TICKET */


$id_ticket = isset($_GET["id_ticket"])
    ? intval($_GET["id_ticket"])
    : 0;


if ($id_ticket <= 0) {
    $mensaje = "No se recibió un ticket válido.";


} else {


    /* CONSULTAR DATOS DEL TICKET */


    $consulta = $conexion->prepare(
        "SELECT
            t.id_ticket,
            t.fecha_ticket,


             c.id_cliente,
             c.nombre,
             c.apellidos,
             c.correo,
             c.telefono,
             c.edad,


             v.id_vehiculo,
             v.placa,
             v.color,
             v.lugar_estacionado,
             v.modelo,
             v.hora_entrada,
             v.hora_salida,
             v.estado


         FROM tickets t


         INNER JOIN clientes c
             ON t.id_cliente = c.id_cliente


         INNER JOIN vehiculos v
             ON t.id_vehiculo = v.id_vehiculo


         WHERE t.id_ticket = ?


         LIMIT 1"
    );


    if (!$consulta) {


         $mensaje =
             "Error al preparar la consulta: " .
             $conexion->error;
} else {


   $consulta->bind_param(
       "i",
       $id_ticket
   );


   if (!$consulta->execute()) {


       $mensaje =
           "Error al consultar el ticket: " .
           $consulta->error;


   } else {


       $consulta->store_result();


       if ($consulta->num_rows === 1) {


              $consulta->bind_result(
                  $ticket_id,
                  $fecha_ticket,


                   $id_cliente,
                   $nombre,
                   $apellidos,
                   $correo,
                   $telefono,
                   $edad,


                   $id_vehiculo,
                   $placa,
                   $color,
                   $lugar_estacionado,
                   $modelo,
                   $hora_entrada,
                   $hora_salida,
                   $estado
              );


              $consulta->fetch();


              $ticket = [
                  "id_ticket" => $ticket_id,
                      "fecha_ticket" => $fecha_ticket,


                      "id_cliente" => $id_cliente,
                      "nombre" => $nombre,
                      "apellidos" => $apellidos,
                      "correo" => $correo,
                      "telefono" => $telefono,
                      "edad" => $edad,


                      "id_vehiculo" => $id_vehiculo,
                      "placa" => $placa,
                      "color" => $color,
                      "lugar_estacionado" => $lugar_estacionado,
                      "modelo" => $modelo,
                      "hora_entrada" => $hora_entrada,
                      "hora_salida" => $hora_salida,
                      "estado" => $estado
                 ];


             } else {


                 $mensaje =
                     "No se encontró el ticket solicitado.";
             }
         }


         $consulta->close();
     }
}


/* PREPARAR DATOS VISUALES */


$folio = "";


if ($ticket !== null) {


     $folio = str_pad(
         $ticket["id_ticket"],
         6,
         "0",
         STR_PAD_LEFT
     );
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


    <title>
        Ticket de valet parking
    </title>


    <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="accesibilidad.css">


</head>


<body>

<?php include "barra_accesibilidad.php"; ?>


<div class="ticket-main">


    <h1 class="ticket-titulo">
        GENERACIÓN DE TICKET
    </h1>


    <?php if ($mensaje !== ""): ?>


          <div class="mensaje-formulario">


             <?php
             echo htmlspecialchars($mensaje);
             ?>


          </div>


          <div class="ticket-btn-box">


             <a
                   href="panel.php"
                   class="btn-ticket"
             >
                   REGRESAR AL PANEL
       </a>


   </div>


<?php elseif ($ticket !== null): ?>


   <div class="ticket-grid">


       <!-- IMAGEN IZQUIERDA -->


       <img
              src="img/ticket1.jpg"
              class="ticket-img ticket-img-lateral"
              alt="Ticket de valet parking"
       >


       <!-- TICKET CENTRAL -->


       <div class="ticket-center">


              <div class="ticket-cabecera">


                 <p class="ticket-marca">
                     VALET PARKING
                 </p>


                 <h2 class="ticket-numero">
                     FOLIO #<?php
                     echo htmlspecialchars($folio);
                     ?>
                 </h2>


                 <p class="ticket-fecha-principal">


                       <?php
                       echo date(
                           "d/m/Y - h:i A",
                           strtotime($ticket["fecha_ticket"])
                       );
                       ?>


                 </p>


              </div>
<div class="ticket-separador"></div>


<div class="ticket-empleado">


   <span>
       Registrado por:
   </span>


   <strong>
       <?php
       echo htmlspecialchars($nombre_empleado);
       ?>
   </strong>


</div>


<div class="ticket-seccion">


   <h3>
       DATOS DEL CLIENTE
   </h3>


   <div class="ticket-fila">


         <span>
             ID de usuario
         </span>


         <strong>
             <?php
             echo intval(
                 $ticket["id_cliente"]
             );
             ?>
         </strong>


   </div>


   <div class="ticket-fila">


         <span>
             Nombre
         </span>


         <strong>
             <?php
             echo htmlspecialchars(
                 $ticket["nombre"] .
                 " " .
                 $ticket["apellidos"]
             );
             ?>
         </strong>


   </div>


   <div class="ticket-fila">


         <span>
             Correo
         </span>


         <strong>
             <?php
             echo htmlspecialchars(
                 $ticket["correo"]
             );
             ?>
         </strong>


   </div>


   <div class="ticket-fila">


         <span>
             Teléfono
         </span>


         <strong>
             <?php
             echo htmlspecialchars(
                 $ticket["telefono"]
             );
             ?>
         </strong>


   </div>


</div>


<div class="ticket-seccion">
<h3>
    DATOS DEL VEHÍCULO
</h3>


<div class="ticket-fila">


   <span>
       ID de vehículo
   </span>


   <strong>
       <?php
       echo intval(
           $ticket["id_vehiculo"]
       );
       ?>
   </strong>


</div>


<div class="ticket-fila">


   <span>
       Modelo
   </span>


   <strong>
       <?php
       echo htmlspecialchars(
           $ticket["modelo"]
       );
       ?>
   </strong>


</div>


<div class="ticket-fila ticket-fila-placa">


   <span>
       Placa
   </span>


   <strong>
       <?php
             echo htmlspecialchars(
                 $ticket["placa"]
             );
             ?>
         </strong>


   </div>


   <div class="ticket-fila">


         <span>
             Color
         </span>


         <strong>
             <?php
             echo htmlspecialchars(
                 $ticket["color"]
             );
             ?>
         </strong>


   </div>


   <div class="ticket-fila">


         <span>
             Lugar
         </span>


         <strong>
             <?php
             echo htmlspecialchars(
                 $ticket["lugar_estacionado"]
             );
             ?>
         </strong>


   </div>


</div>


<div class="ticket-seccion">


   <h3>
    CONTROL DEL SERVICIO
</h3>


<div class="ticket-fila">


   <span>
       Hora de entrada
   </span>


   <strong>
       <?php
       echo date(
           "d/m/Y h:i A",
           strtotime(
               $ticket["hora_entrada"]
           )
       );
       ?>
   </strong>


</div>


<div class="ticket-fila">


   <span>
       Hora de salida
   </span>


   <strong>


         <?php if (
             $ticket["hora_salida"] !== null &&
             $ticket["hora_salida"] !== ""
         ): ?>


            <?php
            echo date(
                "d/m/Y h:i A",
                strtotime(
                    $ticket["hora_salida"]
                )
            );
            ?>


         <?php else: ?>
                 Pendiente


             <?php endif; ?>


         </strong>


   </div>


   <div class="ticket-fila">


         <span>
             Estado
         </span>


         <strong
             class="<?php
             echo $ticket["estado"] === "Entregado"
                 ? "ticket-estado-entregado"
                 : "ticket-estado-estacionado";
             ?>"
         >


             <?php
             echo htmlspecialchars(
                 $ticket["estado"]
             );
             ?>


         </strong>


   </div>


</div>


<div class="ticket-separador"></div>


<div class="ticket-aviso">


   <p>
       Conserva este ticket para solicitar
       la entrega del vehículo.
   </p>


   <p>
         Verifica que los datos sean correctos.
             </p>


          </div>


          <div class="ticket-pie">


             <strong>
                 GRACIAS POR SU PREFERENCIA
             </strong>


          </div>


   </div>


   <!-- IMAGEN DERECHA -->


   <img
          src="img/ticket2.jpg"
          class="ticket-img ticket-img-lateral"
          alt="Servicio de estacionamiento"
   >


</div>


<div class="ticket-btn-box no-imprimir">


   <button
       type="button"
       class="btn-ticket"
       onclick="window.print()"
   >
       IMPRIMIR TICKET
   </button>


   <a
          href="vehiculos.php"
          class="btn-ticket"
   >
          CONSULTAR VEHÍCULOS
   </a>


   <a
          href="panel.php"
          class="btn-ticket"
   >
                    VOLVER AL PANEL
             </a>


          </div>


    <?php endif; ?>


</div>


    <script src="accesibilidad.js"></script>

</body>


</html>