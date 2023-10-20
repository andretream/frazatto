<?php
//path
define('SITEPATH', 'ad/');

//config global para o site
require(SITEPATH . 'admin/config/_conecta.php');
require(SITEPATH . 'admin/config/_funcoes.php');
require(SITEPATH . 'admin/config/_globais.php');

/*ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL ^ E_NOTICE);*/

//base
define('BASEURL', 'https://www.' . URLPADRAO . '/');
//maximo parcelas
define('MAXPARCELAS', '6');
//valor minimo
define('VALORMIN', '30');

session_start();

//pega dados config
$sqlMeta = mysql_query("SELECT * FROM config", $conecta);
$rowMeta = mysql_fetch_array($sqlMeta);
$tit = substr($rowMeta['keywords'], 0, -50);

if ($rowMeta['analytics'] != '') $analytics = $rowMeta['analytics'];
if ($rowMeta['keywords'] != '') $keywords = $rowMeta['keywords'];
if ($rowMeta['description'] != '') $description = $rowMeta['description'];
if ($rowMeta['mapa'] != '') $mapa = $rowMeta['mapa'];

if ($rowMeta['whatsapp'] != '') $whatsapp = $rowMeta['whatsapp'];
$whatsapp = str_replace('(', '', $whatsapp);
$whatsapp = str_replace(')', '', $whatsapp);
$whatsapp = str_replace('-', '', $whatsapp);
$whatsapp = str_replace('.', '', $whatsapp);
///////////

if ($_SERVER['HTTP_HOST'] == URLPADRAO) {
	header("Location:" . BASEURL);
}

//detecta ie8 ou inferior
if (preg_match('/(?i)msie [7-8]/', $_SERVER['HTTP_USER_AGENT'])) {
	$ie8 = 'S';
}
/////

//se for ie6, direciona
if (preg_match('/(?i)msie [5-6]/', $_SERVER['HTTP_USER_AGENT'])) {
	header("Location:/atualizacao/");
}
////


//imagem do email
if (file_exists(SITEPATH . "upload/imagens/newsletter/topo_mailing.jpg"))
	$img_topo = "<img src=http://www." . URLPADRAO . "/ad/upload/imagens/newsletter/topo_mailing.jpg /><br><br>";

//trata dados enviados
//$nome = secure(mb_strtoupper($_POST['nome'], 'UTF-8'));
$nome = secure($_POST['nome']);
$email = secure($_POST['email']);
$assunto = secure($_POST['assunto']);
$mensagem = secure($_POST['mensagem']);
$cpf = secure($_POST['cpf']);
$rg = secure($_POST['rg']);
$email2 = secure($_POST['email2']);
$conheceu = secure($_POST['conheceu']);
$endereco = secure($_POST['endereco']);
$numero = secure($_POST['numero']);
$bairro = secure($_POST['bairro']);
$complemento = secure($_POST['complemento']);
$cidade = secure($_POST['cidade']);
$estado = secure($_POST['estado']);
$cepCli = secure($_POST['cepCli']);
$telefone = secure($_POST['telefone']);
$celular = secure($_POST['celular']);
$contato = secure($_POST['contato']);

$marca = secure($_POST['marca']);
$modelo = secure($_POST['modelo']);
$fabricacao = secure($_POST['fabricacao']);
$motorizacao = secure($_POST['motorizacao']);

$pedido = secure($_POST['pedido']);
$data_pagamento = secure($_POST['data_pagamento']);
$hora_pagamento = secure($_POST['hora_pagamento']);
$valor = secure($_POST['valor']);
$k = secure($_POST['k']);

$ativa_news = ($_POST['ativa_news']) ? $_POST['ativa_news'] : 'N';
$senha = secure($_POST['senha']);

$verifica = secure($_POST['verifica']);

$erro = 0;

$chave = md5($email);
$url = "www." . URLPADRAO;


//formulários
switch ($_POST['tipoform']) {

		///////////////formulário de cadastro
	case 'cadastro':

		///validar cpf
		$validate_cpf = new validar;
		if ($validate_cpf->cpf($cpf))
			$doc_cpf = 'valido';
		else  $doc_cpf = 'invalido';

		///validar cnpj
		$validate_cnpj = new validar;
		if ($validate_cnpj->cnpj($cpf))
			$doc_cnpj = 'valido';
		else  $doc_cnpj = 'invalido';

		//checa se o email ja existe
		$sqlV = mysql_query("SELECT codigo FROM clientes WHERE email='$email' ");
		$totV = mysql_num_rows($sqlV);


		if (empty($_POST['nome']) || empty($_POST['cpf']) || empty($_POST['rg']) || empty($_POST['email']) || empty($_POST['endereco']) || empty($_POST['numero']) || empty($_POST['bairro']) || empty($_POST['cidade']) || empty($_POST['estado']) || empty($_POST['cepCli']) || empty($_POST['telefone'])) { //preenchimento obrigatório

			$erro = 1;
			$direciona = "/cadastro/empty";
		} else if ($totV > 0) { //email existente no sistema

			$erro = 1;
			$direciona = "/cadastro/email";
		} else if ($doc_cpf == 'invalido' && $doc_cnpj == 'invalido') { //documento inválido

			$erro = 1;
			$direciona = "/cadastro/docinvalido";
		} else if ($verifica != '3') { //verificação invalida

			$erro = 1;
			$direciona = "/cadastro/verifica";
		} else if (spamcheck($email)) { //email liberado

			$erro = 0;
			$direciona = "/cadastro/true";
		} else { //email invalido

			$erro = 1;
			$direciona = "/cadastro/false";
		}

		if ($erro == 1) { //algum erro ocorreu

			//sessoes do cadastro
			$_SESSION['cadastro']['nome'] = $nome;
			$_SESSION['cadastro']['cpf'] = $cpf;
			$_SESSION['cadastro']['rg'] = $rg;
			$_SESSION['cadastro']['contato'] = $contato;
			$_SESSION['cadastro']['email'] = $email;
			$_SESSION['cadastro']['endereco'] = $endereco;
			$_SESSION['cadastro']['numero'] = $numero;
			$_SESSION['cadastro']['bairro'] = $bairro;
			$_SESSION['cadastro']['complemento'] = $complemento;
			$_SESSION['cadastro']['cidade'] = $cidade;
			$_SESSION['cadastro']['estado'] = $estado;
			$_SESSION['cadastro']['cepCli'] = $cepCli;
			$_SESSION['cadastro']['telefone'] = $telefone;
			$_SESSION['cadastro']['celular'] = $celular;
		} else { //nenhum erro, processa cadastro

			//$senha = createRandomPassword();
			$senha = createRandomPassword();
			$senhaSQL = base64_encode($senha);

			$chave = md5($email);

			$ativa_news = ($ativa_news) ? $ativa_news : 'N';

			//insere dados de cliente
			mysql_query("INSERT INTO clientes SET nome='" . mb_strtoupper($nome) . "', cpf='$cpf', rg='$rg', email='" . mb_strtoupper($email) . "', cidade='" . mb_strtoupper($cidade) . "', estado='" . mb_strtoupper($estado) . "', ativa='S', telefone='$telefone', endereco='" . mb_strtoupper($endereco) . "', bairro='" . mb_strtoupper($bairro) . "', complemento='" . mb_strtoupper($complemento) . "', numero_casa='$numero', cep='$cepCli', telefone_cel='$celular', senha='$senhaSQL', chave='$chave', ativa_news='$ativa_news', dat_cad='$data', hor_cad='$hora' ");

			$codigoCliente = mysql_insert_id();

			//se for pra cesta grava sessões do cliente
			if ($_POST['pagina'] == "carrinho") {

				$_SESSION['usuario']['ss_codigo'] = $codigoCliente;
				$_SESSION['usuario']['ss_email'] = $email;
				$_SESSION['usuario']['ss_nome'] = $nome;
				$_SESSION['usuario']['ss_senha'] = $senhaSQL;
			}

			if (!empty($rowMeta['telefone'])) $mostra_telefone = "Telefone: " . $rowMeta['telefone'] . "<br>";
			if (!empty($rowMeta['email_contato'])) $mostra_email = "Email: " . $rowMeta['email_contato'] . "<br>";

			//dispara email para o cliente
			$to = $email;

			$subject = "Bem-vindo(a) à " . NOMEDOSITE . " - confirmação de cadastro";

			$message = "<html>
				" . $img_topo . "
				<font face='arial' size='2'>
				Olá $nome!<br><br>
				Seja bem-vindo(a) à " . NOMEDOSITE . ". Essa é uma mensagem automática de confirmação, que contém os seus dados de acesso à loja, para futuras compras.<br><br>
				-------------------------------------------------------------------------------------<br><br>
				<b>Data/hora do cadastro:</b> $dataBra - $hora <br><br>
				<b>Seu endereço ip:</b> $ip <br><br>
				-------------------------------------------------------------------------------------<br><br>
				Utilize os dados abaixo para autenticar-se na loja:<br><br>
				<b>E-mail:</b> $email<br><br>
				<b>Senha:</b> $senha<br><br>
				-------------------------------------------------------------------------------------<br><br>
				Caso tenha alguma dúvida, entre em contato com o nosso atendimento. <br><br>
				<strong>" . NOMEDOSITE . "<br></strong>
				www." . URLPADRAO . " <br><br>
				$mostra_telefone
				$mostra_email
				</font>
				</html>";

			enviarcomsmtp($rowMeta['smtp_servidor'], $rowMeta['smtp_porta'], $rowMeta['smtp_senha'], $rowMeta['smtp_usuario'], $rowMeta['smtp_origem'], $rowMeta['smtp_origem'], NOMEDOSITE, $to, $nome, $subject, $message);

			//limpa sessao
			unset($_SESSION['cadastro']);
		} //FIM //nenhum erro, processa cadastro

		if ($erro == '0' && $_POST['pagina'] == "carrinho") $direciona = "/carrinho.php?etapa=pedido#confirmacao";
		else if ($erro == '1' && $_POST['pagina'] == "carrinho") $direciona = $direciona . "/carrinho";
		else $direciona = $direciona;

		//direciona
		header('Location:' . $direciona . '');
		break;
		/////////////FIM //formulário de cadastro


		///////////////formulário meu cadastro
	case 'atualizadados':


		if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['endereco']) || empty($_POST['numero']) || empty($_POST['cidade']) || empty($_POST['estado']) || empty($_POST['cepCli']) || empty($_POST['telefone']) || empty($_POST['senha'])) { //preenchimento obrigatório

			//direciona
			header('Location:/meucadastro/empty');
			break;
		} else {

			$senha = base64_encode($senha);

			//atualiza endereço do cliente
			mysql_query("UPDATE clientes SET nome='" . mb_strtoupper($nome) . "', email='" . mb_strtoupper($email) . "', endereco='" . mb_strtoupper($endereco) . "', bairro='" . mb_strtoupper($bairro) . "', complemento='" . mb_strtoupper($complemento) . "', numero_casa='$numero', cidade='" . mb_strtoupper($cidade) . "', estado='" . mb_strtoupper($estado) . "', cep='$cepCli', telefone='$telefone', telefone_cel='$celular', senha='$senha', ativa_news='$ativa_news' WHERE codigo='" . $_SESSION['usuario']['ss_codigo'] . "' ");

			//direciona
			header('Location:/meucadastro/true');
			break;
		}
		/////////////FIM //formulário meu cadastro


		///////////////newsletter
	case 'newsletter':

		//checa se o email ja existe
		$sqlV = mysql_query("SELECT codigo FROM news_contatos WHERE email='$email' ");
		$totV = mysql_num_rows($sqlV);

		if (empty($_POST['nome']) || empty($_POST['email'])) { //preenchimento obrigatório

			$erro = '1';
			$direciona = "/newsletter/empty";
		} else if ($totV > 0) { //email existente no sistema

			$erro = '1';
			$direciona = "/newsletter/email";
		} else if (spamcheck($email)) { //email liberado

			$erro = '0';
			$direciona = "/newsletter/true";
		} else { //email invalido

			$erro = '1';
			$direciona = "/newsletter/false";
		}

		if ($erro == '0') { //sem erro grava dados

			//atualiza endereço do cliente
			mysql_query("INSERT INTO news_contatos SET nome='$nome', email='$email', chave='$chave', dat_cad='$data', hor_cad='$hora' ");
		} //FIM //sem erro grava dados

		//direciona
		header('Location:' . $direciona . '');
		break;
		/////////////FIM //newsletter


		/////////////formulário de contato
	case 'confirmacao':

		if (empty($_POST['data_pagamento']) || empty($_POST['hora_pagamento']) || empty($_POST['valor'])) { //preenchimento obrigatório

			$erro = 1;
			$msg = "empty";
		} else if ($verifica != '3') { //verificação invalida

			$erro = 1;
			$msg = "verifica";
		} else {

			$erro = 0;
			$msg = "true";
		}


		$subject = "Confirmação de pagamento - Pedido No. $pedido";

		$message = '
		<html>
			' . $img_topo . '
			Comunicamos abaixo a mensagem de confirmação de pagamento que acabamos de receber.<br><br>

			<div style="padding:5px;border:solid 1px #DDD;background-color:#F7F7F7; margin-bottom:10px;">
			<b>Data/hora do recebimento:</b> ' . $dataBra . ' - ' . $hora . ' <br><br>
			<b>Endereço ip do remetente:</b> ' . $ip . ' <br>
			</div>

			<div style="padding:5px; border:solid 1px #DDD;background-color:#F2F2F2; margin-bottom:10px;">

			<b>Número do pedido:</b> ' . $pedido . ' <br><br>
			<b>Data do pagamento:</b> ' . $data_pagamento . ' <br><br>
			<b>Hora do pagamento:</b> ' . $hora_pagamento . ' <br><br>
			<b>Valor depositado/transferido:</b> R$ ' . $valor . ' <br><br>
			<b>Detalhes do pagamento:</b> ' . $mensagem . ' <br>
			</div>

			<br>
			' . NOMEDOSITE . '<br>
			www.' . URLPADRAO . '<br>
		</html>
		';

		if ($erro == 0) {

			$email_alerta = $rowMeta['email_alerta'];
			//$email_alerta = "andre@tream.com.br";
			$destinatarios = explode(';', $email_alerta);
			foreach ($destinatarios as $to) { //foreach emails alerta
				$to = trim($to);

				//dispara email para admin
				enviarcomsmtp($rowMeta['smtp_servidor'], $rowMeta['smtp_porta'], $rowMeta['smtp_senha'], $rowMeta['smtp_usuario'], $rowMeta['smtp_origem'], $rowMeta['smtp_origem'], NOMEDOSITE, $to, NOMEDOSITE, $subject, $message);
			} //FIM //foreach emails alerta


			unset($_SESSION['confirmacao']); //limpa sessão

		} else {

			$_SESSION['confirmacao']['pedido'] = $pedido;
			$_SESSION['confirmacao']['data_pagamento'] = $data_pagamento;
			$_SESSION['confirmacao']['hora_pagamento'] = $hora_pagamento;
			$_SESSION['confirmacao']['valor'] = $valor;
			$_SESSION['confirmacao']['mensagem'] = $mensagem;
		}

		header('Location:/confirmacaodepagamento/' . $k . '/' . $msg);
		break;
		/////////////FIM //formulário de contato


		/////////////formulário de confirmação de pagamento
	case 'contato':

		if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['mensagem']) || empty($_POST['assunto'])) { //preenchimento obrigatório

			$erro = 1;
			$msg = "empty";
		} else if ($verifica != '3') { //verificação invalida

			$erro = 1;
			$msg = "verifica";
		} else if (spamcheck($email)) {

			$erro = 0;
			$msg = "true";
		} else {

			$erro = 1;
			$msg = "false";
		}


		$subject = $assunto;

		$message = '
		<html>
			' . $img_topo . '
			Comunicamos abaixo a mensagem de contato que acabamos de receber.<br><br>

			<div style="padding:5px;border:solid 1px #DDD;background-color:#F7F7F7; margin-bottom:10px;">
			<b>Data/hora do recebimento:</b> ' . $dataBra . ' - ' . $hora . ' <br><br>
			<b>Endereço ip do remetente:</b> ' . $ip . ' <br>
			</div>

			<div style="padding:5px; border:solid 1px #DDD;background-color:#F2F2F2; margin-bottom:10px;">

			<b>Nome:</b> ' . $nome . ' <br><br>
			<b>Email:</b> ' . $email . ' <br><br>
			<b>Telefone:</b> ' . $telefone . ' <br><br>
			<b>Celular:</b> ' . $celular . ' <br><br>
			<b>Assunto:</b> ' . $assunto . ' <br><br>
			<b>Mensagem:</b> ' . $mensagem . ' <br>
			</div>

			<br>
			' . NOMEDOSITE . '<br>
			www.' . URLPADRAO . '<br>
		</html>
		';

		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=utf-8\n";
		$headers .= "From: " . NOMEDOSITE . " <" . EMAILPADRAO . ">\n\n";

		if ($erro == 0) {

			$email_alerta = $rowMeta['email_alerta'];
			$destinatarios = explode(';', $email_alerta);
			foreach ($destinatarios as $to) { //foreach emails alerta
				$to = trim($to);

				//dispara email para admin
				enviarcomsmtp($rowMeta['smtp_servidor'], $rowMeta['smtp_porta'], $rowMeta['smtp_senha'], $rowMeta['smtp_usuario'], $rowMeta['smtp_origem'], $rowMeta['smtp_origem'], NOMEDOSITE, $to, NOMEDOSITE, $subject, $message);
			} //FIM //foreach emails alerta


			unset($_SESSION['contato']); //limpa sessão

		} else {

			$_SESSION['contato']['nome'] = $nome;
			$_SESSION['contato']['email'] = $email;
			$_SESSION['contato']['telefone'] = $telefone;
			$_SESSION['contato']['celular'] = $celular;
			$_SESSION['contato']['assunto'] = $assunto;
			$_SESSION['contato']['mensagem'] = $mensagem;
		}

		header('Location:/atendimento/' . $msg);
		break;
		/////////////FIM //formulário de confirmação de pagamento


		/////////////formulário de encomenda
	case 'encomenda':

		if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['mensagem']) || empty($_POST['assunto'])) { //preenchimento obrigatório

			$erro = 1;
			$msg = "empty";
		} else if ($verifica != '3') { //verificação invalida

			$erro = 1;
			$msg = "verifica";
		} else if (spamcheck($email)) {

			$erro = 0;
			$msg = "true";
		} else {

			$erro = 1;
			$msg = "false";
		}


		$subject = $assunto;

		$message = '
		<html>
			' . $img_topo . '
			Comunicamos abaixo a mensagem de produtos por encomenda que acabamos de receber.<br><br>

			<div style="padding:5px;border:solid 1px #DDD;background-color:#F7F7F7; margin-bottom:10px;">
			<b>Data/hora do recebimento:</b> ' . $dataBra . ' - ' . $hora . ' <br><br>
			<b>Endereço ip do remetente:</b> ' . $ip . ' <br>
			</div>

			<div style="padding:5px; border:solid 1px #DDD;background-color:#F2F2F2; margin-bottom:10px;">

			<b>Nome:</b> ' . $nome . ' <br><br>
			<b>Email:</b> ' . $email . ' <br><br>
			<b>Telefone:</b> ' . $telefone . ' <br><br>
			<b>Celular:</b> ' . $celular . ' <br><br>
			<b>Produto:</b> ' . $assunto . ' <br><br>
			<b>Marca:</b> ' . $marca . ' <br><br>
			<b>Modelo:</b> ' . $modelo . ' <br><br>
			<b>Ano de fabricação:</b> ' . $fabricacao . ' <br><br>
			<b>Motorização:</b> ' . $motorizacao . ' <br><br>
			<b>Mensagem:</b> ' . $mensagem . ' <br>
			</div>

			<br>
			' . NOMEDOSITE . '<br>
			www.' . URLPADRAO . '<br>
		</html>
		';

		if ($erro == 0) {

			$email_alerta = $rowMeta['email_alerta'];
			$destinatarios = explode(';', $email_alerta);
			foreach ($destinatarios as $to) { //foreach emails alerta
				$to = trim($to);

				//dispara email para admin
				enviarcomsmtp($rowMeta['smtp_servidor'], $rowMeta['smtp_porta'], $rowMeta['smtp_senha'], $rowMeta['smtp_usuario'], $rowMeta['smtp_origem'], $rowMeta['smtp_origem'], NOMEDOSITE, $to, NOMEDOSITE, $subject, $message);
			} //FIM //foreach emails alerta


			unset($_SESSION['encomenda']); //limpa sessão

		} else {

			$_SESSION['encomenda']['nome'] = $nome;
			$_SESSION['encomenda']['email'] = $email;
			$_SESSION['encomenda']['telefone'] = $telefone;
			$_SESSION['encomenda']['celular'] = $celular;
			$_SESSION['encomenda']['assunto'] = $assunto;
			$_SESSION['encomenda']['mensagem'] = $mensagem;
			$_SESSION['encomenda']['marca'] = $marca;
			$_SESSION['encomenda']['modelo'] = $modelo;
			$_SESSION['encomenda']['fabricacao'] = $fabricacao;
			$_SESSION['encomenda']['motorizacao'] = $motorizacao;
		}

		header('Location:/naoencontrei/' . $msg);
		break;
		/////////////FIM //formulário de encomenda


}
//FIM //formulários