<?php 
require('config.php'); 
///////////////////////

//pega dados do pedido
$sqlPed = mysql_query("SELECT * FROM pedidos WHERE codigo='".$_SESSION['session_cobranca']['codigo']."' LIMIT 1");
$rowPed = mysql_fetch_array($sqlPed);
/////

///////
$order_number = $rowPed['codigo'];
$sqlCielo = mysql_query("SELECT * FROM transacoes_cielo WHERE order_number='$order_number' ORDER BY codigo DESC LIMIT 1");
$rowCielo = mysql_fetch_array($sqlCielo);
///////
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo NOMEDOSITE; ?> | Confirmação</title>

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
	
    <!-- Meio
	============================== -->
    <div id="meio-pedidos">
        <div id="faixa-cinza" class="mb15">
        	<h3>CONFIRMAÇÃO</h3>
        </div>
        
        <center>
       
        <p style="font-size:18px; margin:20px;"><strong><?php echo $rowPed['nome'] ?></strong>, </p>
        
        
        <h2 style="padding:40px; background:#FFCC00; color:#FFF; width:500px; margin: 30px auto;">Número do seu Pedido:</span>  <span style="font-size:30px;color:#FFF;"> <?php echo $rowPed['codigo']; ?></span></h2>
        
        <p style="margin-bottom:30px;">Confira o status do pagamento realizado:</p>
        
            		<!--autorizado-->
					<?php if($rowCielo['status']==2 || $rowCielo['status']==7) { ?>
                    <div class="alert alert-success" style="margin:30px 0; padding:40px 0;"><h3>Seu pagamento foi realizado com sucesso.</h3></div>
                    <p style="margin-bottom:60px;">Aguarde agora a atualização do seu pedido.</p>
                    <!--negado-->
                    <?php } else { ?>
                    <div class="alert alert-error" style="margin:20px 0; padding:40px 0;"><h3>Desculpe, mas o pagamento não foi autorizado pela operadora do seu cartão.</h3></div>
                    <p style="margin-bottom:60px;"><strong>Entre em contato com o nosso atendimento para mais informações.</strong></p>
                    <?php } ?>
                    
                    
                    
                     <div class="clear"></div>
                  
                   <p style="margin:10px 0;">Caso tenha alguma dúvida sobre o pedido realizado ou sobre como efetuar o pagamento, entre em contato conosco.</p>
        
        
        
        </center>
          
          
          <div class="clear">&nbsp;</div>
          
          
        </div>
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
<script type="text/javascript">
function hidediv(id) {
	//safe function to hide an element with a specified id
	if (document.getElementById) { // DOM3 = IE5, NS6
		document.getElementById(id).style.display = 'none';
	}
	else {
		if (document.layers) { // Netscape 4
			document.id.display = 'none';
		}
		else { // IE 4
			document.all.id.style.display = 'none';
		}
	}
}

function showdiv(id) {
	//safe function to show an element with a specified id
		  
	if (document.getElementById) { // DOM3 = IE5, NS6
		document.getElementById(id).style.display = 'block';
	}
	else {
		if (document.layers) { // Netscape 4
			document.id.display = 'block';
		}
		else { // IE 4
			document.all.id.style.display = 'block';
		}
	}
}

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

<?php echo $analytics; ?>
</body>
</html>