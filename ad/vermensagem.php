<?php
require('./admin/config/_conecta.php');
require('./admin/config/_funcoes.php');
/////////////////////////////////////////

if($_GET['id']=='' or $_GET['K']=='') {
	echo 'Erro, faltam variaveis';	
	exit;
}

$id = secure($_GET['id']);
$chave = secure($_GET['K']);
$tipo = secure($_GET['T']);

$sqlEmail = mysql_query("SELECT nome FROM clientes WHERE chave='$chave'");
$rowEmail = mysql_fetch_array($sqlEmail);
$nome = $rowEmail['nome'];


$sql = "SELECT assunto, texto, topo FROM news_mensagens WHERE codigo='$id' ";
$res = mysql_query($sql, $conecta);
$row = mysql_fetch_array($res);
$topo = $row['topo'];
//substitui o nome
if(preg_match("/{nome}/", $row['texto'])) { 
$texto = str_replace('{nome}', $nome, $row['texto']); 
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $row['assunto']; ?></title>
</head>
<body>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
<?php
//topo
		if(file_exists("upload/imagens/newsletter/topo_mailing.jpg") && $topo=='S') 
		{
		echo "<tr> 
			<td><img src=upload/imagens/newsletter/topo_mailing.jpg width=700></td>
		  </tr>";
		} 
?>
  <tr>
    <td><?php print $texto; ?><br><br></td>
  </tr>
</table>
</body>
</html>