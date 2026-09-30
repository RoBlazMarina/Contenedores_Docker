# Contenedores_Docker

## Descripción del proyecto

**Contenedores_Docker** es una aplicación sencilla basada en contenedores que demuestra la integración de cuatro servicios mediante Docker Compose:

- Nginx como servidor web.
- PHP-FPM 8.3 para ejecutar código PHP.
- MySQL 8.0 como sistema gestor de bases de datos.
- phpMyAdmin como interfaz gráfica para la administración de MySQL.

El objetivo principal del proyecto es comprobar la comunicación entre los contenedores y verificar que la aplicación PHP puede conectarse correctamente a la base de datos MySQL a través de la red interna de Docker. Además, phpMyAdmin permite gestionar visualmente la base de datos desde el navegador.

Cuando la conexión se establece correctamente, la aplicación muestra el siguiente mensaje:

> ¡Los 3 contenedores están conectados correctamente!

---

## Tecnologías utilizadas

- Docker
- Docker Compose
- Nginx
- PHP 8.3 FPM
- MySQL 8.0
- phpMyAdmin
- PDO
- PHP

---

## Estructura del proyecto

```text
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
```

### Descripción de los archivos

#### docker-compose.yml

Este archivo define los cuatro servicios que forman la aplicación:

- **web**: servidor Nginx.
- **php**: intérprete PHP-FPM.
- **db**: servidor MySQL.
- **phpmyadmin**: interfaz web para la administración de MySQL.

phpMyAdmin está configurado para conectarse automáticamente al servicio `db` mediante la variable:
 
```yaml
PMA_HOST: db

También configura:

- Un volumen persistente para la base de datos.
- Una red compartida entre los contenedores.
- Las dependencias de arranque entre servicios.

#### src/index.php

Archivo PHP encargado de comprobar la conexión con MySQL mediante PDO.

```php
<?php
try {
    $pdo = new PDO(
        "mysql:host=db;dbname=mi_base_datos",
        "mi_usuario",
        "mi_password"
    );

    echo "<h1>¡Los 3 contenedores están conectados correctamente!</h1>";
} catch (PDOException $e) {
    echo "Error de conexión con la base de datos: " . $e->getMessage();
}
?>
```

Utiliza como host `db`, que corresponde al nombre del servicio definido en Docker Compose.

#### php/Dockerfile

Construye la imagen personalizada de PHP.

```dockerfile
FROM php:8.3-fpm

RUN docker-php-ext-install pdo pdo_mysql
```

Funciones:

- Utiliza la imagen oficial PHP 8.3 FPM.
- Instala las extensiones PDO y PDO_MySQL necesarias para conectarse a MySQL.

#### nginx/default.conf

Configura Nginx para servir la aplicación PHP.

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

La línea:

```nginx
fastcgi_pass php:9000;
```

permite que Nginx envíe las peticiones PHP al contenedor PHP-FPM.

---

## Arquitectura de la aplicación

```text
                    ┌───────────┐
                    │ phpMyAdmin│
                    └───┬────── ┘
                        │ 
                        ▼ 
Usuario ──► Nginx ──► PHP-FPM ──► MySQL
```

### Flujo de funcionamiento

### Aplicación web

1. El usuario accede desde el navegador.
2. Nginx recibe la petición.
3. Nginx envía los archivos PHP al servicio PHP-FPM.
4. PHP ejecuta el código.
5. PHP se conecta a MySQL.
6. Se devuelve el resultado al navegador.

### Administración de la base de datos

1. El usuario accede a phpMyAdmin.
2. phpMyAdmin se conecta al contenedor MySQL mediante la red Docker.
3. El usuario puede consultar, modificar y administrar las bases de datos desde el navegador.
---

## Red Docker

Todos los servicios están conectados mediante la red:

```yaml
networks:
  app-network:
    driver: bridge
```

Gracias a esta configuración:

- El contenedor `web` puede comunicarse con `php`.
- El contenedor `php` puede comunicarse con `db`.
- Docker resuelve automáticamente los nombres internos de los servicios.
- El contenedor `phpmyadmin` puede comunicarse con `db`.

---

## Persistencia de datos

MySQL utiliza un volumen persistente:

```yaml
volumes:
  - db_data:/var/lib/mysql
```

Esto permite conservar la información almacenada en la base de datos aunque el contenedor sea eliminado y recreado.

---

## Posibles errores y mejoras

### 1. Arranque de MySQL

Aunque el servicio PHP depende de MySQL mediante:

```yaml
depends_on:
  - db
```

esto únicamente garantiza que el contenedor se inicie, pero no que MySQL esté preparado para aceptar conexiones.

Como mejora, podrían utilizarse:

- Health Checks.
- Scripts de espera como `wait-for-it`.

### 2. Credenciales visibles

Las credenciales se encuentran directamente en el archivo `docker-compose.yml`:

```yaml
MYSQL_USER: mi_usuario
MYSQL_PASSWORD: mi_password
MYSQL_ROOT_PASSWORD: root_password
```

En proyectos reales se recomienda utilizar:

- Variables de entorno.
- Archivos `.env`.
- Docker Secrets.

### 3. Exposición de errores

Actualmente el sistema muestra directamente el mensaje de error devuelto por PDO:

```php
echo $e->getMessage();
```

Esto resulta útil para el desarrollo, pero en producción podría revelar información sensible sobre la infraestructura.

### 4. Seguridad

La base de datos expone el puerto:

```yaml
3306:3306
```

Si no es necesario acceder a MySQL desde el sistema anfitrión, este puerto podría eliminarse para aumentar la seguridad.
 
---
 
## Ejecución del proyecto
 
### 1. Clonar el repositorio
 
```bash
git clone <URL_DEL_REPOSITORIO>
cd Contenedores_Docker
```
 
### 2. Construir y arrancar los contenedores
 
```bash
docker compose up --build
```
 
O, en versiones anteriores:
 
```bash
docker-compose up --build
```
 
### 3. Verificar los contenedores
 
```bash
docker ps
```
 
Deberían aparecer los siguientes contenedores:
 
```text
web
php
db
phpmyadmin
```
 
### 4. Acceder a la aplicación

Aplicación PHP:

```text
http://localhost:8080
```

Si todo funciona correctamente aparecerá el mensaje:

```text
¡Los 3 contenedores están conectados correctamente!
```

phpMyAdmin:

```text
http://localhost:8000
```

Accede utilizando las credenciales configuradas en el servicio MySQL:

```text
Usuario: mi_usuario
Contraseña: mi_password
```
 
---
 
## Detener la aplicación
 
Detener los contenedores:
 
```bash
docker compose down
```
 
Detener los contenedores y eliminar los volúmenes:
 
```bash
docker compose down -v
```
 
---
 
## Conclusión
 
Este proyecto demuestra cómo desplegar una arquitectura web basada en contenedores utilizando Docker Compose. La solución integra Nginx, PHP-FPM, MySQL y phpMyAdmin, permitiendo comprender conceptos fundamentales como redes Docker, persistencia mediante volúmenes, comunicación entre servicios y administración gráfica de bases de datos dentro de un entorno de contenedores.
