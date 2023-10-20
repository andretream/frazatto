<?php 
require('config.php'); 
///////////////////////

$codigo = secure($_GET['codigo']);
$alias = secure($_GET['alias']);
$cat = secure($_GET['cat']);
$sub = secure($_GET['sub']);
$mar = secure($_GET['mar']);

//tipo categoria
if(!empty($_GET['cat']) && $_GET['cat']!='0') {
	
	//consulta categoria
	$sqlCat = mysql_query("SELECT codigo, titulo, alias FROM produtos_categorias WHERE alias='$cat' LIMIT 1");
	$totCat = mysql_num_rows($sqlCat);
	$rowCat = mysql_fetch_array($sqlCat);
	
	//titulo de navegação
	$titulo_navegacao = '<h3>'.$rowCat['titulo'].'</h3>';
	
	if(!empty($_GET['sub'])) {
	
		//consulta subcategoria
		$sqlSub = mysql_query("SELECT codigo, titulo, alias FROM produtos_categorias WHERE cod_cat!='0' AND alias='$sub' LIMIT 1");
		$totSub = mysql_num_rows($sqlSub);
		$rowSub = mysql_fetch_array($sqlSub);
		
		//titulo de navegação
		$titulo_navegacao = '<h3>'.$rowCat['titulo'].'</h3><span class="tituSub">'.$rowSub['titulo'].'</span>';
	}
	
	if(!empty($_GET['mar'])) {
	
		//consulta marcas
		if(empty($_GET['mar']))
		$sqlMar = mysql_query("SELECT codigo, alias FROM produtos_marcas, relaciona_produtos_marcas WHERE rel_cod_pro='$codigo' AND codigo=rel_cod_mar ORDER BY titulo LIMIT 1");
		else
		$sqlMar = mysql_query("SELECT codigo, titulo, alias FROM produtos_marcas WHERE alias='$mar' LIMIT 1");
		
		$totMar = mysql_num_rows($sqlMar);
		$rowMar = mysql_fetch_array($sqlMar);
		
		$titulo_categoria = $titulo_navegacao.' / '.$rowSub['titulo'];
	}
	
}//FIM //tipo categoria

else {
	
	//categoria principal
	$sqlCat = mysql_query("SELECT codigo, alias, cod_cat, rel_cod_cat, titulo FROM produtos_categorias, relaciona_produtos_categorias WHERE rel_cod_pro='$codigo' AND codigo=rel_cod_cat_principal ORDER BY titulo LIMIT 1");
	$totCat = mysql_num_rows($sqlCat);
	while($rowCat = mysql_fetch_array($sqlCat)) {//while cateogira principal
		  
		  								  //titulo de navegação
		  if($rowCat['rel_cod_cat']=='0') $titulo_navegacao = '<h3>'.$rowCat['titulo'].'</h3>';
		  
		  //categoria
		  $sqlSub = mysql_query("SELECT codigo, alias, cod_cat, titulo FROM produtos_categorias, relaciona_produtos_categorias WHERE rel_cod_pro='$codigo' AND codigo=rel_cod_cat AND rel_cod_cat_principal='".$rowCat['codigo']."' ORDER BY titulo LIMIT 1");
		  $totSub = mysql_num_rows($sqlSub);
		  while($rowSub = mysql_fetch_array($sqlSub)) {//while cateogira
			  
			  //titulo de navegação
			  $titulo_navegacao = '<h3>'.$rowCat['titulo'].'</h3><span class="tituSub">'.$rowSub['titulo'].'</span>';
			  
		  }//FIM //while cateogira
	}//FIM //while cateogira principal
	
	//marca
	$sqlMar = mysql_query("SELECT codigo, alias, titulo FROM produtos_marcas, relaciona_produtos_marcas WHERE rel_cod_pro='$codigo' AND codigo=rel_cod_mar AND alias='$mar' ORDER BY titulo LIMIT 1");
	$totMar = mysql_num_rows($sqlMar);
	$rowMar = mysql_fetch_array($sqlMar);
	
	
}

//consulta produto
$query = "SELECT * FROM produtos WHERE codigo='$codigo' LIMIT 1";
$sqlProd = mysql_query($query);
$totProd = mysql_num_rows($sqlProd);

if($totProd=='0') {
	echo '<meta http-equiv="refresh" content="0;URL=/404">';
	exit;
}
$rowProd = mysql_fetch_array($sqlProd);

//consulta um modelo q o produto esteja vinculado
$cod_modelo = mysql_result(mysql_query("SELECT rel_cod_mod FROM relaciona_produtos_modelos WHERE rel_cod_pro='".$rowProd['codigo']."' LIMIT 1"),0);


//foto para exibição
$fotoProd = mysql_query("SELECT codigo, pp FROM fotos WHERE cod_pagina='".$rowProd['codigo']."' AND pp='produtos' ORDER BY codigo LIMIT 1");
$totFotoProd = mysql_num_rows($fotoProd);
$rfProd = mysql_fetch_array($fotoProd);

$caminho_foto_grande = SITEPATH.'upload/imagens/paginas/fotos_'.$rfProd['codigo'].'_'.$rfProd['pp'].'.jpg';
$caminho_foto_s = SITEPATH.'upload/imagens/paginas/fotos_s_'.$rfProd['codigo'].'_'.$rfProd['pp'].'.jpg';
if($totFotoProd > '0' && file_exists($caminho_foto_grande)) {

	$fotoGrande = $caminho_foto_grande;
	$tem_foto = '1';
	$fotoTumb = $caminho_foto_grande;
	
} else {

	$fotoGrande = "img/semfoto.jpg";
	$tem_foto = '0';
	$fotoTumb = "img/logomarca.png";
}

//Otimização
if(!empty($rowProd['keywords'])) $keywords_prod = $rowProd['keywords']; else $keywords_prod = $rowMeta['keywords'];
if(!empty($rowProd['description'])) $description_prod = $rowProd['description']; else $description_prod = $rowMeta['description'];

//titulo navegação
$titulo_pagina = $rowProd['titulo'];
$titulo_categoria = $rowCat['titulo']; 
$link_categoria = 'produtos/categoria/'.$rowCat['alias'];

if(empty($titulo_navegacao)) $titulo_navegacao = '<h3>Produto</h3>';
?>
<!DOCTYPE HTML>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<title><?php echo NOMEDOSITE; ?> | <?php echo $titulo_pagina; ?></title>

<!-- metas -->
<meta name="description" content="<?php echo $description_prod; ?>" />
<meta name="keywords" content="<?php echo $keywords_prod; ?>" />
<meta name="robots" content="index, follow" />
<meta name="rating" content="General" />
<meta name="revisit-after" content="7 days" />
<base href="<?php echo BASEURL; ?>" />
<!-- FIM metas -->

<!-- facebook -->
<meta property="og:image" content="<?php echo BASEURL.$fotoTumb; ?>" />
<link rel="image_src" type="image/jpeg" href="<?php echo BASEURL.$fotoTumb; ?>" />
<meta property="og:title" content="<?php echo NOMEDOSITE; ?> | <?php echo $titulo_pagina; ?>" /> 
<meta property="og:type" content="article"/>
<meta property="og:url" content="http://www.<?php echo URLPADRAO.$_SERVER['REQUEST_URI']; ?>" /> 
<!-- FIM facebook -->

<link href="css/bootstrap.css" type="text/css" rel="stylesheet">
<link href="css/estilo.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="engine1/style.css" />
<link rel="stylesheet" href="css/jquery.bxslider.css" type="text/css" />

<link rel="stylesheet" href="zoom/css/jquery.fancybox.css" />



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
        <div id="faixa-cinza">
        	<?php echo $titulo_navegacao; ?>
            <span class="right cin64 pt10 mr15">&nbsp;</span>
        </div>
        
        <div class="clear">&nbsp;</div>
        
       	<div id="esquerda-prod">
        <div id="gallery_01" class="image">
            <div id="produto-img">
                <?php if($tem_foto=='1') {//produto com foto ?>
                    <img src="<?php echo $caminho_foto_s; ?>" alt="<?php echo $rowProd['titulo']; ?>" width="426" height="426" id="img_01" border="0" data-zoom-image="<?php echo $fotoGrande; ?>" />
                <?php } else {//produto sem foto ?>
                	<img src="<?php echo $fotoGrande; ?>" alt="Sem foto" width="426" height="426" />
                <?php }//FIM //produto sem foto ?>
            </div>
            <?php
			//fotos para exibição
			$sqlFoto = mysql_query("SELECT codigo, pp FROM fotos WHERE cod_pagina='".$rowProd['codigo']."' AND pp='produtos' ORDER BY codigo");
			$totFoto = mysql_num_rows($sqlFoto);
			if($totFoto > '0') {//se tiver fotos adicionais
			?>
				<?php
				while($rowFoto = mysql_fetch_array($sqlFoto)) 
				{//while fotos 
				
					$caminhoIMG = SITEPATH."/upload/imagens/paginas/fotos_".$rowFoto['codigo']."_".$rowFoto['pp'].".jpg";
					$caminhoIMGs = SITEPATH."/upload/imagens/paginas/fotos_s_".$rowFoto['codigo']."_".$rowFoto['pp'].".jpg";
					$caminhoIMGtumb = SITEPATH."/upload/imagens/paginas/fotos_tumb_".$rowFoto['codigo']."_".$rowFoto['pp'].".jpg";
					
					if(file_exists($caminhoIMG)) {//existe foto
				?>
				<div class="fotomini">
					<a href="#" data-image="<?php echo $caminhoIMGs; ?>" data-zoom-image="<?php echo $caminhoIMG; ?>">
                    <img src="<?php echo $caminhoIMGtumb; ?>" alt="<?php echo $rowProd['titulo']; ?>" width="65" height="65" border="0" id="img_01" />
                    </a>
				</div>
			<?php 
					}//FIM //existe foto
				}//FIM //while fotos 
			}//FIM //se tiver fotos adicionais
			?>
            
            <div class="clear">&nbsp;</div>
            
            <!-- AddThis Button BEGIN -->
            <div class=" left addthis_toolbox addthis_default_style addthis_16x16_style">
            <a class="addthis_button_facebook"></a>
            <a class="addthis_button_twitter"></a>
            <a class="addthis_button_email"></a>
            <a class="addthis_button_compact"></a>
            </div>
            <!-- AddThis Button END -->
        </div>
        </div>
        <div id="direita-prod">
        	<h2 class="mb10"><?php echo $rowProd['titulo']; ?></h2>
            <?php if(!empty($rowProd['original'])) { ?><p class="cod"><strong>Num. Original:</strong> <?php echo $rowProd['original']; ?></p><?php } ?>
            <?php if(!empty($rowProd['fabrica'])) { ?><p class="cod"><strong>Num. Fábrica:</strong> <?php echo $rowProd['fabrica']; ?></p><?php } ?>
            <?php if(!empty($rowProd['fornecedor'])) { ?><p class="cod"><strong>Fornecedor:</strong> <?php echo $rowProd['fornecedor']; ?></p><?php } ?>
            <?php /*?><?php if(!empty($rowProd['aplicacao'])) { ?><p class="cod"><strong>Aplicação:</strong> <?php echo $rowProd['aplicacao']; ?></p><?php } ?>
            <?php if(!empty($rowMar['titulo'])) { ?><p class="cod"><strong>Marca:</strong> <?php echo $rowMar['titulo']; ?></p><?php } ?>
            <?php if(!empty($rowProd['ano'])) { ?><p class="cod"><strong>Ano:</strong> <?php echo $rowProd['ano']; ?></p><?php } ?>
            <?php if(!empty($rowProd['modelo'])) { ?><p class="cod"><strong>Modelo:</strong> <?php echo $rowProd['modelo']; ?></p><?php } ?><?php */?>
            
            <div class="clear mt20">&nbsp;</div>
            
            <form name="comprar" action="carrinho.php?action=add" method="post">
                <input type="hidden" name="cod_prod" value="<?php echo $rowProd['codigo']; ?>" />
                <input type="hidden" name="qtd" value="1" />
                
				<?php if($rowProd['valor'] > '0') {//se tiver valor ?>
                    <?php if($rowProd['valor_promocao']!='0.00') {//valor promocional ?>
                        <div class="valorde">
                            <span class="f13">De R$ <?php echo decimal($rowProd['valor']); ?> por</span>
                        </div>
                        <div class="fd-valor">
                            <h2 class="valor">
                                R$ <?php echo decimal($rowProd['valor_promocao']); ?>
                            </h2>
                            <?php 
							$maxPar = maxParcelas($rowProd['valor_promocao'], MAXPARCELAS, VALORMIN);
							if($maxPar > 0) { 
							?>
                            	<span class="f12">À vista</span>
                        	<?php } ?>
                        </div>
                        <input type="hidden" name="valor" value="<?php echo $rowProd['valor_promocao']; ?>" />
                    <?php } else {//valor normal ?>
                        <div class="valorde">
                            <span class="f13">Por apenas</span>
                        </div>
                        <div class="fd-valor">
                            <h2 class="valor">
                                R$ <?php echo decimal($rowProd['valor']); ?>
                            </h2>
                            <?php 
							$maxPar = maxParcelas($rowProd['valor'], MAXPARCELAS, VALORMIN);
							if($maxPar > 0) { 
							?>
                            	<span class="f12">À vista</span>
                        	<?php } ?>
                        </div>
                        <input type="hidden" name="valor" value="<?php echo $rowProd['valor']; ?>" />
                    <?php }//FIM //valor normal ?>
                <?php }//FIM //se tiver valor ?>
                
                <?php
                //consulta variações
                $estoque = '0';
                $est = mysql_query("SELECT codigo, titulo, estoque FROM variacoes WHERE ativa='S' AND status='S' AND codPro='".$rowProd['codigo']."' AND estoque>'0' ORDER BY titulo");
                $est_tot = mysql_num_rows($est);
                
                if($est_tot > '0') //exibe se existir
                {
                ?>
                <label><strong>Selecione uma opção</strong></label>
                <select name="cod_var" class="select">
                    <?php
                    while($es = mysql_fetch_array($est)) 
                    {
                        $estoque += $es['estoque'];
                        if($es['estoque'] > '0') {
                    ?>
                    <option value="<?php echo $es['codigo']; ?>"><?php echo $es['titulo']; ?></option>
                    <?php
                        }
                    } 
                    ?>
                </select>
                <?php
                } else {
                    $estoque = $rowProd['estoque']; 
                }
                
                if($estoque > '0' && $rowProd['valor'] > '0') $exibirBTcomprar = '1';//exibir botão de compra
                else $exibirBTcomprar = '0';//não exibir botão
                ?>
                
                <?php if($exibirBTcomprar=='1') { ?>
                <input type="image" src="img/btcompra.jpg">
                
                <div class="clear">&nbsp;</div>
                
                <a class="fancybox.iframe frete" href="calcularfrete.php?codigo=<?php echo $rowProd['codigo']; ?>"><img src="img/calcula.jpg" border="0" alt="Calcule o valor e o prazo de entrega aqui"></a>	
                <a class="fancybox fancybox.iframe pagamento ml20" href="comopagar.php?codigo=<?php echo $rowProd['codigo']; ?>"><img src="img/comopagar.jpg" border="0" alt="Calcule o valor e o prazo de entrega aqui"></a>	
                <?php } else { ?>
                    <h5 style="color:#C00;">Indisponível</h5>
                <?php } ?>
            
            </form>
            
        </div>
        
        <div class="clear">&nbsp;</div>

        <?php if(!empty($rowProd['texto'])) { ?>
        <div id="faixa-cinza" class="mt25">
        	<h3>DESCRIÇÃO</h3>
		</div>
        <p class="f12 cin64 mt20">
        <?php 
		$texto = str_replace("&lt;p&gt;", "", $rowProd['texto']); 
		$texto = str_replace("&lt;/p&gt;", "<br>", $texto); 
		$texto = str_replace("&amp;quot;", '"', $texto); 
		$texto = str_replace("&nbsp;", '"', $texto); 
		$texto = str_replace("&amp;nbsp;", '"', $texto); 
		$texto = str_replace('"', '', $texto); 
		
		echo $texto
		?>
        </p>
        <?php } ?>
        
        <?php
		//veja também
		$sqlVeja = mysql_query("
		SELECT DISTINCT(codigo), titulo, alias, data_publica, valor, valor_promocao 
		FROM produtos, relaciona_produtos_modelos 
		WHERE ativa='S' AND status='S' AND codigo!='".$rowProd['codigo']."' AND rel_cod_mod='$cod_modelo' AND rel_cod_pro=codigo 
		ORDER BY RAND() LIMIT 4");
		$totVeja = mysql_num_rows($sqlVeja);
		
		if($totVeja > 0) {//se tiver produtos pro conheça também
		?>
        <div id="faixa-cinza" class="mt50">
        	<h3>COMPRE TAMBÉM</h3>
		</div>
        
       <div id="cont-produto" class="pt20">
            
            
            <?php
			while($row = mysql_fetch_array($sqlVeja)) 
			{//while lançamento
				
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
					$alias_categoria = '/'.$aliasCat.'/'.$aliasSub.'/'.$rowMar['alias'];
				}//FIM //while marca
				
				//link detalhe
				$link_detalhe = 'produto/'.$row['alias'].'/'.$row['codigo'].$alias_categoria;
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
                <input type="button" class="bt-comprar" name="comprar" value="COMPRAR">
            </div> 
            </a>
            <?php }//FIM //while lançamento ?>
            
            
            <div class="clear"></div>
        </div>
        <?php 
		}//FIM //se tiver produtos pro conheça também 
		?>   
        
    </div>
</div>

<div class="clear">&nbsp;</div>

<!-- Footer
============================== -->
<?php include('inc_rodape.php'); ?>


<!-- Javascript
============================== -->
<script src="js/jquery-1.8.3.min.js" type="text/javascript"></script> 

 <script src="js/jquery.elevatezoom.js" type="text/javascript"></script>
    
    <script type="text/javascript">
		$("#img_01").elevateZoom({gallery:'gallery_01', cursor: 'pointer', galleryActiveClass: 'active', imageCrossfade: true, loadingIcon: 'http://www.elevateweb.co.uk/spinner.gif'}); 
		$("#img_01").bind("click", function(e) { var ez = $('#img_01').data('elevateZoom');	$.fancybox(ez.getGalleryList()); return false; }); 
	</script>
    
  

<!-- Add fancyBox main JS and CSS files -->
<script type="text/javascript" src="fancybox/source/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
<script type="text/javascript">
	$(document).ready(function() {
		$('.fancybox').fancybox();
		
		$(".frete").fancybox({
			autoSize: false, 
			width     : 640,
			height    : 320
		});
		
		
		$(".pagamento").fancybox({
			autoSize: false, 
			width     : 780,
			height    : 540
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

<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-512d3f98721dd6fc"></script>

<?php echo $analytics; ?>
</body>
</html>