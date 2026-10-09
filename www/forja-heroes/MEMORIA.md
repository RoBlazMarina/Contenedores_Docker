# Memoria · Reto UD2 «Forja de Héroes»

**Alumno/a:** TODO · **Variante:** TODO · **Fecha:** TODO

> Las capturas van en la carpeta `capturas/` y se enlazan así: `![descripción](capturas/nombre.png)`

## 1. Comprobación de resultados (CE 2.e)

Rellena la tabla con los valores que muestra TU ficha y compáralos con la tabla de comprobación del enunciado.

| Dato | Valor esperado (enunciado) | Valor de mi ficha | ¿Coincide? |
|---|---|---|---|
| Nivel | 8 | 8 | Sí |
| Vida máxima | 130 | 130 | Sí |
| % de vida | 31,5% | 31,5% | Sí |
| Daño | 73,0 | 73,0 | Sí |
| Estadística especial (Maná) | 210 | 210 | Sí |
| Poder | 584 | 584 | Sí |
| Resultado frente al rival | Desventaja: mejor retirarse | Desventaja: mejor retirarse | Sí |

`![Ficha de Héroe](capturas/ficha.png)`

## 2. El código fuente y el documento resultante (CE 2.e · CE 2.c)

- Captura de `ficha.php` en el editor (parte de cálculos) y captura del **Ctrl+U** de la página.

`![Código en el editor con los cálculos](capturas/codigo_fuente.png)`

`![Captura del Ctrl+U de la página](capturas/ctrl_u.png)`


- Explica con tus palabras **tres diferencias** entre ambas.

1. **Ausencia total de etiquetas PHP:** En el código fuente existen bloques `<?php ... ?>` y salidas cortas `<?= ... ?>`. En el **Ctrl+U** han desaparecido por completo y solo queda HTML puro.
2. **Sustitución de expresiones por valores procesados:** En el código fuente residen las operaciones y fórmulas (`round()`, `$nivel ** 2`, operadores ternarios); en el navegador solo aparecen los literales finales resultantes (por ejemplo, el texto `31,5 %` o la cadena de bloques `██████░░░░`).
3. **Ejecución de directivas del servidor invisible:** La directiva `declare(strict_types=1);` y las inclusiones como `require_once` están presentes en el script pero no generan rastro alguno en el flujo de bytes enviado al cliente.

- ¿Qué ha pasado con el nombre del héroe en el Ctrl+U? ¿Por qué?

Al utilizar `htmlspecialchars($nombreHeroe)`, los caracteres especiales HTML como `<` y `>` se transforman en `&lt;` y `&gt;`. Esto garantiza que el navegador no confunda el texto del nombre con una etiqueta del DOM, evitando errores de maquetación y mitigando vulnerabilidades de Cross-Site Scripting (XSS).

## 3. Experimento: cambio un dato y predigo el efecto (CE 2.e)

1. Dato que cambio en `inc/heroe.php` (valor antiguo → valor nuevo):
`$experienciaTxt = '3120';` → `$experienciaTxt = '4000';`


2. **Predicción** (antes de recargar): qué valores de la ficha cambiarán y cuánto valdrán:

**Nivel:** Subirá
   - **Vida máxima:** Aumentará
   - **% de vida:** Bajará 
   - **Poder:** Aumentará considerablemente al depender de `$nivel` y el nuevo daño.

3. **Resultado** real (captura):

`![Captura del cambio de valor de la experiencia](capturas/predicción.png)`

4. ¿Acertaste? Si no, ¿por qué?
Sí, ya que muchos valores dependen del nivel, de manera que si este se ve truncado, el resto se recalculará en cascada. Esto, por ejemplo, hace que pueda vencer al enemigo.

## 4. Experimento con strict_types (CE 2.f)

1. Quita el `(int)` del cálculo de los bloques de la barra de vida. Recarga. Captura del error.

`![Captura del error al quitarle el int a la barra de vida](capturas/error_strict_types.png)`

2. Explica qué dice el mensaje (tipo de error, función, argumento, línea).
- **Tipo de error:** `Fatal error: Uncaught TypeError`.
   - **Función afectada:** `str_repeat()`.
   - **Argumento:** Argument #2 (`$times`) debe ser de tipo `int`, pero recibió `float`.
   - **Causa:** La función `round()` devuelve un valor de tipo `float` por defecto. Al llamar a `str_repeat('█', $llenosVida)` sin haber hecho el casteo previo `(int)`, se produce una incompatibilidad de tipos.

3. Comenta la línea `declare(strict_types=1);` (manteniendo el cambio anterior) y recarga. ¿Qué pasa? ¿Por qué?
Al comentar la directiva, el script vuelve a funcionar sin errores fatales. Esto ocurre porque PHP en su modo permisivo (*weak typing*) realiza una conversión automática o coerción implícita (*type juggling*), truncando el `float` a `int` sin detener la ejecución.

4. Deja el código como estaba. ¿Qué opción prefieres para un proyecto real y por qué?
Prefiero trabajar con **`declare(strict_types=1);`** activado. Aunque obliga a escribir código más explícito y tipado, previene comportamientos erráticos silenciosos, localiza bugs de conversión en fases tempranas del desarrollo y asegura que las funciones reciban siempre el tipo de dato que esperan.


## 5. Directivas (CE 2.f)

- Captura de `diagnostico.php`.

`![Captura de la página de diagnóstico](capturas/diagnostico.png)`

- ¿Qué valor pondrías en `display_errors` en un servidor de producción? ¿Por qué?
Pondría **`display_errors = Off` (o `0`)**.
**Motivo:** Si se produce un fallo o una excepción no controlada en un entorno de producción, mostrar el error en pantalla expondría rutas de archivos internas del servidor, nombres de bases de datos, contraseñas o detalles de la lógica interna a usuarios malintencionados. Los errores deben registrarse internamente en un archivo mediante `log_errors = On` y mantenerse ocultos a la vista pública.


## 6. Uso de IA (obligatorio declararlo)

¿Has usado alguna IA? ¿Para qué? Copia una respuesta que te diera y explica si era correcta o qué tuviste que corregir.

- **¿Has usado alguna IA? ¿Para qué?**
  Sí, he utilizado una IA como apoyo en la resolución de dudas técnicas, revisión de advertencias de tipo, errores, formateo de funciones nativas y explicación sobre el modo depuración.

- **Respuesta consultada:**
Le pregunté porqué en el apartado de crónica con el heredoc, la clase no aparecía si no directamente el nombre de la constante.
- **Explicación:**
  La IA comentó que en heredoc las constantes no funcionan bien, razón por la cual propuso crear una variable para contener dicha constante y que en el texto se Crónica se usase dicha variable.
