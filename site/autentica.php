<?php
require('config.php');
///////////////////////////////////

if($_GET['op']=='sair'){
	
	unset($_SESSION['usuario']);
	
	header("location:/home");
	exit;

}

////////////////////////////////////////

if($_GET['op']=='autentica') {

	$email = secure($_POST["email"]);
	$senha = secure($_POST["senha"]);
	$senha = base64_encode($senha);
	
	$sql= mysql_query("SELECT * FROM clientes WHERE ativa='S' AND senha='$senha' AND email='$email' ", $conecta);
	$tot = mysql_num_rows($sql);  
	
	if ($tot > 0) {
		
		$row = mysql_fetch_array($sql);
	
		$codigoCliente = $row['codigo'];
		$emailCliente = $row['email'];
		$nomeCliente = $row['nome'];
		$senhaCliente = $row['senha'];
	
		$_SESSION['usuario']['ss_codigo'] = $codigoCliente;
		$_SESSION['usuario']['ss_email'] = $emailCliente;
		$_SESSION['usuario']['ss_nome'] = $nomeCliente;
		$_SESSION['usuario']['ss_senha'] = $senhaCliente;
	
	
		//se estiver na cesta, autentica e volta pra finalizar o pedido
		if($_POST['pagina']=="carrinho") {
			
			header('Location:carrinho.php?etapa=pedido#confirmacao');
			exit;
			
		} else {
			
			if($_POST['pagina']=="meucadastro") {
				header('Location:/meucadastro');
				exit;
			} else {
				header('Location:/meuspedidos');
				exit;
			}
		}
		
		////////////////////
	
	} else { 
		
		if(!empty($_POST['pagina'])) $pagina = '/'.$_POST['pagina'];
		
		//retorna acesso negado
		header('Location:/login/negado'.$pagina);
		exit;
	}

}
?>