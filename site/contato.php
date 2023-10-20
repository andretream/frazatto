<?php require('config.php'); ?>
<!DOCTYPE HTML>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<title><?php echo NOMEDOSITE; ?> | Atendimento</title>

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
        	<h3>ATENDIMENTO</h3>
        </div>
        
        <?php if($_GET['response']=="true") { ?>
        <div class="alert alert-success">Mensagem enviada com sucesso!</div>
        <?php } ?>
        
        <?php if($_GET['response']=="false") { ?>
        <div class="alert alert-error">Email inválido. Insira um endereço de email válido.</div>
        <?php } ?>
        
        <?php if($_GET['response']=="empty") { ?>
        <div class="alert alert-error">O preenchimento dos campos com asterisco (*) é obrigatório.</div>
        <?php } ?>
        
        <?php if($_GET['response']=="verifica") { ?>
        <div class="alert alert-error">A soma da verificação anti-spam está errada.</div>
        <?php } ?>
        
        <p class="mt25">Preencha o formulário abaixo com seus dados. Estaremos lhe retornando o mais rápido possível.</p>
        
        <p style="color:#999;">Campos assinalados com * precisam ser preenchidos.</p>
        
        <div class="clearfix">&nbsp;</div>
        
        <form name="contato" method="post" action="">
            <input name="tipoform" value="contato" type="hidden">
            <div class="controls">
                <label>Nome *</label>
                <input type="text" name="nome" class="span11" value="<?php if(isset($_GET['response'])) echo $_SESSION['contato']['nome']; ?>" required> 
            </div>
            <div class="controls">
                <label>Email *</label>
                <input type="email" name="email" class="span11" value="<?php if(isset($_GET['response'])) echo $_SESSION['contato']['email']; ?>" required> 
            </div>
            <div class="controls controls-row control-label">
            	<div class="pull-left mr15">
                <label>Telefone</label>
                <input type="text" name="telefone" class="span5" alt="phone" value="<?php if(isset($_GET['response'])) echo $_SESSION['contato']['telefone']; ?>">
                </div>
                <label>Celular</label>
                <input type="text" name="celular" class="span5" alt="phone" value="<?php if(isset($_GET['response'])) echo $_SESSION['contato']['celular']; ?>">
            </div>
            <div class="controls controls-row">
                <label>Assunto *</label>
                <input type="text" name="assunto" class="span11" value="<?php if(isset($_GET['response'])) echo $_SESSION['contato']['assunto']; ?>" required>
            </div>
            
            <div class="controls">
            	<label>Mensagem *</label>
                <textarea name="mensagem" class="span11"  rows="10" required><?php if(isset($_GET['response'])) echo $_SESSION['contato']['mensagem']; ?></textarea>
            </div>
            <div class="controls controls-row">
                <label>Verificação anti-spam *</label>
                1 + 2 = <input name="verifica" id="verifica" type="text" class="input-mini" size="1" maxlength="1" required /><br><br>
            </div>
            <input type="submit" class="btn" value="Enviar">
        </form>        
        
        <div id="faixa-cinza" class="mb15 mt35">
        	<h3>ONDE ESTAMOS ?</h3>
        </div>
        
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3717.3709524410765!2d-50.3573615!3d-21.2963538!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xd32c2dbf8eade9bd!2sFrazatto+%26+Frazatto+Birigui+Ltda!5e0!3m2!1spt-BR!2sbr!4v1412705710566" width="830" height="450" frameborder="0" style="border:0"></iframe>
        
    </div>
</div>

<div class="clear">&nbsp;</div>

<!-- Footer
============================== -->
<?php include('inc_rodape.php'); ?>


<!-- Javascript
============================== -->
<script src="js/jquery.js"></script>
<script type="text/javascript" src="js/meiomask.js" charset="utf-8"></script>
<script type="text/javascript" >
  (function($){
	// call setMask function on the document.ready event
	  $(function(){
		$('input:text').setMask();
	  }
	);
  })(jQuery);
</script>
<!-- mask -->
<script src="js/jquery.filter_input.js"></script>
<script>
$(document).ready(function() {
	$('#verifica').filter_input({regex:'[0-9]'});
});
</script>

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