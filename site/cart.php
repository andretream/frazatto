<?php
$action = $_GET['action'];

$cod_prod = secure($_POST['cod_prod']);
$qtd = secure($_POST['qtd']);
$valor = secure($_POST['valor']);
$cod_var = secure($_POST['cod_var']);
$cod_cesta = secure($_POST['cod_cesta']);
$id = secure($_GET['id']);

//verifica item da cesta
$itCes = mysql_query("SELECT cod_prod, qtd, cod_var FROM cesta WHERE codigo='$id' LIMIT 1");
$it = mysql_fetch_array($itCes);

switch ($action) {

///adicionar um produto
case 'add':
	
	//conulta se o produto add já existe na cesta
	$sqlCesta = mysql_query("SELECT codigo, cod_var, qtd, cod_prod FROM cesta WHERE cod_prod='".$cod_prod."' AND cod_var='".$cod_var."' AND secao='".session_id()."' LIMIT 1");
	$totCesta = mysql_num_rows($sqlCesta);
	$rowCesta = mysql_fetch_array($sqlCesta);
	
	if($totCesta > '0') {//já existe na tabela
		
		//verifica se tem variação
		if($rowCesta['cod_var'] > '0') 
		{
			//conulta estoque da variação
			$estoque_var = @mysql_result(mysql_query("SELECT estoque FROM variacoes WHERE codigo='".$rowCesta['cod_var']."' LIMIT 1"),0);
			
			//se a variação tiver estoque suficiente
			if($estoque_var > $rowCesta['qtd']) 
			{
				//atualizar item na cesta	
				$sql_Add = "UPDATE cesta SET qtd=qtd+1 WHERE codigo='".$rowCesta['codigo']."' ";
				$res_Add = mysql_query($sql_Add, $conecta) or die("Erro ao acrescentar item: ".mysql_error());
			}
			else {//se não tiver no estoque manda mensagem de erro
				$erroEstoque = $rowCesta['codigo'];
			}
			//FIM //se a variação tiver estoque suficiente
			
		}
		//FIM //verifica se tem variação
		
		//se não tiver variação
		else 
		{
			//conulta estoque do produto
			$estoque_prod = @mysql_result(mysql_query("SELECT estoque FROM produtos WHERE codigo='".$rowCesta['cod_prod']."' LIMIT 1"),0);
			
			//se o produto tiver estoque suficiente
			if($estoque_prod > $rowCesta['qtd']) 
			{
				//atualizar item na cesta	
				$sql_Add = "UPDATE cesta SET qtd=qtd+1 WHERE codigo='".$rowCesta['codigo']."' ";
				$res_Add = mysql_query($sql_Add, $conecta) or die("Erro ao acrescentar item: ".mysql_error());
			}
			else {//se não tiver no estoque manda mensagem de erro
				$erroEstoque = $rowCesta['codigo'];
			}
			//FIM //se o produto tiver estoque suficiente
		}
		//FIM //se não tiver variação
		
	} else {//não tem na tabela
	
		//insere item na cesta	
		$sql_Add = "INSERT INTO cesta SET secao='".session_id()."', data_expira='$data', cod_prod='$cod_prod', qtd='1', valor='$valor', cod_var='$cod_var' ";
		$res_Add = mysql_query($sql_Add, $conecta) or die("Erro ao inserir item na cesta: ".mysql_error());
		
	}//FIM //não tem na tabela
	
header("Location:/carrinho");
break;
/////////////////

//acrescentar item
case 'mais':
	
	//verifica se tem variação
	if($it['cod_var']!='0') 
	{
		//conulta estoque da variação
		$estoque_var = @mysql_result(mysql_query("SELECT estoque FROM variacoes WHERE codigo='".$it['cod_var']."' LIMIT 1"),0);
		
		//se a variação tiver estoque suficiente
		if($estoque_var > $it['qtd']) 
		{
			//atualizar item na cesta	
			$sql_Add = "UPDATE cesta SET qtd=qtd+1 WHERE codigo='$id' ";
			$res_Add = mysql_query($sql_Add, $conecta) or die("Erro ao acrescentar item: ".mysql_error());
		}
		else {//se não tiver no estoque manda mensagem de erro
			$erroEstoque = $id;
		}
		//FIM //se a variação tiver estoque suficiente
	}
	//FIM //verifica se tem variação
	
	//se não tiver variação
	else 
	{
		//conulta estoque do produto
		$estoque_prod = @mysql_result(mysql_query("SELECT estoque FROM produtos WHERE codigo='".$it['cod_prod']."' LIMIT 1"),0);
		
		//se o produto tiver estoque suficiente
		if($estoque_prod > $it['qtd']) 
		{
			//atualizar item na cesta	
			$sql_Add = "UPDATE cesta SET qtd=qtd+1 WHERE codigo='$id' ";
			$res_Add = mysql_query($sql_Add, $conecta) or die("Erro ao acrescentar item: ".mysql_error());
		}
		else {//se não tiver no estoque manda mensagem de erro
			$erroEstoque = $id;
		}
		//FIM //se o produto tiver estoque suficiente
	}
	//FIM //se não tiver variação

break;
/////////////////

//subtrair item
case 'menos':
	
	//subtrai se a quantidade q tem no item é maior q 1
	if($it['qtd'] > 1) 
	{
		//atualizar item na cesta	
		$sql_Add = "UPDATE cesta SET qtd=qtd-1 WHERE codigo='$id' ";
		$res_Add = mysql_query($sql_Add, $conecta) or die("Erro ao subtrari item: ".mysql_error());
	}
	
break;
/////////////////

//deletar um produto
case 'delete':
	
	//remove item da cesta	
	$sql_Rem = "DELETE FROM cesta WHERE codigo='$id' ";
	$res_Rem = mysql_query($sql_Rem, $conecta) or die("Erro ao deletar item na cesta: ".mysql_error());
	
	//consulta total de itens na cesta
	$totItens = mysql_num_rows(mysql_query("SELECT codigo FROM cesta WHERE secao='".session_id()."' "));
	
	if($totItens=='0') {//não tendo nenhum item na cesta
		
		unset($_SESSION['itensCesta']);
		unset($_SESSION['carrinho']);
		
		header('Location:/home');
		exit;
		
	}//FIM //não tendo nenhum item na cesta

header("Location:/carrinho");
break;
/////////////////////////

}
?>