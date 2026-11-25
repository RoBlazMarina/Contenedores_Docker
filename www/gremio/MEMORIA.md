# Memoria · Gremio de Aventureros

**Código de alumno/a:** ______  (no pongas tu nombre)

## 1. Mi aventurero
- Usuario que he añadido a la tabla: Lynette <el Halcón Rojo>
- Comando o pasos que he usado para generar el hash: docker compose exec php php -r "echo password_hash('Flecha2020', PASSWORD_DEFAULT);"

## 2. Parte 1 · match
¿Has puesto `default` en `etiquetaRango()`? ¿Qué pasa si llega el rango `'Z'`? ¿Por qué lo prefieres así?
No uso default porque con la eqtiqueta de Rango solo pueden llegarle valores válidos. Es mejor que de haber un error, salte como error en este campo a que se oculte tras una etiqueta.

## 3. Parte 2 · Validación de la URL
Escribe a mano en la barra `tablon.php?rango=Z&oro=hola`. ¿Qué ocurre y qué línea de tu código lo evita?
La página se carga, sin errores ni avisos, ignorando los valores inválidos. Como no tenemos un rango Z ni oro es un String, el filtro de rango queda en "Todos" y la recompensa mínima en [0 / vacío], por lo cual no da error. Esto sucede por las líneas return in_array($rango, RANGOS, true) ? $rango : null; y para oro:
$oro = filter_var($get['oro'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 0, 'max_range' => 10000],
]);
return $oro === false ? 0 : $oro;


## 4. Parte 3 · Sesión frente a cookie
Explica con tus palabras qué guardas en `$_SESSION` y qué guardas en cookies, y por qué no al revés.
En $_SESSION se guarda el id de usuario, las misiones aceptadas, las entradas y horas de visita. A su ves las cookies guardan el tema del navegador, el nombre de usuario para el login y el identificador de la sesión para PHP.

Esto no puede ir al revés porque el usuario puede acceder y modificar las cookies, así que la información importante no puede ir en ella. Estos datos tienen que permanecer en el servidor. Por su parte, las cookies suelen guardar las preferencias del usuario, visualización, nombre de usuario, elementos no críticos que se pueden guardar en su navegador y evitar cargar al servidor con información innecesaria.

## 5. Parte 3 · Seguridad del login
- ¿Qué pasa si en el usuario escribes `' OR '1'='1`? ¿Por qué?
La página muestra "Usuario o contraseña incorrectos." y no se inicia sesión. autenticar() usa una consulta preparada con el marcador :usuario (WHERE usuario = :usuario) y pasa el valor aparte con execute([':usuario' => $usuario]). Con ATTR_EMULATE_PREPARES => false en conexion.php, la sentencia SQL se envía al servidor ya compilada y los datos viajan por separado. Por eso MySQL trata ' OR '1'='1 como un texto literal que se busca en la columna usuario, y como no existe nadie con ese nombre, no devuelve ninguna fila.

- ¿Para qué sirve `session_regenerate_id(true)`?
Genera un identificador de sesión nuevo justo después de autenticarse y, con el parámetro true, borra la sesión antigua del servidor. Sirve para evitar la fijación de sesión (session fixation): un atacante podría conseguir que la víctima use un identificador que él ya conoce (por ejemplo, antes de iniciar sesión) y, cuando la víctima se autentica, usar ese mismo identificador para entrar como ella. Al cambiar el ID en el momento del login, el que conocía el atacante deja de valer.

- ¿Por qué el mensaje de error es el mismo si falla el usuario o la contraseña?
Esto es una medida de seguridad para no revelar qué usuarios existen. Si se aclarase que un usuario no existe, un atacante podría  probar nombres hasta encontrar cuentas válidas, las cuales puede terminar rompiendo con fuerza bruta. Con un mensaje genérico, "Usuario o contraseña incorrectos.", no obtiene esa información. En el código, autenticar() devuelve null en ambos casos y login.php muestra un único mensaje.

## 6. Pruebas que he hecho
| Prueba | Resultado esperado | Resultado obtenido |
|---|---|---|
| Entrar en tablon.php sin login | Redirige a login.php para iniciar sesión | Redirige a login.php?motivo=privado y muestra "Esa sala es solo para miembros del gremio. Inicia sesión." |
| Login con contraseña mala | Se queda en login.php y muestra "Usuario o contraseña incorrectos.", sin iniciar sesión | Se queda en login.php y muestra "Usuario o contraseña incorrectos.", sin iniciar sesión |
| Aceptar la misma misión dos veces | No lo permite, pues al aceptarla, el botón desaparece y la misión aparece en el zurrón.| Sale un mensaje en un recuadro naranja indicando que ya he aceptado la misión. |
| Cerrar sesión y volver al zurrón | Me devuelve a la página del login porque no estoy autenticado. | Me deja en la página login.php?motivo=privado y muestra "Esa sala es solo para miembros del gremio. Inicia sesión." |
| Cambiar el tema y cerrar sesión | Tras cerrar sesión el tema sigue aplicado, porque está en la cookie del tema la guarda el navegador durante un año | Tras cerrar sesión el tema sigue aplicado, porque está en la cookie del tema la guarda el navegador durante un año |

## 7. Dificultades y cómo las he resuelto

El login no pasaba: "No se pudo comprobar tu acceso"
Al intentar entrar con un usuario correcto, la página mostraba siempre el mensaje genérico de error de base de datos. En este punto estuve consultando con la IA cómo resolverlo, primero comprobando qué estaba usando para el ingreso de sesión PHP (que usaba las credenciales por defecto y no los compose) y después confirmando que las 4 variables daban false. Por eso ha habido que meter en el sevicio db del contenedor un bloque environment que contuviese esos datos y remonté el contenedor. Una vez realizado ya pude entrar con uno de los usuario y contraseñas por que recogían la trabla aventureros.