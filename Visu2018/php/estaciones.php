<?php
//
//Trabajo Práctico 4 - Visualización de la información
//Integrantes:
//    Alfredo Luis Rolla
//    Juan Ignacio Mazza
//
    // Conexion a la DB
    include 'db.php';

    //Query para recueprar la metadata de la EM
    $sqlMETA="SELECT idOMM,NomEstacion,Institucion,Longitud,Latitud,Elevacion from SMN_INTA_META_ARG where activo='*' and Institucion='SMN'";
    // Ejecutar el query
    $queryMETA = mysqli_query($connection,$sqlMETA);
    if ( ! $queryMETA) {
        echo mysqli_error();
        die;
    }
    //Recuperar el numero de filas 
    $nr=mysqli_num_rows($queryMETA);
    // Armamos la estructura de los metadatos
    $s_metadata= array();
    for ($x = 0; $x < $nr; $x++) {
        $rs_a = mysqli_fetch_assoc($queryMETA);
    	  $s_metadata[]=array(
        	 'idOMM'       =>  $rs_a["idOMM"],
        	 'Nombre'      =>  utf8_encode($rs_a["NomEstacion"]),
        	 'Lat'         =>  $rs_a["Latitud"],
        	 'Lon'         =>  $rs_a["Longitud"],
					 'Institucion' => $rs_a["Institucion"],
        	 'Alt'         =>  $rs_a["Elevacion"]
         	);
	  }
    // generamos un objeto de tipo JSON (JavaScript Object Notation) 
    echo json_encode($s_metadata);
   // Cerramos la conexion a la base de datos
   mysqli_close($connection);
?>
