<?php
/*  La variable $url se utiliza para llamar la API, las demas variables se utilizan para cargar el contenido.
*/

	$url = "http://datos.labmde.org/api.php?id=25";
	$JSON = file_get_contents($url);
	$data = json_decode($JSON);

	$aux = 0;
	$ID = 0;
	foreach ($data as $obj) {
		if($ID != $obj->identificador){
			$ids[$aux] = $obj->identificador;
			$aux++;
		}
		$ID = $obj->identificador;
	}
	
	$i = 0;
	$mensajes = array();
	foreach ($ids as $id) {
/*Aqui se llaman los campos especificos y se nombran para usuarlos luego en la visualizacion
*/

		$url = "http://datos.labmde.org/api.php?identificador={$id}";
		$JSON = file_get_contents($url);
		$data = json_decode($JSON);
		foreach ($data as $obj) {
			if($obj->id_campo === 5){
				$mensajes[$id]['nombre'] = $obj->contenido;
				$mensajes[$id]['timestamp'] = $obj->timestamp;
			}
			if($obj->id_campo === 155){
				$mensajes[$id]['libro'] = $obj->contenido;
				$mensajes[$id]['timestamp'] = $obj->timestamp;
			}
			if($obj->id_campo === 156){
				$mensajes[$id]['mensaje'] = $obj->contenido;
				$mensajes[$id]['timestamp'] = $obj->timestamp;
			}			
		}
		$i++;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Reseñas de los Libros de Fernando González</title>
	<link rel="stylesheet" href="styles/styles.css">
</head>
<body>
	<div></div>
	<div class="">
		<header id="header" class="cabezote">
		</header><!-- /header -->
		<section>

<!-- Seccion de la visualizacion -->

			<div class="mensajes-historial">
				<?php $aux = 0; foreach ($mensajes as $mensaje): if($aux == 4): break; endif; ?>			
				<div id="mensaje_<?php echo $aux ?>" class="mensaje">
					<span class="autor">Reseña de: <?php echo (isset($mensaje['libro'])) ? $mensaje['libro'] : 'Anónimo' ?></span>
					<p><?php echo (isset($mensaje['mensaje'])) ? $mensaje['mensaje'] : 'No tengo ningún mensaje.' ?></p>
					<span class="autor"><?php echo (isset($mensaje['nombre'])) ? $mensaje['nombre'] : 'Anónimo' ?></span>
					<span id="hora_<?php echo $aux ?>" class="hora"></span>
					<input id="timestamp_<?php echo $aux ?>" type="hidden" value="<?php echo (isset($mensaje['timestamp'])) ? date("Ymd H:i",$mensaje['timestamp']) : '0' ?>">
				</div>
				<?php $aux++; endforeach ?>
			</div>
			<div class="botones">
				<a id="btnCargarMensajes" href="#" class="enviar-mensaje">+ Reseñas</a>
			</div>			
		</section>
	</div>
	<div class="cityl"></div>
	<div class="cityr"></div>

	<footer>

	</footer>
	<script src="js/jquery.min.js"></script>
	<script src="js/moment.min.js"></script>
	<script src="js/isotope.min.js"></script>	
	<script type="text/javascript">	
		$(function(){
			var aux = 4;

			var $container = $('.mensajes-historial');
			// init
			$container.isotope({
			  // options
			  itemSelector: '.mensaje',
			  layoutMode: 'fitRows'
			});			
			$("#btnCargarMensajes").click(function(e){
				e.preventDefault();
				var $newItems = [];
				<?php $aux = 1; foreach ($mensajes as $mensaje): ?>	
				<?php
				if(isset($mensaje['mensaje'])){
					$mensaje['mensaje'] = preg_replace("/[\n|\r|\n\r]/i"," ",$mensaje['mensaje']); 
					$mensaje['mensaje'] = htmlspecialchars($mensaje['mensaje']);
				} 
				else{
					$mensaje['mensaje'] = NULL;
				}
				?>
				$newItems[<?php echo $aux ?>] = $('<div id="mensaje_<?php echo $aux ?>" class="mensaje"><p>' + "<?php echo (isset($mensaje['mensaje'])) ? $mensaje['mensaje'] : 'No tengo ningún mensaje.' ?>" + '</p><span class="autor">' + "<?php echo (isset($mensaje['nombre'])) ? htmlspecialchars($mensaje['nombre']) : 'Anónimo' ?>" + '</span><span id="hora_<?php echo $aux ?>" class="hora"></span><input id="timestamp_<?php echo $aux ?>" type="hidden" value="' + "<?php echo (isset($mensaje['timestamp'])) ? date('Ymd H:i',$mensaje['timestamp']) : '0' ?>" + '></div>');
				<?php $aux++; endforeach ?>

				$('.mensajes-historial').isotope( 'insert', $newItems[aux] );
				aux++;				
			});			
		});
	</script>
</body>
</html>
