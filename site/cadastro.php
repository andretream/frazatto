<?php require('config.php'); ?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo NOMEDOSITE; ?> | Cadastro</title>

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
        	<h3>CADASTRO</h3>
        </div>
        
        <h2 class="texto">Faça o seu cadastro</h2>
        <p>Preencha o formulário abaixo para realizar o seu cadastro em nossa loja. O cadastro é necessário somente em sua primeira compra.<br> O sistema irá gerar uma senha automática e a enviará para seu email, para você utilizar em suas próximas compras</p>
        <hr>
        
        <?php if($_GET['response']=="true") { ?>
        <div class="alert alert-success">Cadastro realizado com sucesso. Você receberá em seu email os dados para autenticar-se na loja.</div>
        <?php } ?>
        
        <?php if($_GET['response']=="false") { ?>
        <div class="alert alert-error">Email inválido. Insira um endereço de email válido.</div>
        <?php } ?>
        
        <?php if($_GET['response']=="empty") { ?>
        <div class="alert alert-error">O preenchimento dos campos com asterisco (*) é obrigatório.</div>
        <?php } ?>
        
        <?php if($_GET['response']=="email") { ?>
        <div class="alert alert-error">O email informado já está cadastrado em nossa loja. Utilize outro email.</div>
        <?php } ?>
        
        <?php if($_GET['response']=="docinvalido") { ?>
        <div class="alert alert-error">O CPF / CNPJ informado é inválido.</div>
        <?php } ?>
        
        <?php if($_GET['response']=="verifica") { ?>
        <div class="alert alert-error">A soma da verificação anti-spam está errada.</div>
        <?php } ?>
        
        <form name="cadastro" method="post" action="">
         <input type="hidden" name="tipoform" value="cadastro" />
         <input type="hidden" name="pagina" value="<?php echo $_GET['pagina']; ?>" />
         
         <label>Nome / Razão social*:</label>
         <input type="text" name="nome" class="span11" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['nome']; ?>" required><br>
         
         <div class="pull-left mr15">
         <label>CPF / CNPJ*:</label>   
         <input type="text" name="cpf" class="span5" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['cpf']; ?>" required>
         </div>
         <label>RG / IE*:</label> 
         <input type="text" name="rg" class="span5" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['rg']; ?>" required><br> 
         
         <label>Email*:</label>
         <input type="email" name="email" class="span11" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['email']; ?>" required><br>
         <div class="pull-left mr15">
         <label>Endereço*:</label>
         <input type="text" name="endereco" class="span8" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['endereco']; ?>" required>
         </div>
         <label>Número*:</label> 
         <input type="text" name="numero" class="span2" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['numero']; ?>" required><br>
         
         <div class="pull-left mr15">
         <label>Bairro*:</label>
         <input type="text" name="bairro" class="span5" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['bairro']; ?>" required>
         </div>
         <label>Complemento</label>
         <input type="text" name="complemento" class="span5" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['complemento']; ?>"><br>
         
         <div class="pull-left mr15">
         <label>Cidade*:</label> 
         <input type="text" name="cidade" class="span6" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['cidade']; ?>" required> 
         </div>
         <div class="pull-left mr15">
         <label>Estado*:</label>
         <input type="text" name="estado" id="estado" class="span2" style="width:106px;" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['estado']; ?>" maxlength="2" required>
         </div>
         <label>CEP*:</label>
         <input type="text" name="cepCli" alt="cep" class="span3" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['cepCli']; ?>"  required><br>
         
         <div class="pull-left mr15">
         <label>Telefone*:</label>
         <input type="text" name="telefone" alt="phone" class="span5" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['telefone']; ?>" required>
         </div>
         <label>Celular:</label>
         <input type="text" name="celular" alt="phone" class="span5" value="<?php if(isset($_GET['response'])) echo $_SESSION['cadastro']['celular']; ?>">
         <br><br>
         
         <input type="checkbox" name="ativa_news" value="S" checked> <span>Desejo receber novidades e promoções da loja em meu email.</span><br><br>
         
         <div class="controls controls-row">
         <label>Verificação anti-spam*:</label>
         1 + 2 = <input name="verifica" id="verifica" type="text" class="input-mini" size="1" maxlength="1" required /><br><br>
         </div>
          
         <input type="submit" class="btn" style="margin-top:-3px;" name="cadastrar" value="Enviar cadastro"><br><br>
         
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
	$('#estado').filter_input({regex:'[a-zA-Z]'});
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