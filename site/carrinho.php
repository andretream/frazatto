<?php
require('config.php');
//////////////////////
require('cart.php');

//remove temp
$sql_Add2 = "DELETE FROM cesta WHERE data_expira!='$data' ";
$res_Add2 = mysql_query($sql_Add2, $conecta);
if (!$res_Add2) {
	echo "<div class=\"error\">Erro ao processar operação. REMOVER TEMP.</div>";
	die;
}
//

//atualiza endereço de entrega
if ($_POST['operacao'] == 'atualizaendereco') {

	$email = secure($_POST['email']);
	$nome = secure($_POST['nome']);
	$telefone = secure($_POST['telefone']);
	$celular = secure($_POST['celular']);
	$endereco = secure($_POST['endereco']);
	$numero = secure($_POST['numero']);
	$cepCli = secure($_POST['cepCli']);
	$cep = secure($_POST['cep']);
	$bairro = secure($_POST['bairro']);
	$complemento = secure($_POST['complemento']);
	$cidade = secure($_POST['cidade']);
	$estado = secure($_POST['estado']);

	$_SESSION['carrinho']['cep'] = $cep;

	if (empty($_POST['email']) || empty($_POST['nome']) || empty($_POST['telefone']) || empty($_POST['endereco']) || empty($_POST['numero']) || empty($_POST['cepCli']) || empty($_POST['bairro']) || empty($_POST['cidade']) || empty($_POST['estado'])) {

		$erro_dados = '1';
		$mensagem_end = '<div class="alert alert-error">Os campos com asterisco (*) são obrigatórios.</div>';
		$_REQUEST['etapa'] = '';
		$libera_botao = 1;
	} else {

		$erro_dados = '0';
		$mensagem_end = '<div class="alert alert-success">Endereço atualizado com sucesso.</div>';
		$_REQUEST['etapa'] = 'pedido';
		$libera_botao = 0;
	}

	//atualiza dados
	mysql_query("UPDATE clientes SET email='$email', nome='$nome', telefone='$telefone', telefone_cel='$celular', endereco='$endereco', numero_casa='$numero', cep='$cepCli', bairro='$bairro', complemento='$complemento', cidade='$cidade', estado='$estado' WHERE codigo='" . $_SESSION['usuario']['ss_codigo'] . "' ");
}
//FIM //atualiza endereço de entrega

//se estiver logado.....
if ($_SESSION['usuario']['ss_codigo']) {

	///puxa endereço do cliente
	$sqlCli = mysql_query("SELECT * FROM clientes WHERE codigo = '" . $_SESSION['usuario']['ss_codigo'] . "' ");
	$rowCli = mysql_fetch_array($sqlCli);
	$cepCli = $rowCli['cep'];
}

/////////////////////////////////////////////////////////////////////////////////////////////

//consulta itens da cesta
$cesta = mysql_query("SELECT * FROM cesta WHERE secao='" . session_id() . "' ORDER BY codigo DESC");
$total_cesta = mysql_num_rows($cesta);

$_SESSION['carrinho']['itensCesta'] = $total_cesta;

if ($total_cesta == 1) $total_itens_cesta = 'Você tem ' . $total_cesta . ' item em sua sacola';
else $total_itens_cesta = 'Você tem ' . $total_cesta . ' itens em sua sacola';

//while itens cesta
while ($ces = mysql_fetch_array($cesta)) {
	$subValor = ($ces['valor'] * $ces['qtd']);

	//consulta produto
	$Prod = mysql_query("SELECT codigo, titulo, peso, largura, altura, profundidade, valor, valor_promocao, estoque FROM produtos WHERE codigo='" . $ces['cod_prod'] . "' LIMIT 1");
	$rowProd = mysql_fetch_array($Prod);

	if ($rowProd['valor_promocao'] != '0.00') $valor_produto = $rowProd['valor_promocao'];
	else $valor_produto = $rowProd['valor'];

	//peso total
	$pesoQtd = ($rowProd['peso'] * $ces['qtd']);
	$pesoTotal = $pesoQtd + $pesoTotal;

	//largura total
	$larguraQtd = ($rowProd['largura'] * $ces['qtd']);
	$larguraTotal += $larguraQtd;

	//altura total
	$alturaQtd = ($rowProd['altura'] * $ces['qtd']);
	$alturaTotal += $alturaQtd;

	//profundidade total
	$profundidadeQtd = ($rowProd['profundidade'] * $ces['qtd']);
	$profundidadeTotal += $profundidadeQtd;

	//subtotal
	$subTotal = $subValor + $subTotal;
} //FIM //while cesta

//trata medidas de limites mínimos
if ($larguraTotal < '11') $larguraTotal = '11';
if ($alturaTotal < '2') $alturaTotal = '2';
if ($profundidadeTotal < '16') $profundidadeTotal = '16';
//FIM //trata medidas de limites mínimos

//limites maximos para calulos dos correios
$pesoMaximo = '30'; //kg
$larguraMaximo = '105'; //cm
$alturaMaximo = '105'; //cm
$profundidadeMaximo = '105'; //cm #profundidade ou comprimento
//FIM //limites maximos para calulos dos correios

//calcula soma maxima das dimensões P + L + A, não podendo ultrapassar 200cm
$dimensoesTotal = $larguraTotal + $alturaTotal + $profundidadeTotal;
if ($dimensoesTotal <= '200') $dimensoes_liberado = '1';
else $dimensoes_liberado = '0';
//FIM //calcula soma maxima das dimensões P + L + A, não podendo ultrapassar 200cm

//verifica se pode ser calculado pelos correios
if ($pesoTotal <= $pesoMaximo && $larguraTotal <= $larguraMaximo && $alturaTotal <= $alturaMaximo && $profundidadeTotal <= $profundidadeMaximo && $dimensoes_liberado == '1') $correios_liberado = '1';
else $correios_liberado = '0';
//FIM //verifica se pode ser calculado pelos correios

//carrega sessao com valor do subtotal para printar no topo
$_SESSION['carrinho']['valorCesta'] = $subTotal;

//verifica se é frete grátis
if ($rowMeta['frete_gratis'] > '0.00' && $subTotal > $rowMeta['frete_gratis']) {
	$_SESSION['carrinho']['tipoFrete'] = 'gratis';
	$frete = $_SESSION['carrinho']['tipoFrete'];
	if (!empty($_POST['cep'])) $cep = secure($_POST['cep']);
	else $cep = $_SESSION['carrinho']['cep'];
}

//####### CALCULO DE FRETE PELA TRANSPORTADORA ###########################################################################################################
if ($rowMeta['FEtransportadora'] == '1' && $frete != 'gratis') {

	//se for o segundo cálculo
	if (isset($_REQUEST['etapa'])) {

		if ($_SESSION['carrinho']['tipoFrete'] > '0') { //se for um tipo transportadora

			//pega os valores já definidos
			$cep = $_SESSION['carrinho']['cep'];
			$frete = $_SESSION['carrinho']['tipoFrete'];

			if ($_SESSION['usuario']['ss_codigo'] && $cep != $cepCli) { //se estiver logado e o cep for diferente do cadastro do cliente #recalcula

				$cep = $cepCli;
				//pega a faixa de cep
				$cep_trans = explode("-", $cep);
				$explodecep = $cep_trans[0];
				$conta_cep = strlen($cep);

				//verifica de qual regiao é o cep
				$sql_regiao = mysql_query("SELECT codigo FROM regiao WHERE de<='" . $explodecep . "' AND ate>='" . $explodecep . "' LIMIT 1");
				$reg = mysql_fetch_array($sql_regiao);
				$_SESSION['carrinho']['codREGIAO'] = $reg['codigo'];

				//consulta peso do frete
				$query_frete = "
				SELECT DISTINCT(T.codigo), T.alias, T.titulo, F.valor, F.prazo 
				FROM transportadoras AS T, fretes AS F 
				WHERE T.status='S' AND T.ativa='S' AND F.ativa='S' AND F.regiao='" . $_SESSION['carrinho']['codREGIAO'] . "' AND F.pesode<='" . $pesoTotal . "' AND F.pesoate>='" . $pesoTotal . "' AND F.transportadora=T.codigo 
				ORDER BY T.titulo
				";
				$sql_frete_peso = mysql_query($query_frete);
				$tot_frete_peso = mysql_num_rows($sql_frete_peso);

				if ($conta_cep < '9') { //cep incorreto

					$_SESSION['carrinho']['cepInvalido'] = true;
					$msgCep = 'Informe o CEP corretamente';
				} else if ($tot_frete_peso == '0') { //não encontrado nenhuma transportadora disponivel

					$_SESSION['carrinho']['cepInvalido'] = true;
					$msgCep = 'Não entregamos nesta localidade';
				} else { //transportadoras disponiveis encontradas

					$_SESSION['carrinho']['cepInvalido'] = false;
					$msgCep = '';

					while ($fre = mysql_fetch_array($sql_frete_peso)) { //while frete

						$codTRANS = $fre['codigo'];
						$valorTRANS = $fre['valor'];
						$_SESSION['carrinho']['codTRANS'][$codTRANS] = $codTRANS;
						$_SESSION['carrinho']['valorTRANS'][$codTRANS] = $valorTRANS;
						$_SESSION['carrinho']['dias_frete'][$codTRANS] = "(" . $fre['prazo'] . " dias úteis após a confirmação de pagamento e postagem)";
					} //FIM //while frete

				} //FIM //transportadoras disponiveis encontradas

			} //FIM //se estiver logado e o cep for diferente do cadastro do cliente

			if ($frete == $_SESSION['carrinho']['codTRANS'][$frete]) {
				$valFrete = $_SESSION['carrinho']['valorTRANS'][$frete];
			}
		} //FIM //se for um tipo transportadora

		//echo $_SESSION['carrinho']['cepInvalido'];

	} //FIM //se for o segundo cálculo

	else { //se for o primeiro calculo
		#somente para exibir as transportadoras, valores e prazos

		$cep = secure($_POST['cep']);
		$frete = secure($_POST['frete']);

		if ($_POST['calculafrete'] == 'CALCULAR' && !empty($cep)) { //se clicou em caclular e o cep for preenchido

			//pega a faixa de cep
			$cep_trans = explode("-", $cep);
			$explodecep = $cep_trans[0];
			$conta_cep = strlen($cep);

			//verifica de qual regiao é o cep
			$sql_regiao = mysql_query("SELECT codigo FROM regiao WHERE de<='" . $explodecep . "' AND ate>='" . $explodecep . "' LIMIT 1");
			$reg = mysql_fetch_array($sql_regiao);
			$_SESSION['carrinho']['codREGIAO'] = $reg['codigo'];

			//consulta peso do frete
			$query_frete = "
			SELECT DISTINCT(T.codigo), T.alias, T.titulo, F.valor, F.prazo 
			FROM transportadoras AS T, fretes AS F 
			WHERE T.status='S' AND T.ativa='S' AND F.ativa='S' AND F.regiao='" . $_SESSION['carrinho']['codREGIAO'] . "' AND F.pesode<='" . $pesoTotal . "' AND F.pesoate>='" . $pesoTotal . "' AND F.transportadora=T.codigo 
			ORDER BY T.titulo
			";
			$sql_frete_peso = mysql_query($query_frete);
			$tot_frete_peso = mysql_num_rows($sql_frete_peso);

			if ($conta_cep < '9') { //cep incorreto

				$_SESSION['carrinho']['cepInvalido'] = true;
				$msgCep = 'Informe o CEP corretamente';
			} else if ($tot_frete_peso == '0') { //não encontrado nenhuma transportadora disponivel

				$_SESSION['carrinho']['cepInvalido'] = true;
				$msgCep = 'Não entregamos nesta localidade';
			} else { //transportadoras disponiveis encontradas

				$_SESSION['carrinho']['cepInvalido'] = false;
				$msgCep = '';

				while ($fre = mysql_fetch_array($sql_frete_peso)) { //while frete

					$codTRANS = $fre['codigo'];
					$valorTRANS = $fre['valor'];
					$_SESSION['carrinho']['codTRANS'][$codTRANS] = $codTRANS;
					$_SESSION['carrinho']['valorTRANS'][$codTRANS] = $valorTRANS;
					$_SESSION['carrinho']['dias_frete'][$codTRANS] = "(" . $fre['prazo'] . " dias úteis após a confirmação de pagamento e postagem)";
				} //FIM //while frete

			} //FIM //transportadoras disponiveis encontradas

		} //FIM //se clicou em caclular e o cep for preenchido

		if ($frete == $_SESSION['carrinho']['codTRANS'][$frete]) {
			$valFrete = $_SESSION['carrinho']['valorTRANS'][$frete];
		}
	} //FIM //se for o primeiro calculo

	if ($_SESSION['carrinho']['cepInvalido'] == '1')
		$_SESSION['carrinho']['frete_transportadora'] = '';
	else
		$_SESSION['carrinho']['frete_transportadora'] = '1';
} else {

	$_SESSION['carrinho']['frete_transportadora'] = '';
}
//####### FIM CALCULO DE FRETE PELA TRANSPORTADORA ###########################################################################################################


//####### CALCULO DE FRETE PELA JADLOG ###########################################################################################################
if ($rowMeta['FEjadlog'] == '1' && $frete != 'gratis') {

	//se for o segundo cálculo
	if (isset($_REQUEST['etapa'])) {

		//pega os valores já definidos
		$valorJADLOG = $_SESSION['carrinho']['valorJADLOG'];
		$cep = $_SESSION['carrinho']['cep'];
		$frete = $_SESSION['carrinho']['tipoFrete'];
		$valFrete = $_SESSION['carrinho']['valFrete'];

		//se for atualizar endereço e o cep for diferente, recalcula
		if ($_SESSION['usuario']['ss_codigo']) {

			if ($cep != $cepCli) {

				$cep = $cepCli;

				if (calculaFreteJadLog(base64_decode($rowMeta['senha_jadlog']), $row['valor'], removerCaracter($rowMeta['cep_jadlog']), removerCaracter($cep), $pesoTotal, removerCaracter($rowMeta['cnpj_jadlog'])) == false) {

					$_SESSION['carrinho']['cepInvalido'] = true;
					$msgCep = 'Informe o CEP corretamente';
				} else {

					$_SESSION['carrinho']['cepInvalido'] = false;
					$msgCep = '';

					$valorJADLOG = calculaFreteJadLog(base64_decode($rowMeta['senha_jadlog']), $row['valor'], removerCaracter($rowMeta['cep_jadlog']), removerCaracter($cep), $pesoTotal, removerCaracter($rowMeta['cnpj_jadlog']));
					$_SESSION['carrinho']['valorJADLOG'] = str_replace(',', '.', umzero($valorJADLOG));
				}

				if ($frete == 'jadlog') {
					$valFrete = $_SESSION['carrinho']['valorJADLOG'];
				}
			}
		} //FIM verifica cep igual



	} //FIM //se for o segundo cálculo

	else { //se for o primeiro calculo

		$cep = secure($_POST['cep']);
		$frete = secure($_POST['frete']);
		//calcula no site dos correios

		//////////////
		if ($_POST['calculafrete'] == 'CALCULAR' && !empty($cep)) {

			if (calculaFreteJadLog(base64_decode($rowMeta['senha_jadlog']), $row['valor'], removerCaracter($rowMeta['cep_jadlog']), removerCaracter($cep), $pesoTotal, removerCaracter($rowMeta['cnpj_jadlog'])) == false) {

				$_SESSION['carrinho']['cepInvalido'] = true;
				$msgCep = 'Informe o CEP corretamente';
			} else {

				$_SESSION['carrinho']['cepInvalido'] = false;
				$msgCep = '';

				$valorJADLOG = calculaFreteJadLog(base64_decode($rowMeta['senha_jadlog']), $row['valor'], removerCaracter($rowMeta['cep_jadlog']), removerCaracter($cep), $pesoTotal, removerCaracter($rowMeta['cnpj_jadlog']));
				$_SESSION['carrinho']['valorJADLOG'] = str_replace(',', '.', umzero($valorJADLOG));
			}
		} /////

		if ($frete == 'jadlog') {
			$valFrete = $_SESSION['carrinho']['valorJADLOG'];
		}
	} //FIM //se for o primeiro calculo

	if ($_SESSION['carrinho']['cepInvalido'] == '1')
		$_SESSION['carrinho']['frete_jadlog'] = '';
	else
		$_SESSION['carrinho']['frete_jadlog'] = '1';
} else {

	$_SESSION['carrinho']['frete_jadlog'] = '';
}
//####### FIM CALCULO DE FRETE PELA JADLOG ###########################################################################################################


//####### CALCULO DE FRETE PELOS CORREIOS ###########################################################################################################
if ($rowMeta['FEcorreios'] == '1' && $correios_liberado == '1' && $frete != 'gratis') {

	//se for o segundo cálculo
	if (isset($_REQUEST['etapa'])) {

		if ($_SESSION['carrinho']['tipoFrete'] == 'pac' || $_SESSION['carrinho']['tipoFrete'] == 'sedex') { //se for um tipo do correio

			//pega os valores já definidos
			$valorPAC = $_SESSION['carrinho']['valorPAC'];
			$valorSEDEX = $_SESSION['carrinho']['valorSEDEX'];
			$cep = $_SESSION['carrinho']['cep'];
			$frete = $_SESSION['carrinho']['tipoFrete'];
			$valFrete = $_SESSION['carrinho']['valFrete'];

			//se for atualizar endereço e o cep for diferente, recalcula
			if ($_SESSION['usuario']['ss_codigo']) {

				if ($cep != $cepCli) {

					$cep = $cepCli;

					if (calculaFrete('04014', $rowMeta['cep'], $cep, $pesoTotal, $alturaTotal, $larguraTotal, $profundidadeTotal) == false) {

						$_SESSION['carrinho']['cepInvalido'] = true;
						$msgCep = 'Informe o CEP corretamente';
					} else {

						$_SESSION['carrinho']['cepInvalido'] = false;
						$msgCep = '';

						//PAC
						$valorPAC = calculaFrete('04510', $rowMeta['cep'], $cep, $pesoTotal, $alturaTotal, $larguraTotal, $profundidadeTotal);
						$_SESSION['carrinho']['valorPAC'] = str_replace(',', '.', $valorPAC);

						//SEDEX
						$valorSEDEX = calculaFrete('04014', $rowMeta['cep'], $cep, $pesoTotal, $alturaTotal, $larguraTotal, $profundidadeTotal);
						$_SESSION['carrinho']['valorSEDEX'] = str_replace(',', '.', $valorSEDEX);
					}

					if ($frete == 'pac') {
						$valFrete = $_SESSION['carrinho']['valorPAC'];
					}
					if ($frete == 'sedex' || $frete == 'sedex a cobrar') {
						$valFrete = $_SESSION['carrinho']['valorSEDEX'];
					}
				}
			} //FIM verifica cep igual

		} //FIM //se for um tipo do correio

	} //FIM //se for o segundo cálculo

	else { //se for o primeiro calculo

		$cep = secure($_POST['cep']);
		$frete = secure($_POST['frete']);
		//calcula no site dos correios

		//////////////
		if ($_POST['calculafrete'] == 'CALCULAR' && !empty($cep)) {

			if (calculaFrete('04014', $rowMeta['cep'], $cep, $pesoTotal, $alturaTotal, $larguraTotal, $profundidadeTotal) == false) {

				$_SESSION['carrinho']['cepInvalido'] = true;
				$msgCep = 'Informe o CEP corretamente';
			} else {

				$_SESSION['carrinho']['cepInvalido'] = false;
				$msgCep = '';

				//PAC
				$valorPAC = calculaFrete('04510', $rowMeta['cep'], $cep, $pesoTotal, $alturaTotal, $larguraTotal, $profundidadeTotal);
				$_SESSION['carrinho']['valorPAC'] = str_replace(',', '.', $valorPAC);

				//SEDEX
				$valorSEDEX = calculaFrete('04014', $rowMeta['cep'], $cep, $pesoTotal, $alturaTotal, $larguraTotal, $profundidadeTotal);
				$_SESSION['carrinho']['valorSEDEX'] = str_replace(',', '.', $valorSEDEX);
			}
		} /////

		if ($frete == 'pac') {
			$valFrete = $_SESSION['carrinho']['valorPAC'];
		}
		if ($frete == 'sedex' || $frete == 'sedex a cobrar') {
			$valFrete = $_SESSION['carrinho']['valorSEDEX'];
		}
	} //FIM //se for o primeiro calculo

	if ($_SESSION['carrinho']['cepInvalido'] == '1')
		$_SESSION['carrinho']['frete_correio'] = '';
	else
		$_SESSION['carrinho']['frete_correio'] = '1';
} else {

	$_SESSION['carrinho']['frete_correio'] = '';
}
//####### FIM CALCULO DE FRETE PELOS CORREIOS ###########################################################################################################

/*if(empty($_SESSION['carrinho']['frete_transportadora']) && empty($_SESSION['carrinho']['frete_correio']) && $frete!='gratis' && !empty($cep)) {
	$_SESSION['carrinho']['cepInvalido'] = true;
	if(empty($msgCep)) $msgCep = 'Não foi possível calcular'; else $msgCep = $msgCep;
} else {
	$_SESSION['carrinho']['cepInvalido'] = false;
	$msgCep = '';
}
*/

if (!empty($cep) && $_SESSION['carrinho']['cepInvalido'] == false) {

	if (isset($frete)) {

		if ($_SESSION['carrinho']['tipoFrete'] == 'gratis') {
			$valor_frete = 'GRÁTIS';
			$input_frete = '<input type="hidden" name="valFrete" value="0.00" />';
		} else {
			$valor_frete = 'R$ ' . decimal($valFrete);
			$input_frete = '<input type="hidden" name="valFrete" value="' . $valFrete . '" />';
		}
	} else {

		$valor_frete = 'R$ 0,00';
	}
} else {

	if (!empty($msgCep)) $valor_frete = $msgCep;
	else $valor_frete = 'Informe seu CEP';
}


if (!empty($cep) && $_SESSION['carrinho']['cepInvalido'] == false && $_SESSION['carrinho']['tipoFrete'] != 'gratis') {

	$exibe_frete = '1'; //libera exibição de frete

} else {

	$exibe_frete = '0'; //NÃO libera exibição de frete

	$valFrete = '0.00';
}

//total do carrinho com frete
$Total = ($subTotal - $valor_desconto) + $valFrete;


//verificação de segurança
if ($_SESSION['carrinho']['tipoFrete'] != 'gratis') {
	if ($_SESSION['carrinho']['valFrete'] == '0.00' || $_SESSION['carrinho']['cepInvalido'] == true) {

		$nao_prosseguir = 1;
	}
}

//libera botão prossegui
if (!empty($cep) && $_SESSION['carrinho']['cepInvalido'] == false && isset($_REQUEST['frete']) && !isset($_REQUEST['etapa'])) {
	$libera_botao = 1;
} else if ($_SESSION['carrinho']['tipoFrete'] == 'gratis' && !empty($cep) && $_SESSION['carrinho']['cepInvalido'] == false) {
	$libera_botao = 1;
} else if ($libera_botao == '1') {
	$libera_botao = 1;
} else {
	$libera_botao = 0;
}
////////////////////////////

?>
<!DOCTYPE HTML>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<title><?php echo NOMEDOSITE; ?> | Carrinho</title>

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
	<meta property="og:type" content="article" />
	<meta property="og:url" content="http://www.<?php echo URLPADRAO . $_SERVER['REQUEST_URI']; ?>" />
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
			<a name="calcfrete"></a>
			<div id="faixa-cinza" class="mb15">
				<h3>CARRINHO</h3>
			</div>

			<?php if ($erro_dados == '1') { ?>
				<div class="alert alert-error"><?php echo $mensagem_end; ?></div>
			<?php } ?>

			<?php
			if ($total_cesta > '0') { //quando tiver algum item na cesta
			?>
				<form action="carrinho.php#calcfrete" method="post" name="cart" id="cart">
					<table width="100%" class="table table-condensed table-striped">
						<thead>
							<tr>
								<th width="14%" height="29" align="center" bgcolor="#FFCC00">Excluir</th>
								<th width="40%" align="left" valign="middle" bgcolor="#FFCC00">Nome do produto</th>
								<th width="19%" align="center" valign="middle" bgcolor="#FFCC00">Qtde.</th>
								<th width="14%" align="left" bgcolor="#FFCC00">Valor unit R$</th>
								<th width="13%" align="left" bgcolor="#FFCC00">Valor total R$</th>
							</tr>
						</thead>
						<tbody>

							<?php
							$cesta = mysql_query("SELECT * FROM cesta WHERE secao='" . session_id() . "' ORDER BY codigo DESC");
							while ($ces = mysql_fetch_array($cesta)) { //while cesta

								$subValor = ($ces['valor'] * $ces['qtd']);

								//consulta produto
								$Prod = mysql_query("SELECT codigo, titulo, peso, valor, valor_promocao, estoque FROM produtos WHERE codigo='" . $ces['cod_prod'] . "' LIMIT 1");
								$rowProd = mysql_fetch_array($Prod);

								//consulta variação
								$Var = mysql_query("SELECT codigo, titulo, estoque FROM variacoes WHERE codigo='" . $ces['cod_var'] . "' LIMIT 1");
								$rowVar = mysql_fetch_array($Var);

								//foto para exibição
								$sqlFoto = mysql_query("SELECT codigo, pp FROM fotos WHERE cod_pagina='" . $rowProd['codigo'] . "' AND pp='produtos' ORDER BY codigo LIMIT 1");
								$totFoto = mysql_num_rows($sqlFoto);
								$rowFoto = mysql_fetch_array($sqlFoto);

								if ($totFoto > 0 && file_exists(SITEPATH . 'upload/imagens/paginas/fotos_' . $rowFoto['codigo'] . '_' . $rowFoto['pp'] . '.jpg'))
									$fotoProd = SITEPATH . 'upload/imagens/paginas/fotos_tumb_' . $rowFoto['codigo'] . '_' . $rowFoto['pp'] . '.jpg';
								else
									$fotoProd = "img/semfoto.jpg";
							?>
								<tr>
									<td height="83" align="center"><a href="carrinho.php?action=delete&id=<? echo $ces['codigo']; ?>"><img src="img/fechar.png" border="0" alt="Fechar"></a></td>
									<td align="left">
										<div style="width:90px; float:left;">
											<img src="<?php echo $fotoProd ?>" width="70" border="0" alt="<?php echo $rowProd['titulo']; ?>">
										</div>
										<div style="text-align:left;">
											<strong><?php echo $rowProd['titulo']; ?></strong>
											<?php if (!empty($rowVar['titulo'])) echo '<br>' . $rowVar['titulo']; ?>
											<?php if (!empty($rowMeta['prazo_envio'])) echo '<br>Prazo de envio: ' . $rowMeta['prazo_envio']; ?>
											<?php if ($erroEstoque == $ces['codigo']) echo '<br><span style="color:#F00;">Essa é a quantidade máxima em estoque!</span>'; ?>
										</div>
									</td>
									<td align="center">
										<div align="center">
											<a href="carrinho.php?action=menos&id=<? echo $ces['codigo']; ?>"><img src="img/menos.png" border="0" alt="Adicionar" style="margin:5px 10px 0 42px; float:left;"></a>
											<div style="float:left;"><input type="text" name="qtd" value="<?php echo $ces['qtd']; ?>" readonly class="span1"></div>
											<a href="carrinho.php?action=mais&id=<? echo $ces['codigo']; ?>"><img src="img/add.png" border="0" alt="Adicionar" style="margin:5px 0 0 10px; float:left;"></a>
										</div>
									</td>
									<td align="left"><strong>R$ <?php echo decimal($ces['valor']); ?></strong></td>
									<td align="left"><strong>R$ <?php echo decimal($subValor); ?></strong></td>
								</tr>
							<?php
							} //FIM //while cesta
							?>

							<tr>
								<td height="40" align="center"></td>
								<td align="left"></td>
								<td align="left"></td>
								<td align="left"><strong>Sub-total</strong></td>
								<td align="left"><strong>R$ <?php echo decimal($subTotal); ?></strong></td>
							</tr>
							<tr>
								<td colspan="3" align="left" style="padding:20px 0 10px 25px;">
									<h2 class="muted">CALCULAR FRETE</h2>
									<p>Informe o seu CEP ao lado para calcular o frete:</p>
									<div style="float:left; margin-left:350px; margin-top:-45px;">
										<input type="text" class="input2" style="width:170px; float:left; margin-right:10px;" name="cep" alt="cep" value="<?php echo $cep; ?>">
										<div id="bt1" style="width:150px; float:left;">
											<input type="submit" class="btn" style="padding:4px 15px;" name="calculafrete" id="botaoFRETE" value="CALCULAR">
										</div>
										<div id="bt2" style="display:none; width:150px; float:left;">
											<input type="button" class="btn" style="padding:4px 15px;" disabled name="calculafrete" value="Aguarde...">
										</div>
									</div>
								</td>
								<td align="left"><strong>FRETE</strong></td>
								<td align="left"><strong><?php echo $cepInvalido; ?><?php
																					echo $valor_frete;
																					echo $input_frete;
																					?></strong></td>
							</tr>
							<?php
							if ($exibe_frete == '1') { //frete do correio 
							?>
								<tr>
									<td height="40" colspan="3" align="left" style="padding:25px;">
										<h3 class="muted" style="margin-bottom:20px;">Selecione a modalidade de entrega</h3>

										<?php if ($_SESSION['carrinho']['frete_correio'] == '1') { //quando puder calcular correio 
										?>
											<?php if ($_SESSION['carrinho']['valorPAC'] > '0') { ?>
												<p style="float:left; margin-bottom:10px;">
													<input type="radio" name="frete" id="fretePac" value="pac" <?php if ($frete == 'pac') echo "checked"; ?> onclick="this.form.submit();" style="margin:-10px 10px 0 0;">
													<span for="fretePac"><img src="img/pac-logo.png" width="78" height="23" align="middle" title="PAC" alt="PAC"></span>
												<h6 style="float:left; margin-left:10px;"><strong>PAC - R$ <?php echo decimal($_SESSION['carrinho']['valorPAC']); ?></strong></h6>
												<span style="font-family:Arial, Helvetica, sans-serif; font-size:12px; margin:2px 0 0 5px;">&nbsp;&nbsp;(7 a 15 dias úteis após a confirmação de pagamento e postagem)</span>
												</p>
												<div class="clearfix"></div>
											<?php } ?>
											<?php if ($_SESSION['carrinho']['valorSEDEX'] > '0') { ?>
												<p style="float:left; margin-bottom:10px;">
													<input type="radio" name="frete" id="freteSedex" value="sedex" <?php if ($frete == 'sedex') echo "checked"; ?> onclick="this.form.submit();" style="margin:-10px 10px 0 0;">
													<span for="freteSedex"><img src="img/sedex-logo.png" width="78" height="23" align="absmiddle" title="SEDEX" alt="SEDEX"></span>
												<h6 style="float:left; margin-left:10px;"><strong>SEDEX - R$ <?php echo decimal($_SESSION['carrinho']['valorSEDEX']); ?></strong></h6>
												<span style="font-family:Arial, Helvetica, sans-serif; font-size:12px; margin:2px 0 0 5px;"> &nbsp;&nbsp;(2 a 5 dias úteis após a confirmação de pagamento e postagem)</span>
												</p>
												<div class="clearfix"></div>
											<?php } ?>
										<?php } //FIM //quando puder calcular correios 
										?>

										<?php
										if ($_SESSION['carrinho']['frete_jadlog'] == '1' && $_SESSION['carrinho']['valorJADLOG'] > '0') { //quando puder calcular jadlog 

											$caminhoJad = SITEPATH . "upload/imagens/transportadoras/jadlog.jpg";
											if (file_exists($caminhoJad))
												$imgJad = $caminhoJad;
											else
												$imgJad = "img/frete-transp.png";
										?>
											<p style="float:left; margin-bottom:10px;">
												<input type="radio" name="frete" id="freteJadLog" value="jadlog" <?php if ($frete == 'jadlog') echo "checked"; ?> onclick="this.form.submit();" style="margin:-10px 10px 0 0;">
												<span for="freteJadLog"><img src="<?php echo $imgJad; ?>" width="78" height="23" align="absmiddle" alt="JadLog"></span>
											<h6 style="float:left; margin-left:10px;"><strong>JADLOG - R$ <?php echo decimal($_SESSION['carrinho']['valorJADLOG']); ?></strong></h6>
											<?php if (!empty($rowMeta['de_jadlog']) && !empty($rowMeta['ate_jadlog'])) { ?>
												<span style="font-family:Arial, Helvetica, sans-serif; font-size:12px; margin:2px 0 0 5px;">&nbsp;&nbsp;(<?php echo $rowMeta['de_jadlog']; ?> a <?php echo $rowMeta['ate_jadlog']; ?> dias úteis após a confirmação de pagamento e postagem)</span>
											<?php } ?>
											</p>
											<div class="clearfix"></div>
										<?php } //FIM //quando puder calcular jadlog 
										?>

										<?php if ($_SESSION['carrinho']['frete_transportadora'] == '1') { //quando puder calcular transportadora 
										?>
											<?php
											$sql = mysql_query("
					SELECT DISTINCT(T.codigo), T.alias, T.titulo, F.valor, F.prazo 
					FROM transportadoras AS T, fretes AS F 
					WHERE T.status='S' AND T.ativa='S' AND F.ativa='S' AND F.regiao='" . $_SESSION['carrinho']['codREGIAO'] . "' AND F.pesode<='" . $pesoTotal . "' AND F.pesoate>='" . $pesoTotal . "' AND F.transportadora=T.codigo 
					ORDER BY T.titulo
					");
											while ($row = mysql_fetch_array($sql)) { //while transportadoras

												$codTRANS = $row['codigo'];
												$caminho = SITEPATH . "upload/imagens/transportadoras/transportadora_" . $row['codigo'] . ".jpg";
												if (file_exists($caminho))
													$imgTrans = $caminho;
												else
													$imgTrans = "img/frete-transp.png";
											?>
												<p style="float:left;">
													<input type="radio" name="frete" value="<?php echo $row['codigo']; ?>" <?php if ($frete == $row['codigo']) echo "checked"; ?> onclick="this.form.submit();" style="margin:-10px 10px 0 0;">
													<img src="<?php echo $imgTrans; ?>" width="78" height="23" align="absmiddle" alt="<?php echo $row['titulo']; ?>">
												<h6 style="float:left; margin-left:10px; text-transform:uppercase;"><strong><?php echo $row['titulo']; ?> - R$ <?php echo decimal($_SESSION['carrinho']['valorTRANS'][$codTRANS]); ?></strong></h6>
												<span style="font-family:Arial, Helvetica, sans-serif; font-size:12px; margin:2px 0 0 5px;"> &nbsp;&nbsp;<?php echo $_SESSION['carrinho']['dias_frete'][$codTRANS]; ?></span>
												</p>
												<div class="clearfix"></div>
											<?php } //FIM //while transportadoras 
											?>
										<?php } //FIM //quando puder calcular transportadora 
										?>
									</td>
									<td align="center">&nbsp;</td>
									<td align="center">&nbsp;</td>
								</tr>
							<?php } //FIM //frete do correio 
							?>
							<tr>
								<td height="40" colspan="3" align="left">&nbsp;</td>
								<td align="left">
									<h4 style="margin-top:5px; font-weight:bold; font-size:17px;">Total do pedido</h4>
								</td>
								<td align="left">
									<h4 style="margin-top:5px; font-weight:bold; font-size:17px;">R$ <?php echo decimal($Total); ?></h4>
								</td>
							</tr>

						</tbody>
					</table>
				</form>

				<?php if (!isset($_REQUEST['etapa'])) { ?>
					<div align="left" style="width:200px; float:left;"><input type="button" name="maisitens" class="btn btn-large" value="Continuar comprando" onClick="location.href='home'"></div>
				<?php } ?>

				<?php
				if ($libera_botao == '1') {
					//frete
					$_SESSION['carrinho']['valFrete'] = $valFrete;
					$_SESSION['carrinho']['tipoFrete'] = $frete;
					$_SESSION['carrinho']['cep'] = $cep;

				?>
					<div align="right" style="width:200px; float:right;"><input type="button" name="finalizar" class="btn btn-primary btn-large" style="color:#FFF;" value="Finalizar pedido" onClick="location.href='carrinho.php?etapa=pedido#confirmacao'"></div>
				<?php } ?>

				<div class="clear">&nbsp;</div>

				<?php
				if ($_REQUEST['etapa'] == 'pedido') { //etapa do pedido

					//se estiver logado.....
					if ($_SESSION['usuario']['ss_codigo']) {

				?>

						<a name="confirmacao"></a>
						<center>
							<h2>Confirmação de dados e endereço para entrega</h2>
							<hr>
							<p style="margin-bottom:30px; text-align:center;">Confira abaixo os seus dados e endereço e efetue as alterações que achar necessário. O endereço abaixo será utilizado para a entrega do seu pedido.</p>
						</center>

						<?php echo $mensagem_end; ?>

						<form action="carrinho#confirmacao" method="POST" name="endereco" id="endereco" style="width:100%; margin:0; padding:0;">
							<input type="hidden" name="cep" value="<?php echo $cep; ?>" />
							<input type="hidden" name="operacao" value="atualizaendereco" />
							<input type="hidden" name="etapa" value="pedido" />
							<table class="table table-condensed" width="100%" border="0" cellspacing="2" cellpadding="2">
								<tr>
									<td height="34" colspan="2" align="center" bgcolor="#FFCC00">Dados cadastrais</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td height="25">Nome completo:</td>
									<td>Email:</td>
								</tr>
								<tr>
									<td bgcolor="#F4F4F4"><input type="text" name="nome" value="<?php echo $rowCli['nome']; ?>" class="span6" required> *</td>
									<td bgcolor="#F4F4F4"><input type="email" name="email" value="<?php echo $rowCli['email']; ?>" class="span6" required> *</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td height="26">Telefone:</td>
									<td>Celular:</td>
								</tr>
								<tr>
									<td bgcolor="#F4F4F4"><input type="text" name="telefone" value="<?php echo $rowCli['telefone']; ?>" alt="phone" class="span6" required> *</td>
									<td bgcolor="#F4F4F4"><input type="text" name="celular" value="<?php echo $rowCli['telefone_cel']; ?>" alt="phone" class="span6"></td>
								</tr>
							</table>

							<div class="clearfix">&nbsp;</div>

							<table class="table table-condensed" width="100%" border="0" cellspacing="2" cellpadding="0">
								<tr>
									<td height="34" colspan="4" align="center" bgcolor="#FFCC00">Endereço para entrega</td>
								</tr>
								<tr>
									<td width="432">&nbsp;</td>
									<td width="418">&nbsp;</td>
									<td width="418">&nbsp;</td>
									<td width="230">&nbsp;</td>
								</tr>
								<tr>
									<td height="27">Endereço:</td>
									<td>Número:</td>
									<td>Complemento:</td>
									<td>CEP:</td>
								</tr>
								<tr>
									<td height="35" valign="middle" bgcolor="#F4F4F4"><input type="text" name="endereco" value="<?php echo $rowCli['endereco']; ?>" class="span4" required> *</td>
									<td valign="middle" bgcolor="#F4F4F4"><input type="text" name="numero" value="<?php echo $rowCli['numero_casa']; ?>" class="span2" required>
										*</td>
									<td valign="middle" bgcolor="#F4F4F4"><input type="text" name="complemento" value="<?php echo $rowCli['complemento']; ?>" class="span3"></td>
									<td valign="middle" bgcolor="#F4F4F4"><input type="text" name="cepCli" value="<?php echo $rowCli['cep']; ?>" alt="cep" class="span2" required> *</td>
								</tr>
								<tr>
									<td height="26">&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td height="26">Bairro:</td>
									<td>&nbsp;</td>
									<td>Cidade:</td>
									<td>Estado:</td>
								</tr>
								<tr>
									<td bgcolor="#F4F4F4"><input type="text" name="bairro" value="<?php echo $rowCli['bairro']; ?>" class="span4" required> *</td>
									<td bgcolor="#F4F4F4">&nbsp;</td>
									<td bgcolor="#F4F4F4"><input type="text" name="cidade" value="<?php echo $rowCli['cidade']; ?>" size="45" class="span3" required> *</td>
									<td bgcolor="#F4F4F4"><input type="text" name="estado" id="estado" value="<?php echo $rowCli['estado']; ?>" size="2" class="span1" required> *</td>
								</tr>
							</table>
							<div align="right" style="width:200px; float:right;"><input type="submit" name="atualizar" class="btn btn-primary btn-large" style="color:#FFF;" value="Atualizar endereço"></div>

						</form>


						<div class="clear">&nbsp;</div>

						<?php
						//volta pra etapa anterior se não tiver calculado o frete
						if ($nao_prosseguir != '1') {
						?>

							<div class="clearfix">&nbsp;</div>

							<form action="processa_pedido.php" method="POST" name="pagamento" id="pagamento">

								<center>
									<a name="confirmacao"></a>
									<h2>Forma de pagamento</h2>
									<hr>
									<p style="margin-bottom:30px; text-align:center;">Escolha abaixo a forma de pagamento desejada.</p>
								</center>

								<?php if ($rowMeta['FPcielo'] == '1') { //pagarme 
								?>
									<div style="padding:10px 0 10px 0; text-align:center; margin-bottom:20px; background: url(img/fdd4.jpg) repeat-x top; border-radius:5px; border:solid 1px #CCC;">
										<h2 style="margin-bottom:20px; font-size:18px; color:#039;">
											<input type="radio" name="pagamento" value="Cielo" checked="checked" />
											CARTÃO DE CRÉDITO OU BOLETO
										</h2>
										<p style="font-size:14px; margin-bottom:20px;"><span class="blue"><strong>R$ <?php echo decimal($Total); ?></strong></span> - Cartões de crédito - Ambiente seguro</p>
										<img src="img/visa.gif" alt="visa" width="62" height="35" hspace="5" />&nbsp;&nbsp;&nbsp;&nbsp; <img src="img/master.gif" alt="master" width="62" height="35" hspace="5" />&nbsp;&nbsp;&nbsp;&nbsp; <img src="img/diners.gif" alt="diners" width="62" height="35" hspace="5" />&nbsp;&nbsp;&nbsp;&nbsp;
										<img src="img/discover.gif" width="62" height="35" alt="discover" />&nbsp;&nbsp;&nbsp;&nbsp;
										<img src="img/elo.gif" width="62" height="35" alt="elo" />&nbsp;&nbsp;&nbsp;&nbsp;
										<img src="img/aura.gif" alt="elo" width="62" height="35" hspace="5" />&nbsp;&nbsp;&nbsp;&nbsp;
									</div>
								<?php } //FIM //pagarme 
								?>



								<?php if ($rowMeta['FPpagseguro'] == '1') { //pagseguro 
								?>
									<div style="padding:10px 0 10px 0; text-align:center; margin-bottom:20px; background: url(img/fdd4.jpg) repeat-x top; border-radius:5px; border:solid 1px #CCC;">
										<h2 style="margin-bottom:20px; font-size:18px; color:#039;">
											<input type="radio" name="pagamento" value="PagSeguro" checked="checked" />
											PAGSEGURO
										</h2>
										<p style="font-size:14px; margin-bottom:20px;"><span class="blue"><strong>R$ <?php echo decimal($Total); ?></strong></span> - Cartões de crédito e débito, pelo PagSeguro</p>
										<img src="img/pagseguro.jpg" width="527" height="39" alt="pagseguro" />
									</div>
								<?php } //FIM //pagseguro 
								?>



								<?php
								if ($rowMeta['FPdeposito'] == '1') { //deposito 

									//se tiver desconto
									if ($rowMeta['desconto_deposito'] > '0') {

										$desc = ($rowMeta['desconto_deposito'] / 100);
										$desconto_deposito = $desc * $subTotal;
										$TotalDesc =  $subTotal - $desconto_deposito + $valFrete;

										$frase_desc = ' (' . $rowMeta['desconto_deposito'] . '% de desconto)';
									} //FIM //se tiver desconto
									else $TotalDesc =  $Total;
								?>
									<div style="padding:10px 0 10px 0; text-align:center; margin-bottom:20px; background: url(img/fdd4.jpg) repeat-x top; border-radius:5px; border:solid 1px #CCC;">
										<h2 style="margin-bottom:20px; font-size:18px; color:#039;">
											<input type="radio" name="pagamento" value="Deposito" checked="checked" />
											DEPÓSITO BANCÁRIO
										</h2>
										<p style="font-size:14px; margin-bottom:20px;">
											<strong>R$ <?php echo decimal($TotalDesc); ?><?php echo $frase_desc; ?></strong> - Depósito ou transferência bancária
										</p>
									</div>
								<?php } //FIM //deposito 
								?>


								<input type="submit" name="finalizar" class="btn btn-danger btn-large" value="Concluir a compra" style="float:right;">

								<input type="hidden" name="total" value="<?php echo $Total; ?>" />
								<input type="hidden" name="frete" value="<?php echo $frete; ?>" />
								<input type="hidden" name="valFrete" value="<?php echo $valFrete; ?>" />
								<input type="hidden" name="subtotal" value="<?php echo $subTotal; ?>" />
								<input type="hidden" name="desconto_boleto" value="<?php echo $desconto_boleto; ?>" />
								<input type="hidden" name="desconto_deposito" value="<?php echo $desconto_deposito; ?>" />
								<input type="hidden" name="desconto_boleto_porcento" value="<?php echo $rowMeta['desconto_boleto']; ?>" />
								<input type="hidden" name="desconto_deposito_porcento" value="<?php echo $rowMeta['desconto_deposito']; ?>" />

							</form>

						<?php
						} //FIM //volta pra etapa anterior se não tiver calculado o frete
						?>

						<div class="clear" style="margin-bottom:40px;">&nbsp;</div>

				<?php
					} //FIM //se estiver logado

					else { //senão vai pro login

						echo '<meta http-equiv="refresh" content="0;URL=login/acesso/carrinho" />';
						exit;
					} //FIM //ir pro login

				} //FIM //etapa do pedido
				?>


				<div class="clear">&nbsp;</div>

			<?php
			} //FIM //quando tiver algum item na cesta

			else {
				//se a cesta estiver vazia
			?>
				<div class="neutro" style="text-align:center; padding:20px; margin:40px 0 50px 0;">
					<h2 style="margin-bottom:20px;">Seu carrinho de compras está vazio.</h2>
					<p style="margin-bottom:10px;">Volte e adicione um ou mais produtos.</p>
				</div>

				<div class="clear">&nbsp;</div>
			<?php
			} //FIM //se a cesta estiver vazia
			?>

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
	<script type="text/javascript" src="js/meiomask.js" charset="utf-8"></script>
	<script type="text/javascript">
		(function($) {
			// call setMask function on the document.ready event
			$(function() {
				$('input:text').setMask();
			});
		})(jQuery);
	</script>
	<!-- mask -->
	<script src="js/jquery.filter_input.js"></script>
	<script>
		$(document).ready(function() {
			$('#estado').filter_input({
				regex: '[a-zA-Z]'
			});
		});
	</script>

	<script type="text/javascript">
		$("#botaoFRETE").click(function() {
			$("#bt1").hide();
			$("#bt2").show();
		});
	</script>

	<script src="js/jquery.bxslider.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.slidermarcas').bxSlider({
				slideWidth: 100,
				minSlides: 2,
				maxSlides: 20,
				slideMargin: 1
			});
		});
	</script>

	<script type="text/javascript" src="js/scripts-site.js"></script>
	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-512d3f98721dd6fc"></script>

	<?php echo $analytics; ?>
</body>

</html>