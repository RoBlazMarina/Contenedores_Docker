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
    $party[$i]['dano'] = round($dano, 2); //En este caso redondeamos el daño.
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
            $danioParty    = 0.0;
            $heroesActivos = 0;
            $lider         = null;

            foreach ($party as $heroe) {
                // Si el héroe está fuera de combate, saltamos a la siguiente iteración
                if ($heroe['estado'] === 'K.O.') {
                    continue;
                }

                // Acumulamos el daño y sumamos uno a los héroes activos
                $danioParty += $heroe['danio'];
                $heroesActivos++;

                // Determinamos si es el nuevo líder (el primero activo o el de mayor poder)
                if ($lider === null || $heroe['poder'] > $lider['poder']) {
                    $lider = $heroe;
                }
            }


// TODO 2.9  $hayPicara y $hayClerigo con in_array(..., ..., true).

            $clasesParty = array_column($party, 'clase');

            $hayPicara  = in_array('Pícara', $clasesParty, true);
            $hayClerigo = in_array('Clérigo', $clasesParty, true);

// =====================================================================
// R4 · LA EXPEDICIÓN (sala a sala)
// =====================================================================
// TODO 4.1  Inicializa: $oro = $oroInicial, $xpGanada = 0, $diario = [],
//           $salasExploradas = 0.


// TODO 4.2  foreach ($salas as $n => $sala)  con un switch sobre $sala['tipo']:
//             'vacía'    → línea en el diario y pasar a la SIGUIENTE SALA
//                          (cuidado: continue dentro de switch... ¿1 o 2?)
//             'trampa'   → si hay Pícara, se evita; si no, resta 'valor' de vida
//             'combate'  → resta intdiv(valor, 4) de vida y suma 'valor' a la XP
//             'tesoro'   → suma 'valor' al oro y, si la sala trae 'objeto',
//                          añádelo al inventario (+1 unidad)
//             'descanso' → cura 'valor' (el doble si hay Clérigo), sin pasar
//                          de $vidaMaxParty (min)
// TODO 4.3  Tras el switch: si $vidaParty <= 0 → vida a 0, línea en el diario
//           y break.
// TODO 4.4  $salasPendientes = salas totales − exploradas.

// =====================================================================
// R5 · EL JEFE
// =====================================================================
// TODO 5.1  $tieneLlave con array_key_exists($jefe['llave'], $inventario).
//           $golpe = daño de la party redondeado a int.
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

// =====================================================================
// R4 (cont.) · INVENTARIO FINAL
// =====================================================================
// TODO 4.5  foreach con clave => valor: $valorInventario (cantidad × precio).


// TODO 4.6  $totalObjetos (array_sum). Copia del inventario ordenada de mayor
//           a menor cantidad CONSERVANDO las claves. $objetoMasAbundante.

// =====================================================================
// R6 · EL MAPA
// =====================================================================
// TODO 6.1  Dos for anidados (filas y columnas, con count) para contar cuántas
//           veces aparece cada carácter: $conteoCeldas[$celda] = (… ?? 0) + 1;




// TODO 6.2  ¿El número de 'S' coincide con count($salas)? → $mapaCoherente
// TODO 6.3  Barra de vida de la party con for.

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
    <span class="insignia">VARIANTE TODO</span>
    <h1><?=$nombreMazmorra?></h1>
    <p class="subtitulo">Informe de expedición · líder: TODO</p>

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
        <div class="stat"><div class="etq">Poder total</div><div class="val">TODO</div></div>
        <div class="stat"><div class="etq">Nivel medio</div><div class="val">TODO (1 decimal)</div></div>
        <div class="stat"><div class="etq">Daño por ronda</div><div class="val">TODO (1 decimal)</div></div>
        <div class="stat"><div class="etq">Héroes activos</div><div class="val">TODO / TODO</div></div>
    </div>

    <h2>Diario de la expedición</h2>
    <ol class="diario">
        <!-- TODO R7: un <li> por línea del diario -->
    </ol>
    <p>Salas exploradas: TODO de TODO
        <!-- TODO: solo si quedan salas sin explorar, mostrar cuántas en .peligro -->
        · XP ganada: TODO</p>

    <h2>El jefe: TODO</h2>
    <p>Vida TODO · ataque TODO · golpe de la party: TODO por ronda</p>
    <!-- TODO R7: la tabla de rondas solo se muestra si hubo combate -->
    <table>
        <tr><th>Ronda</th><th>Vida del jefe</th><th>Vida de la party</th></tr>
    </table>
    <p class="resultado TODO-vida-o-peligro"><strong>TODO resultado</strong> · TODO motivo</p>
    <p class="barra vida">PARTY TODO barra vida/vidaMax</p>

    <h2>Inventario</h2>
    <table>
        <tr><th>Objeto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
        <!-- TODO R7: una fila por objeto del inventario ORDENADO -->
    </table>
    <div class="rejilla">
        <div class="stat"><div class="etq">Objetos</div><div class="val">TODO</div></div>
        <div class="stat"><div class="etq">Valor del inventario</div><div class="val">TODO mo</div></div>
        <div class="stat"><div class="etq">Oro final</div><div class="val">TODO mo</div></div>
        <div class="stat"><div class="etq">Más abundante</div><div class="val peq">TODO</div></div>
    </div>

    <h2>Mapa de la mazmorra</h2>
    <!-- TODO R7: tabla con foreach anidados (filas y celdas). Cada <td> lleva
         la clase CSS de LEYENDA_MAPA[$celda][0] y el carácter dentro. -->
    <table class="mapa">
    </table>
    <p><!-- TODO: leyenda con el recuento de cada carácter --></p>
    <!-- TODO: mensaje de coherencia del mapa (if: else: endif;) -->

    <footer>Informe generado el TODO con PHP TODO · cada recarga lo recalcula todo desde cero.</footer>
</main>
</body>
</html>
