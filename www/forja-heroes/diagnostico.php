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

    /*Inicializamos las directivas*/
$iniPrincipal = php_ini_loaded_file() ?: 'Ninguno (configuración por defecto)';
$iniScanned = php_ini_scanned_files();
$iniAdicionales =($iniScanned !== false && $iniScanned !== '') ? $iniScanned : 'Ninguno';

    //Lecturas de forja.ini

    $valDisplayErrors  = ini_get('display_errors');
    $valErrorReporting = ini_get('error_reporting');
    $valDateTimezone   = ini_get('date.timezone');
    $valShortOpenTag   = ini_get('short_open_tag');
    $valExposePhp      = ini_get('expose_php');
    $valMemoryLimit    = ini_get('memory_limit');

//Comprobamos el error_reporting
$esEAll = (error_reporting() === E_ALL) ? ' (Coincide con E_ALL)' : ' (No coincide con E_ALL)';
$txtErrorReporting = $valErrorReporting . $esEAll;

$pqDisplayErrors  = 'En desarrollo se activa (On/1) para ver errores de inmediato en pantalla.';
$pqErrorReporting = 'E_ALL reporta todos los niveles de fallos, avisos y advertencias.';
$pqDateTimezone   = 'Sincroniza el huso horario con la hora local de la península (Europe/Madrid).';
$pqShortOpenTag   = 'Habilita la etiqueta de apertura corta <? en el servidor.';
$pqExposePhp      = 'En producción se apaga para no exponer la presencia de PHP en cabeceras HTTP.';
$pqMemoryLimit    = 'Tope de memoria de 256M para evitar que un script sature la RAM.';

//- Cambiar date.timezone con ini_set, mostrar la hora antes y después,
//    el valor devuelto por ini_set y restaurarla con ini_restore

$horaAntes = date('H:i:s (T)');
$retornoTimezone = ini_set('date.timezone', 'America/New_York');
$horaDespues = date('H:i:s (T)');
ini_restore('date.timezone');
$horaRestaurada = date('H:i:s (T)');

// - Intentar cambiar short_open_tag con ini_set y mostrar el resultado

$shortAntes = ini_get('short_open_tag');
$retornoShort = ini_set('short_open_tag', '0'); // Devuelve false
$shortDespues = ini_get('short_open_tag');
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
            <tr>
                <td><code>display_errors</code></td>
                <td><?= htmlspecialchars($valDisplayErrors) ?></td>
                <td><?= htmlspecialchars($pqDisplayErrors) ?></td>
            </tr>
            <tr>
                <td><code>error_reporting</code></td>
                <td><?= htmlspecialchars($txtErrorReporting) ?></td>
                <td><?= htmlspecialchars($pqErrorReporting) ?></td>
            </tr>
            <tr>
                <td><code>date.timezone</code></td>
                <td><?= htmlspecialchars($valDateTimezone) ?></td>
                <td><?= htmlspecialchars($pqDateTimezone) ?></td>
            </tr>
            <tr>
                <td><code>short_open_tag</code></td>
                <td><?= htmlspecialchars($valShortOpenTag) ?></td>
                <td><?= htmlspecialchars($pqShortOpenTag) ?></td>
            </tr>
            <tr>
                <td><code>expose_php</code></td>
                <td><?= htmlspecialchars($valExposePhp ?: '0 (Off)') ?></td>
                <td><?= htmlspecialchars($pqExposePhp) ?></td>
            </tr>
            <tr>
                <td><code>memory_limit</code></td>
                <td><?= htmlspecialchars($valMemoryLimit) ?></td>
                <td><?= htmlspecialchars($pqMemoryLimit) ?></td>
            </tr>
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

    <footer><a href="ficha.php">← Volver a la ficha</a></footer>
</main>
</body>
</html>