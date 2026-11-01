<?php
/**
 * =====================================================================
 *  expedicion.php · Forja de Héroes II · La party y la mazmorra
 * =====================================================================
 *  0613 DWES · UD3 · Tarea de refuerzo (sin nota)
 *  Autor/a: Marina Rodríguez Blázquez · Variante: C
 *
 *  Estructura OBLIGATORIA (como en el paso 09 del caso guiado):
 *   1) strict_types y carga de inc/reglas.php y del fichero de datos
 *   2) CÁLCULOS: aquí arriba, sin HTML
 *   3) PRESENTACIÓN: HTML con sintaxis alternativa
 *      (foreach (...): ... endforeach;   if (...): ... else: ... endif;)
 *
 *  AHORA SÍ puedes usar if, switch, match, bucles y arrays.
 *  Todavía NO: funciones propias ni formularios (UD4).
 *
 *  R8 · Comenta cada bloque explicando QUÉ hace y POR QUÉ.
 *       Usa al menos una vez //, # y /* ... *\/.
 */

declare(strict_types=1);

require_once __DIR__ . '/inc/reglas.php';
require_once __DIR__ . '/inc/datos-' . VARIANTE . '.php';

// =====================================================================
// R2 · LA PARTY
// =====================================================================
// TODO 2.1  Recorre $party con  foreach ($party as $i => $heroe)  y calcula
//           para cada héroe: nivel, vidaMax (usa MULT_VIDA[clase]), pctVida.


foreach ($party as $i => $heroe){
    //Estructura array principal as posición => array asociativo


    //2.1.1. El nivel, que lleva +1, porque nunca se parte del nivel 0.
    $nivel= intdiv($heroe['xp'], XP_POR_NIVEL) + 1;

    //2.1.2. Vida máxima = base * multiplicador de clase
    


    $vidaMax = (int) (VIDA_BASE + $heroe['constitucion'] * $nivel * MULT_VIDA[$heroe['clase']]);


    //2.1.3. Porcentaje de vida
    $vidaActual = $heroe['vida'];
    $pctVida = round(($vidaActual /$vidaMax)* 100, 2); // Redondeamos a 2 decimales.





// TODO 2.2  (R3) Daño con match sobre la clase. SIN default (lee el enunciado).

    $dano = match ($heroe['clase']){ //Por cada clase, solo en las coincidencias de clase se aplica.

        'Guerrera' => ((($heroe['fuerza']**2)/10) +($nivel*2)),
        'Hechicero' => (($heroe['inteligencia']*3) +(($nivel**2) / 4)),
        'Pícara' => (($heroe['destreza']*2) + ($nivel**1.5)),
        'Clérigo' => ((($heroe['inteligencia']+$heroe['constitucion'])*1.5) + $nivel)
        

    };

    $poder =(int) round($dano * $nivel); //Calculamos el poder en este punto que usaremos más tarde


// TODO 2.3  (R3) Estado con if / elseif / else: K.O. · En peligro · Herido · En pie

if ($vidaActual <= 0){
    $estado ='K.O.';
}elseif ($pctVida <35){
    $estado ='En peligro';
}elseif ($pctVida <70){
    $estado = 'Herido';
}else {
    $estado ='En pie';
}





// TODO 2.4  (R3) Posición con switch: Guerrera y Clérigo → 'Primera línea'
//           (casos agrupados), Pícara → 'Flanco', Hechicero → 'Retaguardia',
//           default → 'Reserva'.
    switch ($heroe['clase']){
        case 'Guerrera':
        case 'Clérigo':
            $posicion = 'Primera línea';
            break;
        
        case 'Pícara':
            $posicion = 'Flanco';
            break;

        case 'Hechicero':
            $posicion = 'Retaguardia';
            break;

        default:
            $posicion = 'Reserva';
            break;
    }



// TODO 2.5  Barra de vida del héroe con un bucle for (prohibido str_repeat).

            //Primero calculamos los bloques rellenos:
            $bloquesLlenos = (int) round(($pctVida / 100) * BLOQUES_BARRA);

            $barra =''; //Declaramos la barra

            for ($c = 0; $c < BLOQUES_BARRA; $c++){
                if ($c <$bloquesLlenos){
                    $barra .='█';
                }else {
                    $barra .= '░';
                }

            }

// TODO 2.6  Guarda todo en la matriz: $party[$i]['nivel'] = ...; etc.
//           Claves: nivel, vidaMax, pctVida, danio, poder, estado, posicion, barra

    $party[$i]['nivel'] = $nivel;
    $party[$i]['vidaMax'] = $vidaMax;
    $party[$i]['pctVida'] = $pctVida;
    $party[$i]['dano'] = $dano;
    $party[$i]['estado'] = $estado;
    $party[$i]['poder'] =$poder;
    $party[$i]['posicion'] = $posicion;
    $party[$i]['barra'] =$barra;

}
// TODO 2.7  Totales con array_sum + array_column: $vidaParty, $vidaMaxParty,
//           $poderParty, y $nivelMedio (media).

            //Vida total del grupo
            $vidaParty = array_sum(array_column($party, 'vida'));

            //Vida máxima del grupo
            $vidaMaxParty = array_sum(array_column($party, 'vidaMax'));

            //Poder del grupo
            $poderParty = array_sum(array_column($party, 'poder'));

            //Nivel medio de la party
            $nivelesParty = array_column($party, 'nivel');
            $nivelMedio = count($nivelesParty) > 0 ? array_sum($nivelesParty) / count ($nivelesParty) : 0.0;


// TODO 2.8  Segundo foreach: $danioParty, $heroesActivos y $lider, saltando
//           con continue a los héroes K.O. ($lider empieza valiendo null).

            //Empezamos declarando las variables que necesitamos
            $danoParty    = 0.0;
            $heroesActivos = 0;
            $lider         = null;

            foreach ($party as $heroe) {
                // Si el héroe está fuera de combate, saltamos a la siguiente iteración
                if ($heroe['estado'] === 'K.O.') {
                    continue;
                }

                // Acumulamos el daño y sumamos uno a los héroes activos
                $danoParty += $heroe['dano'];
                $heroesActivos++;

                // Determinamos si es el nuevo líder (el primero activo o el de mayor poder)
                if ($lider === null || $heroe['poder'] > $lider['poder']) {
                    $lider = $heroe;
                }
            }


// TODO 2.9  $hayPicara y $hayClerigo con in_array(..., ..., true).

            //Creamos una variable que contenga el array específicamente el apartado de clase.
            $clasesParty = array_column($party, 'clase');

            //Hacemos con in_array, un boleano que recorra todo el array anteror
            $hayPicara  = in_array('Pícara', $clasesParty, true);
            $hayClerigo = in_array('Clérigo', $clasesParty, true);

// =====================================================================
// R4 · LA EXPEDICIÓN (sala a sala)
// =====================================================================
// TODO 4.1  Inicializa: $oro = $oroInicial, $xpGanada = 0, $diario = [],
//           $salasExploradas = 0.

            $oro = $oroInicial;
            $xpGanada = 0;
            $diario =[];
            $salasExploradas = 0;


// TODO 4.2  foreach ($salas as $n => $sala)  con un switch sobre $sala['tipo']:
//             'vacía'    → línea en el diario y pasar a la SIGUIENTE SALA
//                          (cuidado: continue dentro de switch... ¿1 o 2?)
//             'trampa'   → si hay Pícara, se evita; si no, resta 'valor' de vida
//             'combate'  → resta intdiv(valor, 4) de vida y suma 'valor' a la XP
//             'tesoro'   → suma 'valor' al oro y, si la sala trae 'objeto',
//                          añádelo al inventario (+1 unidad)
//             'descanso' → cura 'valor' (el doble si hay Clérigo), sin pasar
//                          de $vidaMaxParty (min)

    foreach ($salas as $d => $sala){
        //Nunca va a haber una sala cero, y se van sumando salas con cada ciclo.
        $numSala = $d +1;
        $salasExploradas++;

    switch ($sala ['tipo']){
        case 'vacía':
            $diario[] = "Sala {$numSala} - {$sala['nombre']}: vacía. La party continua a la siguiente sala.";
            continue 2; //Fuerza el reinicio del foreach, así no se realiza el 4.3

        case 'trampa':
            if ($hayPicara) {
                $diario[] = "Sala {$numSala} · {$sala['nombre']}: trampa detectada y evitada.";
            } else {
                $vidaParty -= $sala['valor'];
                $diario[] = "Sala {$numSala} · {$sala['nombre']}: trampa. La party ha perdido {$sala['valor']} de vida.";
            }
            //La party ha perdido el valor de asignado a la sala en datos-C    }
            break;


        case 'combate':
            $danoRecibido = intdiv($sala['valor'], 4);
            $vidaParty -= $danoRecibido;
            $xpGanada  += $sala['valor'];
            $diario[]  = "Sala {$numSala} · {$sala['nombre']}: combate, −{$danoRecibido} de vida y +{$sala['valor']} XP.";
            break;

        case 'tesoro':
            $oro += $sala['valor'];
            $textoObjeto = '';

            // Siempre sumaremos 1 a la unidad existente, para que no se reinicie el conteo.
            if (isset($sala['objeto'])) {
                $objeto = $sala['objeto'];
                $inventario[$objeto] = ($inventario[$objeto] ?? 0) + 1;
                $textoObjeto = " y «{$objeto}»";
        }

        $diario[] = "Sala {$numSala} · {$sala['nombre']}: +{$sala['valor']} mo{$textoObjeto}.";
            break;

        case 'descanso':
            // Si hay Clérigo, usamos un comparador ternario apra que la curación se duplique
            $curacion = $hayClerigo ? ($sala['valor'] * 2) : $sala['valor'];

            // Con min() no se supera el máximo de vida.
            $vidaParty = min($vidaMaxParty, $vidaParty + $curacion);

            $detalleClerigo = $hayClerigo ? ' (bendición del Clérigo)' : '';
            $diario[] = "Sala {$numSala} · {$sala['nombre']}: descanso, +{$curacion} de vida{$detalleClerigo}.";
            break;


    }


// TODO 4.3  Tras el switch: si $vidaParty <= 0 → vida a 0, línea en el diario
//           y break.
    if ($vidaParty <= 0) {
        $vidaParty = 0;
        $diario[] = "La party ha caído en combate en la Sala {$numSala}. Fin de la expedición.";
        break;
    }
    
    }
// TODO 4.4  $salasPendientes = salas totales exploradas.
    $salasTotales    = count($salas);
    $salasPendientes = $salasTotales - $salasExploradas;

// =====================================================================
// R5 · EL JEFE
// =====================================================================
// TODO 5.1  $tieneLlave con array_key_exists($jefe['llave'], $inventario).
//           $golpe = daño de la party redondeado a int.

            //Comprobamos que en el inventario está la llave.
            $tieneLlave =array_key_exists($jefe['llave'], $inventario);

            //Redondeamos el daño de la party
            $golpe = (int) round($danoParty);


// TODO 5.2  if / elseif / else:
//             party sin vida      → 'Derrota' (no llegó)
//             sin llave           → 'Puerta sellada'
//             en otro caso        → combate con while:
//               mientras jefe > 0 Y party > 0 Y ronda < MAX_RONDAS:
//                 ronda++; el jefe recibe $golpe (mínimo 0);
//                 si el jefe sigue vivo, la party recibe su 'ataque' (mínimo 0);
//                 añade la fila a $rondas[] = ['ronda'=>…, 'vidaJefe'=>…, 'vidaParty'=>…]
//               $resultado con match(true): Victoria · Derrota · Retirada
//               Si es Victoria, suma la recompensa al oro.

    $rondas = [];
$vidaJefe = $jefe['vida'];

    if ($vidaParty <= 0) {
        $resultado = 'Derrota'; //no llegaron
    } elseif (!$tieneLlave) {
        $resultado = 'Puerta sellada';
    } else {
    $ronda = 0;

    //Iniciamos el combate

    while ($vidaJefe > 0 && $vidaParty > 0 && $ronda < MAX_RONDAS) {
        $ronda++;

        // La party ataca primero (la vida del jefe no baja de 0)
        $vidaJefe = max(0, $vidaJefe - $golpe);

        // Si el jefe sobrevive, contraataca
        if ($vidaJefe > 0) {
            $vidaParty = max(0, $vidaParty - $jefe['ataque']);
        }

        // Guardamos el registro de la ronda
        $rondas[] = [
            'ronda'     => $ronda,
            'vidaJefe'  => $vidaJefe,
            'vidaParty' => $vidaParty,
        ];
    }

    // Cuando se cierra el while, determinamos el final del combate
    $resultado = match (true) {
        $vidaJefe === 0  => 'Victoria',
        $vidaParty === 0 => 'Derrota',
        default          => 'Retirada', // Se alcanzaron las 8 rondas sin que nadie cayera
        //Este último es para que no sea un combate eterno
        };

    // Si vencieron, el botín que consiguen
    if ($resultado === 'Victoria') {
        $oro += $jefe['recompensa'];
    }
}



// =====================================================================
// R4 (cont.) · INVENTARIO FINAL
// =====================================================================
// TODO 4.5  foreach con clave => valor: $valorInventario (cantidad × precio).

$valorInventario = 0.0;
$subtotales = [];

foreach ($inventario as $objeto => $cantidad) {
    // Si el objeto estuviera en el inventario pero no en la lista de precios, vale 0.0
    $precioUnitario = $precios[$objeto] ?? 0.0;
    $valorInventario += $cantidad * $precioUnitario;

    $subtotal = $cantidad * $precioUnitario;

    $subtotales[$objeto] = $subtotal;



}

// TODO 4.6  $totalObjetos (array_sum). Copia del inventario ordenada de mayor
//           a menor cantidad CONSERVANDO las claves. $objetoMasAbundante.

// Total de objetos que lleva el grupo
$totalObjetos = array_sum($inventario);

// Copia del inventario a ordenar
$inventarioOrdenado = $inventario;
arsort($inventarioOrdenado);

// El primer elemento tras arsort() es el que más cantidad tiene.
$objetoMasAbundante = array_key_first($inventarioOrdenado);


// =====================================================================
// R6 · EL MAPA
// =====================================================================
// TODO 6.1  Dos for anidados (filas y columnas, con count) para contar cuántas
//           veces aparece cada carácter: $conteoCeldas[$celda] = (… ?? 0) + 1;

// Conteo de frecuencias de cada tipo de celda en la matriz
$conteoCeldas = [];
$filas = count($mapa);

for ($f = 0; $f < $filas; $f++) {
    $columnas = count($mapa[$f]);
    for ($c = 0; $c < $columnas; $c++) {
        $celda = $mapa[$f][$c];
        $conteoCeldas[$celda] = ($conteoCeldas[$celda] ?? 0) + 1;
    }
}


// TODO 6.2  ¿El número de 'S' coincide con count($salas)? → $mapaCoherente

/* Comprobación de integridad entre la matriz y el listado de salas*/
$mapaCoherente = (($conteoCeldas['S'] ?? 0) === count($salas));

// TODO 6.3  Barra de vida de la party con for.

# Porcentaje final de la party evitando división por cero
$pctVidaPartyFinal = $vidaMaxParty > 0 ? ($vidaParty / $vidaMaxParty) * 100 : 0.0;

// Bloques proporcionales acotados
$bloquesPartyFinal = (int) round(($pctVidaPartyFinal / 100) * BLOQUES_BARRA);
$bloquesPartyFinal = max(0, min(BLOQUES_BARRA, $bloquesPartyFinal));

$barraVidaParty = '';
for ($b = 0; $b < BLOQUES_BARRA; $b++) {
    $barraVidaParty .= ($b < $bloquesPartyFinal) ? '█' : '░';
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Expedición · <?=$nombreMazmorra?> · Forja de Héroes II</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<main>
    <span class="insignia">Variante <?= VARIANTE ?></span>
    <h1><?=$nombreMazmorra?></h1>
   <p class="subtitulo">Informe de expedición · líder: <?= $lider !== null ? htmlspecialchars($lider['nombre']) : 'Ninguno' ?></p>
    <h2>La party</h2>
    <table>
        <tr><th>Héroe</th><th>Clase</th><th>Nivel</th><th>Vida</th><th>Daño</th><th>Poder</th><th>Posición</th><th>Estado</th></tr>
        
        <!-- TODO R7: una fila por héroe con foreach: ... endforeach;
             Estado en <td class="peligro"> si es K.O. o En peligro, y en
             <td class="vida"> en otro caso (if: ... else: ... endif;) -->
        <?php foreach ($party as $heroe): ?>
                <tr>
                    <td><?= htmlspecialchars($heroe['nombre']) ?></td>
                    <td><?= $heroe['clase'] ?></td>
                    <td><?= $heroe['nivel'] ?></td>
                    <td>
                        <span class="barra mini"><?= $heroe['barra'] ?></span> 
                        <?= $heroe['vida'] ?>/<?= $heroe['vidaMax'] ?>
                    </td>
                    <td><?= number_format($heroe['dano'], 1, ',', '.') ?></td>
                    <td><?= $heroe['poder'] ?></td>
                    <td><?= $heroe['posicion'] ?></td>
                    
                    <!-- R7: clase CSS según el estado -->
                    <?php if ($heroe['estado'] === 'K.O.' || $heroe['estado'] === 'En peligro'): ?>
                        <td class="peligro"><?= $heroe['estado'] ?></td>
                    <?php else: ?>
                        <td class="vida"><?= $heroe['estado'] ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>

    </table>

    <div class="rejilla">
        <div class="stat"><div class="etq">Poder total</div><div class="val"><?= $poderParty ?></div></div>
        <div class="stat"><div class="etq">Nivel medio</div><div class="val"><?= number_format($nivelMedio, 1, ',', '.') ?></div></div>
        <div class="stat"><div class="etq">Daño por ronda</div><div class="val"><?= number_format($danoParty, 1, ',', '.') ?></div></div>
        <div class="stat"><div class="etq">Héroes activos</div><div class="val"><?= $heroesActivos ?> / <?= count($party) ?></div></div>
    </div>

    <h2>Diario de la expedición</h2>
    <ol class="diario">
        <!-- TODO R7: un <li> por línea del diario -->
        <?php foreach ($diario as $linea): ?>
            <li><?= htmlspecialchars($linea) ?></li>
        <?php endforeach; ?>
    </ol>
    <p>Salas exploradas: <?= $salasExploradas ?> de <?= $salasTotales ?>
        <!-- TODO: solo si quedan salas sin explorar, mostrar cuántas en .peligro -->
        <?php if ($salasPendientes > 0): ?>
            · <span class="peligro">(<?= $salasPendientes ?> pendientes)</span>
        <?php endif; ?>
        · XP ganada: <?= $xpGanada ?></p>

    <h2>El jefe: <?= htmlspecialchars($jefe['nombre']) ?></h2>
    <p>Vida <?= $jefe['vida'] ?> · ataque <?= $jefe['ataque'] ?> · golpe de la party: <?= $golpe ?> por ronda</p>
    <!-- TODO R7: la tabla de rondas solo se muestra si hubo combate -->
    <?php if (count($rondas) > 0): ?> 
    <table>
        <tr><th>Ronda</th><th>Vida del jefe</th><th>Vida de la party</th></tr>
        <?php foreach ($rondas as $r): ?>
            <tr>
                <td><?= $r['ronda'] ?></td>
                <td><?= $r['vidaJefe'] ?></td>
                <td><?= $r['vidaParty'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>

    <p class="resultado <?= $resultado === 'Victoria' ? 'vida' : 'peligro' ?>"><strong><?= $resultado ?></strong> · <?php if (isset($motivoJefe)): ?>
        · <?= $motivoJefe ?>
    <?php endif; ?></p>
    <p class="barra vida">PARTY <span class="barra"><?= $barraVidaParty ?></span> <?= $vidaParty ?>/<?= $vidaMaxParty ?></p>

    <h2>Inventario</h2>
    <table>
        <tr><th>Objeto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
        <!-- TODO R7: una fila por objeto del inventario ORDENADO -->
        <?php foreach ($inventarioOrdenado as $objeto => $cantidad): ?>
        <tr>
            <td><?= htmlspecialchars($objeto) ?></td>
            <td><?= $cantidad ?></td>
            <td><?= number_format($precios[$objeto] ?? 0.0, 2, ',', '.') ?> mo</td>
            <td><?= number_format($subtotales[$objeto] ?? 0.0, 2, ',', '.') ?> mo</td>
        </tr>
    <?php endforeach; ?>
    </table>
   <div class="rejilla">
    <div class="stat"><div class="etq">Objetos</div><div class="val"><?= $totalObjetos ?></div></div>
    <div class="stat"><div class="etq">Valor del inventario</div><div class="val"><?= number_format($valorInventario, 2, ',', '.') ?> mo</div></div>
    <div class="stat"><div class="etq">Oro final</div><div class="val"><?= number_format($oro, 2, ',', '.') ?> mo</div></div>
    <div class="stat"><div class="etq">Más abundante</div><div class="val peq"><?= htmlspecialchars($objetoMasAbundante) ?> (<?= $inventarioOrdenado[$objetoMasAbundante] ?? 0 ?>)</div></div>
</div>

    <h2>Mapa de la mazmorra</h2>
    <!-- TODO R7: tabla con foreach anidados (filas y celdas). Cada <td> lleva
         la clase CSS de LEYENDA_MAPA[$celda][0] y el carácter dentro. -->
    <table class="mapa">
        <?php foreach ($mapa as $fila): ?>
        <tr>
            <?php foreach ($fila as $celda): ?>
                <td class="<?= LEYENDA_MAPA[$celda][0] ?? 'suelo' ?>"><?= $celda ?></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </table>
    <p><!-- TODO: leyenda con el recuento de cada carácter -->
        <strong>Leyenda:</strong>
    <?php foreach (LEYENDA_MAPA as $caracter => $datos): ?>
        <span class="<?= $datos[0] ?>"><?= $caracter ?></span> <?= $datos[1] ?>: <?= $conteoCeldas[$caracter] ?? 0 ?> |
    <?php endforeach; ?>
    </p>
    <!-- TODO: mensaje de coherencia del mapa (if: else: endif;) -->
<?php if ($mapaCoherente): ?>
    <p class="vida">✔ Coherencia confirmada: el número de salas en el mapa coincide con la expedición.</p>
<?php else: ?>
    <p class="peligro">✖ Incoherencia detectada: el mapa no tiene el mismo número de salas que el registro.</p>
<?php endif; ?>
    <footer>Informe generado el <?= date('d/m/Y H:i') ?> con PHP <?= PHP_VERSION ?> · cada recarga lo recalcula todo desde cero.</footer>
</main>
</body>
</html>
