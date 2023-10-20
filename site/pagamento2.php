<?php
require('config.php');
///////////////////////

$chave = secure($_GET['k']);
//pega dados do pedido
$sqlPed = mysql_query("SELECT * FROM pedidos WHERE chave='$chave' LIMIT 1");
$rowPed = mysql_fetch_array($sqlPed);

//sessão com codigo da cobrança
$_SESSION['session_cobranca']['codigo'] = $rowPed['codigo'];
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <title><?php echo NOMEDOSITE; ?> | Pagamento</title>

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

    <!--Start of Zopim Live Chat Script-->
    <script type="text/javascript">
        window.$zopim || (function(d, s) {
            var z = $zopim = function(c) {
                    z._.push(c)
                },
                $ = z.s =
                d.createElement(s),
                e = d.getElementsByTagName(s)[0];
            z.set = function(o) {
                z.set.
                _.push(o)
            };
            z._ = [];
            z.set._ = [];
            $.async = !0;
            $.setAttribute('charset', 'utf-8');
            $.src = '//v2.zopim.com/?2PwFsO6sK8b862RfvVIpvWG0SBIaoufc';
            z.t = +new Date;
            $.
            type = 'text/javascript';
            e.parentNode.insertBefore($, e)
        })(document, 'script');
    </script>
    <!--End of Zopim Live Chat Script-->

    <script src="//assets.pagar.me/checkout/checkout.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="pagarme/js/custom2.js"></script>
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
                <h3>PAGAMENTO</h3>
            </div>

            <center>
                <?php
                if ($_GET['trocar'] != 'pagamento') { //realizar pagamento
                ?>

                    <p style="font-size:18px; margin:20px;"><strong><?php echo $rowPed['nome'] ?></strong>, seu pedido foi realizado com sucesso!<br>
                        Agora, efetue o pagamento para finalizar a sua compra.
                    </p>


                    <h2 style="padding:40px; background:#FFCC00; color:#FFF; width:500px; margin: 30px auto;">Número do seu Pedido:</span> <span style="font-size:30px;color:#FFF;"> <?php echo $rowPed['codigo']; ?></span></h2>

                    <p style="margin-bottom:30px;">A confirmação do seu pedido foi enviada para o seu email. Guarde para consulta posterior, se desejar. </p>

                    <div style="margin-bottom:30px;"><strong>Agora siga as instruções abaixo para realizar o pagamento, com a opção escolhida na etapa anterior:</strong></div>



                    <?php
                    //pagamento por depósito bancário
                    if ($rowPed['pagamento'] == 'Deposito') {

                        if ($rowPed['valor_desconto'] > '0.00') $Total = $rowPed['total'] - $rowPed['valor_desconto'];
                        else $Total = $rowPed['total'];
                    ?>

                        <p style="margin-bottom:30px;">Efetue depósito ou transferência na conta abaixo, no valor exato do pedido.</p>

                        <h1 style="margin-bottom:30px;">Valor do pedido: R$ <?php echo decimal($Total); ?></h1>


                        <div class="neutro" style="width:560px; margin:auto;">
                            <h3 style="margin-bottom:20px;"><?php echo $rowMeta['banco']; ?></h3>
                            <h4>
                                <p>Agência: <?php echo $rowMeta['agencia']; ?></p>
                                <p>Conta Corrente: <?php echo $rowMeta['conta']; ?></p>
                                <p>Favorecido: <?php echo $rowMeta['favorecido']; ?></p>
                                <p>CNPJ: <?php echo $rowMeta['cnpj']; ?></p>
                            </h4>
                        </div>

                        <div class="clear">&nbsp;</div>

                        <p style="margin-bottom:30px;">Após efetuar o depósito ou transferência, faça a confirmação do pagamento através do link abaixo:</p>

                        <input type="button" class="btn btn-large btn-danger" style="width:580px; margin-bottom:30px;" value="Confirmar pagamento" onClick="location.href='confirmacaodepagamento/<?php echo $rowPed['chave']; ?>'" />

                    <?php } ?>



                    <?php
                    //pagamento por boleto bancário 
                    if ($rowPed['pagamento'] == 'Boleto') {

                        if ($rowPed['valor_desconto'] > '0.00') $Total = $rowPed['total'] - $rowPed['valor_desconto'];
                        else $Total = $rowPed['total'];
                    ?>

                        <p>&nbsp;</p>
                        <p>Clique no botão abaixo para imprimir o boleto para pagamento. O boleto pode ser pago em casas lotéricas e em toda a rede bancária até o vencimento.</p>
                        <h1>&nbsp;</h1>
                        <h1>Valor do pedido: R$ <?php echo decimal($Total); ?></h1>
                        <p>&nbsp;</p>

                        <p><img src="img/boleto.jpg" alt="boleto" width="54" height="39" /></p>
                        <p>&nbsp;</p>
                        <a href="boleto/?k=<?php echo $rowPed['chave']; ?>" target="_blank">
                            <div class="btn btn-danger btn-large" style="margin-top:30px; font-size:20px; color:#FFF; width:540px; margin:auto;">Imprimir boleto</div>
                        </a>

                        <p style="margin:20px 0;">Após pago, o boleto pode levar de 1 a 2 dias úteis para ser compensado.</p>

                    <?php } ?>


                    <?php

                    //pagamento por pagseguro - cartões
                    if ($rowPed['pagamento'] == 'PagSeguro') {

                        $cepCliente = str_replace("-", '', $rowPed['cep']);
                        list($dddCli, $foneCli) = explode(")", $rowPed['telefone']);
                        $dddCli = trim(str_replace("(", '', $dddCli));
                        $foneCli = trim($foneCli);

                    ?>

                        <p style="margin:10px 0;">Clique no botão abaixo para efetuar o pagamento através de cartão de crédito, débito ou transferência. <br>
                            O pagamento é protegido e efetuado diretamente no site do PagSeguro.</p>
                        <h1 style="margin:20px 0;">Valor do pedido: R$ <?php echo decimal($rowPed['total']); ?></h1>


                        <img src="img/pagseguro.jpg" alt="cartoes" /><br />

                        <form target="pagseguro" method="post" action="https://pagseguro.uol.com.br/v2/checkout/payment.html">

                            <input type="hidden" name="receiverEmail" value="<?php echo $rowMeta['pagseguro_email']; ?>">
                            <input type="hidden" name="currency" value="BRL">
                            <!-- Itens do pagamento (ao menos um item é obrigatório) -->
                            <input type="hidden" name="itemId1" value="<?php echo $rowPed['codigo']; ?>">
                            <input type="hidden" name="itemDescription1" value="<?php echo "Valor do pedido No. " . $rowPed['codigo'] . " realizado na loja " . NOMEDOSITE . ""; ?>">
                            <input type="hidden" name="itemAmount1" value="<?php echo $rowPed['total']; ?>">
                            <input type="hidden" name="itemQuantity1" value="1">
                            <!-- Código de referência do pagamento no seu sistema (opcional) -->
                            <input type="hidden" name="reference" value="<?php echo $rowPed['codigo']; ?>">
                            <!-- Informações de frete (opcionais) -->
                            <input type="hidden" name="shippingAddressPostalCode" value="<?php echo $cepCliente; ?>">
                            <input type="hidden" name="shippingAddressStreet" value="<?php echo $rowPed['endereco']; ?>">
                            <input type="hidden" name="shippingAddressNumber" value="<?php echo $rowPed['numero']; ?>">
                            <input type="hidden" name="shippingAddressDistrict" value="<?php echo $rowPed['bairro']; ?>">
                            <input type="hidden" name="shippingAddressCity" value="<?php echo $rowPed['cidade']; ?>">
                            <input type="hidden" name="shippingAddressState" value="<?php echo $rowPed['estado']; ?>">
                            <input type="hidden" name="shippingAddressCountry" value="BRA">
                            <!-- Dados do comprador (opcionais) -->
                            <input type="hidden" name="senderName" value="<?php echo $rowPed['nome']; ?>">
                            <input type="hidden" name="senderAreaCode" value="<?php echo $dddCli; ?>">
                            <input type="hidden" name="senderPhone" value="<?php echo $foneCli; ?>">
                            <input type="hidden" name="senderEmail" value="<?php echo $rowPed['email']; ?>">

                            <input type="hidden" name="encoding" value="UTF-8">

                            <input type="submit" name="submit" class="btn btn-large btn-danger" value="PAGUE AGORA" style="margin:30px 0; width:560px; color:#FFF;">

                        </form>

                    <?php } ?>


                    <?php

                    //pagamento por pagseguro - cartões
                    if ($rowPed['pagamento'] == 'bCash') {
                    ?>
                        <p style="margin:10px 0;">Clique no botão abaixo para efetuar o pagamento através cartão de crédito, débito ou boleto. <br>
                            O pagamento é protegido e efetuado diretamente no site do bCash!.</p>
                        <h1 style="margin:20px 0;">Valor do pedido: R$ <?php echo decimal($rowPed['total']); ?></h1>


                        <img src="img/cartoes-bcash.gif" alt="cartoes" /><br />

                        <form name="pagamentodigital" action="https://www.pagamentodigital.com.br/checkout/pay/" method="post" style="border:0;" target="_blank">
                            <input name="email_loja" type="hidden" value="<?php echo $rowMeta['email_bcash']; ?>">
                            <input name="produto_codigo_1" type="hidden" value="<?php echo $_SESSION['usuario']['pedido']; ?>">
                            <input name="produto_descricao_1" type="hidden" value="Pedido <?php echo $_SESSION['usuario']['pedido']; ?>">
                            <input name="produto_qtde_1" type="hidden" value="1">
                            <input name="produto_valor_1" type="hidden" value="<?php print $rowPed['total']; ?>">
                            <input name="tipo_integracao" type="hidden" value="PAD">
                            <input name="frete" type="hidden" value="0">
                            <input name="email" type="hidden" value="<?php echo $rowCli['email']; ?>">
                            <input name="nome" type="hidden" value="<?php echo $rowCli['nome']; ?>">
                            <input type="submit" src="img/botao-pagar.jpg" name="submit" class="btn btn-large btn-danger" value="Pague agora" style="font-size:20px; margin-top:30px; width:580px;">
                        </form>


                    <?php } ?>


                    <?php

                    //pagamento por pagseguro - cartões
                    if ($rowPed['pagamento'] == 'PayPal') {

                        if ($rowPed['frete'] == 'pac') {
                            $fretePS = '1';
                        }
                        if ($rowPed['frete'] == 'sedex') {
                            $fretePS = '2';
                        }

                        //dados cliente
                        $sqlCli = mysql_query("SELECT * FROM clientes WHERE codigo='$rowPed[cliente]'");
                        $rowCli = mysql_fetch_array($sqlCli);

                        $cepCliente = str_replace("-", '', $rowCli['cep']);
                        list($dddCli, $foneCli) = explode(")", $rowCli['telefone']);
                        $dddCli = trim(str_replace("(", '', $dddCli));
                        $foneCli = trim($foneCli);


                    ?>

                        <p style="margin:10px 0;">Clique no botão abaixo para efetuar o pagamento através de sua conta Paypal ou cartão de crédito.<br>
                            O pagamento é protegido e efetuado diretamente no site do Paypal.</p>
                        <h1 style="margin:20px 0;">Valor do pedido: R$ <?php echo decimal($rowPed['total']); ?></h1>

                        <img src="img/paypal.gif" alt="cartoes" /><br />

                        <form method="post" action="../php_paypal/process.php" target="_blank">
                            <input type="hidden" name="firstname" value="<?php echo $rowCli['nome']; ?>" />

                            <input type="hidden" name="email" value="<?php echo $rowCli['email']; ?>" />
                            <input type="hidden" name="item_name" value="Pedido <?php echo $_SESSION['usuario']['pedido']; ?>" />
                            <input type="hidden" name="item_number" value="<?php echo $_SESSION['usuario']['pedido']; ?>" />
                            <input type="hidden" name="amount" value="<?php print $rowPed['total']; ?>" />
                            <input type="hidden" name="quantity" value="1" />
                            <input type="submit" name="submit" class="btn btn-danger btn-large" value="Pague agora" style="margin-top:30px; font-size:20px; color:#FFF; width:580px;">
                        </form>

                    <?php } ?>



                    <?php

                    //if ($rowPed['pagamento'] == 'Cielo') {

                    $_SESSION['cielo']['order_number'] = '';

                    $order_number = $rowPed['codigo'];
                    $_SESSION['cielo']['order_number'] = $order_number;
                    $_SESSION['cielo']['valor'] = $rowPed['total'];
                    ?>


                    <br />

                    <h1>Valor do pedido: R$ <?php echo decimal($rowPed['total']); ?></h1>
                    <p>&nbsp;</p>
                    <p>Clique no botão PAGAR AGORA, para concluir o pedido, efetuando o pagamento.</p>
                    <p>&nbsp;</p>
                    <p><img src="img/cartoes-janela.gif" width="232" height="30" alt="pagar.me"></p>
                    <br /><br />

                    <?php
                    $telefone = array();
                    preg_match('/\(([0-9]*)\) ([0-9]*)/i', trim($rowPed['telefone']), $telefone);

                    $data_venc = strtotime($data_venc) * 1000;

                    $cidade = $rowPed['cidade'];
                    $estado = $rowPed['estado'];
                    $documento = $rowPed["cpf"];

                    // Criando objeto params
                    $params = array(
                        "customerDocumentNumber" => limpaDocumento($documento),
                        "customerName" => $rowPed['nome'],
                        "customerEmail" => $rowPed['email'],
                        "customerAddressStreet" => $rowPed["endereco"],
                        "customerAddressNeighborhood" => $rowPed["bairro"],
                        "customerAddressStreetNumber" => $rowPed["numero"],
                        "customerAddressCity" => $cidade,
                        "customerAddress-State" => $estado,
                        "customerAddressZipcode" => $rowPed["cep"],
                        "customerPhoneDdd" => $telefone[1],
                        "customerPhoneNumber" => $telefone[2],
                        "customerData" => false,
                        "amount" => preg_replace('/[.,]*/i', '', tofloat(decimal($rowPed['total']))),
                        "createToken" => true,
                        "interestRate" => 0,
                        "postbackUrl" => "https://www.frazatto.com.br/pagarme/retorno_pagarme.php?postback={$order_number}",
                        "boletoExpirationDate" => $data_venc
                    );
                    ?>

                    <input type="hidden" id="url_proccess" value="pagarme/retorno_pagarme.php?CT=<?php echo $order_number; ?>&&token=" />
                    <input type="hidden" id="params" value='<?php echo str_ireplace('\'', '', json_encode($params)); ?>' />

                    <input id="pay-button" type="button" class="btn btn-large btn-block btn-danger" style="font-size:20px; color:#FFF; width:580px;" value="Realizar Pagamento">

                    <p style="margin:20px 0;">O pagamento será realizado diretamente no ambiente de nossa operadora, totalmente seguro.</p>

                    <?php //} 
                    ?>

                    <div class="clear"></div>
                    <div class="neutro" style="margin-top:20px;">Caso tenha alguma dúvida sobre o pedido realizado ou sobre como efetuar o pagamento, entre em contato conosco.</div>


                    <?php if ($rowPed['situacao'] == '1') { ?>
                        <input type="button" class="btn btn-large" value="Alterar forma de pagamento" onClick="location.href='pagamento.php?trocar=pagamento&k=<?php echo $chave; ?>'" style="margin:40px 0;" />
                    <?php } ?>

                <?php } //FIM //realizar pagamento 
                ?>

                <?php
                if ($_GET['trocar'] == 'pagamento') { //alterar forma de pagamento

                    $chave = secure($_GET['k']);

                    //pega dados do pedido
                    $sqlPed = mysql_query("SELECT * FROM pedidos WHERE chave='$chave' LIMIT 1");
                    $rowPed = mysql_fetch_array($sqlPed);

                    $Total = $rowPed['total'];
                ?>


                    <div style="font-size:18px; text-align:center; margin:20px 0;">
                        <p><strong><?php echo $rowPed['nome'] ?></strong>, escolha sua nova forma de pagamento.</p>
                    </div>

                    <div style="padding:20px; margin-bottom:20px;">
                        <h2 style="padding:40px; background:#FFCC00; color:#FFF; width:500px; margin:auto;">Número do seu Pedido:</span> <span style="font-size:30px;color:#FFF;"> <?php echo $rowPed['codigo']; ?></span></h2>
                    </div>


                    <form action="processa_pedido.php" method="POST" name="pagamento">

                        <input type="hidden" name="trocar" value="pagamento" />
                        <input type="hidden" name="chave" value="<?php echo $chave; ?>" />

                        <h2>Forma de pagamento</h2>

                        <p style="margin-bottom:30px; text-align:center;">Escolha abaixo a forma de pagamento desejada. </p>

                        <?php
                        //if ($rowMeta['FPcielo'] == '1') { //pagarme 
                        ?>

                        <div style="padding:10px 0 10px 0; text-align:center; margin-bottom:20px; background: url(img/fdd4.jpg) repeat-x top; border-radius:5px; border:solid 1px #CCC;">
                            <h2 style="margin-bottom:20px; font-size:18px; color:#039;">
                                <input type="radio" name="pagamento" value="Cielo" checked="checked" />
                                CARTÃO DE CRÉDITO OU BOLETO
                            </h2>
                            <p style="font-size:14px; margin-bottom:20px;"><span class="blue"><strong>R$ <?php echo decimal($Total); ?></strong></span> - Cartão de crédito ou boleto - Ambiente seguro</p>
                            <img src="img/visa.gif" alt="visa" width="62" height="35" hspace="5" />&nbsp;&nbsp;&nbsp;&nbsp; <img src="img/master.gif" alt="master" width="62" height="35" hspace="5" />&nbsp;&nbsp;&nbsp;&nbsp; <img src="img/diners.gif" alt="diners" width="62" height="35" hspace="5" />&nbsp;&nbsp;&nbsp;&nbsp;
                            <img src="img/discover.gif" width="62" height="35" alt="discover" />&nbsp;&nbsp;&nbsp;&nbsp;
                            <img src="img/elo.gif" width="62" height="35" alt="elo" />&nbsp;&nbsp;&nbsp;&nbsp;
                            <img src="img/aura.gif" alt="elo" width="62" height="35" hspace="5" />&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>
                        <?php //} //FIM //pagarme 
                        ?>

                        <?php if ($rowMeta['FPpaypal'] == '1') { //paypal 
                        ?>
                            <div style="padding:10px 0 10px 0; text-align:center; margin-bottom:20px; background: url(img/fdd4.jpg) repeat-x top; border-radius:5px; border:solid 1px #CCC;">
                                <h2 style="margin-bottom:20px; font-size:18px; color:#039;">
                                    <input type="radio" name="pagamento" value="PayPal" checked="checked" />
                                    PAYPAL
                                </h2>
                                <p style="font-size:14px; margin-bottom:20px;"><span class="blue"><strong>R$ <?php echo decimal($Total); ?></strong></span> - Cartões de crédito VISA e Mastercard, pelo Paypal</p>
                                <img src="img/paypal.gif" alt="visa" width="246" height="30" hspace="5" />
                            </div>
                        <?php } //FIM //paypal 
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
                        if ($rowMeta['FPboleto'] == '1') { //boleto 

                            //se tiver desconto
                            if ($rowMeta['desconto_boleto'] > '0') {

                                $desc = ($rowMeta['desconto_boleto'] / 100);
                                $desconto_boleto = $desc * $Total;
                                $TotalDesc =  $Total - $desconto_boleto;

                                $frase_desc = ' (' . $rowMeta['desconto_boleto'] . '% de desconto)';
                            } //FIM //se tiver desconto
                            else $TotalDesc =  $Total;
                        ?>
                            <div style="padding:10px 0 10px 0; text-align:center; margin-bottom:20px; background: url(img/fdd4.jpg) repeat-x top; border-radius:5px; border:solid 1px #CCC;">
                                <h2 style="margin-bottom:20px; font-size:18px; color:#039;">
                                    <input type="radio" name="pagamento" value="Boleto" checked="checked" />
                                    BOLETO BANCÁRIO
                                </h2>
                                <p style="font-size:14px; margin-bottom:20px;">
                                    <strong>R$ <?php echo decimal($TotalDesc); ?><?php echo $frase_desc; ?></strong> - Pode ser pago em toda rede bancária
                                </p>
                                <img src="img/boleto.jpg" width="54" height="39" alt="boleto bancário" />
                            </div>
                        <?php } //FIM //boleto 
                        ?>

                        <?php
                        if ($rowMeta['FPdeposito'] == '1') { //deposito 

                            //se tiver desconto
                            if ($rowMeta['desconto_deposito'] > '0') {

                                $desc = ($rowMeta['desconto_deposito'] / 100);
                                $desconto_deposito = $desc * $Total;
                                $TotalDesc =  $Total - $desconto_deposito;

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

                        <input type="hidden" name="desconto_boleto" value="<?php echo $desconto_boleto; ?>" />
                        <input type="hidden" name="desconto_deposito" value="<?php echo $desconto_deposito; ?>" />

                        <input type="submit" name="finalizar" class="btn btn-danger btn-large" value="Realizar pagamento" style="float:right; margin-bottom:30px;">

                    </form>

                <?php } //FIM //alterar forma de pagamento 
                ?>



            </center>









            <div class="clear">&nbsp;</div>


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
    <script type="text/javascript" src="js/scripts-site.js"></script>
    <script type="text/javascript">
        function hidediv(id) {
            //safe function to hide an element with a specified id
            if (document.getElementById) { // DOM3 = IE5, NS6
                document.getElementById(id).style.display = 'none';
            } else {
                if (document.layers) { // Netscape 4
                    document.id.display = 'none';
                } else { // IE 4
                    document.all.id.style.display = 'none';
                }
            }
        }

        function showdiv(id) {
            //safe function to show an element with a specified id

            if (document.getElementById) { // DOM3 = IE5, NS6
                document.getElementById(id).style.display = 'block';
            } else {
                if (document.layers) { // Netscape 4
                    document.id.display = 'block';
                } else { // IE 4
                    document.all.id.style.display = 'block';
                }
            }
        }
    </script>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-512d3f98721dd6fc"></script>

    <!-- Add fancyBox main JS and CSS files -->
    <script type="text/javascript" src="fancybox/source/jquery.fancybox.js?v=2.1.5"></script>
    <link rel="stylesheet" type="text/css" href="fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
    <script type="text/javascript">
        $(document).ready(function() {
            $('.fancybox').fancybox();

            $(".chat").fancybox({
                autoSize: false,
                width: 640,
                height: 420
            });


            // Change title type, overlay closing speed
            $(".fancybox-effects-a").fancybox({
                helpers: {
                    title: {
                        type: 'outside'
                    },
                    overlay: {
                        speedOut: 0
                    }
                }
            });
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

    <?php echo $analytics; ?>
</body>

</html>