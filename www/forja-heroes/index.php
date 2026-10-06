<?php
/**
 * =====================================================================
 *  ficha.php · Forja de Héroes · UD2 · Reto evaluable
 * =====================================================================
 *  Autor/a: Marina Rodríguez Blázquez · Variante: B
 *
 *  Estructura OBLIGATORIA (como en el paso 09 del caso guiado):
 *   1) directiva strict_types
 *   2) carga de inc/heroe.php con require_once y __DIR__
 *   3) CÁLCULOS: aquí arriba, sin HTML
 *   4) PRESENTACIÓN: HTML con los valores incrustados
 *
 *  PROHIBIDO en este reto: if, else, switch, match, bucles, arrays propios
 *  y funciones propias (llegan en la UD3 y la UD4). Usa operadores,
 *  el ternario y los operadores ?? y ?:.
 */

// TODO 1. strict_types
declare(strict_types=1);

// TODO 2. require_once del fichero de datos
require_once __DIR__ . '/inc/heroe.php';
// ---------------------------------------------------------------------
// TODO 3. CÁLCULOS (sigue el orden del enunciado: R1 a R8)
// ---------------------------------------------------------------------
// R1 · Conversión de tipos
        $fuerzaTxt= (int) $fuerzaTxt;
        $destrezaTxt= (int) $destrezaTxt;
        $inteligenciaTxt= (int) $inteligenciaTxt;
        $constitucionTxt= (int) $constitucionTxt;
        $experienciaTxt= (int) $experienciaTxt;
        $vidaActualTxt= (int) $vidaActualTxt;
        $oroTxt=(float) $oroTxt;

// R2 · Progresión (nivel, xpEnNivel, xpParaSubir, pctNivel)

    $nivel= intdiv ($experienciaTxt, XP_POR_NIVEL) +1;
    $xpEnNivel= $experienciaTxt % XP_POR_NIVEL;
    $xpParaSubir=XP_POR_NIVEL- $xpEnNivel;
    $pcNivel= ($xpEnNivel*100)/XP_POR_NIVEL;




// R3 · Vida (vidaMax, pctVida)
    $vidaMax = VIDA_BASE + (( $constitucionTxt * $nivel ) * MULT_VIDA);
    $pctVida = ($vidaActualTxt / $vidaMax) * 100;

// R4 · Combate (daño, estadística especial, poder, comparación con el rival)
    $danio = (($inteligenciaTxt)*3) + (($nivel**2)/4);
    $mana= ($inteligenciaTxt * 10) + ($experienciaTxt % 100);
    $poder = (int) round($danio * $nivel);
    
    //Aquí iniciamos el operador terciario
    $comparacion = $poder <=> PODER_RIVAL;

    $veredicto = ($comparacion === 1)
        ? "Ventaja: ¡a la carga!"
        : (($comparacion === 0) //Si la primera condición no se cumple, se pasa a la segunda, que puede o no cumplirse.
            ? "Empate: combate igualado"
            : "Desventaja: mejor retirarse");

// R5 · Estado y decisiones

    $comparaciónvida = $pctVida <=> 0;
    $estado = ($comparaciónvida > 0)
        ? (($comparaciónvida > 35)
            ? "En pie"
            : "En peligro")
        : "K.O.";


// R6 · Barras de progreso
// R7 · Textos (htmlspecialchars, ?? , ?: , number_format, .= , heredoc)

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TODO: "Ficha de " + $nombreHeroe </title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<main>
    <!-- TODO: insignia VETERANO/NOVATO, usando la etiqueta LARGA con echo -->
    <span class="insignia">TODO</span>
    <!-- TODO: a partir de aquí, usa la etiqueta CORTA de salida -->
    <h1><?= $nombreHeroe ?></h1>
    <p class="subtitulo"> <?= CLASE_HEROE ?> · <?=$apodo?> · <?=$lema?> </p>

    <h2>Estadísticas</h2>
    <div class="rejilla">
        <div class="stat"><div class="etq">Nivel</div><div class="val">TODO</div></div>
        <div class="stat"><div class="etq">Vida</div><div class="val">TODO actual / máxima</div></div>
        <div class="stat"><div class="etq">Daño</div><div class="val">TODO (1 decimal, coma)</div></div>
        <div class="stat"><div class="etq">TODO estadística especial</div><div class="val">TODO</div></div>
        <div class="stat"><div class="etq">Poder</div><div class="val">TODO</div></div>
        <div class="stat"><div class="etq">Oro</div><div class="val">TODO (formato español) mo</div></div>
    </div>
    <table>
        <tr><th>Fuerza</th><th>Destreza</th><th>Inteligencia</th><th>Constitución</th></tr>
        <tr><td><?=$fuerzaTxt?></td><td><?=$destrezaTxt?></td><td><?=$inteligenciaTxt?></td><td><?=$constitucionTxt?></td></tr>
    </table>

    <h2>Progreso</h2>
    <p class="barra vida">VIDA TODO barra · porcentaje %</p>
    <p class="barra xp">XP&nbsp;&nbsp; TODO barra · xpEnNivel/XP_POR_NIVEL (faltan N)</p>

    <h2>Estado y decisiones</h2>
    <table>
        <tr><td>Estado</td><td><?= $estado ?></td></tr>
        <tr><td>¿Puede ascender de rango? (nivel ≥ 5 y vida ≥ 50 %)</td><td>TODO Sí/No</td></tr>
        <tr><td>¿Necesita poción? (vida &lt; 40 % o no veterano)</td><td>TODO Sí/No</td></tr>
        <tr><td>Rival: <?=NOMBRE_RIVAL?> (<?=PODER_RIVAL?>)</td><td>TODO resultado de la nave espacial → veredicto</td></tr>
    </table>

    <h2>Crónica</h2>
    <pre>TODO descripción (heredoc)</pre>
    <p><em>TODO registro de combate (.=)</em></p>

    <h2>Modo depuración</h2>
    <pre>TODO: tipos antes/después de convertir, var_dump y == frente a ===</pre>

    <footer>TODO fecha y hora de generación · versión de PHP ·
        <a href="diagnostico.php">Diagnóstico del servidor</a></footer>
</main>
</body>
</html>
