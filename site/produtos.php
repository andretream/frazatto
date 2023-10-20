<?php 
require('config.php'); 
///////////////////////

$tipo = secure($_GET['tipo']);
$cat = secure($_GET['cat']);
$sub = secure($_GET['sub']);
$marca = secure($_POST['marca']);
$modelo = secure($_POST['modelo']);

//tipo categoria
if($tipo=='categoria' && !empty($_GET['cat'])) {
	
	//consulta categoria
	$sqlCat = mysql_query("SELECT codigo, titulo, alias FROM produtos_categorias WHERE alias='$cat' LIMIT 1");
	$totCat = mysql_num_rows($sqlCat);
	$rowCat = mysql_fetch_array($sqlCat);
	
	//tabela de relacionamento de categorias
	$RELcat = ", relaciona_produtos_categorias C";
	
	//relacionamento com os produtos
	$SQLcat = "AND C.rel_cod_cat_principal='".$rowCat['codigo']."' AND C.rel_cod_pro=P.codigo";
	
	//para o link do produto
	$SQLcategoria = "AND rel_cod_cat_principal='".$rowCat['codigo']."' ";
	
	//titulo de navegação
	$titulo_navegacao = '<h3>'.$rowCat['titulo'].'</h3>';
	
	//titulo da pagina
	$titulo_pagina = $rowCat['titulo'];
	
	//para o link do detalhe do produto #categoria
	$Lcat = '/'.$rowCat['alias'];
	
	if(!empty($_GET['sub'])) {
	
		//consulta subcategoria
		$sqlSub = mysql_query("SELECT codigo, titulo, alias FROM produtos_categorias WHERE cod_cat!='0' AND alias='$sub' LIMIT 1");
		$totSub = mysql_num_rows($sqlSub);
		$rowSub = mysql_fetch_array($sqlSub);
		
		//titulo de navegação
		$titulo_navegacao = '<h3>'.$rowCat['titulo'].'</h3><span class="tituSub">'.$rowSub['titulo'].'</span>';
		
		//titulo da pagina
		$titulo_pagina = $titulo_pagina.' / '.$rowSub['titulo'];
		
		//relacionamento com os produtos
		$SQLsub = "AND C.rel_cod_cat='".$rowSub['codigo']."' AND C.rel_cod_pro=P.codigo";
		
		//para o link do detalhe do produto #subcategoria
		$Lsub = '/'.$rowSub['alias'];
	}
	
	if(!empty($_POST['marca'])) {
	
		//consulta subcategoria
		$sqlMar = mysql_query("SELECT codigo, titulo, alias FROM produtos_marcas WHERE codigo='$marca' LIMIT 1");
		$totMar = mysql_num_rows($sqlMar);
		$rowMar = mysql_fetch_array($sqlMar);
		
		if(!empty($marca)) $codigo_marca = $marca;
		else $codigo_marca = $rowMar['codigo'];
		
		//quando tiver modelo
		if(!empty($modelo)) {
			//tabela de relacionamento de modelos
			$RELmar = ", relaciona_produtos_modelos M";
			//para marca e modelo
			$SQLmar = "AND M.rel_cod_mar='$codigo_marca' AND M.rel_cod_pro=P.codigo AND M.rel_cod_mod='$modelo'";
		}
		//FIM //quando tiver modelo
		else {//somente marca
			//tabela de relacionamento de marcas
			$RELmar = ", relaciona_produtos_marcas M";
			//para marca
			$SQLmar = "AND M.rel_cod_mar='$codigo_marca' AND M.rel_cod_pro=P.codigo";
		}//FIM //somente marca
		
		//se não tiver subcategoria
		if(empty($sub)) $subL = '0/'; else $subL = '';
		
		//para o link do detalhe do produto #marca
		$Lmar = '/'.$subL.$rowMar['alias'];
	}
	
	//se não tiver subcategoria
	if(empty($sub)) $sub = '0';
	
	//link da lista de marcas
	$linkMarca = 'produtos/categoria/'.$cat.'/'.$sub.'/';

}//FIM //tipo categoria

//tipo marca
else if($tipo=='marca') {
	
	//consulta linha
	$sqlLinha = mysql_query("SELECT codigo, titulo, alias FROM produtos_marcas WHERE alias='$cat' LIMIT 1");
	$totLinha = mysql_num_rows($sqlLinha);
	$rowLin = mysql_fetch_array($sqlLinha);
	
	if(!empty($marca)) $codigo_marca = $marca;
	else $codigo_marca = $rowLin['codigo'];
	
	//quando tiver modelo
	if(!empty($modelo)) {
		//tabela de relacionamento de modelos
		$RELmar = ", relaciona_produtos_modelos M";
		//para marca e modelo
		$SQLmar = "AND M.rel_cod_mar='$codigo_marca' AND M.rel_cod_pro=P.codigo AND M.rel_cod_mod='$modelo'";
	}
	//FIM //quando tiver modelo
	else {//somente marca
		//tabela de relacionamento de marcas
		$RELmar = ", relaciona_produtos_marcas M";
		//para marca
		$SQLmar = "AND M.rel_cod_mar='$codigo_marca' AND M.rel_cod_pro=P.codigo";
	}//FIM //somente marca
	
	//titulo de navegação
	$titulo_navegacao = '<h3>'.$rowLin['titulo'].'</h3>';
	
	//titulo da pagina
	$titulo_pagina = $rowLin['titulo'];

	//para o link do detalhe do produto #marca
	$Lmar = '/0/0/'.$rowLin['alias'];
	
	//link da lista de marcas
	$linkMarca = 'produtos/marca/';
	
}//FIM //tipo linha

//produtos
else {
	
	//titulo de navegação
	$titulo_navegacao = '<h3>Produtos</h3>';
	
	//titulo da pagina
	$titulo_pagina = 'Produtos';
	
}//FIM //produtos

if(empty($titulo_pagina)) $titulo_pagina = 'Produtos';
if(empty($titulo_navegacao)) $titulo_navegacao = '<h3>Produtos</h3>';

$queryProd = "SELECT DISTINCT(P.codigo), P.titulo, P.alias, P.data_publica, P.valor, P.valor_promocao 
FROM produtos P $RELcat $RELmar 
WHERE P.status='S' AND P.ativa='S' $SQLcat $SQLsub $SQLmar 
ORDER BY RAND()";
$sqlProd = mysql_query($queryProd);
$totProd = mysql_num_rows($sqlProd);

//quantidade de produtos
if($totProd > '0' && $totProd=='1') $totalProdutos = '1 produto localizado'; else if($totProd > '1') $totalProdutos = $totProd.' produtos localizados';

//codigo da marca
if(!empty($_POST['marca'])) { 
	$cod_marca = $marca; 
	$action = '/'.$tipo; 
	if(!empty($cat)) 
	$action .= '/'.$cat; 
	if(!empty($sub)) 
	$action .= '/'.$sub;
} else if($tipo=='marca') { 
	$cod_marca = $rowLin['codigo'];
	$action = '/'.$tipo; 
} else {
	$cod_marca = '';
	$action = '/'.$tipo; 
	if(!empty($cat)) 
	$action .= '/'.$cat; 
	if(!empty($sub)) 
	$action .= '/'.$sub;
}
?>
<!DOCTYPE HTML>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<title><?php echo NOMEDOSITE; ?> | <?php echo $titulo_pagina; ?></title>

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



<script src="js/jquery.js"></script>
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
            <span class="right cin64 pt10 mr15"><?php echo $totalProdutos; ?></span>
        </div>
        
        <?php
		$sql = mysql_query("SELECT codigo, titulo, alias FROM produtos_marcas WHERE ativa='S' ORDER BY titulo");
		$totMarcas = mysql_num_rows($sql);
		
		if($totMarcas > '0') {//existindo marcas
		?>
        <p class="f12 mt20 pl20" style="margin-bottom:20px;"><strong>Filtre produtos pela marca e modelo do seu veículo:</strong></p>
        
        <div id="cont-filtro">
            
            <form method="post" action="produtos<?php echo $action; ?>">
            	<script>
				$(document).ready(function(){
					$('#marca').change(function(){
						$('#modelo').load('modelos_marcas.php?marca='+$('#marca').val() );
					});
				});
				</script>
                <div class="float-left" style="margin-left:20px;">
                    <label for="marca">Marca: </label>
                    <select name="marca" id="marca" style="width:250px;" required>
                        <option value="">Selecione a marca:</option>
                        <?php
                        $sql = mysql_query("SELECT codigo, titulo FROM produtos_marcas ORDER BY titulo");
                        while($row = mysql_fetch_array($sql)) {
                        ?>
                          <option value="<?php print $row['codigo']; ?>" <?php if($row['codigo']==$cod_marca) echo 'selected'; ?>><?php print $row['titulo']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="float-left" style="margin-left:20px;">
                    <label for="modelo">Modelo: </label>
                    <?php if(!empty($cod_marca)) { ?>
                    <select name="modelo" id="modelo" style="width:300px;">
                        <option value="">Selecione o modelo</option>
                        <?php
						$sql = mysql_query("
						SELECT codigo, titulo 
						FROM produtos_modelos 
						WHERE cod_mar='$cod_marca' AND ativa='S' 
						ORDER BY titulo
						");
						while($row = mysql_fetch_array($sql)) {
						?>
                        	<option value="<?php echo $row['codigo']; ?>" <?php if($row['codigo']==$modelo) echo 'selected'; ?>><?php echo $row['titulo']; ?></option>
                        <?php } ?>
                    </select>
                    <?php } else { ?>
                    <select name="modelo" id="modelo" style="width:300px;">
                        <option>Selecione a marca antes</option>
                    </select>
                    <?php } ?>
                </div>
                
                <input type="submit" name="filtrar" class="btn float-left" style="margin:24px 0 0 20px" value="Buscar produtos" />
            
            </form>
            
        </div>
        <?php }//FIM //existindo marcas ?>
        
       	<div id="cont-produto" class="pt20">
            
            <div class="clearfix"></div>
            <?php
			if($totProd=='0') echo '<div class="alert  alert-default  uppercase  fade  in" style="width:780px;">Nenhum produto encontrado.</div>';
			
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
				$sqlC = mysql_query("SELECT codigo, alias, cod_cat, rel_cod_cat FROM produtos_categorias, relaciona_produtos_categorias WHERE rel_cod_pro='".$row['codigo']."' AND codigo=rel_cod_cat_principal $SQLcategoria ORDER BY titulo");
				while($rowC = mysql_fetch_array($sqlC)) {//while cateogira principal
					  
					  if($rowC['rel_cod_cat']=='0') $alias_categoria = '/'.$rowC['alias'];
					  
					  //categoria
					  $sqlS = mysql_query("SELECT codigo, alias, cod_cat FROM produtos_categorias, relaciona_produtos_categorias WHERE rel_cod_pro='".$row['codigo']."' AND codigo=rel_cod_cat AND rel_cod_cat_principal='".$rowC['codigo']."' ORDER BY titulo");
					  while($rowS = mysql_fetch_array($sqlS)) {//while cateogira
						  $alias_categoria = '/'.$rowC['alias'].'/'.$rowS['alias'];
					  }//FIM //while cateogira
					  
				}//FIM //while cateogira principal
				
				
				if(empty($row['alias'])) $alias_produto = '0'; else $alias_produto = $row['alias'];
				//link detalhe
				$link_detalhe = 'produto/'.$alias_produto.'/'.$row['codigo'].$Lcat.$Lsub.$Lmar;
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
                        <p class="por">Por R$ <?php echo decimal($row['valor']); ?></p>
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
            
            
             <div class="alerta" style="margin-top:60px; width:98%;">
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
</div>

<div class="clear">&nbsp;</div>

<!-- Footer
============================== -->
<?php include('inc_rodape.php'); ?>


<!-- Javascript
============================== -->
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

<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-512d3f98721dd6fc"></script>

<?php echo $analytics; ?>
</body>
</html>