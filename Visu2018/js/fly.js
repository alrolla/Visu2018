//================================================================
// Funcion que hace una transicion de una estacion a otra VOLANDO!
function  fly2Estacion( lat,lon){
  var Pestacion = ol.proj.transform([lon, lat], 'EPSG:4326', 'EPSG:3857');
  var duration = 2000;
  var start = +new Date();
  var pan = ol.animation.pan({
    duration: duration,
    source: /** @type {ol.Coordinate} */ (view.getCenter()),
    start: start
  });
  var bounce = ol.animation.bounce({
    duration: duration,
    resolution: 1* view.getResolution(),
    start: start
  });
  map.beforeRender(pan, bounce);
  view.setCenter(Pestacion);
  if (feature_o) {
    if(feature_o.get('content').Institucion == "SMN"){
       feature_o.setStyle(iconStyleSMN);
    }else{
      feature_o.setStyle(iconStyleINTA);
    }
  }
  var feature = vectorSource.getClosestFeatureToCoordinate(ol.proj.transform([lon, lat], 'EPSG:4326', 'EPSG:3857'));
  feature.setStyle(iconStyle2);
  feature_o=feature;
  var info = document.getElementById('info');
  var mp   = document.getElementById('map');

  var rect = mp.getBoundingClientRect();
  var t_id = document.getElementById('t_id');
  var t_nom= document.getElementById('t_nom');
  var t_lat= document.getElementById('t_lat');
  var t_lon= document.getElementById('t_lon');
  var t_alt= document.getElementById('t_alt');

  info.style.position = "absolute";
  info.style.left = (rect.right-224)+'px';
  info.style.top = (rect.top+2)+'px';

  if (feature) {
    feature_o=feature;
    feature.setStyle(iconStyle2);
    console.log("feature");
    console.log(feature);
    t_id.innerHTML =  feature.get('content').idOMM ;
    t_nom.innerHTML =  feature.get('content').Nombre;
    t_lat.innerHTML =  feature.get('content').Lat;
    t_lon.innerHTML =  feature.get('content').Lon;
    t_alt.innerHTML =  feature.get('content').Alt + " (m)";
    idOMM=feature.get('content').idOMM;

    $('#info').show();
  }

}
