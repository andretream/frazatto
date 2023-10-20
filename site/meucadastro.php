<?php 
require('config.php'); 
//////////////////////

if(!isset($_SESSION['usuario']['ss_codigo']) || empty($_SESSION['usuario']['ss_codigo'])) 
{
	header('location:/login/acesso/meuspedidos');
	exit;
}

//seleciona dados do cliente
$sqlCli = mysql_query("SELECT * FROM clientes WHERE codigo='".$_SESSION['usuario']['ss_codigo']."' LIMIT 1");
$rowCli = mysql_fetch_array($sqlCli);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo NOMEDOSITE; ?> | Meu cadastro</title>

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
	
    <!-- Meio
	============================== -->
    <div id="meio-pedidos">
        <div id="faixa-cinza" class="mb15">
        	<h3>MEU CADASTRO</h3>
        </div>
        
        <?php include('inc_bemvindo.php'); ?>
        
        <div class="row">
            <?php include('inc_menucliente.php'); ?>
            <div class="span11 pull-right">
                
            <h2 class="texto">Formulário</h2>
            <hr>
            
            <?php if($_GET['response']=="true") { ?>
            <div class="alert alert-success">Dados atualizados com sucesso.</div>
            <?php } ?>
            
            <?php if($_GET['response']=="empty") { ?>
            <div class="alert alert-error">O preenchimento dos campos com asterisco (*) é obrigatório.</div>
            <?php } ?>
            
            <div style="width:800px;">
            <form name="cadastro" method="post" action="">
             <input type="hidden" name="tipoform" value="atualizadados" />
             
             <label>Nome / Razão social*:</label>
             <input type="text" name="nome" style="width:786px;" value="<?php echo $rowCli['nome']; ?>" required><br>
             
             <div class="pull-left mr15">
             <label>CPF / CNPJ:</label>   
             <input type="text" name="cpf" style="width:378px;" value="<?php echo $rowCli['cpf']; ?>" readonly>
             </div>
             <label>RG / IE:</label> 
             <input type="text" name="rg" style="width:378px;" value="<?php echo $rowCli['rg']; ?>" readonly><br> 
             
             <div class="pull-left mr15">
             <label>Endereço*:</label>
             <input type="text" name="endereco" style="width:630px;" value="<?php echo $rowCli['endereco']; ?>" required>
             </div>
             <label>Número*:</label> 
             <input type="text" name="numero" class="span2" value="<?php echo $rowCli['numero_casa']; ?>" required><br>
             
             <div class="pull-left mr15">
             <label>Bairro*:</label>
             <input type="text" name="bairro" style="width:378px;" value="<?php echo $rowCli['bairro']; ?>" required>
             </div>
             <label>Complemento:</label>
             <input type="text" name="complemento" style="width:378px;" value="<?php echo $rowCli['complemento']; ?>"><br>
             
             <div class="pull-left mr15">
             <label>Cidade*:</label> 
             <input type="text" name="cidade" class="span6" value="<?php echo $rowCli['cidade']; ?>" required> 
             </div>
             <div class="pull-left mr15">
             <label>Estado*:</label>
             <input type="text" name="estado" id="estado" style="width:180px;" value="<?php echo $rowCli['estado']; ?>" maxlength="2" required>
             </div>
             <label>CEP*:</label>
             <input type="text" name="cepCli" alt="cep" style="width:100px;" value="<?php echo $rowCli['cep']; ?>"  required><br>
             
             <div class="pull-left mr15">
             <label>Telefone*:</label>
             <input type="text" name="telefone" alt="phone" style="width:378px;" value="<?php echo $rowCli['telefone']; ?>" required>
             </div>
             <label>Celular:</label>
             <input type="text" name="celular" alt="phone" style="width:378px;" value="<?php echo $rowCli['telefone_cel']; ?>">
             <br>
             
             <label>Email*:</label>
             <input type="email" name="email" style="width:786px;" value="<?php echo $rowCli['email']; ?>" required><br>
             
             <div class="controls controls-row">
             <label>Senha*:</label>
             <input type="password" name="senha" id="senha" class="span3" value="<?php echo base64_decode($rowCli['senha']); ?>"> <p style="margin:12px 0 0 10px;">&nbsp;&nbsp;somente letras e números</p>
             </div>
             <br>
             
             <input type="checkbox" name="ativa_news" value="S" <?php if($rowCli['ativa_news']=='S') echo 'checked="checked"'; ?>> <span>Desejo receber novidades e promoções da loja em meu email.</span><br><br><br>
              
             <input type="submit" class="btn" style="margin-top:-3px;" name="cadastrar" value="Salvar"><br><br>
             
            </form>
            </div>    
                
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
	$('#senha').filter_input({regex:'[a-zA-Z0-9]'});
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
</body>
</html>