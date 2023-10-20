<?php
///////////// NOTIFICAÇÃO CIELO ///////////////
///////////////////////////////////////////////
require('admin/config/_conecta.php');
require('admin/config/_funcoes.php');
require('admin/config/_globais.php');

//exibe erros
/*ini_set('display_errors', 1);
error_reporting(E_ALL);*/

//posts da cielo
if(!empty($_POST['checkout_cielo_order_number'])) $id_transacao = $_POST['checkout_cielo_order_number']; //Identificador único gerado pelo CHECKOUT CIELO
if(!empty($_POST['created_date'])) $dataHora = $_POST['created_date']; //Data da criação do pedido (dd/MM/yyyy HH:mm:ss)
if(!empty($_POST['amount'])) $valor_fatura = $_POST['amount']; //Preço unitário do produto, em centavos
if(!empty($_POST['order_number'])) $numero_fatura = $_POST['order_number']; //Número do pedido enviado pela loja
if(!empty($_POST['customer_name'])) $nome_cliente = $_POST['customer_name']; //Nome do consumidor
if(!empty($_POST['customer_identity'])) $cpf_cliente = $_POST['customer_identity']; //Identificação do consumidor (CPF ou CNPJ)
if(!empty($_POST['customer_email'])) $email_cliente = $_POST['customer_email']; //E-mail do consumidor
if(!empty($_POST['customer_phone'])) $telefone_cliente = $_POST['customer_phone']; //Telefone do consumidor
if(!empty($_POST['payment_method_type'])) $meio_pagamento = $_POST['payment_method_type']; //Cód. do tipo de meio de pagamento ## 1 = Cartão de Crédito; 2 = Boleto Bancário; 3 = Débito Online; 4 = Cartão de Débito;
if(!empty($_POST['payment_method_brand'])) $bandeira = $_POST['payment_method_brand']; //Bandeira (somente para transações com meio de pagamento cartão de crédito) ## 1 = Visa; 2 = Mastercad; 3 = AmericanExpress; 4 = Diners; 5 = Elo; 6 = Aura; 7 = JCB;
if(!empty($_POST['payment_maskedcredicard'])) $cartao_cliente = $_POST['payment_maskedcredicard']; else $cartao_cliente = NULL; //Cartão Mascarado (Somente para transações com meio de pagamento cartão de crédito)
if(!empty($_POST['payment_installments'])) $numero_parcelas = $_POST['payment_installments']; //Número de parcelas
if(!empty($_POST['payment_antifrauderesult'])) $status_antifraude = $_POST['payment_antifrauderesult']; else $status_antifraude = NULL; //Status das transações de cartão de Crédito no Antifraude ## 1 = Baixo Risco; 2 = Alto Risco; 3 = Não Finalizado; 4 = Risco Moderado;
if(!empty($_POST['payment_status'])) $status_transacao = $_POST['payment_status']; //Status da transação ## 1 = Pendente (Para todos os meios de pagamento); 2 = Pago (Para todos os meios de pagamento); 3 = Negado (Somente para Cartão Crédito); 5 = Cancelado (Para cartões de crédito); 6 = Não Finalizado (Todos os meios de pagamento); 7 = Autorizado (somente para Cartão de Crédito);

if(!empty($id_transacao) && !empty($numero_fatura)) { //somente quando retornar id da transação e o numero da fatura

	//grava transações
	$sqlVerifica = mysql_query("SELECT transacao FROM transacoes_cielo WHERE transacao='$id_transacao'");
	if(mysql_num_rows($sqlVerifica)==0) {
		
		//insere
		mysql_query("INSERT INTO transacoes_cielo SET transacao='$id_transacao', bandeira='$bandeira', status='$status_transacao', order_number='$numero_fatura', valor='$valor_fatura', antifraude='$status_antifraude', parcelas='$numero_parcelas', finalcartao='$cartao_cliente', tipo='$meio_pagamento', data='$data', hora='$hora' ");
		
	} else {
		
		//se já existe, atualiza
		mysql_query("UPDATE transacoes_cielo SET status='$status_transacao' WHERE transacao='$id_transacao' ");
		
	}
	
	
	///// se aprovado
	if($status_transacao==2 || $status_transacao==7) {//se status de aprovação
		
		$sqlPed = mysql_query("SELECT * FROM pedidos WHERE codigo='$numero_fatura' AND situacao='1' LIMIT 1");
		while($rowPed = mysql_fetch_array($sqlPed)) {//while pedido
		
			//...e manda email pro admin
			$sqlMeta = mysql_query("SELECT * FROM config", $conecta);
			$rowMeta = mysql_fetch_array($sqlMeta);	
			
			if(file_exists("upload/imagens/newsletter/topo_mailing.jpg"))
			$img_topo = "<img src=http://www.".URLPADRAO."/ad/upload/imagens/newsletter/topo_mailing.jpg /><br><br>";
			
			if(!empty($rowMeta['telefone'])) $mostra_telefone = "Telefone: ".$rowMeta['telefone']."<br>";
			if(!empty($rowMeta['email_contato'])) $mostra_email = "Email: ".$rowMeta['email_contato']."<br>";
			
			//mensagem
			$subject = "Pagamento confirmado - Pedido $numero_fatura";
		
			$message = "<html>
			<font face='arial' size='2'>
			$img_topo<br><br>
			<h2>Olá ".$rowPed['nome'].",</h2><br><br> 
			Este é um alerta automático informando que o seu pagamento foi confirmado.<br><br>
			-------------------------------------------------------------------------------------<br>
			<h2><b>No. do pedido:</b> $numero_fatura </h2>
			-------------------------------------------------------------------------------------<br><br>
			
			Seu pedido está em processamento agora e assim que for postado, você receberá uma mensagem em seu email. <br><br>
			
			Caso tenha alguma dúvida ref. ao seu pedido, entre em contato com o nosso atendimento. Ficaremos felizes em atendê-lo(a).<br><br>
			Até a próxima compra! <br><br>
			<strong>".NOMEDOSITE."<br></strong>
			www.".URLPADRAO."<br><br>
			$mostra_telefone
			$mostra_email
			</font>
			</html>
			";
			
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=utf-8\n";
			$headers .= "From: ".NOMEDOSITE." <".EMAILPADRAO.">\r\n";
			
			mail($rowPed['email'], $subject, $message, $headers);  
			mail($rowMeta['emails'], $subject, $message, $headers);  
			//mail("prog@tream.com.br", "COPIA - ".$subject, $message, $headers); 
			
			//faz update no pedido....
			mysql_query("UPDATE pedidos SET situacao='2' WHERE codigo='$numero_fatura'");
			
		}//FIM //while pedido
		
	}//FIM //se status de aprovação

	echo '<status>OK</status>';

}//FIM //somente quando retornar id da transação e o numero da fatura