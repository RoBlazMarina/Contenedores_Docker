Contenedores_Docker
Descripción del proyecto

Contenedores_Docker es una aplicación sencilla basada en contenedores que demuestra la integración de tres servicios mediante Docker Compose:

-Nginx como servidor web.
-PHP-FPM 8.3 para ejecutar código PHP.
-MySQL 8.0 como sistema gestor de bases de datos.

El objetivo principal del proyecto es comprobar la comunicación entre los tres contenedores y verificar que la aplicación PHP puede conectarse correctamente a la base de datos MySQL a través de la red interna de Docker.

Cuando la conexión se establece correctamente, la aplicación muestra el mensaje:

¡Los 3 contenedores están conectados correctamente!

Tecnologías utilizadas
-Docker
-Docker Compose
-Nginx
-PHP 8.3 FPM
-MySQL 8.0
-PDO para la conexión a bases de datos

Estructura del proyecto
Contenedores_Docker/
│
├── docker-compose.yml
│
├── nginx/
│   └── default.conf
│
├── php/
│   └── Dockerfile
│
└── src/
    └── index.php

Descripción de cada componente
docker-compose.yml

Define los tres servicios que forman la aplicación:

-web: contenedor Nginx.
-php: contenedor PHP-FPM construido a partir del Dockerfile.
-db: contenedor MySQL.

Además:

-Configura la red compartida app-network.
-Define el volumen persistente db_data.
-Establece las dependencias entre servicios.

src/index.php

Aplicación PHP encargada de:

1. Conectarse a la base de datos MySQL mediante PDO.
2. Mostrar un mensaje de éxito si la conexión se realiza correctamente.
3. Mostrar un mensaje de error en caso contrario.

Código principal:
$pdo = new PDO(
    "mysql:host=db;dbname=mi_base_datos",
    "mi_usuario",
    "mi_password"
);
El parámetro db coincide con el nombre del servicio definido en Docker Compose, por lo que Docker resuelve automáticamente el nombre del host.

php/Dockerfile

Construye la imagen personalizada de PHP.
FROM php:8.3-fpm

RUN docker-php-ext-install pdo pdo_mysql

Funciones:

-Utiliza PHP 8.3 FPM como imagen base.
-Instala las extensiones necesarias para trabajar con MySQL mediante PDO.

nginx/default.conf

Configura Nginx para:

-Escuchar en el puerto 80.
-Servir los archivos de la carpeta /var/www/html.
-Redirigir las peticiones PHP al contenedor PHP-FPM.

La comunicación entre Nginx y PHP se realiza mediante:
fastcgi_pass php:9000;
Donde php es el nombre del servicio definido en Docker Compose.

Funcionamiento de la arquitectura
Usuario
   │
   ▼
Nginx (web)
   │
   ▼
PHP-FPM (php)
   │
   ▼
MySQL (db)

1. El usuario accede al navegador.
2. Nginx recibe la petición.
3. Nginx envía los archivos PHP al contenedor PHP.
4. PHP ejecuta el código.
5. PHP se conecta a MySQL.
6. El resultado se devuelve al navegador.

Configuración de red

Los tres contenedores comparten la red:
networks:
  app-network:
    driver: bridge
Gracias a ello:
- web puede comunicarse con php.
- php puede comunicarse con db.
- Los servicios pueden localizarse mediante sus nombres.

- Persistencia de datos

La base de datos utiliza un volumen Docker:

volumes:
- db_data:/var/lib/mysql

Esto permite conservar los datos aunque el contenedor MySQL sea eliminado y vuelto a crear.

Posibles problemas o mejoras detectadas
1. Dependencia de MySQL

Aunque el servicio PHP depende de MySQL:

depends_on:
- db

esto solo garantiza que el contenedor se inicie, no que la base de datos esté completamente preparada para aceptar conexiones.

En proyectos reales suele añadirse:

-Un script de espera (wait-for-it.sh).
-Healthchecks en Docker Compose.

2. Credenciales expuestas

Actualmente las credenciales están escritas directamente en el archivo:
MYSQL_USER: mi_usuario
MYSQL_PASSWORD: mi_password
MYSQL_ROOT_PASSWORD: root_password


En entornos de producción se recomienda utilizar:
-Variables de entorno.
-Archivos .env.
-Docker Secrets.

3. Gestión de errores

En index.php se muestra el error completo:

PHP
echo $e->getMessage();

Esto resulta útil durante el desarrollo, pero en producción puede revelar información sensible sobre la infraestructura.

4. Versión de Docker Compose

La línea:
version: "3.8"

sigue siendo válida, aunque las versiones más recientes de Docker Compose suelen omitir este campo.

Cómo ejecutar el proyecto
1. Clonar el repositorio

git clone <URL_DEL_REPOSITORIO>
cd Contenedores_Docker
Mostrar más líneas
2. Construir y levantar los contenedores
Shell
docker compose up --build

o:


docker-compose up --build



según la versión instalada.

3. Verificar que los contenedores están activos

docker ps



Deberían aparecer:

-web
-php
-db

4. Acceder a la aplicación

Abrir en el navegador:

http://localhost:8080


Si todo funciona correctamente se mostrará el mensaje:


¡Los 3 contenedores están conectados correctamente!


5. Detener los contenedores

docker compose down


Si además se desea eliminar los volúmenes:

docker compose down -v


Conclusión

Este proyecto constituye un ejemplo básico de arquitectura multicontenedor utilizando Docker Compose. Permite comprender cómo configurar y conectar un servidor web Nginx, un intérprete PHP-FPM y una base de datos MySQL dentro de una misma red Docker, facilitando el despliegue y la administración de aplicaciones web modernas mediante contenedores.
