<?php 
require('config.php'); 
//////////////////////

if(isset($_SESSION['usuario']['ss_codigo']) || !empty($_SESSION['usuario']['ss_codigo'])) 
{
	header('location:/meuspedidos');
	exit;
}
?>
<!DOCTYPE HTML>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<title><?php echo NOMEDOSITE; ?> | Autenticação</title>

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
        <div id="faixa-cinza" class="mb15">
        	<h3>AUTENTICAÇÃO</h3>
        </div>
        
        <?php if($_GET['op']=='negado') { ?>
        <div class="alert alert-error">Dados inválidos. Acesso negado.</div>
        <?php } ?>
        
        <!-- Login 
        ================================= -->
        <div class="cont-box mr25">
            <h2>Já sou cliente</h2>
            <p>Autentique-se abaixo:</p>
            
            <form name="formLogin" method="post" action="autentica.php?op=autentica">
                <input type="hidden" name="pagina" value="<?php echo $_GET['pagina']; ?>" />
                
                <input type="email" name="email" class="span4" size="70" placeholder="Email"><br>
                <input type="password" name="senha" class="span4" size="70" placeholder="Senha"><br>
                
                <input type="submit" class="btn" style="margin-top:-2px;" name="cadastrar" value="Enviar">
                <span class="">Esqueceu sua senha? <a href="javascript:senha('esqueci_senha.php');">Clique aqui</a></span>
            </form>
        </div>
            
        <div class="cont-box">
            <h2>Ainda não sou cliente</h2>
            
            <p><strong>Se essa é sua primeira compra, clique no botão abaixo para se cadastrar:</strong></p><br>
            <p>Efetuando o cadastro em nosso site, você também poderá optar em receber ofertas imperdíveis em seu email.</p><br>
            <input type="button" class="btn" style="margin-top:12px;" name="cadastrar" value="Cadastre-se grátis" onClick="location.href='cadastro<?php if(isset($_GET['pagina'])) echo '/acesso/'.$_GET['pagina']; ?>'"><br>
            
            <div class="clear"></div>
        </div>
        
    </div>
</div>

<div class="clear">&nbsp;</div>

<!-- Footer
============================== -->
<?php include('inc_rodape.php'); ?>


<!-- Javascript
============================== -->
<script src="js/jquery.js"></script>
<script type="text/javascript" src="js/scripts-site.js"></script>
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