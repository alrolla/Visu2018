<!DOCTYPE html>
<!--
Trabajo Práctico 4 - Visualización de la información
Integrantes:
    Alfredo Luis Rolla
    Juan Ignacio Mazza
-->
<html>
<head>
  <meta charset="iso-8859-1" />
  <title>Trabajo Práctico 4 - Visualización de la información</title>
  <!-- link a fonts necesarios -->
  <link rel="stylesheet" type="text/css" href="js/font-awesome-4.6.3/css/font-awesome.min.css" >
  <!-- link a CSS openlayers (MAPA!) -->
  <link rel="stylesheet" type="text/css" href="js/ol_v3.18.2/ol.css" >
  <!-- link al CSS para el sidebar del menu de la izquierda -->
  <link rel="stylesheet" type="text/css" href="js/sidebar-v2/css/ol3-sidebar.css" />
  <!-- link al CSS del mapa -->
  <link rel="stylesheet" type="text/css" href="css/mapa.css">
  <!-- link a javascript de JQUERY -->
  <script
    src="https://code.jquery.com/jquery-2.2.4.min.js"
    integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
    crossorigin="anonymous">
  </script>
  <!-- link al javascript de openlayers (MAPA) -->
  <script src="js/ol_v3.18.2/ol.js" type="text/javascript"></script>
  <!-- link al javascript del SIDEBAR -->
  <script src="js/sidebar-v2/js/jquery-sidebar.js" type="text/javascript"></script>
  <!-- link al javascript del Fly2Station -->
  <script src="js/fly.js" type="text/javascript"></script>
</head>
<body>
    <!-- Imaginamos que en este DIV pondremos la imagen D3 de la temperatura y precipitacion-->
    <div id="ventana" class="ventana" style="position:absolute;top:50px;left:100px;height:430px;width:680px;z-index:39999;background-color: #dddddd;display :none;overflow:hidden;">
      <!-- Imaginamos que este sera el boton para cerrar la ventana de graficado -->
      <button type="button" class="btn_min" style="position: relative;left: 630px;top:2px;font-size:10px" onClick="hide('ventana');">Cerrar</button>
      <!-- Imaginamos que esta sera el area grafica de la ventana -->
      <div id="plot" class="ventana2" style="position:relative;left:-2px;top:4px;height:420px;width:680px;z-index:39999;background-color: #ffffff;overflow:hidden;"></div>
    </div>
    <!-- Fin del DIV CON LA IMAGEN D3 -->

    <!-- Definicion del menu de la izquierda para seleccionar la estacion meteorologica por nombre -->
    <div id="sidebar" class="sidebar collapsed">
        <!-- Nav tabs -->
        <div class="sidebar-tabs">
            <ul role="tablist">
                <li><a href="#home" role="tab"><i class="fa fa-bars"></i></a></li>
            </ul>
        </div>

        <!-- Paneles para mostrar los nombre de las estaciones
             para usar el sidebar-pane -->
        <div class="sidebar-content">
            <div class="sidebar-pane" id="home">
                <h1 class="sidebar-header">
                     Estación Met.
                    <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
                </h1>
      <!-- Recuperamos los nombres de las estaciones de la DB y armamos el menu en PHP-->
          <?php
            //Conectar a la base de datos
            include 'php/db.php';
            // Query para recuperar los nombre de las estaciones
            $sqlMETA="SELECT idOMM,NomEstacion,Institucion,Longitud,Latitud,Elevacion from SMN_INTA_META_ARG where activo='*' and Institucion='SMN' order by NomEstacion";
            $queryMETA = mysqli_query($connection,$sqlMETA);
            if ( ! $queryMETA) {
                echo mysqli_error($connection); // Si da error ...
                die;
            }
            // recupero el numero de filas del query
        	  $nr=mysqli_num_rows($queryMETA);
            // Loop sobre las estaciones para escribir la funcion fly2Estacion
        	  $s_metadata= array();
            for ($x = 0; $x < $nr; $x++) {
                $rs_a = mysqli_fetch_assoc($queryMETA);
                //Genero el html del boton con fly2Estacion
                $boton="\"fly2Estacion(".$rs_a["Latitud"].",".$rs_a["Longitud"].");\">".utf8_encode($rs_a["NomEstacion"]);
               ?>
   		          <button type="button" class="btn_est" onclick=<?php echo $boton; ?> </button>
          		 <?php
  	         }
            // Cierro la coneccion a la base de datos
              mysqli_close($connection);
	          ?>
            </div>

        </div>
    </div>

    <!-- DIV para ubicar el mapa de OPENLAYERS!!! -->
    <div id="map" class="sidebar-map"></div>


    <!-- Definimos el DIV "info" que contiene el cuadro de la metadata de la estacion
         del borde superior derecho -->
    <!-- Al inicio esta OCULTA -->
    <div id="info" class="metaEst" style="position:absolute;top:50px;left:100px;height:120px;width:220px;z-index:19999;background-color: #ccffcc;display:none">
     <table style="margin:5px;font-size:12px">
        <tr>
            <td width="25px" align="right">id:</td>
            <td width="175px" id="t_id" ></td> <!-- idOMM -->
         </tr>
        <tr>
            <td width="25px" align="right">nom:</td>
            <td width="175px" id="t_nom" ></td> <!-- Nombre de la estacion -->
         </tr>
        <tr>
            <td width="25px" align="right">lat:</td>
            <td width="175px" id="t_lat" ></td> <!-- Latitud de la estacion -->
         </tr>
        <tr>
            <td width="25px" align="right">lon:</td>
            <td width="175px" id="t_lon" ></td> <!-- Longitud de la estacion -->
         </tr>
        <tr>
            <td width="25px" align="right">alt:</td>
            <td width="175px" id="t_alt" ></td> <!-- Altura orografica de la estacion -->
         </tr>
        <tr >
            <!-- Aca van los botones para visualizar las variables y sus visualizaciones
             por ahora estan con comentarios ... -->

            <!-- <td width="100%" align="center" colspan=2>
            <button type="button" class="btn_min" onclick="load_home('Tmin');">TMin</button>
            <button type="button" class="btn_med" onclick="load_home('Tmed');">TMed</button>
            <button type="button" class="btn_max" onclick="load_home('Tmax');">TMax</button>
             <button type="button" class="btn_pre" onclick="load_home('Prcp');">Precip.</button>
            </td> -->
         </tr>
     </table>
     </div>

<!-- Aca empieza el codigo javascript de la aplicacion ..... -->
    <script type="text/javascript">


    	var idOMM; //variable global del id de la Est.Meteo

    	var features = []; //variable global de las features , puntos (lat,lon)

      // Disparamos un ajax de jquery para recuperar un json con los datos de las estaciones
      // para ubicar en el mapa en modo sincronico ( espera a que termine el query para seguir!!!)
      // javascript es asincronico por default !!!

      // Los datos quedan en la variable 'est'
    	var est=$.ajax({
    		url: "php/estaciones.php",
    		dataType: "json",
    		async:false,
    		success: function(datos){
    			return(datos);
    		}
    	}).responseJSON;

      //Defino un estilo del icono que define una est. Meteo SELECCIONADA.
      // amarillo borde negro
    	iconStyle2 = new ol.style.Style({
    			               image: new ol.style.Circle({
    			 						radius: 8,
    			 						stroke: new ol.style.Stroke({
    			   						color: '#000'
    			 		                   	}),
    			 						fill: new ol.style.Fill({
    			   						color: '#FFD700' // attribute colour
    			                          	})
    		                              })
    		            });

      // Loop para Recorrer los datos secuencialmente
      //dibujando un circulo verde con borde negro
    	 $.each(est, function (i, item) {
          // Definimos un icono para SMN y otro para INTA de distinto color
          iconStyleINTA = new ol.style.Style({
      			              image: new ol.style.Circle({
                                  			 		radius: 5,
                                  			 		stroke: new ol.style.Stroke({
                                  			   		color: '#000'
                                  			 		                   	}),
                                  			 		fill: new ol.style.Fill({
                                  			   		color: '#ff0000' // attribute colour
                                  			                          	})
                                                    })
                                            });
          iconStyleSMN = new ol.style.Style({
      			              image: new ol.style.Circle({
                                  			 		radius: 5,
                                  			 		stroke: new ol.style.Stroke({
                                  			   		color: '#000'
                                  			 		                   	}),
                                  			 		fill: new ol.style.Fill({
                                  			   		color: '#00ff00' // attribute colour
                                  			                          	})
                                                    })
                                            });
       		// Creamos los marcadores de las estaciones en el mapa
          // Dentro del marqker guarda los datos de la estacion en 'item'
       		var marker = new ol.Feature({
         	content: item,
         	mapid: i,
         	title: item.idOMM,
         	geometry: new ol.geom.Point(
           		ol.proj.transform(
             		[Number(item.Lon), Number(item.Lat)],
             		'EPSG:4326', 'EPSG:3857')
           		)
       		});
          if(item.Institucion == "SMN"){
       		   marker.setStyle(iconStyleSMN);
          }else{
            marker.setStyle(iconStyleINTA);
          }
       		features.push(marker); // armamos la lista de marcadores
        }); //Fin del loop creando iconos de laas estaciones Meteo

        var sidebar = $('#sidebar').sidebar(); // Activa la barra de la izquierda

        // generamos la capa de informacion del mapa de BASE
        var openStreetMapLayer = new ol.layer.Tile({
         source:new ol.source.XYZ({crossOrigin:null,
             urls:['https://tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png'],
             attributions:[new ol.Attribution({html:'<a href="https://www.openstreetmap.org/">� OpenStreetMap contributors, CC-BY-SA</a>'})]
        })});
        // Generamos la capa de informacion con los puntos sobre el mapa
    		var vectorSource = new ol.source.Vector({
        		features: features

    		});
    		var vectorLayer = new ol.layer.Vector({
        		source: vectorSource
    		});
        // definimos la vista inical de l mapa centrada en la lat , lon y un zoom de 6
    		var view = new ol.View({
                    center: ol.proj.transform([-61, -35], 'EPSG:4326', 'EPSG:3857'),
                    zoom: 7
                });
        // Definimos la variable con toda la informacion del mapa
        var map = new ol.Map({
            target: 'map',
            layers: [
                openStreetMapLayer
            ],
            view: view
        });

        // Agregamos la informacion de los mapas al mapa general
     		map.addLayer(vectorLayer);

        var feature_o; // variable global de un punto seleccionado
        var feature;   // variable global de un punto no seleccionado

        // Definicion de la Funcion para mostrar un punto seleccionado con el click
        var displayFeatureInfo = function(pixel) {
          console.log("pixel",pixel);
          console.log("featurexxx ",feature_o);
          if (feature_o) {
            if(feature_o.get('content').Institucion == "SMN"){
               feature_o.setStyle(iconStyleSMN);
            }else{
              feature_o.setStyle(iconStyleINTA);
            }
          }
          // Retorna en el click si hay una feature ( un punto de estacion )
          var feature = map.forEachFeatureAtPixel(pixel, function(feature, layer) {
            return feature;
          });
          if (feature) {console.log("feature",feature.get('content').Nombre );} //PRUEBA
          var info = document.getElementById('info'); // Recuperamos del DOM el DIV de la ventana de info
          var mp = document.getElementById('map');    //Recuperamos del DOM el DIV del mapa
          var t_id= document.getElementById('t_id');  // idOMM
          var t_nom= document.getElementById('t_nom');// Nombre de la estacion
          var t_lat= document.getElementById('t_lat');// Latitud
          var t_lon= document.getElementById('t_lon');// Longitud
          var t_alt= document.getElementById('t_alt');// Altura

          var rect = mp.getBoundingClientRect(); // Recuperamos el rectangulo usado por el mapa
          // Acomodamos la posicion de la ventana superior derecha con la informacion de la estacion
          info.style.position = "absolute";
          info.style.left = (rect.right-224)+'px';
          info.style.top = (rect.top+2)+'px';
		
          // Si hay un icono debajo del click
          if (feature) {
            feature_o=feature;
            feature.setStyle(iconStyle2);
            t_id.innerHTML =  feature.get('content').idOMM ;
            t_nom.innerHTML =  feature.get('content').Nombre;
            t_lat.innerHTML =  feature.get('content').Lat;
            t_lon.innerHTML =  feature.get('content').Lon;
            t_alt.innerHTML =  feature.get('content').Alt + " (m)";
            idOMM=feature.get('content').idOMM;

            $('#info').show();
          }

        };
	    
        // Hacemos sensible el mapa al evento CLICK
        map.on('click', function(evt) {
          displayFeatureInfo(evt.pixel);
        });
      </script>

</body>
</html>
