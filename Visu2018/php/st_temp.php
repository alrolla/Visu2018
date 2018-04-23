<!DOCTYPE html>
<meta charset="iso-8859-1" />
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
      "Tmax" => "Temperatura Maxima",
      "Tmin" => "Temperatura Minima",
      "Tmed" => "Temperatura Media"
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

	.tick line{
	  stroke: #aaaaaa;
	  opacity: 0.8;
	  stroke-dasharray: 2,2;
	}

	.area.above {
	  fill: rgb(255, 0, 0);
/*
	  stroke: #999999;
	  stroke-width: 1.5px;
 */
	}

	.area.below {
	  fill: rgb(0, 0, 255);
/*
	  stroke: #999999;
	  stroke-width: 1.5px;
 */
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

</style>
<script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="../js/d3/d3.js"></script>
<body>
	<div id="Titulo" style="position:relative;top:0px;left:10px;height:15px;text-align: center;color:#000000;font-family:arial;font-size:14px;">
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
<div style="position: absolute;top:257px;left:574px;width:91px;height:70px;z-index:9999;">
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
    bottom: 100,
    left: 40
  },
  margin2 = {
    top: 330,
    right: 10,
    bottom: 40,
    left: 40
  },
  width = 600 - margin.left - margin.right,
  height = 400 - margin.top - margin.bottom,
  height2 = 400 - margin2.top - margin2.bottom;

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
			.tickSize(-width);


var brush = d3.brushX()
	.extent([[0, 0], [width, height2]])
    .on("brush", brushed);



var line = d3.area()
	.curve(d3.curveMonotoneX)
    .x(function(d) { return x(d.fecha); })
    .y(function(d) { return y(d.clima); });



var area = d3.area()
	.curve(d3.curveMonotoneX)
    .x(function(d) { return x(d.fecha); })
    .y1(function(d) { return y(d.clima); });

var line2 = d3.area()

    .x(function(d) { return x2(d.fecha); })
    .y(function(d) { return y2(d.clima); });


var area2 = d3.area()
    .x(function(d) { return x2(d.fecha); })
    .y0(height2)
    .y1(function(d) { return y2(d.clima); });

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
  .attr("transform", "translate(" + 5 + "," + margin.top + ")");

var focus = chart.append("g")
  .attr("class", "focus")
  .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var context = chart.append("g")
  .attr("class", "context")
  .attr("transform", "translate(" + margin2.left  + "," + margin2.top   + ")");

d3.json("../php/ts.php?nest=<?php echo $nest ?>&var=<?php echo $var ?> ",  function(error, data) {
      //data.sort;
	  data.forEach(function(d) {
	  d.fecha = parseDate(d.fecha);
	  d["clima"] = +d["clima"];
	  if(d.actual == null){d.actual=d.clima};
	  d["actual"] = +d["actual"];

	});

	x.domain(d3.extent(data, function(d) {return d.fecha;}));

	y.domain([
  		Math.floor(d3.min(data, function(d) { return Math.min(d["clima"], d["actual"]);}))-1,
  		Math.ceil(d3.max(data, function(d) { return Math.max(d["clima"], d["actual"]);})+1)
	]);

	x2.domain(x.domain());
	y2.domain(y.domain());

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
  		.attr("class", "x axis")
  		.call(yAxis);

	focus.append("path")
  		.attr("class", "area above")
  		.attr("clip-path", "url(#clipaboveintersect)")
  		.attr("d", area.y0(function(d) {return y(d["actual"]);}));

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
  		.attr("y", -40)
  		.attr("x", -height/2+100)
  		.attr("style","font-size  : 20" )
  		.attr("dy", ".71em")
  		.style("text-anchor", "end")
  		.text(<?php echo "'".$titulo[$var]."'" ?>+" (�C)");



	context.datum(data);

	context.append("clipPath")
  		.attr("id", "clip-below2")
  		.append("path")
  		.attr("d", area2.y0(height2));

	context.append("clipPath")
  		.attr("id", "clip-above2")
  		.append("path")
  		.attr("d", area2.y0(0));

	context.append("path")
  		.attr("class", "area above")
  		.attr("clip-path", "url(#clip-above2)")
  		.attr("d", area2.y0(function(d) {return y2(d["actual"]);}));

	context.append("path")
  		.attr("class", "area below")
  		.attr("clip-path", "url(#clip-below2)")
  		.attr("d", area2);

	context.append("path")
  		.attr("class", "line2")
  		.attr("d", line2);

	context.append("g")
  		.attr("class", "x axis")
  		.attr("transform", "translate(0," + height2 + ")")
  		.call(xAxis2);

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
	  	.attr("y", 0)
	  	.attr("height", height2)
	  	.attr("width", width)
	  	.style("stroke", "#222222")
	  	.style("fill", "none")
	  	.style("stroke-width", 0.5);

});
function brushed() {
  var selection = d3.event.selection;
  x.domain(selection.map(x2.invert, x2));
  focus.select("#clip-below>path").attr("d", area.y0(height));
  focus.select("#clip-above>path").attr("d", area.y0(0));
  focus.select(".area.above").attr("d", area.y0(function(d) {return y(d["actual"]);}));
  focus.select(".area.below").attr("d", area);
  focus.select("path.line").attr("d", line);
  focus.select(".x.axis").call(xAxis);
}




</script>
