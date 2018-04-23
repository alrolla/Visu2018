<?php
    include 'db.php';

    $sqlMETA="SELECT idOMM,NomEstacion,Institucion,Longitud,Latitud,Elevacion from SMN_INTA_META_ARG where activo='*' and Institucion='SMN'";

    $queryMETA = mysqli_query($connection,$sqlMETA);
    if ( ! $queryMETA) {
        echo mysqli_error();
        die;
    }

		$nr=mysqli_num_rows($queryMETA);

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

	echo json_encode($s_metadata);

  mysqli_close($connection);
?>
