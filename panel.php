<?php


session_start();


if (!isset($_SESSION["id_cuenta"])) {
    header("Location: login.php?error=sesion");
    exit;
}


require_once "conexion.php";


$nombre_usuario = $_SESSION["nombre"] ?? "Usuario";
$apellidos_usuario = $_SESSION["apellidos"] ?? "";


$total_clientes = 0;
$total_coches = 0;
$total_entregados = 0;
$total_estacionados = 0;


function obtenerTotal($conexion, $sql)
{
    $consulta = $conexion->query($sql);


    if (!$consulta) {
        return 0;
     }


     $fila = $consulta->fetch_assoc();
     return intval($fila["total"] ?? 0);
}


$total_clientes = obtenerTotal(
    $conexion,
    "SELECT COUNT(*) AS total FROM clientes"
);


$total_coches = obtenerTotal(
    $conexion,
    "SELECT COUNT(*) AS total FROM vehiculos"
);


$total_entregados = obtenerTotal(
    $conexion,
    "SELECT COUNT(*) AS total
     FROM vehiculos
     WHERE estado = 'Entregado'"
);


$total_estacionados = obtenerTotal(
    $conexion,
    "SELECT COUNT(*) AS total
     FROM vehiculos
     WHERE estado = 'Estacionado'"
);


?>


<!DOCTYPE html>
<html lang="es">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-
scale=1.0">
    <title>Panel de control</title>
    <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="accesibilidad.css">
</head>


<body>

<?php include "barra_accesibilidad.php"; ?>


<div class="panel-main panel-dashboard">
   <header class="panel-header">


         <div>
             <h1 class="panel-titulo">PANEL DE CONTROL</h1>


             <p class="panel-bienvenida">
                  Bienvenido,
                  <strong>
                      <?php
                      echo htmlspecialchars(
                          $nombre_usuario . " " . $apellidos_usuario
                      );
                      ?>
                  </strong>
             </p>
         </div>


         <a href="logout.php" class="btn-cerrar-sesion">
             CERRAR SESIÓN
         </a>


   </header>


   <section class="panel-estadisticas">


         <div class="panel-card-estadistica">
             <h2>CLIENTES</h2>
             <p class="panel-numero"><?php echo $total_clientes; ?></p>
         </div>


         <div class="panel-card-estadistica">
             <h2>VEHÍCULOS</h2>
             <p class="panel-numero"><?php echo $total_coches; ?></p>
         </div>


         <div class="panel-card-estadistica">
             <h2>ESTACIONADOS</h2>
             <p class="panel-numero"><?php echo $total_estacionados;
?></p>
         </div>


         <div class="panel-card-estadistica">
             <h2>ENTREGADOS</h2>
             <p class="panel-numero"><?php echo $total_entregados; ?></p>
         </div>
   </section>


   

   <section class="panel-acciones">


         <h2 class="panel-subtitulo">¿QUÉ HAREMOS HOY?</h2>


         <div class="panel-opciones">


            <div class="panel-opcion">
                <h3>Registrar usuario y vehículo</h3>
                <p>Guarda los datos del cliente y de su automóvil.</p>
                <a href="register_usuario.php" class="panel-boton">
                    REGISTRAR
                </a>
            </div>


            <div class="panel-opcion">
                <h3>Consultar vehículos</h3>
                <p>Busca un vehículo y registra su entrega.</p>
                <a href="vehiculos.php" class="panel-boton">
                    CONSULTAR
                </a>
            </div>


            <div class="panel-opcion">
                <h3>Historial de entregas</h3>
                <p>Consulta los vehículos que ya fueron entregados.</p>
                <a href="historial.php" class="panel-boton">
                    VER HISTORIAL
                </a>
            </div>


         </div>


   </section>


</div>
    <script src="accesibilidad.js"></script>

        </main>

    <!-- AQUÍ VA EL FOOTER -->

    <footer class="footer-proyecto">

        <p>© 2026 Valet Parking. Todos los derechos reservados.</p>

        <p>Desarrollado por Gerardo Vargas Jiménez</p>

        <p>
            Contacto:
            <a href="mailto:gerar.do.vaj33@gmail.com">
                gerar.do.vaj33@gmail.com
            </a>
        </p>

    </footer>

    <script src="accesibilidad.js"></script>

</body>
</html>

</body>
</html>

