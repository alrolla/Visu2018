
# VISUALIZACION DE LA INFORMACION 2018
## Trabajo Práctico 4 - Visualización de Temperaturas 
* Alfredo Luis Rolla 
* Juan Ignacio Mazza 

La primera parte corresponde al mapa (usamos **openlayers**) y la seleccion de las estaciones sobre las que luego **mostramos en D3 la visualizacion de la temperatura mínima, media y máxima.**
Los datos estan en una base de datos MySQL, por eso usamos php para manejarnos con la base de datos, usando javascript y **ajax** , para recuperar objetos de tipo **json**, para ser usados por **D3**.


 * **index.php**   : Codigo principal de la aplicacion
 * **st_temp.php** : Codigo escrito usando D3 para mostrar en modo dinamico las series temporales diarias de temperatura minima,media y maxima. Pasando como parametros el ID de la estacion y la variable a mostrar (Tmin,Tmed,Tmax).
 * **serieT.php** : Recupera un objeto json de la base de datos MySQL. El objeto es una serie temporal correspondiente a una estacion meteorologica que contiene los ultimos 365 datos de observaciones de la variable en analisis (Tmin,Tmed,Tmax) y 365 datos correspondientes a la media diaria de los ultimos 30 años ( 1981-2010). Por estandarizacion de la organizacion meteorologica mundial.
 * **estaciones** : Codigo que recupera la metadata de las estaciones


## Visualizacion propuesta Parte 1.

![](img/Paso_1.png?raw=true)

[link a la visualización implementada del Paso 1](http://ciclon.cima.fcen.uba.ar/Visu2018/)

![](img/Implementacion_Parte_1.png?raw=true)

## Visualizacion propuesta Parte 2.

![](img/Paso_2.png?raw=true)

[link a la visualización implementada del Paso 2](http://ciclon.cima.fcen.uba.ar/Visu2018/)

![](img/Implementacion_Parte_2.png?raw=true)
