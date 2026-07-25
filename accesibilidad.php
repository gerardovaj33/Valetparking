<?php
session_start();


$regreso = isset($_SESSION["id_cuenta"])
    ? "panel.php"
    : "index.php";
?>
<!DOCTYPE html>
<html lang="es">


<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Accesibilidad</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="accesibilidad.css">
</head>


<body>



    <main class="accesibilidad-main">


         <section class="accesibilidad-card">


             <h1>ACCESIBILIDAD</h1>


             <p>
                    Cambia la apariencia del sistema según tus necesidades.
                    Las opciones se guardan automáticamente en este
navegador.
             </p>
             <div class="accesibilidad-opciones">


                    <button
                        type="button"
                        class="accesibilidad-boton"
                        id="toggleDaltonico"
                    >
                        Vista para daltónicos
                    </button>


                    <button
                        type="button"
                        class="accesibilidad-boton"
                        id="toggleTextoGrande"
                    >
                        Aumentar texto
                    </button>


                    <button
                        type="button"
                        class="accesibilidad-boton"
                        id="toggleContraste"
                    >
                        Alto contraste
                    </button>


             </div>


             <a
                    href="<?php echo $regreso; ?>"
                    class="accesibilidad-regresar"
             >
                    REGRESAR
             </a>


          </section>


   </main>


   <script src="accesibilidad.js"></script>


</body>
</html>