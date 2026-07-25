<?php


session_start();


if (!isset($_SESSION["id_cuenta"])) {
    header("Location: login.php?error=sesion");
    exit;
}


require_once "conexion.php";
$busqueda = trim($_GET["busqueda"] ?? "");
$mensaje = "";


/* MARCAR VEHÍCULO COMO ENTREGADO */
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["entregar_vehiculo"])
) {


   $id_vehiculo = intval($_POST["id_vehiculo"] ?? 0);


   if ($id_vehiculo > 0) {


       $actualizar = $conexion->prepare(
           "UPDATE vehiculos
           SET
               estado = 'Entregado',
               hora_salida = NOW()
           WHERE id_vehiculo = ?
           AND estado = 'Estacionado'"
       );


       if ($actualizar) {


           $actualizar->bind_param(
               "i",
               $id_vehiculo
           );


           if ($actualizar->execute()) {


               if ($actualizar->affected_rows > 0) {


                   $mensaje =
                       "Vehículo entregado correctamente.";


               } else {


                   $mensaje =
                       "El vehículo ya estaba entregado o no existe.";
               }


           } else {
                   $mensaje =
                       "No se pudo actualizar el vehículo.";
            }


            $actualizar->close();


        } else {


            $mensaje =
                "Error al preparar la actualización.";
        }
    }
}


/* CONSULTAR VEHÍCULOS */
if ($busqueda !== "") {


    $consulta = $conexion->prepare(
        "SELECT
            v.id_vehiculo,
            v.placa,
            v.color,
            v.modelo,
            v.lugar_estacionado,
            v.hora_entrada,
            v.hora_salida,
            v.estado,


            c.id_cliente,
            c.nombre,
            c.apellidos,
            c.telefono,
            c.correo,


            t.id_ticket


        FROM vehiculos v


        INNER JOIN clientes c
            ON v.id_cliente = c.id_cliente


        LEFT JOIN tickets t
            ON t.id_vehiculo = v.id_vehiculo


        WHERE
            v.placa LIKE ?
            OR c.nombre LIKE ?
            OR c.apellidos LIKE ?
            OR c.telefono LIKE ?


         ORDER BY v.id_vehiculo DESC"
    );


    $texto_busqueda = "%" . $busqueda . "%";


    $consulta->bind_param(
        "ssss",
        $texto_busqueda,
        $texto_busqueda,
        $texto_busqueda,
        $texto_busqueda
    );


} else {


    $consulta = $conexion->prepare(
        "SELECT
            v.id_vehiculo,
            v.placa,
            v.color,
            v.modelo,
            v.lugar_estacionado,
            v.hora_entrada,
            v.hora_salida,
            v.estado,


            c.id_cliente,
            c.nombre,
            c.apellidos,
            c.telefono,
            c.correo,


            t.id_ticket


         FROM vehiculos v


         INNER JOIN clientes c
             ON v.id_cliente = c.id_cliente


         LEFT JOIN tickets t
             ON t.id_vehiculo = v.id_vehiculo
         ORDER BY v.id_vehiculo DESC"
    );
}


$vehiculos = [];


if ($consulta) {


    $consulta->execute();


    $consulta->bind_result(
        $id_vehiculo,
        $placa,
        $color,
        $modelo,
        $lugar_estacionado,
        $hora_entrada,
        $hora_salida,
        $estado,


         $id_cliente,
         $nombre,
         $apellidos,
         $telefono,
         $correo,


         $id_ticket
    );


    while ($consulta->fetch()) {


         $vehiculos[] = [
             "id_vehiculo" => $id_vehiculo,
             "placa" => $placa,
             "color" => $color,
             "modelo" => $modelo,
             "lugar_estacionado" => $lugar_estacionado,
             "hora_entrada" => $hora_entrada,
             "hora_salida" => $hora_salida,
             "estado" => $estado,


            "id_cliente" => $id_cliente,
            "nombre" => $nombre,
            "apellidos" => $apellidos,
            "telefono" => $telefono,
            "correo" => $correo,
               "id_ticket" => $id_ticket
          ];
     }


     $consulta->close();


} else {


     $mensaje =
         "No se pudo realizar la consulta: " .
         $conexion->error;
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


     <title>Consultar vehículos</title>


     <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="accesibilidad.css">


</head>


<body>

<?php include "barra_accesibilidad.php"; ?>


<div class="vehiculos-main">


     <div class="vehiculos-encabezado">


          <div>


               <h1 class="vehiculos-titulo">
                   CONSULTAR VEHÍCULOS
         </h1>


         <p class="vehiculos-descripcion">
              Busca un vehículo y registra su entrega.
         </p>


   </div>


   <a
         href="panel.php"
         class="btn-regresar"
   >
       REGRESAR AL PANEL
   </a>


</div>


<?php if ($mensaje !== ""): ?>


   <div class="mensaje-formulario">


         <?php
         echo htmlspecialchars($mensaje);
         ?>


   </div>


<?php endif; ?>


<form
    method="GET"
    action="vehiculos.php"
    class="vehiculos-buscador"
>


   <input
       type="text"
       name="busqueda"
       class="input"
       placeholder="Buscar por placa, nombre, apellido o teléfono"
       value="<?php
       echo htmlspecialchars($busqueda);
       ?>"
   >
   <button
       type="submit"
       class="panel-boton"
   >
       BUSCAR
   </button>


   <a
          href="vehiculos.php"
          class="panel-boton"
   >
       MOSTRAR TODOS
   </a>


</form>


<?php if (count($vehiculos) > 0): ?>


   <div class="vehiculos-lista">


          <?php foreach ($vehiculos as $vehiculo): ?>


              <div class="vehiculo-card">


                  <div class="vehiculo-card-header">


                     <div>


                          <h2>
                              <?php
                              echo htmlspecialchars(
                                  $vehiculo["modelo"]
                              );
                              ?>
                          </h2>


                          <p class="vehiculo-placa">
                              Placa:
                              <?php
                              echo htmlspecialchars(
                                  $vehiculo["placa"]
                              );
                              ?>
                          </p>


                     </div>
   <span
       class="<?php
       echo $vehiculo["estado"] === "Entregado"
           ? "estado-entregado"
           : "estado-estacionado";
       ?>"
   >


         <?php
         echo htmlspecialchars(
             $vehiculo["estado"]
         );
         ?>


   </span>


</div>


<div class="vehiculo-datos-grid">


   <div class="vehiculo-dato">


         <strong>Cliente</strong>


         <span>
             <?php
             echo htmlspecialchars(
                 $vehiculo["nombre"] .
                 " " .
                 $vehiculo["apellidos"]
             );
             ?>
         </span>


   </div>


   <div class="vehiculo-dato">


         <strong>Teléfono</strong>


         <span>
             <?php
             echo htmlspecialchars(
                 $vehiculo["telefono"]
        );
        ?>
    </span>


</div>


<div class="vehiculo-dato">


    <strong>Correo</strong>


    <span>
        <?php
        echo htmlspecialchars(
            $vehiculo["correo"]
        );
        ?>
    </span>


</div>


<div class="vehiculo-dato">


    <strong>Color</strong>


    <span>
        <?php
        echo htmlspecialchars(
            $vehiculo["color"]
        );
        ?>
    </span>


</div>


<div class="vehiculo-dato">


    <strong>Lugar</strong>


    <span>
        <?php
        echo htmlspecialchars(
            $vehiculo["lugar_estacionado"]
        );
        ?>
    </span>
</div>


<div class="vehiculo-dato">


    <strong>Hora de entrada</strong>


    <span>
        <?php
        echo date(
            "d/m/Y h:i A",
            strtotime(
                $vehiculo["hora_entrada"]
            )
        );
        ?>
    </span>


</div>


<div class="vehiculo-dato">


    <strong>Hora de salida</strong>


    <span>


         <?php if (
             $vehiculo["hora_salida"] !== null &&
             $vehiculo["hora_salida"] !== ""
         ): ?>


             <?php
             echo date(
                 "d/m/Y h:i A",
                 strtotime(
                     $vehiculo["hora_salida"]
                 )
             );
             ?>


         <?php else: ?>


             Pendiente


         <?php endif; ?>
                            </span>


                       </div>


                   </div>


                   <div class="vehiculo-acciones">


                       <?php if (
                           $vehiculo["id_ticket"] !== null
                       ): ?>


                            <a
                                 href="ticket.php?id_ticket=<?php
                                 echo intval(
                                     $vehiculo["id_ticket"]
                                 );
                                 ?>"
                                 class="panel-boton"
                            >
                                VER TICKET
                            </a>


                       <?php endif; ?>


                       <?php if (
                           $vehiculo["estado"] === "Estacionado"
                       ): ?>


                            <form
                                method="POST"
                                action="vehiculos.php?busqueda=<?php
                                echo urlencode($busqueda);
                                ?>"
                                onsubmit="return confirm('¿Seguro que
deseas entregar este vehículo?');"
                            >


                                 <input
                                     type="hidden"
                                     name="id_vehiculo"
                                     value="<?php
                                     echo intval(
                                         $vehiculo["id_vehiculo"]
                                     );
                                          ?>"
                                    >


                                    <button
                                        type="submit"
                                        name="entregar_vehiculo"
                                      onclick="return confirm('¿Seguro que deseas entregar este vehículo?');"
                                        class="btn-entregar"
                                    >
                                        ENTREGAR VEHÍCULO
                                    </button>


                                </form>


                            <?php else: ?>


                                <span class="vehiculo-ya-entregado">
                                    VEHÍCULO ENTREGADO
                                </span>


                            <?php endif; ?>


                      </div>


                   </div>


            <?php endforeach; ?>


         </div>


   <?php else: ?>


         <div class="sin-resultados">


            <h2>No se encontraron vehículos</h2>


            <p>
                   Intenta buscar con otra placa, nombre o teléfono.
            </p>


         </div>


   <?php endif; ?>


</div>
    <script src="accesibilidad.js"></script>

</body>


</html>

