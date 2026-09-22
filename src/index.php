<?php
try {
	$pdo = new PDO("mysql:host=db;dbname=mi_base_datos", "mi_usuario", "mi_password");
	echo "<h1>¡Los 3 contenedores están conectados correctamente!</h1>";
} catch (PDOException $e) {
	echo "Error de conexión con la base de datos: " . $e->getMessage();
}
?>
