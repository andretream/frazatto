<?php 
require('config.php'); 
///////////////////////

$busca = strtoupper(secure($_POST['busca']));
$busca_original = strtoupper(secure($_POST['busca']));
$busca = str_replace(" ", "%", $busca);
$SQLbusca = "AND (UPPER(titulo) LIKE \"%$busca%\" OR UPPER(busca) LIKE \"%$busca%\" OR UPPER(texto) LIKE \"%$busca%\" OR UPPER(tags) LIKE \"%$busca%\")";

$queryProd = "SELECT codigo, titulo, alias, data_publica, valor, valor_promocao FROM produtos WHERE status='S' AND ativa='S' $SQLbusca ORDER BY titulo";
$sqlProd = mysql_query($queryProd);
$totProd = mysql_num_rows($sqlProd);

if($totProd=='0') $totBusca = 'não localizou nenhum produto'; else if($totProd=='1') $totBusca = 'localizou 1 produto'; else $totBusca = 'localizou '.$totProd.' produtos';
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo NOMEDOSITE; ?> | Resultado da busca</title>

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
<meta property="og:type" content="article"/>
<meta property="og:url" content="http://www.<?php echo URLPADRAO.$_SERVER['REQUEST_URI']; ?>" /> 
<!-- FIM facebook -->

<link href="css/bootstrap.css" type="text/css" rel="stylesheet">
<link href="css/estilo.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="engine1/style.css" />
<link rel="stylesheet" href="css/jquery.bxslider.css" type="text/css" />

<!--Start of Zopim Live Chat Script-->
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//v2.zopim.com/?2PwFsO6sK8b862RfvVIpvWG0SBIaoufc';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
</script>
<!--End of Zopim Live Chat Script-->

</head>

<body>

<!-- Header
============================== -->
<?php include('inc_topo2.php'); ?>


<!-- Conteudo
============================== -->
<div id="container" class="pt20">
	
    <!-- Lateral
	============================== -->
	<?php include('inc_lateral.php'); ?>
    
    <!-- Meio
	============================== -->
    <div id="meio">
        <div id="faixa-cinza">
        	<h3>RESULTADO DA BUSCA</h3>
        </div>
        
        <div id="cont-busca">
        	<h4 class="f16 ">Sua busca por <strong>"<?php echo $busca_original; ?>"</strong> <?php echo $totBusca; ?></h4>
            <p>Você também pode localizar o produto desejado pelas categorias e marcas, se preferir</p>
        </div>
        
        <div id="cont-produto" class="pt20">
            
            <?php
			while($row = mysql_fetch_array($sqlProd)) 
			{//while
				
				//foto para exibição
				$sqlFoto = mysql_query("SELECT codigo, pp FROM fotos WHERE cod_pagina='".$row['codigo']."' AND pp='produtos' ORDER BY codigo LIMIT 1");
				$totFoto = mysql_num_rows($sqlFoto);
				$rowFoto = mysql_fetch_array($sqlFoto);
				
				if($totFoto > 0 && file_exists(SITEPATH.'upload/imagens/paginas/fotos_'.$rowFoto['codigo'].'_'.$rowFoto['pp'].'.jpg'))
				$fotoProduto = SITEPATH.'upload/imagens/paginas/fotos_'.$rowFoto['codigo'].'_'.$rowFoto['pp'].'.jpg';
				else
				$fotoProduto = "img/semfoto.jpg";
				
				//define tamanho da imagem
				$im = imagecreatefromjpeg($fotoProduto); 
				$w = imagesx($im); 
				$h = imagesy($im);
				
				if($w > $h) { $tamanho = 'width="180"'; }
				else if($w < $h) { $tamanho = 'height="180"'; }
				else { $tamanho = 'width="180" height="180"'; }
				
				//categoria principal
				$sqlCat = mysql_query("SELECT codigo, alias, cod_cat, rel_cod_cat FROM produtos_categorias, relaciona_produtos_categorias WHERE rel_cod_pro='".$row['codigo']."' AND codigo=rel_cod_cat_principal ORDER BY titulo LIMIT 1");
				$totCat = mysql_num_rows($sqlCat);
				while($rowCat = mysql_fetch_array($sqlCat)) {//while cateogira principal
					  
					  $aliasCat = $rowCat['alias'];
					  if($rowCat['rel_cod_cat']=='0') $alias_categoria = '/'.$rowCat['alias'];
					  
					  //categoria
					  $sqlSub = mysql_query("SELECT codigo, alias, cod_cat FROM produtos_categorias, relaciona_produtos_categorias WHERE rel_cod_pro='".$row['codigo']."' AND codigo=rel_cod_cat AND rel_cod_cat_principal='".$rowCat['codigo']."' ORDER BY titulo LIMIT 1");
					  $totSub = mysql_num_rows($sqlSub);
					  if($totSub=='0') $aliasSub = '0';
					  while($rowSub = mysql_fetch_array($sqlSub)) {//while cateogira
						  $aliasSub = $rowSub['alias'];
						  $alias_categoria = '/'.$rowCat['alias'].'/'.$rowSub['alias'];
					  }//FIM //while cateogira
				}//FIM //while cateogira principal
				
				//marca
				$sqlMar = mysql_query("SELECT codigo, alias FROM produtos_marcas, relaciona_produtos_marcas WHERE rel_cod_pro='".$row['codigo']."' AND codigo=rel_cod_mar ORDER BY titulo LIMIT 1");
				while($rowMar = mysql_fetch_array($sqlMar)) {//while marca
					if($totCat > '0') {
						$alias_categoria = '/'.$aliasCat.'/'.$aliasSub.'/'.$rowMar['alias'];
					} else {
						$alias_categoria = '/0/0/'.$rowMar['alias'];
					}
				}//FIM //while marca
				
				if(empty($row['alias'])) $alias_produto = '0'; else $alias_produto = $row['alias'];
				//link detalhe
				$link_detalhe = 'produto/'.$alias_produto.'/'.$row['codigo'].$alias_categoria;
			?>
            <a href="<?php echo $link_detalhe; ?>">
            <div class="produto">
            	<?php if(novidade(15, $row['data_publica'])) { ?><div class="faixanovo"></div><?php } ?>
                <div class="image">
                	<img src="<?php echo $fotoProduto; ?>" alt="<?php echo $row['titulo']; ?>" width="180" height="180" border="0">
                </div>
                <div class="dados">
                <p class="f13"><?php echo $row['titulo']; ?></p>
                
                <?php 
				if($row['valor']!='0.00') {//exibir se tiver valor
					
					if($row['valor_promocao']!='0.00') 
					{ //valor promocional
					
						$maxPar = maxParcelas($row['valor_promocao'], MAXPARCELAS, VALORMIN);
				?>	
                        <p class="de">De R$ <?php echo decimal($row['valor']); ?></p>
                        <p class="por">Por R$ <?php echo decimal($row['valor_promocao']); ?></p>
                        
                        <?php if($maxPar > 0) { ?>
                        	<p class="vezes"><strong><?php echo $maxPar; ?>x</strong> de <strong>R$ <?php echo decimal($row['valor_promocao'] / $maxPar); ?></strong></p>
                        <?php } ?>
                
					<?php 
                    } else { //valor normal
						
						$maxPar = maxParcelas($row['valor'], MAXPARCELAS, VALORMIN);
                    ?>
                        <p class="apenas">Por apenas</p>
                        <p class="por">R$ <?php echo decimal($row['valor']); ?></p>
                        <?php if($maxPar > 0) { ?>
                        	<p class="vezes"><strong><?php echo $maxPar; ?>x</strong> de <strong>R$ <?php echo decimal($row['valor'] / $maxPar); ?></strong></p>
                        <?php } ?>
                <?php 
					} //FIM //valor normal
					
				} //FIM //exibir se tiver valor
				?>
                </div>
                <input type="submit" class="bt-comprar" name="comprar" value="COMPRAR">
            </div> 
            </a>
            <?php }//FIM //while ?>
            
        </div>
        
        <div class="clear"></div>
        <div class="alerta">
        	<a href="naoencontrei">
            <div class="alerta-interna">
                <div class="float-left">
                    <img src="img/lupagrande.png" />
                </div>
                <div class="float-left">
                    <h3 class="swisb">Não encontrou o produto que procurava?</h3>
                    <p>Clique aqui que o localizaremos para você.</p>
                </div>
            </div>
            </a>
        </div>
        <!--alerta-->
        
    </div>
</div>

<div class="clear">&nbsp;</div>

<!-- Footer
============================== -->
<?php include('inc_rodape.php'); ?>


<!-- Javascript
============================== -->
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
			width     : 640,
			height    : 420
		});
		
		
		// Change title type, overlay closing speed
		$(".fancybox-effects-a").fancybox({
			helpers: {
				title : {
					type : 'outside'
				},
				overlay : {
					speedOut : 0
				}
			}
		});
	});
</script>

<script src="js/jquery.bxslider.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
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