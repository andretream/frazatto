<?php
require('config.php');
//////////////////////

$alias = secure($_GET['alias']);
$rowCont = mysql_fetch_array(mysql_query("SELECT codigo, titulo, texto FROM paginas WHERE alias='$alias' LIMIT 1"));
?>
<!DOCTYPE HTML>
<html>

<head>
	<meta charset="utf-8">
	<title><?php echo NOMEDOSITE; ?> | <?php echo $rowCont['titulo']; ?></title>

	<!-- metas -->
	<meta name="description" content="<?php echo $description; ?>" />
	<meta name="keywords" content="<?php echo $keywords; ?>" />
	<meta name="robots" content="index, follow" />
	<meta name="rating" content="General" />
	<meta name="revisit-after" content="7 days" />
	<base href="<?php echo BASEURL; ?>" />
	<!-- FIM metas -->

	<!-- facebook -->
	<meta property="og:image" content="<?php echo BASEURL; ?>img/logo.png" />
	<link rel="image_src" type="image/jpeg" href="<?php echo BASEURL; ?>img/logo.png" />
	<meta property="og:title" content="<?php echo NOMEDOSITE; ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:url" content="http://www.<?php echo URLPADRAO . $_SERVER['REQUEST_URI']; ?>" />
	<!-- FIM facebook -->

	<link href="css/bootstrap.css" type="text/css" rel="stylesheet">
	<link href="css/estilo.css" type="text/css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="engine1/style.css" />
	<link rel="stylesheet" href="css/jquery.bxslider.css" type="text/css" />

</head>

<body>

	<!-- Header
============================== -->
	<?php include('inc_topo.php'); ?>


	<!-- Conteudo
============================== -->
	<div id="container" class="pt20">

		<!-- Lateral
	============================== -->
		<?php include('inc_lateral.php'); ?>

		<!-- Meio
	============================== -->
		<div id="meio">
			<div id="faixa-interna">
				<h3><?php echo ($rowCont['titulo']); ?></h3>
			</div>
			<p class="f12 cin64 mt25 mb35">
				<?php echo $rowCont['texto']; ?>
			</p>


			<?php
			$sql = mysql_query("SELECT codigo, titulo, alias, data_publica, valor, valor_promocao FROM produtos WHERE ativa='S' AND status='S' ORDER BY RAND() LIMIT 4");
			$totLan = mysql_num_rows($sql);

			if ($totLan > '0') { //se tiver lançamento
			?>
				<div id="faixa-cinza" class="mt60">
					<h3>OFERTAS ESPECIAIS</h3>
				</div>
				<div id="cont-produto" class="pt20">

					<?php
					while ($row = mysql_fetch_array($sql)) { //while lançamento

						//foto para exibição
						$sqlFoto = mysql_query("SELECT codigo, pp FROM fotos WHERE cod_pagina='" . $row['codigo'] . "' AND pp='produtos' ORDER BY codigo LIMIT 1");
						$totFoto = mysql_num_rows($sqlFoto);
						$rowFoto = mysql_fetch_array($sqlFoto);

						if ($totFoto > 0 && file_exists(SITEPATH . 'upload/imagens/paginas/fotos_' . $rowFoto['codigo'] . '_' . $rowFoto['pp'] . '.jpg'))
							$fotoProduto = SITEPATH . 'upload/imagens/paginas/fotos_' . $rowFoto['codigo'] . '_' . $rowFoto['pp'] . '.jpg';
						else
							$fotoProduto = "img/semfoto.jpg";

						//define tamanho da imagem
						$im = imagecreatefromjpeg($fotoProduto);
						$w = imagesx($im);
						$h = imagesy($im);

						if ($w > $h) {
							$tamanho = 'width="180"';
						} else if ($w < $h) {
							$tamanho = 'height="180"';
						} else {
							$tamanho = 'width="180" height="180"';
						}


						if (empty($row['alias'])) $alias_produto = '0';
						else $alias_produto = $row['alias'];
						//link detalhe
						$link_detalhe = 'produto/' . $alias_produto . '/' . $row['codigo'];
					?>
						<a href="<?php echo $link_detalhe; ?>">
							<div class="produto">
								<div class="image">
									<img src="<?php echo $fotoProduto; ?>" alt="<?php echo $row['titulo']; ?>" <?php echo $tamanho; ?> border="0">
								</div>
								<div class="dados">
									<p class="f13"><?php echo $row['titulo']; ?></p>

									<?php
									if ($row['valor'] != '0.00') { //exibir se tiver valor

										if ($row['valor_promocao'] != '0.00') { //valor promocional

											$maxPar = maxParcelas($row['valor_promocao'], MAXPARCELAS, VALORMIN);
									?>
											<p class="de">De R$ <?php echo decimal($row['valor']); ?></p>
											<p class="por">Por R$ <?php echo decimal($row['valor_promocao']); ?></p>

											<?php if ($maxPar > 0) { ?>
												<p class="vezes"><strong><?php echo $maxPar; ?>x</strong> de <strong>R$ <?php echo decimal($row['valor_promocao'] / $maxPar); ?></strong></p>
											<?php } ?>

										<?php
										} else { //valor normal

											$maxPar = maxParcelas($row['valor'], MAXPARCELAS, VALORMIN);
										?>
											<p class="por">Por R$ <?php echo decimal($row['valor']); ?></p>
											<?php if ($maxPar > 0) { ?>
												<p class="vezes"><strong><?php echo $maxPar; ?>x</strong> de <strong>R$ <?php echo decimal($row['valor'] / $maxPar); ?></strong></p>
											<?php } ?>
									<?php
										} //FIM //valor normal

									} //FIM //exibir se tiver valor
									?>
								</div>
								<input type="button" class="bt-comprar" name="comprar" value="COMPRAR">
							</div>
						</a>
					<?php } //FIM //while lançamento 
					?>

				</div>
			<?php } //FIM //se tiver lançamento 
			?>

		</div>
	</div>

	<div class="clear">&nbsp;</div>

	<!-- Footer
============================== -->
	<?php include('inc_rodape.php'); ?>


	<!-- Javascript
============================== -->
	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-512d3f98721dd6fc"></script>
	<script src="js/jquery.js"></script>
	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-512d3f98721dd6fc"></script>

	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="fancybox/source/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
	<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox();

			$(".chat").fancybox({
				autoSize: false,
				width: 640,
				height: 420
			});


			// Change title type, overlay closing speed
			$(".fancybox-effects-a").fancybox({
				helpers: {
					title: {
						type: 'outside'
					},
					overlay: {
						speedOut: 0
					}
				}
			});
		});
	</script>

	<script src="js/jquery.bxslider.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.slidermarcas').bxSlider({
				slideWidth: 100,
				minSlides: 2,
				maxSlides: 20,
				slideMargin: 1
			});
		});
	</script>

	<?php echo $analytics; ?>
</body>

</html>