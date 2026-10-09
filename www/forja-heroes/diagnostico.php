<?php
/**
 * =====================================================================
 *  diagnostico.php · Panel de directivas del servidor          [CE 2.f]
 * =====================================================================
 *  Autor/a: Marina Rodríguez Blázquez
 *
 *   (ver requisito R9 del enunciado):
 *

 *
 *   - Cambiar date.timezone con ini_set, mostrar la hora antes y después,
 *     el valor devuelto por ini_set y restaurarla con ini_restore
 *   - Intentar cambiar short_open_tag con ini_set y mostrar el resultado
 */

//- strict_types
declare(strict_types=1);

//- Leer con ini_get las 6 directivas de tu forja.ini y comprobar
//   error_reporting() con E_ALL

$directivas = [
    'display_errors',
    'error_reporting',
    'date.timezone',
    'short_open_tag',
    'expose_php',
    'memory_limit'
];

//- Mostrar qué ficheros .ini adicionales ha cargado PHP

$iniAdicionales = php_ini_scanned_files();

//- Cambiar date.timezone con ini_set, mostrar la hora antes y después,
//    el valor devuelto por ini_set y restaurarla con ini_restore

$horaAntes = date('H:i:s (T)');
$timezoneAnterior = ini_set('date.timezone', 'America/New_York');
$horaDespues = date('H:i:s (T)');
ini_restore('date.timezone');
$horaRestaurada = date('H:i:s (T)');

// - Intentar cambiar short_open_tag con ini_set y mostrar el resultado

$shortOpenTagAntes = ini_get('short_open_tag');
$resultadoCambioShort = ini_set('short_open_tag', '0'); // Devuelve false si no puede cambiarse
$shortOpenTagDespues = ini_get('short_open_tag');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Diagnóstico del servidor · Forja de Héroes</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<main>
    <h1>Diagnóstico del servidor</h1>
    <p class="subtitulo">PHP <?= PHP_VERSION ?> · <?= PHP_OS ?></p>

    <h2>Directivas configuradas en php/forja.ini</h2>
    <table>
        <thead>
            <tr>
                <th>Directiva</th>
                <th>Valor (ini_get)</th>
                <th>Por qué</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($directivasInfo as $directiva => $info): ?>
                <tr>
                    <td><code><?= htmlspecialchars($directiva) ?></code></td>
                    <td><?= htmlspecialchars((string) $info['valor']) ?></td>
                    <td><?= htmlspecialchars($info['por_que']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><strong>Fichero .ini principal:</strong> <?= htmlspecialchars($iniPrincipal) ?></p>
    <p><strong>Ficheros .ini adicionales:</strong> <?= htmlspecialchars($iniAdicionales) ?></p>

    <h2>Cambio en tiempo de ejecución (ini_set)</h2>
    <table>
        <thead>
            <tr>
                <th>Prueba / Directiva</th>
                <th>Resultado de la comprobación</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>date.timezone</code> (Hora inicial)</td>
                <td><?= htmlspecialchars($horaAntes) ?></td>
            </tr>
            <tr>
                <td><code>date.timezone</code> (Retorno de ini_set)</td>
                <td><?= var_export($retornoTimezone, true) ?></td>
            </tr>
            <tr>
                <td><code>date.timezone</code> (Hora en America/New_York)</td>
                <td><?= htmlspecialchars($horaDespues) ?></td>
            </tr>
            <tr>
                <td><code>date.timezone</code> (Hora tras ini_restore)</td>
                <td><?= htmlspecialchars($horaRestaurada) ?></td>
            </tr>
            <tr>
                <td><code>short_open_tag</code> (Valor inicial)</td>
                <td><?= var_export((bool)$shortAntes, true) ?></td>
            </tr>
            <tr>
                <td><code>short_open_tag</code> (Retorno de ini_set a 0)</td>
                <td><?= var_export($retornoShort, true) ?> (Falla: devuelve false)</td>
            </tr>
            <tr>
                <td><code>short_open_tag</code> (Valor tras intento)</td>
                <td><?= var_export((bool)$shortDespues, true) ?> (Sin cambios)</td>
            </tr>
        </tbody>
    </table>

    <footer><a href="index.php">← Volver a la ficha</a></footer>
</main>
</body>
</html>