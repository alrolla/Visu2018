<!DOCTYPE html>
<meta charset="utf-8">
<style>
<?php
	date_default_timezone_set('America/Argentina/Buenos_Aires');
    $username = "root";
    $password = "";
    $host = "localhost:3306";
    $database="SMN";

    $connection = mysqli_connect($host, $username, $password,$database);
    $nest=$_GET["nest"];
    $var=$_GET["var"];
    $titulo=array(
      "Prcp" => "Precipitación"
      );
    $sqlMETA="SELECT idOMM,NomEstacion,Institucion,Longitud,Latitud,Elevacion from SMN_META_ARG where idOMM=".$nest;
     //echo "<br>"$sqlDATA."\n";;
    $queryMETA = mysqli_query($connection,$sqlMETA);
    if ( ! $queryMETA) {
        echo mysqli_error($connection);
        die;
    }

	$rs_a = mysqli_fetch_assoc($queryMETA);


?>
body {
	  font: 10px sans-serif;
	}

	.axis path,
	.axis line {
	  fill: none;
	  stroke: black;
	  shape-rendering: crispEdges;
	}

	.x.axis path {
	  stroke: lightgrey;
	  stroke-width: 1;

	}

	.y.axis path {
	  stroke: lightgrey;
	  stroke-width: 1;

	}

	.tick line{
	  stroke: #aaaaaa;
	  opacity: 0.8;
	  stroke-dasharray: 2,2;
	}

	.area.above {
	  fill: #A9DFBF   ;

	}

	.area.below {
	  fill: #F0B27A;

	}

	.line {
	  fill: none;
	  stroke: #000000;
	  stroke-width: 2px;
	}

	.line2 {
	  fill: none;
	  stroke: #008800;
	  stroke-width: 1px;
	}

	.brush .extent {
	  stroke: #ffffff;
	  fill-opacity: .125;
	  shape-rendering: crispEdges;
	}

	.focus circle {
	  fill: none;
	  stroke: steelblue;
	}

	.overlay {
	  fill: none;
	  pointer-events: all;
	}

	.contextbar {

  		stroke: #0000ff;
	}

</style>
<script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="../js/d3/d3.js"></script>
<body>
	<div id="Titulo" style="position:relative;top:0px;left:0px;height:15px;width:620px;text-align: center;color:#000000;font-family:arial;font-size:14px;">
	</div>



<div id="cuadro" style="color:#000 ;  position: fixed;
    top: 23px;
    left: 574px;
    width: 100px;
    height: 60px;
    border: 1px solid #000000; visibility: hidden; ">
    <div id="fechal" style="position: fixed;left: 576px;top:25px;font-size:10px"> Fecha:&nbsp;&nbsp;&nbsp; </div><div id="fecha" style="position: fixed;left: 610px;top:25px;font-size:10px"> </div>
    <div id="climal" style="position: fixed;left: 576px;top:40px;font-size:10px"> Clima: </div><div id="clima" style="position: fixed;left: 610px;top:40px;font-size:10px"> </div>
    <div id="actuall" style="position: fixed;left: 576px;top:55px;font-size:10px"> Actual:&nbsp;&nbsp;&nbsp; </div><div id="actual" style="position: fixed;left: 610px;top:55px;font-size:10px"> </div>
    <div id="anomalial" style="position: fixed;left: 576px;top:70px;font-size:10px"> Anom.:&nbsp;&nbsp;&nbsp; </div><div id="anomalia" style="position: fixed;left: 610px;top:70px;font-size:10px"> </div>

</div>
<div style="position: absolute;top:203px;left:574px;width:91px;height:70px;z-index:9999;">
	<table bgcolor='#ffffff'style="color:#000000;font-family:arial;font-size:10px;" width='100%' >
	<tr>
	    <td>
	    <img src="../img/CIMA70.jpg" height="65" >
	    </td>
	</tr>
	</table>
</div>

<script>
$("#Titulo").html( 'Est: ' + <?php echo "'".$rs_a["idOMM"]."'" ?>
				   + ' - ' + <?php echo "'".$rs_a["NomEstacion"]."'" ?>
				   + ' / Lat: '+ <?php echo "'".$rs_a["Latitud"]."'" ?>
				   + ' / Lon: '+ <?php echo "'".$rs_a["Longitud"]."'" ?>
				   + ' / Alt: '+ <?php echo "'".$rs_a["Elevacion"]."'" ?>

);

var f_fecha=d3.timeFormat("%d-%m-%Y");
var f_valor=d3.format(".1f");
var margin = {
   			 top: 0,
   			 right: 42,
   			 bottom: 155,
    		left: 40
  			};
var margin2 = {
    		top: 270,
    		right: 10,
   			bottom: 40,
    		left: 40
  			};
var  width = 576 - margin.left - margin.right;
var  height = 400 - margin.top - margin.bottom;
var  height2 = 400 - margin2.top - margin2.bottom;

var parseDate = d3.timeParse("%Y-%m-%d");


var x =  d3.scaleTime().range([0, width]);
var x2 =  d3.scaleTime().range([0, width]);

var y = d3.scaleLinear().range([height, 0]);

var y2 = d3.scaleLinear().range([height2, 0]);


var xAxis = d3.axisBottom(x)
				.tickSize(-height);

var xAxis2 = d3.axisBottom(x2)
				.ticks(10);

var yAxis = d3.axisLeft(y)
			.ticks(10)
			.tickSize(-width);

var yAxis2 = d3.axisLeft(y2)
			.ticks(9)
			.tickSize(-width);

var brush = d3.brushX()
	.extent([[0, 0], [width, height2]])
    .on("brush", brushed);



var line = d3.area()
	.curve(d3.curveMonotoneX)
    .x(function(d) { return x(d.fecha); })
    .y(function(d) { return y(d.climaa); });



var area = d3.area()
	.curve(d3.curveMonotoneX)
    .x(function(d) { return x(d.fecha); })
    .y1(function(d) { return y(d.climaa); });

var line2 = d3.area()

    .x(function(d) { return x2(d.fecha); })
    .y(function(d) { return y2(d.actuala); });


var area2 = d3.area()
    .x(function(d) { return x2(d.fecha); })
    .y0(height2)
    .y1(function(d) { return y2(d.climaa); });

var bisectDate = d3.bisector(function(d) { return d.fecha; }).left

var svg = d3.select("body").append("svg")
  .attr("width", width + margin.left + margin.right)
  .attr("height", height + margin.top + margin.bottom);


svg.append("defs").append("clipPath")
  .attr("id", "clip")
  .append("rect")
  .attr("width", width)
  .attr("height", height);

var chart = svg.append("g")
  .attr("transform", "translate(" + 30 + "," + margin.top + ")");

var focus = chart.append("g")
  .attr("class", "focus")
  .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var context = chart.append("g")
  .attr("class", "context")
  .attr("transform", "translate(" + margin2.left  + "," + margin2.top   + ")");


d3.json("../php/pre.php?nest=<?php echo $nest ?>&var=<?php echo $var ?> ",  function(error, data) {
      //data.sort;
      i=1;
	  data.forEach(function(d) {
	  	d.fecha = parseDate(d.fecha);
	  	if(i==1){
	  		a_clima=+d.clima;
	  		a_actual=+d.actual;
	  	}else{
	  		a_clima=(+a_clima+(+d.clima)).toFixed(1);
	  		a_actual=(+a_actual+(+d.actual)).toFixed(1);
	  	}
	  	d.actuala = +a_actual;
	  	d.climaa = +a_clima;
	  	d.clima = +d.clima;
	  	if(d.actual == null){d.actual=0};
	  	d.actual = +d.actual;
	  	i=i+1;

		});


	x.domain(d3.extent(data, function(d) {return d.fecha;}));
    lims=Math.ceil(d3.max(data, function(d) { return Math.max(d.climaa, d.actuala);}));
	y.domain([
  		Math.floor(d3.min(data, function(d) { return Math.min(d.climaa, d.actuala);})),
  		(lims*1.1)
	]);

	x2.domain(x.domain());

	y2.domain([
  		Math.floor(d3.min(data, function(d) { return Math.min( d.actual);}))-1,
  		Math.ceil(d3.max(data, function(d) { return Math.max( d.actual);})+1)
	]);

	focus.datum(data);

	focus.append("clipPath")
  		.attr("id", "clip-below")
  		.append("path")
  		.attr("id", "clipbelowshape")
  		.attr("d", area.y0(height));

	focus.append("clipPath")
  		.attr("id", "clip-above")
  		.append("path")
  		.attr("id", "clipaboveshape")
  		.attr("d", area.y0(0));

	var clipaboveintersect = focus.append("clipPath")
  		.attr("id", "clipaboveintersect")
  		.attr("clip-path", "url(#clip)");

	clipaboveintersect.append("use")
  		.attr("xlink:href", "#clipaboveshape");

	var clipbelowintersect = focus.append("clipPath")
  		.attr("id", "clipbelowintersect")
  		.attr("clip-path", "url(#clip)");

	clipbelowintersect.append("use")
  		.attr("xlink:href", "#clipbelowshape");

	focus.append("g")
  		.attr("class", "x axis")
  		.attr("transform", "translate(0," + height + ")")
  		.call(xAxis);

	focus.append("g")
  		.attr("class", "y axis")
  		.style("font-size","9px")
  		.call(yAxis);

	focus.append("path")
  		.attr("class", "area above")
  		.attr("clip-path", "url(#clipaboveintersect)")
  		.attr("d", area.y0(function(d) {return y(d.actuala);}));

	focus.append("path")
  		.attr("class", "area below")
  		.attr("clip-path", "url(#clipbelowintersect)")
  		.attr("d", area);

	focus.append("path")
  		.attr("class", "line")
  		.attr("d", line)
  		.attr("clip-path", "url(#clip)");

  var focusx= focus.append("g")
      .attr("class", "focus")
      .style("opacity", "0");

  var circle1=focusx.append("circle")
      .attr("r", 4)
      .style("stroke" , "black")
      .style("fill" , "red");

  var circle2=focusx.append("circle")
      .attr("r", 4)
      .style("stroke" , "black")
      .style("fill" , "blue");

//   focusx.append("text")
//       .attr("y", -30)
//       .attr("dy", ".35em");

  var linea=focus.append("g")
      .attr("class", "focus")
      .style("opacity", "1");

  linea.append("line")
	  .attr("x1", 0)  //<<== change your code here
	  .attr("y1", 0)
	  .attr("x2", 0)  //<<== and here
	  .attr("y2", 0)
	  .style("stroke-width", 2)
	  .style("stroke", "#ffff")
	  .style("stroke-dasharray","2,2")
	  .style("fill", "none");



  focus.append("rect")
      .attr("class", "overlay")
      .attr("width", width)
      .attr("height", height)
      .on("mousemove", mousemove)
      .on("mouseover", mouseover)
	  .on("mouseout", mouseout);

  function mouseover() {
  		focusx.style("opacity", "1");
  		linea.style("opacity", "1");
  		d3.select("#cuadro").style("visibility", "visible");
	}


  function mouseout() {
  		focusx.style("opacity", "0");
  		linea.style("opacity", "0");
  		d3.select("#cuadro").style("visibility", "hidden");
	}

  function mousemove() {
	var x0 = x.invert(d3.mouse(this)[0]);
    var i = bisectDate(data, x0, 1);
    var d0 = data[i - 1];
    var d1 = data[i];
    var d = x0 - d0.fecha > d1.fecha - x0 ? d1 : d0;

   linea.select("line")
	  .attr("x1", x(d.fecha))
	  .attr("y1", 0)
	  .attr("x2", x(d.fecha))
	  .attr("y2", height )
	  .style("stroke-width", 1)
	  .style("stroke", "#000000")
	  .style("fill", "none");
    if(d.actuala > d.climaa){
    circle1.attr("transform", "translate(" + x(d.fecha) + "," + y(d.actuala) + ")")
    .style("fill" , "#7DCEA0");
    circle2.attr("transform", "translate(" + x(d.fecha) + "," + y(d.climaa) + ")")
    .style("fill" , "#dddddd");
    }else{
    circle1.attr("transform", "translate(" + x(d.fecha) + "," + y(d.actuala) + ")")
    .style("fill" , "#E59866");
    circle2.attr("transform", "translate(" + x(d.fecha) + "," + y(d.climaa) + ")")
    .style("fill" , "#dddddd");



    }
    d3.select("#fecha").text(f_fecha(d.fecha));
    d3.select("#clima").text(f_valor(d.climaa));
    if(d.actuala > d.climaa){
    	d3.select("#actual").text(f_valor(d.actuala)+' ('+ f_valor(d.actual)+')')
    						.style("color","#7DCEA0");
    	d3.select("#anomalia").text(f_valor(d.actuala-d.climaa))
    						.style("color","#7DCEA0");
    }else{
    	d3.select("#actual").text(f_valor(d.actuala)+' ('+ f_valor(d.actual)+')')
    						.style("color","#E59866");
    	d3.select("#anomalia").text(f_valor(d.actuala-d.climaa))
    						.style("color","#E59866");
    }
    //focusx.select("text").text(d.actual);

  	}


	focus.append("rect")
	  	.attr("x", 0)
	  	.attr("y", 0)
	  	.attr("height", height)
	  	.attr("width", width)
	  	.style("stroke", "black")
	  	.style("fill", "none")
	  	.style("stroke-width", 2);



  	focus.append("text")
  		.attr("transform", "rotate(-90)")
  		.attr("y", -65)
  		.attr("x", -height/2+90)
  		.attr("style","font-size  : 20" )
  		.attr("dy", ".71em")
  		.style("text-anchor", "end")
  		.text(<?php echo "'P R E C I P I T A C I O N '" ?>+" (mm)");

  	focus.append("text")
  		.attr("transform", "rotate(-90)")
  		.attr("y", -40)
  		.attr("x", -height*2/2-50)
  		.attr("style","font-size  : 16" )
  		.attr("dy", ".71em")
  		.style("text-anchor", "end")
  		.text(<?php echo "'(Diaria)'" ?>);

  	focus.append("text")
  		.attr("transform", "rotate(-90)")
  		.attr("y", -40)
  		.attr("x", -height/2+40)
  		.attr("style","font-size  : 16" )
  		.attr("dy", ".71em")
  		.style("text-anchor", "end")
  		.text(<?php echo "'(Acumulada)'"; ?>);


	context.datum(data);


 	var barWidth = width / data.length;


   var bar = context.selectAll("g")
      		.data(data)
    		.enter().append("g")
      		.attr("transform", function(d, i) { return "translate(" + i * barWidth + ",0)"; });

 	bar.append("rect")
 	  .attr("class","contextbar")
      .attr("y", function(d) { return (y2(d.actual)+0); })
      .attr("height", function(d) {return height2- y2(d.actual); })
      .attr("width", barWidth );

	context.append("g")
  		.attr("class", "x axis")
  		.attr("transform", "translate(0," + height2  + ")")
  		.call(xAxis2);
	context.append("g")
  		.attr("class", "x axis")
  		.style("font-size","9px")
  		.call(yAxis2);


	context.append("g")
  		.attr("class", "x brush")
  		.call(brush)
  		.selectAll("rect")
  		.attr("y", -6)
  		.attr("height", height2 + 7)
  		.style({
            "fill": "#69f",
            "fill-opacity": "0.9"
        });

	context.append("rect")
	  	.attr("x", 0)
	  	.attr("y", -1)
	  	.attr("height", height2)
	  	.attr("width", width)
	  	.style("stroke", "#000000")
	  	.style("fill", "none")
	  	.style("stroke-width", 2);

});


function brushed(data) {
  var selection = d3.event.selection;
  x.domain(selection.map(x2.invert, x2));
  var dataFiltered = data.filter(function(d, i) {
    if ( (d.fecha >= x.domain()[0]) && (d.fecha <= x.domain()[1]) ) {

      return d;
    }
  })
  i=1;
  dataFiltered.forEach(function(d) {

	if(i==1){
		a_clima=+d.clima;
		a_actual=+d.actual;
	}else{
		a_clima=(+a_clima+(+d.clima)).toFixed(1);
		a_actual=(+a_actual+(+d.actual)).toFixed(1);
	}
	d.actuala = +a_actual;
	d.climaa = +a_clima;
	d.clima = +d.clima;
	if(d.actual == null){d.actual=0};
	d.actual = +d.actual;
	i=i+1;

	});
  focus.datum(dataFiltered);
  y.domain(d3.extent(dataFiltered, function(d) { return d.climaa; }));
  lims=Math.ceil(d3.max(dataFiltered, function(d) { return Math.max(d.climaa, d.actuala);}));
  y.domain([0,(lims*1.05)]);
  focus.select("#clip-below>path").attr("d", area.y0(height));
  focus.select("#clip-above>path").attr("d", area.y0(0));
  focus.select(".area.above").attr("d", area.y0(function(d) {return y(d.actuala);}));
  focus.select(".area.below").attr("d", area);
  focus.select("path.line").attr("d", line);
  focus.select(".x.axis").call(xAxis);
  focus.select(".y.axis").call(yAxis);
}



</script>
