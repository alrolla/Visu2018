<!DOCTYPE html>
<?php
// Recupero los parametros de la pagina y leo la metadata de LA ESTACION !!!
   include 'db.php';

    $nest=$_GET["nest"];
    $var=$_GET["var"];
    $titulo=array(
      "Tmax" => "Temperatura Maxima",
      "Tmin" => "Temperatura Minima",
      "Tmed" => "Temperatura Media"
      );
    $sqlMETA="SELECT idOMM,NomEstacion,Institucion,Longitud,Latitud,Elevacion from SMN_INTA_META_ARG where idOMM=".$nest;

    $queryMETA = mysqli_query($connection,$sqlMETA);

    if ( ! $queryMETA) {
        echo mysqli_error($connection);
        die;
    }
	  $rs_a = mysqli_fetch_assoc($queryMETA);

?>

<meta charset="iso-8859-1" />
<html>
<head>

	<script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
	<script src="../js/d3/d3.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/st_temp.css" >

</head>

<body>
  <!-- Lugar donde se pondra la metada de la estacion -->
	<div id="Titulo" >
	</div>
  <!-- Cuadro donde se muestra la informacion de cada dia -->
  <div id="cuadro" >
  	<div id="fechal" > Fecha: </div>      <div id="fecha"  > </div>
    <div id="climal" > Clima: </div>      <div id="clima"  > </div>
    <div id="actuall" > Actual: </div>    <div id="actual" > </div>
    <div id="anomalial" > Anom.: </div>   <div id="anomalia"> </div>
  </div>

  <div id="cuadro2" >
  	<div > <span class="dotOver"> </span>  Sobre Normal</div>
    <div > <span class="dotNormal"> </span>  1980-2010</div>
    <div > <span class="dotBelow"> </span>  Bajo Normal</div>

  </div>

  <div style="position: absolute;top:330px;left:577px;width:95px;">
  	    <img src="../img/dataViz_teaser.jpg" height="90" >
  </div>
<script  type="text/javascript">

$("#Titulo").html( 'Est: ' + <?php echo "'".$rs_a["idOMM"]."'" ?>
				   + ' - ' + <?php echo "'".$rs_a["NomEstacion"]."'" ?>
				   + ' / Lat: '+ <?php echo "'".$rs_a["Latitud"]."'" ?>
				   + ' / Lon: '+ <?php echo "'".$rs_a["Longitud"]."'" ?>
				   + ' / Alt: '+ <?php echo "'".$rs_a["Elevacion"]."'" ?>

);

//$('#cuadro').show();
//=== DEFINICIONES GRAFICO GRANDE ===========================================================

// Definimos los margenes del grafico Principal (para controlar el area de graficado)
var MargenGrande = {
    top: 1,
    right: 40,
    bottom: 100,
    left: 50
  }

//Definimos el ancho y alto real de graficado
var	Ancho = 600 - MargenGrande.left - MargenGrande.right;
var	Alto  = 400 - MargenGrande.top - MargenGrande.bottom;

// Definimos el area de dibujo
var svg = d3.select("body").append("svg")
	                           .attr("width", Ancho + MargenGrande.left + MargenGrande.right )
	                           .attr("height", Alto + MargenGrande.top + MargenGrande.bottom);
// Definimos los extremos del area de dibujo para recortar luego, CLIPPING
svg.append("defs").append("clipPath")
   .attr("id", "clip")
   .append("rect")
   .attr("width", Ancho)
   .attr("height", Alto);

var f_parser = d3.timeParse("%Y-%m-%d"); // Parsea una fecha con el formato especificado
var f_fecha=d3.timeFormat("%d-%m-%Y");
var f_valor=d3.format(".1f");

var x =  d3.scaleTime().range([0, Ancho]); // Escala del eje x TIEMPO(FECHAS)
var y = d3.scaleLinear().range([Alto, 0]); // Escala del eje y VARIABLE DEPENDIENTE

var xAxis = d3.axisBottom(x).tickSize(-Alto); // Plotea el eje y la grilla del eje X
var yAxis = d3.axisLeft(y).tickSize(-Ancho);  // Plotea el eje y la grilla del eje Y

//Funcion para generar la linea de la CLIMATOLOGIA (linea media de los 30 años de 1980 -2010)
// usamos la opcion d3.curveMonotoneX para unir los puntos porque es suavizada!
var linea = d3.area().curve(d3.curveMonotoneX)
    .x(function(d) { return x(d.fecha); })
    .y(function(d) { return y(d.clima); });

var area = d3.area()
						 .curve(d3.curveMonotoneX)
   			 		 .x(function(d) { return x(d.fecha); })
   			 		 .y1(function(d) { return y(d.clima); });



var AreaGrafica = svg.append("g")
  						 .attr("transform", "translate(" + 0 + "," + MargenGrande.top + ")");

var GrafGrande = AreaGrafica.append("g")
 								 .attr("class", "GrafGrande")
 							 	 .attr("transform", "translate(" + MargenGrande.left + "," + MargenGrande.top + ")");

// Funcion para obtener el valor a izquierda del vector de FECHAS
// para mostrar la REFERENCIA
var bisectDate = d3.bisector(function(d) { return d.fecha; }).left

//=== DEFINICIONES GRAFICO MINIATURA ===========================================================
// Definimos lo mismo pero para la MINIATURA de referencia
// Definimos los margenes del grafico Principal (para controlar el area de graficado)
var MargenMini = {
    top: 330,
    right: 40,
    bottom: 20,
    left: 50
  }

//Definimos el ancho y alto de la Miniatura
var	AnchoMini = 600 - MargenMini.left - MargenMini.right;
var	AltoMini = 400 - MargenMini.top - MargenMini.bottom;

var xMini =  d3.scaleTime().range([0, AnchoMini]); // Escala del eje x TIEMPO(FECHAS)
var yMini = d3.scaleLinear().range([AltoMini, 0]); // Escala del eje y VARIABLE DEPENDIENTE

var xAxisMini = d3.axisBottom(xMini).tickSize(5); // Plotea la grilla del eje x
// yAxisMini no lo definimos porque no tiene sentido

var lineaMini = d3.area()
    .x(function(d) { return xMini(d.fecha); })
    .y(function(d) { return yMini(d.clima); });

var areaMini = d3.area()
    .x(function(d) { return xMini(d.fecha); })
    .y0(AltoMini)
    .y1(function(d) { return yMini(d.clima); });

var GrafMini = AreaGrafica.append("g")
  .attr("class", "GrafMini")
  .attr("transform", "translate(" + MargenMini.left  + "," + MargenMini.top   + ")");

// FUNCION PARA CONTROLAR EL BRUS SOBRE EL EJE X
var GrafMiniBrush = d3.brushX()
	                    .extent([[0, 0], [AnchoMini, AltoMini]])
                      .on("brush", brushed);

//console.log("serieT.php?nest=<?php echo $nest ?>&var=<?php echo $var ?> ");

//Ejecutamos desde D3 un php !!! y recuperamos un json
d3.json("serieT.php?nest=<?php echo $nest ?>&var=<?php echo $var ?> ",  function(error, data) {
    // Vamos armando los vectores a plotear
	  data.forEach(function(d) { //Data es el objetojson recuperado
	  d.fecha = f_parser(d.fecha); // aplico la funcion definida mas arriba f_parser
	  d["clima"] = +d["clima"];
	  if(d.actual == null){d.actual=d.clima};
	  d["actual"] = +d["actual"];

    });
//=== DEFINICIONES GRAFICO GRANDE ===========================================================
		GrafGrande.datum(data);
    // Definimos e dominio de  los eje X e Y
    // transforma las unidades de los ejes (Fecha, temperatura) en unidades del grafico (pixels)
		x.domain(d3.extent(data, function(d) {return d.fecha;}));
		y.domain([
		  		Math.floor(d3.min(data, function(d) { return Math.min(d["clima"], d["actual"]);}))-1,
		  		Math.ceil(d3.max(data, function(d) { return Math.max(d["clima"], d["actual"]);})+1)
			]);

		// Esta funcion genera un AREA uniendo los puntos de la CLIMATOLOGIA (d.fecha y d.clima)
    // Despues se pueden usar las propiedades de este objeto para recortar areas superior
    // e inferior del grafico ( para pintar de rojo y azul)
		var area = d3.area()
								 .curve(d3.curveMonotoneX)
		   			 		 .x(function(d) { return x(d.fecha); })
                 .y0(y(0))
		   			 		 .y1(function(d) { //console.log(d.clima,y(d.clima));
                                   return y(d.clima); });
    //==   Aca recortamos el AREA delimitada por la linea del pie del grafico y la curva media
    //     (CLIMATOLOGIA), es decir, el area INFERIOR de la curva de la serie ACTUAL (AZUL)...
    //      ademas de recortar con #clip el area interna del grafico
    GrafGrande.append("clipPath")
    		.attr("id", "clip-below")
    		.append("path")
        .attr("clip-path", "url(#clip)")
    		.attr("d", area.y0(Alto));

    GrafGrande.append("path")
    		.attr("class", "area below")
    		.attr("clip-path", "url(#clip-below)")
    		.attr("d", area.y0(function(d) {return y(d["actual"]);}));

    //==   Aca generamos un AREA delimitada por el tope del grafico y la curva media
    //     (CLIMATOLOGIA), es decir, el area SUPERIOR de la curva de la serie ACTUAL (ROJO)...
    //     ademas de recortar con #clip el area interna del grafico
    GrafGrande.append("clipPath")
    		.attr("id", "clip-above")
    		.append("path")
        .attr("clip-path", "url(#clip)")
        .attr("d", area.y0(0));

  	GrafGrande.append("path")
    		.attr("class", "area above")
    		.attr("clip-path", "url(#clip-above)")
    		.attr("d", area.y0(function(d) {return y(d["actual"]);}));

    //== Esto dibuja el eje X ( escala temporal) que fue definido mas arriba en una funcion xAxis
    // Se pueden cambiar los parametros ...
    GrafGrande.append("g")
	  		.attr("class", "x axis")
	  		.attr("transform", "translate(0," + Alto + ")")
	  		.call(xAxis);
    //== Esto dibuja el eje Y ( escala lineal) que fue definido mas arriba en la funcion  yAxis
    // Se pueden cambiar los parametros ...
    GrafGrande.append("g")
	  		.attr("class", "y axis")
	  		.call(yAxis);
  	GrafGrande.append("text")
  		.attr("transform", "rotate(-90)")
  		.attr("y", -40)
  		.attr("x", -Alto/2+100)
  		.attr("style","font-size  : 20" )
  		.attr("dy", ".71em")
  		.style("text-anchor", "end")
  		.text(<?php echo "'".$titulo[$var]."'" ?>+" (ºC)");
    // Esto dibuja el rectangulo del area completa del grafico X,Y en negro
  	GrafGrande.append("rect")
  	  	.attr("x", 0)
  	  	.attr("y", 0)
  	  	.attr("height", Alto)
  	  	.attr("width", Ancho)
  	  	.style("stroke", "black")
  	  	.style("fill", "none")
  	  	.style("stroke-width", 2);
    // Esto dibuja en color NEGRO la linea de la CLIMATOLOGIA ( media)
    GrafGrande.append("path")
            .attr("class", "linea")
            .attr("d", linea)
  		      .attr("clip-path", "url(#clip)");

//== DEFINICIONES GRAFICO "MINIATURA" ===================================================
    //Asignamos los datos el grupo svg correspondiente
  	GrafMini.datum(data);
    // Definimos e dominio de  los eje X e Y
    // transforma las unidades de los ejes (Fecha, temperatura) en unidades del grafico (pixels)
    xMini.domain(d3.extent(data, function(d) {return d.fecha;}));
		yMini.domain([
		  		Math.floor(d3.min(data, function(d) { return Math.min(d["clima"], d["actual"]);}))-1,
		  		Math.ceil(d3.max(data, function(d) { return Math.max(d["clima"], d["actual"]);})+1)
			]);

  	GrafMini.append("clipPath")
    		.attr("id", "clip-belowMini")
    		.append("path")
    		.attr("d", areaMini.y0(AltoMini));

    GrafMini.append("path")
    		.attr("class", "area below")
    		.attr("clip-path", "url(#clip-belowMini)")
    		.attr("d", areaMini.y0(function(d) {return yMini(d["actual"]);}));

  	GrafMini.append("clipPath")
    		.attr("id", "clip-aboveMini")
    		.append("path")
    		.attr("d", areaMini.y0(0));

  	GrafMini.append("path")
    		.attr("class", "area above")
    		.attr("clip-path", "url(#clip-aboveMini)")
    		.attr("d", areaMini.y0(function(d) {return yMini(d["actual"]);}));


  	GrafMini.append("path")
    		.attr("class", "lineaMini")
    		.attr("d", lineaMini);

  	GrafMini.append("g")
    		.attr("class", "x axis")
    		.attr("transform", "translate(0," + AltoMini + ")")
    		.call(xAxisMini);

  	GrafMini.append("rect")
  	  	.attr("x", 0)
  	  	.attr("y", 0)
  	  	.attr("height", AltoMini)
  	  	.attr("width", AnchoMini)
  	  	.style("stroke", "#666666")
  	  	.style("fill", "none")
  	  	.style("stroke-width", 2);

    GrafMini.append("g")
    		.attr("class", "x brush")
    		.call(GrafMiniBrush)
    		.selectAll("rect")
    		.attr("y", -6)
    		.attr("height", AltoMini + 7)
    		.style({
              "fill": "#69f",
              "fill-opacity": "0.9"
          });
//Aca empieza la referencia ....
    var LineaCursor= GrafGrande.append("g")
        .attr("class", "focus")
        .style("opacity", "0");

    var circle1=LineaCursor.append("circle")
        .attr("r", 4)
        .style("stroke" , "black")
        .style("fill" , "red");

    var circle2=LineaCursor.append("circle")
        .attr("r", 4)
        .style("stroke" , "black")
        .style("fill" , "blue");

    // Asignamos a los eventos las funciones especificas del mouse
    GrafGrande.append("rect")
        .attr("class", "overlay")
        .attr("width", Ancho)
        .attr("height", Alto)
        .on("mousemove", mousemove)
        .on("mouseover", mouseover)
  	    .on("mouseout", mouseout);

    var Referencia=GrafGrande.append("g")
        .attr("class", "focus")
        .style("opacity", "1");

    Referencia.append("line")
  	  .attr("x1", 0)  //<<== change your code here
  	  .attr("y1", 0)
  	  .attr("x2", 0)  //<<== and here
  	  .attr("y2", 0)
  	  .style("stroke-width", 2)
  	  .style("stroke", "#ffff")
  	  .style("stroke-dasharray","2,2")
  	  .style("fill", "none");

      // Funciones para manejar la entrada y salida del area grafica con el mouse
      // Si estoy DENTRO del area grafica GRANDE
      function mouseover() {
      		LineaCursor.style("opacity", "1");
      		Referencia.style("opacity", "1");
      		d3.select("#cuadro").style("visibility", "visible");
      }

      // Si estoy FUERA del area grafica GRANDE
      function mouseout() {
      		LineaCursor.style("opacity", "0");
      		Referencia.style("opacity", "0");
      		d3.select("#cuadro").style("visibility", "hidden");
      }

      // De acuerdo a donde esta el mouse de acuerdo al eje X
      // Usamos la funcion bisectDate ( definida) para obtener el indice al dato anterior y posterior
      // de la posicion acutal


      function mousemove() {
            var x0 = x.invert(d3.mouse(this)[0]);
            var i = bisectDate(data, x0, 1);
            var d0 = data[i - 1];
            var d1 = data[i];
            var d = x0 - d0.fecha > d1.fecha - x0 ? d1 : d0;

            Referencia.select("line")
            .attr("x1", x(d.fecha))
            .attr("y1", 0)
            .attr("x2", x(d.fecha))
            .attr("y2", Alto )
            .style("stroke-width", 2)
            .style("stroke", "#000000")
            .style("fill", "none");
            if(d.actual > d.clima){
              circle1.attr("transform", "translate(" + x(d.fecha) + "," + y(d.actual) + ")")
              .style("fill" , "#FF5733");
              circle2.attr("transform", "translate(" + x(d.fecha) + "," + y(d.clima) + ")")
              .style("fill" , "#dddddd");
            }else{
              circle1.attr("transform", "translate(" + x(d.fecha) + "," + y(d.actual) + ")")
              .style("fill" , "#3372FF");
              circle2.attr("transform", "translate(" + x(d.fecha) + "," + y(d.clima) + ")")
              .style("fill" , "#dddddd");
            }

            d3.select("#fecha").text(f_fecha(d.fecha));
            d3.select("#clima").text(f_valor(d.clima));
            if(d.actual > d.clima){
            	d3.select("#actual").text(f_valor(d.actual))
            						.style("color","#ff0000");
            	d3.select("#anomalia").text(f_valor(d.actual-d.clima))
            						.style("color","#ff0000");
            }else{
            	d3.select("#actual").text(f_valor(d.actual))
            						.style("color","#0000ff");
            	d3.select("#anomalia").text(f_valor(d.actual-d.clima))
            						.style("color","#0000ff");
            }


      	}


});
// Funcion para manejar el BRUSH sobre la MINIATURA
function brushed() {
  var selection = d3.event.selection;
  x.domain(selection.map(xMini.invert, xMini));
  GrafGrande.select("#clip-below>path").attr("d", area.y0(Alto));
  GrafGrande.select("#clip-above>path").attr("d", area.y0(0));
  GrafGrande.select(".area.above").attr("d", area.y0(function(d) {return y(d["actual"]);}));
  GrafGrande.select(".area.below").attr("d", area);
  GrafGrande.select("path.linea").attr("d", linea);
  GrafGrande.select(".x.axis").call(xAxis);
}

</script>
