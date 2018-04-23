<?php
	date_default_timezone_set('America/Argentina/Buenos_Aires');
    $username = "root";
    $password = "";
    $host = "localhost:3306";
    $database="SMN";

    $connection = mysqli_connect($host, $username, $password, $database);

    $Estacion=$_GET["nest"];
    $variable=$_GET["var"];

    $sqlDATA="SELECT Fecha,Tmax,Tmin,Tmed from SMN_DATA_ARG where idOMM='".$Estacion ."' order by Fecha desc limit 366";

    $queryDATA = mysqli_query($connection,$sqlDATA);
    if ( ! $queryDATA) {
        echo mysqli_error($connection);
        die;
    }
    $sqlCLIM="SELECT MM,DD,Tmax,Tmin,Tmed from SMN_CLIM_ARG where idOMM='".$Estacion ."' and TCLim=1960 order by MM asc, DD asc";
     //echo $sqlCLIM."\n";;
    $queryCLIM = mysqli_query($connection,$sqlCLIM);
    if ( ! $queryCLIM) {
        echo mysqli_error($connection);
        die;
    }

	$nr=mysqli_num_rows($queryDATA);

	$s_act_f= array();
	$s_act_v= array();
    for ($x = 0; $x < $nr; $x++) {
        $rs_a = mysqli_fetch_assoc($queryDATA);
        $s_act_f[]= $rs_a["Fecha"];
        $s_act_v[]= $rs_a[$variable];
	}

	$n=date("z",mktime(0,0,0,substr($s_act_f[0],5,2),substr($s_act_f[0],8,2),"2000")) ;

	$s_clim_f= array();
	$s_clim_v= array();
    for ($x = 0; $x < $nr; $x++) {
        $rs_c = mysqli_fetch_assoc($queryCLIM);
        $s_clim_f[]= $rs_c["MM"]."-".$rs_c["DD"];
        $s_clim_v[]= $rs_c[$variable];

	}

	$s_clim_f=array_reverse($s_clim_f);
	$s_clim_v=array_reverse($s_clim_v);

	$n1=366-$n-1;
	$s_clim_f=array_merge(array_slice($s_clim_f, $n1 % 366) ,array_slice($s_clim_f, 0, $n1 % 366));
	$s_clim_v=array_merge(array_slice($s_clim_v, $n1 % 366) , array_slice($s_clim_v, 0, $n1 % 366));

	$s_act_f= array_reverse($s_act_f);
	$s_act_v= array_reverse($s_act_v);
    $s_clim_v=array_reverse($s_clim_v);

    $s_tmp = array();


    for ($x = 0; $x < $nr; $x++) {
    	$s_tmp[]=array(
        	 'fecha'    =>  $s_act_f[$x],
        	 'actual'   =>  $s_act_v[$x],
        	 'clima'    =>  $s_clim_v[$x]
         	);

	}

	echo json_encode($s_tmp);

  mysqli_close($connection);
?>
