<?php
require('./admin/config/_conecta.php');
require('./admin/config/_funcoes.php');
/////////////////////////////////////////
session_start;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> Remoção de e-mail</title>
<link href="css/admin.css" rel="stylesheet" type="text/css">
<style type="text/css">
body {
	font-family:Arial, Helvetica, sans-serif;
	font-size:13px;
	color:#333;
}
azul {
	color:#069;
}
</style>
<script src="SpryAssets/SpryValidationRadio.js" type="text/javascript"></script>
<link href="SpryAssets/SpryValidationRadio.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table width="800" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr></tr>
  <tr>
    <td align="center">
      <? if(!isset($_GET['op'])) { 
	  
	  	if($_GET['K']=='') {
		echo 'Erro, faltam variaveis';	
		exit;
		}

	$chave = secure($_GET['K']);
	$tipo = secure($_GET['T']);

	  
	  $sqlEmail = mysql_query("SELECT nome, email, chave FROM clientes WHERE chave='$chave'");
	  $rowEmail = mysql_fetch_array($sqlEmail);
	  
	  if(mysql_num_rows($sqlEmail)>0) {
		
		//define sessão  
	  	$_SESSION['news']['tipo'] = $tipo;
		$_SESSION['news']['email'] = $rowEmail['email'];
	  	$_SESSION['news']['chave'] = $rowEmail['chave'];
		
	  ?>
      <p>Olá <strong><? echo $rowEmail['nome'] ?></strong></p>
      <form id="form2" name="form2" method="post" action="?op=remove">
        <input type="hidden" name="chave" value="<? echo $_SESSION['news']['chave']; ?>" />
      <input type="hidden" name="email" value="<? echo $_SESSION['news']['email']; ?>" />
      <p>Você confirma a remoção do e-mail <strong><? echo $_SESSION['news']['email'] ?></strong> de nossa newsletter?</p>
      <input type="submit" name="submit" value="Sim, confirmo a remoção" />
    </form>
      <? } else { ?>
      <p>O e-mail <strong><? echo $_SESSION['news']['email']; ?></strong> não foi encontrado ou já foi removido de nossa newsletter.</p>
      <? } ?>
      <? } else {
		 
		 $email = secure($_POST['email']);
		 $chave = secure($_POST['chave']);
		 
		 $sqlRem = "UPDATE clientes SET ativa_news='N' WHERE chave='$chave' ";
		 //se deletar
		 if(mysql_query($sqlRem))
		 {
	  ?>
      <br />
      <p>O e-mail <strong><? echo $email; ?></strong> foi removido de nossa newsletter em <? echo $dataBra; ?> às <? echo $hora; ?>. <br />
        <br />
        Obrigado.</p>
      <? } else { ?>
      <br />
      <p>O e-mail <strong><? echo $email; ?></strong> não foi encontrado ou já foi removido de nossa newsletter.</p>
      <? } } ?>
      </td>
  </tr>
  <tr></tr>
</table>
</body>
</html>