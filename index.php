<?php

function get_connection() {  // Devuelve la conexión con la base de datos
	try {  // Habría que modificar la linea siguiente con los datos de accedo a la base de datos
		$pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=pruebas;", 'user', '1234',
			array (
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // TODO: Esta parte está OO, pero abajo está procedural.
			));
		return $pdo;
	} catch (PDOException $e) {
		echo 'Error con la base de datos: ' . $e->getMessage();
	}
}

/** Comprobar usuario con consulta simple */
function comprobar_usuario(string $correo, string $clave): bool {
	$pdo = get_connection();

	$sql = "SELECT id FROM users WHERE email='$correo' AND clave='$clave'";
	// Al hacer el ataque, la consulta queda así: ... AND clave='xxx' OR '1'"
	echo "La sentencia que se está enviando a la Base de Datos para combrobar el usuario es:
		<br/> $sql; <br/><br/>"; // Para visualizar cómo queda

	$resul = $pdo->query($sql);

	var_dump($resul->rowCount());


	if ($resul->rowCount()==0) {
		return FALSE;
	} else {
		return TRUE; // Si devuelve valores
	}
}

/** Comprobar usuario con sentencias preparadas */
function comprobar_usuario_prepared(string $correo, string $clave): bool {
	$pdo = get_connection();

	$handle = $pdo->prepare("SELECT id FROM users WHERE email=? AND clave=?");
	$handle->bindValue(1, $correo);
	$handle->bindValue(2, $clave);
	$handle->execute();

	var_dump($handle->rowCount());


	if ($handle->rowCount()==0) {
		return FALSE;
	} else {
		return TRUE; // Si devuelve valores
	}
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (!isset($_POST['con_preparadas'])) {
		$usu = comprobar_usuario($_POST['email'], $_POST['clave']);
	} else {
		$usu = comprobar_usuario_prepared($_POST['email'], $_POST['clave']);
	}

	if ($usu) echo "<b>Autenticación realizada.</b>";
	else echo "<b>El usuario no existe.</b>";
	echo '<br/><br/><br/>';
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>SQL Injections</title>
	<meta charset="UTF-8">
</head>

<body>
	__________________________________________ <br /><br />
	<b>Ejemplo de SQL Injections:</b><br />
	Usuario válido (si se usa el script adjunto para la bbdd): '<b>user@dom.com</b>', con cotraseña '<b>1234</b>' <br />
	Cualquier otro usuario no está en la base de datos, pero podemos intentar realizar un ataque SQL Injection
	introduciendo en clave «<b>xxx' OR '1=1</b>» de modo que al sustituirse en la consulta pasa lo siguiente: <br /><br />


	La consulta es <br />
	SELECT 1 FROM users WHERE email='$correo' AND clave='$clave'; <br />
	sustituyendo las variables en el caso normal: <br />
	SELECT 1 FROM users WHERE <b>email='user@dom.com'</b> AND <b>clave='1234'</b>; (que funciona en ambos casos) <br />
	Pero al hacer el ataque: <br />
	SELECT 1 FROM users WHERE <b>email='xxx@xxx.xxx' AND clave='xxx' OR '1=1'</b>; <br />
	que operando es equivalente a <br /> <br />
	SELECT 1 FROM users WHERE <b>FALSE AND FALSE OR '1=1'</b>; <br />
	SELECT 1 FROM users WHERE <b>FALSE OR '1=1'</b>; <br />
	SELECT 1 FROM users WHERE <b>FALSE OR TRUE </b>; <br />
	SELECT 1 FROM users WHERE <b>TRUE</b>; <br />
	De ese modo podríamos autenticarnos en un sistema en el que no tenemos acceso concedido. <br />
	Usar sentencias preparadas permite evitar este problema. <br />
	Se pueden encontrar otras muchas opciones de SQL Injection, por ejemplo
	<a href='https://www.w3schools.com/sql/sql_injection.asp'> aquí </a>. <br />
	__________________________________________ <br /><br /><br />


	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
		<label for="email">Correo </label>
		<input id="email" name="email" type="email" required="required" />
		<br /><br />
		<label for="clave">Clave </label>
		<input id="clave" name="clave" type="text" required="required" /> (La he dejado como type="text" en lugar de password para el ejemplo)
		<br /><br />
		<label for="clave">Usar sentencias preparadas</label>
		<input type="checkbox" name="con_preparadas" />
		<input type="submit" value="Submit" />
	</form>
</body>

</html>