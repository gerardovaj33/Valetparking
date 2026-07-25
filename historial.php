<?php


session_start();


if (!isset($_SESSION["id_cuenta"])) {
    header("Location: login.php?error=sesion");
    exit;
}


require_once "conexion.php";


$busqueda = trim($_GET["buscar"] ?? "");
$vehiculos = [];
$mensaje = "";


$sql = "SELECT
            v.id_vehiculo,
            v.placa,
            v.modelo,
            v.hora_entrada,
            v.hora_salida,
            c.nombre,
            c.apellidos,
            t.id_ticket
        FROM vehiculos v
        INNER JOIN clientes c
            ON v.id_cliente = c.id_cliente
        LEFT JOIN tickets t
            ON t.id_vehiculo = v.id_vehiculo
         WHERE v.estado = 'Entregado'";


if ($busqueda !== "") {
    $sql .= " AND (
                v.placa LIKE ?
                OR v.modelo LIKE ?
                OR c.nombre LIKE ?
                OR c.apellidos LIKE ?
              )";
}


$sql .= " ORDER BY v.hora_salida DESC";


$consulta = $conexion->prepare($sql);


if (!$consulta) {
    $mensaje = "No se pudo cargar el historial.";
} else {


     if ($busqueda !== "") {
         $buscar = "%" . $busqueda . "%";


         $consulta->bind_param(
             "ssss",
             $buscar,
             $buscar,
             $buscar,
             $buscar
         );
     }


     if ($consulta->execute()) {
         $resultado = $consulta->get_result();


         while ($fila = $resultado->fetch_assoc()) {
             $vehiculos[] = $fila;
         }
     } else {
         $mensaje = "No se pudo consultar el historial.";
     }


     $consulta->close();
}


?>
<!DOCTYPE html>
<html lang="es">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-
scale=1.0">
    <title>Historial de vehículos</title>
    <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="accesibilidad.css">
</head>


<body>

<?php include "barra_accesibilidad.php"; ?>


<div class="historial-main">


    <header class="historial-header">


         <div>
             <h1 class="historial-titulo">HISTORIAL DE VEHÍCULOS</h1>
             <p class="historial-descripcion">
                  Vehículos que ya fueron entregados.
             </p>
         </div>


         <a href="panel.php" class="historial-btn-regresar">
             REGRESAR AL PANEL
         </a>


    </header>


    <form method="GET" action="historial.php" class="historial-buscador">


         <input
             type="text"
             name="buscar"
             class="historial-input"
             placeholder="Buscar por placa, cliente o modelo"
             value="<?php echo htmlspecialchars($busqueda); ?>"
         >


         <button type="submit" class="historial-btn-buscar">
             BUSCAR
         </button>


         <?php if ($busqueda !== ""): ?>
             <a href="historial.php" class="historial-btn-limpiar">
            LIMPIAR
       </a>
   <?php endif; ?>


</form>


<?php if ($mensaje !== ""): ?>


   <div class="mensaje-formulario">
       <?php echo htmlspecialchars($mensaje); ?>
   </div>


<?php elseif (count($vehiculos) === 0): ?>


   <div class="historial-vacio">
       <h2>No hay resultados</h2>
       <p>No se encontraron vehículos entregados.</p>
   </div>


<?php else: ?>


   <div class="historial-tabla-contenedor">


          <table class="historial-tabla">


              <thead>
                  <tr>
                      <th>Ticket</th>
                      <th>Cliente</th>
                      <th>Placa</th>
                      <th>Modelo</th>
                      <th>Entrada</th>
                      <th>Salida</th>
                      <th>Estado</th>
                      <th>Acción</th>
                  </tr>
              </thead>


              <tbody>


              <?php foreach ($vehiculos as $vehiculo): ?>


                  <tr>


                        <td>
                           <?php
                           echo !empty($vehiculo["id_ticket"])
                               ? "#" . intval($vehiculo["id_ticket"])
                               : "Sin ticket";
                           ?>
                       </td>


                       <td>
                           <?php
                           echo htmlspecialchars(
                               $vehiculo["nombre"] . " " .
                               $vehiculo["apellidos"]
                           );
                           ?>
                       </td>


                        <td class="historial-placa">
                            <?php echo
htmlspecialchars($vehiculo["placa"]); ?>
                        </td>


                        <td>
                            <?php echo
htmlspecialchars($vehiculo["modelo"]); ?>
                        </td>


                       <td>
                           <?php
                           echo date(
                               "d/m/Y h:i A",
                               strtotime($vehiculo["hora_entrada"])
                           );
                           ?>
                       </td>


                       <td>
                           <?php
                           echo date(
                               "d/m/Y h:i A",
                               strtotime($vehiculo["hora_salida"])
                           );
                           ?>
                       </td>


                       <td>
                            <span class="historial-
estado">ENTREGADO</span>
                        </td>


                          <td>
                              <?php if (!empty($vehiculo["id_ticket"])): ?>
                                  <a
                                      href="ticket.php?id_ticket=<?php
                                      echo intval($vehiculo["id_ticket"]);
                                      ?>"
                                      class="historial-btn-ticket"
                                  >
                                      VER TICKET
                                  </a>
                              <?php else: ?>
                                  No disponible
                              <?php endif; ?>
                          </td>


                      </tr>


                   <?php endforeach; ?>


                   </tbody>


             </table>


          </div>


   <?php endif; ?>


</div>


    <script src="accesibilidad.js"></script>

</body>
</html>

