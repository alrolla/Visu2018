<?
 // Conexion a la base de datos
  date_default_timezone_set('America/Argentina/Buenos_Aires');
  $username = "root";
  $password = "";
  $host = "localhost:3306";
  $database="Visu2018";

  $connection = mysqli_connect($host, $username, $password,$database);
?>
