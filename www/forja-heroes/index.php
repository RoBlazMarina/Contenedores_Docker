<?php
/**
 * =====================================================================
 *  ficha.php · Forja de Héroes · UD2 · Reto evaluable
 * =====================================================================
 *  Autor/a:  · Variante: B
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
        $fuerza= (int) $fuerzaTxt;
        $destreza= (int) $destrezaTxt;
        $inteligencia= (int) $inteligenciaTxt;
        $constitucion= (int) $constitucionTxt;
        $experiencia= (int) $experienciaTxt;
        $vidaActual= (int) $vidaActualTxt;
        $oro=(float) $oroTxt;

// R2 · Progresión (nivel, xpEnNivel, xpParaSubir, pctNivel)

    $nivel= intdiv ($experiencia, XP_POR_NIVEL) +1;
    $xpEnNivel= $experiencia % XP_POR_NIVEL;
    $xpParaSubir=XP_POR_NIVEL- $xpEnNivel;
    $pcNivel= ($xpEnNivel*100)/XP_POR_NIVEL;




// R3 · Vida (vidaMax, pctVida)
    $vidaMax = VIDA_BASE + (( $constitucionTxt * $nivel ) * MULT_VIDA);
    $pctVida = ($vidaActual / $vidaMax) * 100;
    $pctVidaFormateado = number_format($pctVida, 1, ',', '.'); //Salían demasiados decimales...

// R4 · Combate (daño, estadística especial, poder, comparación con el rival)
    $danio = (($inteligencia)*3) + (($nivel**2)/4);
    $mana= ($inteligencia * 10) + ($experiencia % 100);
    $poder = (int) round($danio * $nivel);
    
    //Aquí iniciamos el operador terciario
    $comparacion = $poder <=> PODER_RIVAL;

    $veredicto = ($comparacion === 1)
        ? "Ventaja: ¡a la carga!"
        : (($comparacion === 0) //Si la primera condición no se cumple, se pasa a la segunda, que puede o no cumplirse.
            ? "Empate: combate igualado"
            : "Desventaja: mejor retirarse");

// R5 · Estado y decisiones

    $estado = ($pctVida <= 0)
        ? "K.O."
        :(($pctVida > 35)
            ? "En pie"
            : "En peligro");

    $puedeAscender = ($nivel >= 5 && $pctVida >= 50) ? 'Sí' : 'No';

    $necesitaPocion = ($pctVida < 40 || !$esVeterano) ? 'Sí' : 'No';

    $insignia = $esVeterano ? 'VETERANO' : 'NOVATO';

// R6 · Barras de progreso



$llenosVida =(int) round (($pctVida/100) * BLOQUES_BARRA);
$vaciosVida = BLOQUES_BARRA - $llenosVida;

$llenosXP =(int) round(($pcNivel/100) * BLOQUES_BARRA);
$vaciosXP = BLOQUES_BARRA - $llenosXP;

$barraVida = str_repeat('█', $llenosVida) . str_repeat('░', $vaciosVida);
$barraXp = str_repeat('█', $llenosXP) . str_repeat('░', $vaciosXP);



// R7 · Textos (htmlspecialchars, ?? , ?: , number_format, .= , heredoc)

$apodo =$apodo ?? 'Sin apodo'; //Esto es para que no salga en blanco el parámetro.
$lema = $lema ?: 'Sin lema (todavía)';

// 1. Oro: 2 decimales, coma para decimales, punto para miles + sufijo ' mo'
$oroTxtFormateado = number_format($oro, 2, ',', '.') . ' mo';

// 2. Daño: 1 decimal, coma decimal, punto de miles (opcional por si supera 1.000)
$danioFormateado = number_format($danio, 1, ',', '.');

// 3. Registro de combate (construido con .= en al menos tres pasos: hora, nombre, nivel, rival y veredicto.)
$nombreRival= NOMBRE_RIVAL;

$registroCombate = '[' . date('H:i:s') . '] ';
$registroCombate .= "{$nombreHeroe} (Nv. {$nivel}) avista a {$nombreRival}. ";
$registroCombate .= "Veredicto del combate: {$veredicto}";

// 4. Crónica de tres líneas con heredoc (que interpole nombre, clase, nivel, XP que falta y estado)
$claseHeroe = CLASE_HEROE; // Las constantes no se interpolan bien con heredoc
$cronica = <<<CRONICA
El heroe {$nombreHeroe} ({$claseHeroe}), con nivel {$nivel}, avanza en su viaje.
Aun le faltan {$xpParaSubir} XP para alcanzar el siguiente rango de poder.
Actualmente su estado es: {$estado}.
CRONICA;


//5. Depuración de buffer (para capturar los var_dump limpios)

ob_start();
echo "experienciaTxt -> " . get_debug_type($experienciaTxt) . PHP_EOL;
echo "experiencia    -> " . get_debug_type($experiencia) . PHP_EOL;
var_dump($oro);
var_dump($pctVida);
var_dump($esVeterano);
var_dump(null); // O la variable de apodo original para que imprima NULL
var_dump($experienciaTxt == $experiencia);
var_dump($experienciaTxt === $experiencia);
$depuracion = ob_get_clean();

// 6. Pie de página
$fechaGeneracion = date('d/m/Y H:i:s');


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ficha de <?= htmlspecialchars($nombreHeroe) ?> </title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<main>
    <!-- TODO: insignia VETERANO/NOVATO, usando la etiqueta LARGA con echo -->
    <span class="insignia"><?php echo $insignia; ?></span>
    <!-- TODO: a partir de aquí, usa la etiqueta CORTA de salida -->
    <h1><?= htmlspecialchars($nombreHeroe) ?></h1>
    <p class="subtitulo"> <?= CLASE_HEROE ?> · <?=$apodo?> · <?=$lema?> </p>

    <h2>Estadísticas</h2>
    <div class="rejilla">
        <div class="stat"><div class="etq">Nivel</div><div class="val"><?= $nivel ?></div></div>
        <div class="stat"><div class="etq">Vida</div><div class="val"><?= $vidaActualTxt?> / <?= $vidaMax?></div></div>
        <div class="stat"><div class="etq">Daño</div><div class="val"><?= $danioFormateado?></div></div>
        <div class="stat"><div class="etq"><?= ESTADISTICA_ESPECIAL?></div><div class="val"><?= $mana?></div></div>
        <div class="stat"><div class="etq">Poder</div><div class="val"><?= $poder?></div></div>
        <div class="stat"><div class="etq">Oro</div><div class="val"><?= $oroTxtFormateado?></div></div>
    </div>
    <table>
        <tr><th>Fuerza</th><th>Destreza</th><th>Inteligencia</th><th>Constitución</th></tr>
        <tr><td><?=$fuerzaTxt?></td><td><?=$destrezaTxt?></td><td><?=$inteligenciaTxt?></td><td><?=$constitucionTxt?></td></tr>
    </table>

    <h2>Progreso</h2>
    <p class="barra vida">VIDA <?= $barraVida ?> · <?= $pctVidaFormateado ?> %</p>
    <p class="barra xp">XP <?= $barraXp ?> · <?= $xpEnNivel?>/ <?= XP_POR_NIVEL?> (faltan <?= $xpParaSubir?>)</p>

    <h2>Estado y decisiones</h2>
    <table>
        <tr><td>Estado</td><td><?= $estado ?></td></tr>
        <tr><td>¿Puede ascender de rango? </td><td><?= $puedeAscender ?></td></tr>
        <tr><td>¿Necesita poción? </td><td><?=$necesitaPocion?></td></tr>
        <tr><td>Rival: <?=NOMBRE_RIVAL?> (<?=PODER_RIVAL?>)</td><td><?=$veredicto?></td></tr>
    </table>

    <h2>Crónica</h2>
    <pre><?= htmlspecialchars($cronica) ?></pre>
    <p><em><?= htmlspecialchars($registroCombate) ?></em></p>

    <h2>Modo depuración</h2>
    <pre><?= htmlspecialchars($depuracion) ?></pre>

    <footer>Generado el <?= $fechaGeneracion ?> · PHP <?= PHP_VERSION ?> ·
        <a href="diagnostico.php">Diagnóstico del servidor</a></footer>
</main>
</body>
</html>
