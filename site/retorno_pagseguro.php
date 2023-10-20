<?php
//arquivo de retorno do pagseguro - para liberar créditos

/*ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL ^ E_NOTICE);*/

$retorno_host = 'localhost'; // Local da base de dados MySql
$retorno_database = 'frazatto_site'; // Nome da base de dados MySql
$retorno_usuario = 'frazatto_site'; // Usuario com acesso a base de dados MySql
$retorno_senha = 'omz89mcwt3rb';  // Senha de acesso a base de dados MySql

//funções
$data = date('Y-m-d');
$hora = (strftime("%H:%M:%S"));

//decimal
function decimal($valor)
{
$valor = number_format( ($valor),2,chr(44), ".");	
return $valor;
}
//

$lnk = mysql_connect($retorno_host, $retorno_usuario, $retorno_senha) or die ('Nao foi possível conectar ao MySql: ' . mysql_error());
mysql_select_db($retorno_database, $lnk) or die ('Nao foi possível ao banco de dados selecionado no MySql: ' . mysql_error());	

//consulta config
$sqlDados = mysql_query("SELECT * FROM config");
$rowMeta = mysql_fetch_array($sqlDados);
//

//imagem do email
if(file_exists("ad/upload/imagens/newsletter/topo_mailing.jpg"))
$img_topo = "<img src=https://www.".$rowMeta['dominio']."/ad/upload/imagens/newsletter/topo_mailing.jpg /><br><br>";
else 
$img_topo = "";

if(!empty($rowMeta['telefone'])) $mostra_telefone = "Telefone: ".$rowMeta['telefone']."<br>"; else $mostra_telefone = "";
if(!empty($rowMeta['email_contato'])) $mostra_email = "Email: ".$rowMeta['email_contato']."<br>"; else $mostra_email = "";

##############################################################
#                         CONFIGURAÇÕES
##############################################################

$retorno_site = 'https://www.'.$rowMeta['dominio'].'';  // Site para onde o usuário vai ser redirecionado
$token_pagseguro = $rowMeta['pagseguro_token']; // Token gerado pelo PagSeguro


###############################################################
#              NÃO ALTERE DESTA LINHA PARA BAIXO
################################################################


// Validando dados no PagSeguro

$PagSeguro = 'Comando=validar';
$PagSeguro .= '&Token=' . $token_pagseguro; 
$Cabecalho = "Retorno PagSeguro";

foreach ($_POST as $key => $value)
{
 $value = urlencode(stripslashes($value));
 $PagSeguro .= "&$key=$value";
}

if (function_exists('curl_exec'))
{
 $curl = true;
}
elseif ( (PHP_VERSION >= 4.3) && ($fp = @fsockopen ('ssl://pagseguro.uol.com.br', 443, $errno, $errstr, 30)) )
{
 $fsocket = true;
}
elseif ($fp = @fsockopen('pagseguro.uol.com.br', 80, $errno, $errstr, 30))
{
 $fsocket = true;
}

if ($curl == true)
{
 $ch = curl_init();

 curl_setopt($ch, CURLOPT_URL, 'https://pagseguro.uol.com.br/Security/NPI/Default.aspx');
 curl_setopt($ch, CURLOPT_POST, true);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $PagSeguro);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_HEADER, false);
 curl_setopt($ch, CURLOPT_TIMEOUT, 30);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

  curl_setopt($ch, CURLOPT_URL, 'https://pagseguro.uol.com.br/Security/NPI/Default.aspx');
  $resp = curl_exec($ch);

 curl_close($ch);
 $confirma = (strcmp ($resp, "VERIFICADO") == 0);
}
elseif ($fsocket == true)
{
 $Cabecalho  = "POST /Security/NPI/Default.aspx HTTP/1.0\r\n";
 $Cabecalho .= "Content-Type: application/x-www-form-urlencoded\r\n";
 $Cabecalho .= "Content-Length: " . strlen($PagSeguro) . "\r\n\r\n";

 if ($fp || $errno>0)
 {
    fputs ($fp, $Cabecalho . $PagSeguro);
    $confirma = false;
    $resp = '';
    while (!feof($fp))
    {
       $res = @fgets ($fp, 1024);
       $resp .= $res;
       if (strcmp ($res, "VERIFICADO") == 0)
       {
          $confirma=true;
          break;
       }
    }
    fclose ($fp);
 }
 else
 {
    echo "$errstr ($errno)<br />\n";
 }
}

/////
//confirma
if ($confirma) {
	
	 // Recebendo Dados
	 $TransacaoID = $_POST['TransacaoID'];
	 $VendedorEmail  = $_POST['VendedorEmail'];
	 $Referencia = $_POST['Referencia'];
	 $TipoFrete = $_POST['TipoFrete'];
	 $ValorFrete = $_POST['ValorFrete'];
	 $Extras = $_POST['Extras'];
	 $Anotacao = $_POST['Anotacao'];
	 $TipoPagamento = $_POST['TipoPagamento'];
	 $StatusTransacao = $_POST['StatusTransacao'];
	 $CliNome = $_POST['CliNome'];
	 $CliEmail = $_POST['CliEmail'];
	 $CliEndereco = $_POST['CliEndereco'];
	 $CliNumero = $_POST['CliNumero'];
	 $CliComplemento = $_POST['CliComplemento'];
	 $CliBairro = $_POST['CliBairro'];
	 $CliCidade = $_POST['CliCidade'];
	 $CliEstado = $_POST['CliEstado'];
	 $CliCEP = $_POST['CliCEP'];
	 $CliTelefone = $_POST['CliTelefone'];
	 $NumItens = $_POST['NumItens'];
	 $ProdValor_1 = $_POST['ProdValor_1'];
	 $ProdID_1 = $_POST['ProdID_1'];
	
	  //verifica se já tem a transação no banco de dados - pega pelo numero da transação, passada pelo PagSeguro
	  $sqlp = "select TransacaoID from transacoes_pagseguro where TransacaoID='$TransacaoID' ";
	  $resp = mysql_query($sqlp);
	  if(mysql_num_rows($resp)>0) 
	  {
		  
		  //se já existe, atualiza
		  mysql_query("UPDATE transacoes_pagseguro SET TipoPagamento='$TipoPagamento', StatusTransacao='$StatusTransacao' WHERE TransacaoID='$TransacaoID' ");
		  
	  } else {
   		  
		  //cria transação na tabela
		  mysql_query("INSERT INTO transacoes_pagseguro SET
		  TransacaoID='$TransacaoID',	
		  VendedorEmail='$VendedorEmail',	
		  Referencia='$Referencia',	
		  TipoFrete='$TipoFrete',	
		  ValorFrete='$ValorFrete',	
		  Extras='$Extras',	
		  Anotacao='$Anotacao',	
		  TipoPagamento='$TipoPagamento',	
		  StatusTransacao='$StatusTransacao',	
		  CliNome='$CliNome',	
		  CliEmail='$CliEmail',	
		  CliEndereco='$CliEndereco',	
		  CliNumero='$CliNumero',	
		  CliComplemento='$CliComplemento',	
		  CliBairro='$CliBairro',	
		  CliCidade='$CliCidade',	
		  CliEstado='$CliEstado',	
		  CliCEP='$CliCEP',	
		  CliTelefone='$CliTelefone',	
		  NumItens='$NumItens',
		  ProdValor_1='$$ProdValor_1',
		  ProdID_1='$ProdID_1',
		  Liberacao='N',
		  Data='$data',
		  Hora='$hora'");

	  }
	  
	  
	  ///se a transação for APROVADA, libera os créditos
	  if($StatusTransacao=="Aprovado") { 
	  
	  		//Verifica na tabela se já tem a transação e está aprovada
			$sqlp = mysql_query("SELECT TransacaoID FROM transacoes_pagseguro WHERE TransacaoID='$TransacaoID' AND Liberacao='N' ");
			if(mysql_num_rows($sqlp)>0) 
			{
				  
				  //consulta dados do pedido
				  $ped = mysql_fetch_array(mysql_query("SELECT * FROM pedidos WHERE codigo='$Referencia' LIMIT 1"));
				  
				  //atualiza pedido
				  mysql_query("UPDATE pedidos SET situacao='2' WHERE codigo='$Referencia' ") or die ("Erro ao atualizar pedido!");
	  
				  //para o cliente
				  $assunto = "Pagamento confirmado";
					  
				  $mensagem = '<html>
				  <font face="arial" size="2">
				  '.$img_topo.'
				  <h3>Pagamento confirmado - Pedido No. '.$ped['codigo'].' realizado na loja '.utf8_encode($rowMeta['nomesite']).'</h3>
				  <h4>Olá '.$ped['nome'].',</h4>
				  Obrigado pelo pagamento.<br>
				  Esta é uma mensagem automática informando que o pagamento do seu pedido foi confirmado. 
				  <br />
				  Valor: <strong>R$ '.decimal($ped['total']).'</strong>
				  <br /><br />
				  Caso tenha alguma dúvida entre em contato com o nosso atendimento. <br /><br />
				  Atenciosamente,<br><br>
				  <b>'.utf8_encode($rowMeta['nomesite']).'</b><br>
				  '.$rowMeta['email_contato'].'<br><br>
				  '.$mostra_telefone.'
				  '.$mostra_email.'
				  </font>
				  </html>';
						  
				  $headers  = "MIME-Version: 1.0\n";
				  $headers .= "Content-type: text/html; charset=utf-8\n";
				  $headers .= "From: ".$rowMeta['nomesite']." <".$rowMeta['email_contato'].">\r\n";
				  
				  mail($ped['email'], $assunto, $mensagem, $headers);  
				  
				  //faz o update da tabela transações, com liberação=S
				  mysql_query("UPDATE transacoes_pagseguro SET Liberacao='S' WHERE TransacaoID='$TransacaoID' ");
				  
							  
		}//FIM //Verifica na tabela se já tem a transação e está aprovada

	  }//FIM ///se a transação for APROVADA, libera os créditos

	
}//FIM //confirma
////

header("Location: $retorno_site"); exit();
