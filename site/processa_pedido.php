<?php
require('config.php');
require('cart.php');

$cliente = $_SESSION['usuario']['ss_codigo'];
$nomecliente = $_SESSION['usuario']['ss_nome'];
$emailcliente = $_SESSION['usuario']['ss_email'];

//#### trocar pagamento ################################################################################################################
if ($_POST['trocar'] == 'pagamento') {

	//trata dados
	$chave = secure($_POST['chave']);
	$pagamento = secure($_POST['pagamento']);
	$desconto_boleto = secure($_POST['desconto_boleto']);
	$desconto_deposito = secure($_POST['desconto_deposito']);

	if ($pagamento == 'Boleto' && $rowMeta['desconto_boleto'] > '0') $valor_desconto = $desconto_boleto;
	else if ($pagamento == 'Deposito' && $rowMeta['desconto_deposito'] > '0') $valor_desconto = $desconto_deposito;
	else $valor_desconto = '0.00';

	//atualiza pedido
	$sql_Add = "UPDATE pedidos SET pagamento='$pagamento', valor_desconto='$valor_desconto' WHERE chave='$chave' ";
	$res_Add = mysql_query($sql_Add, $conecta) or die("Erro ao atualizar pagamento: " . mysql_error());

	header("Location:/pagamento/$chave");
	exit;

	//#### finalizar pedido ################################################################################################################	
} else {

	//trata dados
	$chave = md5($cliente . $data . $hora);
	$total = secure($_POST['total']);
	$subtotal = secure($_POST['subtotal']);
	$valor_frete = secure($_POST['valFrete']);
	$frete = secure($_POST['frete']);

	if (is_numeric($frete)) {
		$frete = @mysql_result(mysql_query("SELECT titulo FROM transportadoras WHERE codigo='$frete' LIMIT 1"), 0);
	}

	$pagamento = secure($_POST['pagamento']);
	$desconto_boleto = secure($_POST['desconto_boleto']);
	$desconto_deposito = secure($_POST['desconto_deposito']);
	$desconto_boleto_porcento = secure($_POST['desconto_boleto_porcento']);
	$desconto_deposito_porcento = secure($_POST['desconto_deposito_porcento']);

	//#### 1ª verificação de erro no pedido #########################
	//volta pra etapa anterior se não tiver calculado o frete
	if ($_SESSION['carrinho']['tipoFrete'] != 'gratis') {
		if ($_SESSION['carrinho']['valFrete'] == '0.00' || $_SESSION['carrinho']['cepInvalido'] == true) {

			//retorna pra cesta
			header("Location:/carrinho");
			exit;
		}
	}

	//#### 2ª verificação de erro no pedido #########################

	//consulta itens do pedido
	$sql = mysql_query("SELECT codigo, cod_prod, qtd, valor, cod_var FROM cesta WHERE secao='" . session_id() . "' ");
	while ($row = mysql_fetch_array($sql)) {
		$qtd = $row['qtd'];

		//titulo e estoque do produto
		$tep = mysql_fetch_array(mysql_query("SELECT titulo, estoque FROM produtos WHERE codigo='" . $row['cod_prod'] . "' LIMIT 1"));
		$titulo_prod = $tep['titulo'];
		$estoque_prod = $tep['estoque'];
		$nome_opcao = $tep['nome_opcao'];
		$nome_opcao2 = $tep['nome_opcao2'];

		$x = 0;

		//verifica se tem variação
		if ($row['cod_var'] != '0') {
			//titulo e estoque da variação
			$tev = mysql_fetch_array(mysql_query("SELECT titulo, estoque FROM variacoes WHERE codigo='" . $row['cod_var'] . "' LIMIT 1"));
			$estoque_variacao = $tev['estoque'];

			if ($estoque_variacao > '0') { //se tiver variação no estoque

				for ($i = 1; $i <= $qtd; $i++) {

					if ($i <= $estoque_variacao) {

						$x++;
						//retira do estoque
						$sql_Add = "UPDATE variacoes SET estoque=estoque-1 WHERE codigo='" . $row['cod_var'] . "' ";
						$res_Add = mysql_query($sql_Add, $conecta) or die("Erro ao retirar estoque da variação: " . mysql_error());
					}
				}

				//atualiza qtd da cesta pra quantidade q retirou em estoque
				mysql_query("UPDATE cesta SET qtd='$x' WHERE codigo='" . $row['codigo'] . "' ") or die("Erro ao atualizar cesta: " . mysql_error());
			} else { //se não tiver a varição em estoque

				//retira produto da cesta
				$sql_Rem = "DELETE FROM cesta WHERE codigo='" . $row['codigo'] . "' ";
				$res_Rem = mysql_query($sql_Rem, $conecta) or die("Erro ao deletar item na cesta: " . mysql_error());
			} //FIM //se não tiver a varição em estoque
		}
		//FIM //verifica se tem variação

		//se não tiver variação
		else {
			if ($estoque_prod > '0') { //se tiver estoque do produto

				for ($i = 1; $i <= $qtd; $i++) {

					if ($i <= $estoque_prod) {

						$x++;
						//retira do estoque
						$sql_Add = "UPDATE produtos SET estoque=estoque-1 WHERE codigo='" . $row['cod_prod'] . "' ";
						$res_Add = mysql_query($sql_Add, $conecta) or die("Erro ao retirar estoque do produto: " . mysql_error());
					}
				}

				//atualiza qtd da cesta pra quantidade q retirou em estoque
				mysql_query("UPDATE cesta SET qtd='$x' WHERE codigo='" . $row['codigo'] . "' ") or die("Erro ao atualizar cesta: " . mysql_error());
			} else { //se não tiver a estoque do produto

				//retira produto da cesta
				$sql_Rem = "DELETE FROM cesta WHERE codigo='" . $row['codigo'] . "' ";
				$res_Rem = mysql_query($sql_Rem, $conecta) or die("Erro ao deletar item na cesta: " . mysql_error());
			} //FIM //se não tiver a estoque do produto
		}
		//FIM //se não tiver variação

	} //FIM //consulta itens do pedido

	//#### FIM verificação de erro no pedido #########################

	//verifica se tem algum item na cesta
	$tot_cesta = mysql_num_rows(mysql_query("SELECT codigo FROM cesta WHERE secao='" . session_id() . "' "));

	//se a cesta estiver vazia 
	if ($tot_cesta == 0) {
		//retorna pra cesta vazia
		header("Location:/carrinho");
		exit;
	} else { //se existir item na cesta prossegue com o pedido

		///////////// recalcula valor do pedido
		$subValor = '0';
		$subtotal = '0';
		//itens do pedido
		$cesta = mysql_query("SELECT codigo, cod_prod, qtd, valor, cod_var FROM cesta WHERE secao='" . session_id() . "' ");
		while ($ces = mysql_fetch_array($cesta)) {
			$subValor = ($ces['valor'] * $ces['qtd']);
			//subtotal
			$subtotal += $subValor;
		}

		//total do carrinho com frete
		$total = $subtotal + $valor_frete;

		///////////// FIM recalcula valor do pedido


		//se for deposito com desconto
		if ($pagamento == 'Boleto' && $rowMeta['desconto_boleto'] > '0') {
			$total_desconto =  $total - $desconto_boleto;
			$mostra_valor = " (com " . $rowMeta['desconto_boleto'] . "% de desconto no Boleto Bancário): R$ " . decimal($total_desconto);
			$valor_desconto = $desconto_boleto;
		} else if ($pagamento == 'Deposito' && $rowMeta['desconto_deposito'] > '0') {
			$total_desconto =  $total - $desconto_deposito;
			$mostra_valor = " (com " . $rowMeta['desconto_deposito'] . "% de desconto no Depósito Bancário): R$ " . decimal($total_desconto);
			$valor_desconto = $desconto_deposito;
		} else { //senão o valor é normal

			$mostra_valor = ": R$ " . decimal($total);
		}
		/////////////////////////////////

		//consulta dados do cliente
		$sqlCli = mysql_query("SELECT * FROM clientes WHERE codigo='$cliente'");
		$rowCli = mysql_fetch_array($sqlCli);

		//grava o pedido
		$sqlPedido = mysql_query("INSERT INTO pedidos SET cliente='$cliente', chave='$chave', data='$data', hora='$hora', subtotal='$subtotal', total='$total', valor_frete='$valor_frete', frete='$frete', pagamento='$pagamento', status='S', situacao='1', valor_desconto='$valor_desconto', nome='" . $rowCli['nome'] . "', email='" . $rowCli['email'] . "', cpf='" . $rowCli['cpf'] . "', rg='" . $rowCli['rg'] . "', endereco='" . $rowCli['endereco'] . "', numero='" . $rowCli['numero_casa'] . "', bairro='" . $rowCli['bairro'] . "', complemento='" . $rowCli['complemento'] . "', cidade='" . $rowCli['cidade'] . "', estado='" . $rowCli['estado'] . "', cep='" . $rowCli['cep'] . "', telefone='" . $rowCli['telefone'] . "', celular='" . $rowCli['telefone_cel'] . "' ");
		$codigoPedido = mysql_insert_id();



		$itens = '
		<table width="700" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<th width="60%" height="35" align="left" bgcolor="#F1F1F1" style="padding:10px;border-bottom:solid 1px #CCC;">Produto</th>
				<th width="15%" height="35" align="right" style="padding:10px;border-bottom:solid 1px #CCC;">Val.Unit.</th>
				<th width="10%" align="center" bgcolor="#F1F1F1" style="padding:10px;border-bottom:solid 1px #CCC;">Qtd</th>
				<th width="15%" height="35" align="right" style="padding:10px;border-bottom:solid 1px #CCC;">Val.Tot.</th>
			</tr>
		';

		//itens do pedido
		$sql = mysql_query("SELECT codigo, cod_prod, qtd, valor, cod_var FROM cesta WHERE secao='" . session_id() . "' ");
		while ($row = mysql_fetch_array($sql)) {
			$val_uni = number_format(($row['valor']), 2, chr(44), ".");
			$sub_total = ($row['valor'] * $row['qtd']);
			$sub_tot = number_format(($sub_total), 2, chr(44), ".");
			$qtd = $row['qtd'];

			//titulo e estoque do produto
			$tep = mysql_fetch_array(mysql_query("SELECT titulo, estoque, sku FROM produtos WHERE codigo='" . $row['cod_prod'] . "' LIMIT 1"));
			$titulo_prod = $tep['titulo'];
			$estoque_prod = $tep['estoque'];
			$sku_prod = $tep['sku'];

			$titulo_produto = $titulo_prod;

			//verifica se tem variação
			if ($row['cod_var'] != '0') {
				//titulo e estoque da variação
				$tev = mysql_fetch_array(mysql_query("SELECT titulo, estoque FROM variacoes WHERE codigo='" . $row['cod_var'] . "' LIMIT 1"));
				$titulo_variacao = $tev['titulo'];
			}
			//FIM //verifica se tem variação

			//se não tiver variação
			else {
				$titulo_variacao = '';
			}
			//FIM //se não tiver variação


			//itens para exibir no email
			$itens .= '
			<tr>
			<td width="60%" bgcolor="#F1F1F1" style="padding:10px;border-bottom:solid 1px #CCC;"><strong>' . $titulo_prod . '</strong><br>' . $titulo_variacao . '</td>
			<td width="15%" align="right" style="padding:10px;border-bottom:solid 1px #CCC;">R$ ' . $val_uni . '</td>
			<td width="10%" align="center" bgcolor="#F1F1F1" style="padding:10px;border-bottom:solid 1px #CCC;">' . $qtd . '</td>
			<td width="15%" align="right" style="padding:10px;border-bottom:solid 1px #CCC;">R$ ' . $sub_tot . '</td>
			</tr>
			';

			//grava relacionamentode itens
			mysql_query("INSERT INTO pedidos_itens SET pedido='$codigoPedido', qtd='$qtd', produto='" . $row['cod_prod'] . "', titulo_produto='$titulo_produto', valor_unitario='" . $row['valor'] . "', valor_qtd='$sub_total', cod_variacao='" . $row['cod_var'] . "', sku='$sku_prod' ");
		}
		//FIM //itens orçamento

		if ($_SESSION['carrinho']['tipoFrete'] == 'gratis') {
			$exibe_frete = 'Grátis';
		} else {
			$exibe_frete = 'R$ ' . decimal($valor_frete);
		}

		$itens .= '
		<tr>
		<td colspan="4" align="right" style="padding:10px;border-bottom:solid 1px #CCC;">Sub-total: R$ ' . decimal($subtotal) . '</td>
		</tr>';

		if (!empty($desconto)) {

			$itens .= '
		<tr>
		<td colspan="4" align="right" bgcolor="#F1F1F1" style="padding:10px;border-bottom:solid 1px #CCC;">Desconto de ' . $desconto . '%: R$ ' . decimal($valor_desconto) . '</td>
		</tr>
		<tr>
		<td colspan="4" align="right" style="padding:10px;border-bottom:solid 1px #CCC;">Sub-total com desconto: R$ ' . decimal($valor_com_desconto) . '</td>
		</tr>';
		}

		if ($frete == 'gratis') $modalidade = "";
		else {
			if (is_numeric($frete) == '1') $frete = @mysql_result(mysql_query("SELECT titulo FROM transportadoras WHERE codigo='$frete' LIMIT 1"), 0);
			else $frete = $frete;
			$modalidade = '<b>Modalidade de envio:</b> <span style="text-transform:uppercase;">' . $frete . '</span><br><br>';
		}

		$itens .= '
		<tr>
		<td colspan="4" align="right" bgcolor="#F1F1F1" style="padding:10px;border-bottom:solid 1px #CCC;">Valor do Frete: ' . $exibe_frete . '</td>
		</tr>
		<tr>
		<td colspan="4" align="right" style="padding:10px;border-bottom:solid 1px #CCC;">Total do pedido' . $mostra_valor . '</td>
		</tr>
		</table>
		<br>
		';

		////////////////////////////

		//envia email para o cliente

		$subject = "Pedido No. $codigoPedido realizado com sucesso";

		if (file_exists("ad/upload/imagens/newsletter/topo_mailing.jpg")) {
			$img_topo = "<img src=http://www." . URLPADRAO . "/ad/upload/imagens/newsletter/topo_mailing.jpg /><br><br>";
		}

		if (!empty($rowMeta['telefone'])) $mostra_telefone = "Telefone: " . $rowMeta['telefone'] . "<br>";
		if (!empty($rowMeta['email_contato'])) $mostra_email = "Email: " . $rowMeta['email_contato'] . "<br>";

		$message = "<html>
		<font face='arial' size='2'>
		$img_topo
		<h2>Olá $nomecliente,</h2>
		Este é um alerta automático confirmando um novo pedido realizado através da loja " . NOMEDOSITE . ".<br><br>
		Em primeiro lugar, gostaríamos de agradecer a preferência e confiança na " . NOMEDOSITE . ". <br><br>
		Nossa meta é sua total satisfação e estamos trabalhando ao máximo para que isto ocorra tanto antes como depois da compra. <br><br>
		
		Todos os dados fornecidos por você durante o processo de compra trafegaram com total segurança.<br><br>
		
		-------------------------------------------------------------------------------------<br>
		<h2><b>No. do pedido:</b> $codigoPedido </h2>
		-------------------------------------------------------------------------------------<br><br>
		<b>Realizado em:</b> $dataBra - $hora <br><br>
		-------------------------------------------------------------------------------------<br><br>
		<b>ITENS DO PEDIDO:</b><br><br>
		$itens
		-----------------------------------------------------------------------------------------------------------------------------------<br><br>
		$modalidade
		<b>Forma de pagamento escolhida:</b> $pagamento <br><br>
		
		Caso ainda não tenha realizado o pagamento, clique no link abaixo:<br>
		<a href=" . BASEURL . "pagamento/$chave>" . BASEURL . "pagamento/$chave</a><br><br>
		-----------------------------------------------------------------------------------------------------------------------------------<br><br>
		Caso tenha alguma dúvida ref. ao seu pedido, entre em contato com o nosso atendimento. Ficaremos felizes em atendê-lo(a).<br><br>
		Até a próxima compra! <br><br>
		<strong>" . NOMEDOSITE . "<br></strong>
		www." . URLPADRAO . "<br><br>
		$mostra_telefone
		$mostra_email
		</font>
		</html>
		";

		enviarcomsmtp($rowMeta['smtp_servidor'], $rowMeta['smtp_porta'], $rowMeta['smtp_senha'], $rowMeta['smtp_usuario'], $rowMeta['smtp_origem'], $rowMeta['smtp_origem'], NOMEDOSITE, $emailcliente, $nomecliente, $subject, $message);


		////////////////////////////



		//envia email para a loja
		$subject2 = "Novo Pedido No. $codigoPedido realizado com sucesso";

		$message2 = "<html>
		<font face='arial' size='2'>
		$img_topo
		Este é um alerta automático confirmando um novo pedido realizado através da loja virtual.<br><br>
		
		-----------------------------------------------------------------------------------------------------------------------------------<br>
		<h2><b>No. do pedido:</b> $codigoPedido </h2>
		-----------------------------------------------------------------------------------------------------------------------------------<br><br>
		<b>Realizado em:</b> $dataBra - $hora <br><br>
		-----------------------------------------------------------------------------------------------------------------------------------<br><br>
		<b>DADOS DO CLIENTE:</b><br><br>
		
		<b>Nome/Razão Social:</b> " . $rowCli['nome'] . " <br><br>
		<b>CPF/CNPJ:</b> " . $rowCli['cpf'] . " <br><br>
		<b>Email:</b> " . $rowCli['email'] . " <br><br>
		<b>Endereço:</b> " . $rowCli['endereco'] . ", " . $rowCli['numero_casa'] . " <br><br>
		<b>Bairro:</b> " . $rowCli['bairro'] . " <br><br>
		<b>Cidade / Estado:</b> " . $rowCli['cidade'] . " / " . $rowCli['estado'] . " <br><br>
		<b>CEP:</b> " . $rowCli['cep'] . " <br><br>
		<b>Telefone:</b> " . $rowCli['telefone'] . "  " . $rowCli['telefone_cel'] . " <br><br>
		-----------------------------------------------------------------------------------------------------------------------------------<br><br>
		<b>ITENS DO PEDIDO:</b><br><br>
		$itens
		-----------------------------------------------------------------------------------------------------------------------------------<br><br>
		$modalidade
		<b>Forma de pagamento escolhida:</b> $pagamento <br><br>
		-----------------------------------------------------------------------------------------------------------------------------------<br><br>
		<strong>" . NOMEDOSITE . "<br></strong>
		www." . URLPADRAO . "<br><br>
		$mostra_telefone
		$mostra_email
		</font>
		</html>
		";

		$email_alerta = $rowMeta['email_alerta'];
		$destinatarios = explode(';', $email_alerta);
		foreach ($destinatarios as $to) { //foreach emails alerta
			$to = trim($to);

			//dispara email para admin
			enviarcomsmtp($rowMeta['smtp_servidor'], $rowMeta['smtp_porta'], $rowMeta['smtp_senha'], $rowMeta['smtp_usuario'], $rowMeta['smtp_origem'], $rowMeta['smtp_origem'], NOMEDOSITE, $to, NOMEDOSITE, $subject2, $message2);
		} //FIM //foreach emails alerta	

		////////////////////////////


		//remove itens
		$sql_Add2 = "DELETE FROM cesta WHERE secao='" . session_id() . "' ";
		$res_Add2 = mysql_query($sql_Add2, $conecta);
		if (!$res_Add2) {
			echo "<div class=\"error\">Erro ao processar operação. REMOVER ITENS.</div>";
			die;
		}

		$_SESSION['itensCesta'] = '';
		unset($_SESSION['carrinho']);
		$_SESSION['usuario']['pedido'] = $codigoPedido;
		//

		header("Location:/pagamento/$chave");
		exit;
	} //FIM //se existir item na cesta prossegue com o pedido

}
//#### FIM finalizar pedido ###############################################################################################################