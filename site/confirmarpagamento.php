<?php
require('config.php'); 
///////////////////////

$chave = secure($_GET['k']);
//pega dados do pedido
$sqlPed = mysql_query("SELECT * FROM pedidos WHERE chave='$chave' LIMIT 1");
$rowPed = mysql_fetch_array($sqlPed);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo NOMEDOSITE; ?> | Confirmação de pagamento</title>

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
        	<h3>CONFIRMAÇÃO DE PAGAMENTO</h3>
        </div>
        
        <?php if($_GET['response']=="true") { ?>
        <div class="alert alert-success">Confirmação enviada com sucesso. Aguarde a atualização do status do seu pedido.</div>
        <?php } ?>
        
        <?php if($_GET['response']=="empty") { ?>
        <div class="alert alert-error">O preenchimento dos campos com asterisco (*) é obrigatório.</div>
        <?php } ?>
        
        <?php if($_GET['response']=="verifica") { ?>
        <div class="alert alert-error">A soma da verificação anti-spam está errada.</div>
        <?php } ?>
        
        <p class="mt25">Se você realizou um pedido e efetuou um pagamento por depósito ou transferência bancária, faça a confirmação do pagamento através do formulário abaixo. Após o envio, os dados serão verificados e validados junto ao banco, antes da liberação do seu pedido.</p>
        
        <p style="color:#999;">Campos assinalados com * precisam ser preenchidos.</p>
        
        <div class="clearfix">&nbsp;</div>
        
        <form name="confirmacao" method="post" action="">
            <input name="tipoform" value="confirmacao" type="hidden">
            <input name="k" value="<?php echo $chave; ?>" type="hidden">
            <div class="controls">
                <label>Número do pedido</label>
                <input type="text" name="pedido" class="span2" readonly value="<?php if(isset($_GET['response'])) echo $_SESSION['confirmacao']['pedido']; else echo $rowPed['codigo']; ?>" required> 
            </div>
            <div class="controls">
                <label>Data do pagamento *</label>
                <input type="text" name="data_pagamento" class="span2" alt="date" value="<?php if(isset($_GET['response'])) echo $_SESSION['confirmacao']['data_pagamento']; ?>" required> 
            </div>
            <div class="controls controls-row">
                <label>Hora do pagamento *</label>
                <input type="text" name="hora_pagamento" class="span2" alt="time" value="<?php if(isset($_GET['response'])) echo $_SESSION['confirmacao']['hora_pagamento']; ?>" required>
            </div>
            <div class="controls controls-row">
                <label>Valor depositado/transferido em R$ *</label>
                <input type="text" name="valor" class="span2" alt="decimal" value="<?php if(isset($_GET['response'])) echo $_SESSION['confirmacao']['valor']; ?>" required>
            </div>
            
            <div class="controls">
            	<label>Cole ou digite aqui os detalhes do pagamento (ou o comprovante digital)</label>
                <textarea name="mensagem" class="span11"  rows="10"><?php if(isset($_GET['response'])) echo $_SESSION['confirmacao']['mensagem']; ?></textarea>
            </div>
            <div class="controls controls-row">
                <label>Verificação anti-spam *</label>
                1 + 2 = <input name="verifica" id="verifica" type="text" class="input-mini" size="1" maxlength="1" required /><br><br>
            </div>
            <input type="submit" class="btn" value="Enviar">
        </form>        
        
        
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