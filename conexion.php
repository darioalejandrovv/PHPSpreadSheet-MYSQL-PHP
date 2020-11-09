<?
$db_host="localhost";
$db_user="root";
$db_password="";
$db_name="Empresa";

$conn=mysqli_connect($db_host, $db_user,$db_password, $db_name);
if(mysqli_connect_errno()){
    echo "Error en conexión a base de datos: " .mysqli_connect_error();
}

?>