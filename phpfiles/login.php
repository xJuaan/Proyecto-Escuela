<?php
session_start();

// Configuración de conexión
$dsn = "sqlsrv:server=DESKTOP-QQ52G65\SQLEXPRESS;Database=Proyecto";

try {
    $conn = new PDO($dsn);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Verifica si se enviaron los datos del formulario
if (
    isset($_POST['correo']) && isset($_POST['nombre']) &&
    isset($_POST['primer_apellido']) && isset($_POST['fecha_nacimiento']) &&
    isset($_POST['telefono']) && isset($_POST['direccion'])
) {
    // Recibe los datos del formulario
    $correo = $_POST['correo'];
    $nombre = $_POST['nombre'];
    $primerApellido = $_POST['primer_apellido'];
    $segundoApellido = $_POST['segundo_apellido'] ?? null; // Campo opcional
    $fechaNacimiento = $_POST['fecha_nacimiento'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    // Consulta para obtener el último valor de 'Matricula'
    $sql = "SELECT MAX(CAST(Matricula AS INT)) AS MaxMatricula FROM Alumnos";
    $stmtMax = $conn->prepare($sql);
    $stmtMax->execute();
    $result = $stmtMax->fetch(PDO::FETCH_ASSOC);

    // Genera un nuevo valor para Matricula
    $newMatricula = str_pad(($result['MaxMatricula'] + 1), 8, '0', STR_PAD_LEFT);

    // Consulta para insertar los datos en la tabla Alumnos (incluyendo 'Matricula')
    $sql = "INSERT INTO Alumnos (Matricula, Correo, Nombre, PrimerApellido, SegundoApellido, FechaNacimiento, Telefono, Direccion) 
            VALUES (:matricula, :correo, :nombre, :primer_apellido, :segundo_apellido, :fecha_nacimiento, :telefono, :direccion)";
    
    $stmt = $conn->prepare($sql);

    // Bind de los parámetros
    $stmt->bindParam(':matricula', $newMatricula, PDO::PARAM_STR);
    $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':primer_apellido', $primerApellido, PDO::PARAM_STR);
    $stmt->bindParam(':segundo_apellido', $segundoApellido, PDO::PARAM_STR);
    $stmt->bindParam(':fecha_nacimiento', $fechaNacimiento, PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);

    // Ejecuta la consulta
    if ($stmt->execute()) {
        // Redirige a la misma página con un mensaje de éxito
        header("Location: ../htmlfiles/registeralms.html?success=true");
        exit();
    } else {
        // Redirige a la misma página con un mensaje de error
        header("Location: ../htmlfiles/registeralms.html?error=true");
        exit();
    }
} else {
    // Redirige a la misma página si no se ingresaron los datos requeridos
    header("Location: ../htmlfiles/registeralms.html?error=missing");
    exit();
}

// Cierra la conexión
$conn = null;
?>
