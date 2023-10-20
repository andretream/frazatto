<?php
require('config.php');
//////////////////////

$codigo = secure($_GET['codigo']);
$sql = mysql_query("SELECT peso, largura, altura, profundidade, valor FROM produtos WHERE codigo='$codigo'");
$row = mysql_fetch_array($sql);

//calcula no site dos correios
$cep = secure($_POST['cep']);
if ($_POST['qtd'] == '0') $qtd = '1';
else $qtd = secure($_POST['qtd']);

$pesoTotal = ($row['peso'] * $qtd);
$larguraTotal = ($row['largura'] * $qtd);
$alturaTotal = ($row['altura'] * $qtd);
$profundidadeTotal = ($row['profundidade'] * $qtd);

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

//////////////
if ($cep != '') { //quando o frete for preenchido

    $resultado = '';
    $erroTrans = '0';
    $erroCorreio = '0';

    //####### CALCULO DE FRETE PELA TRANSPORTADORA ###########################################################################################################
    if ($rowMeta['FEtransportadora'] == '1') {

        //pega a faixa de cep
        $cep_trans = explode("-", $cep);
        $explodecep = $cep_trans[0];
        $conta_cep = strlen($cep);

        //verifica de qual regiao é o cep
        $sql_regiao = mysql_query("SELECT codigo FROM regiao WHERE de<='" . $explodecep . "' AND ate>='" . $explodecep . "' LIMIT 1");
        $reg = mysql_fetch_array($sql_regiao);

        //consulta peso do frete
        $query_frete = "
	SELECT DISTINCT(T.codigo), T.alias, T.titulo, F.valor, F.prazo 
	FROM transportadoras AS T, fretes AS F 
	WHERE T.status='S' AND T.ativa='S' AND F.ativa='S' AND F.regiao='" . $reg['codigo'] . "' AND F.pesode<='" . $pesoTotal . "' AND F.pesoate>='" . $pesoTotal . "' AND F.transportadora=T.codigo 
	ORDER BY T.titulo
	";
        $sql_frete_peso = mysql_query($query_frete);
        $tot_frete_peso = mysql_num_rows($sql_frete_peso);

        if ($conta_cep < '9') { //cep incorreto
            $erroTrans = '1';
        } else if ($tot_frete_peso == '0') { //não encontrado nenhuma transportadora disponivel
            $erroTrans = '1';
        } else { //transportadoras disponiveis encontradas

            while ($fre = mysql_fetch_array($sql_frete_peso)) { //while frete

                $codTRANS = $fre['codigo'];
                $valFrete = $fre['valor'];
                $dias_frete = "(" . $fre['prazo'] . " dias úteis após a confirmação de pagamento e postagem)";

                $resultado .= '
			<p style="font-family:Arial, Helvetica, sans-serif;">' . $fre['titulo'] . ' - R$ ' . decimal($valFrete) . '<span style="font-size:11px; font-family:Arial, Helvetica, sans-serif; font-weight:normal;">&nbsp;' . $dias_frete . '*</span></p>
			';
            } //FIM //while frete

        } //FIM //transportadoras disponiveis encontradas

    }
    //####### FIM CALCULO DE FRETE PELA TRANSPORTADORA ###########################################################################################################

    //####### CALCULO DE FRETE PELA JADLOG ###########################################################################################################
    if ($rowMeta['FEjadlog'] == '1') {

        if (calculaFreteJadLog(base64_decode($rowMeta['senha_jadlog']), $row['valor'], removerCaracter($rowMeta['cep_jadlog']), removerCaracter($cep), $pesoTotal, removerCaracter($rowMeta['cnpj_jadlog'])) == false) {
            $erroJadLog = '1';
        } else {
            $valorJADLOG = calculaFreteJadLog(base64_decode($rowMeta['senha_jadlog']), $row['valor'], removerCaracter($rowMeta['cep_jadlog']), removerCaracter($cep), $pesoTotal, removerCaracter($rowMeta['cnpj_jadlog']));

            if (!empty($rowMeta['de_jadlog']) && !empty($rowMeta['ate_jadlog'])) $entrega = '<span style="font-size:11px; font-family:Arial, Helvetica, sans-serif; font-weight:normal;">&nbsp;(' . $rowMeta['de_jadlog'] . ' a ' . $rowMeta['ate_jadlog'] . ' dias úteis após a confirmação de pagamento e postagem)*</span>';

            $resultado .= '
		<p style="font-family:Arial, Helvetica, sans-serif;">JadLog - R$ ' . umzero($valorJADLOG) . $entrega . '</p>
		';
        }
    }
    //####### FIM CALCULO DE FRETE PELA JADLOG ###########################################################################################################

    //####### CALCULO DE FRETE PELOS CORREIOS ###########################################################################################################
    if ($rowMeta['FEcorreios'] == '1') {

        if (calculaFrete('04014', $rowMeta['cep'], $cep, $pesoTotal, $alturaTotal, $larguraTotal, $profundidadeTotal) == false) {
            $erroCorreio = '1';
        } else {
            //PAC
            $valorPAC = calculaFrete('04510', $rowMeta['cep'], $cep, $pesoTotal, $alturaTotal, $larguraTotal, $profundidadeTotal);
            $valorPAC = str_replace(',', '.', $valorPAC);
            //SEDEX
            $valorSEDEX = calculaFrete('04014', $rowMeta['cep'], $cep, $pesoTotal, $alturaTotal, $larguraTotal, $profundidadeTotal);
            $valorSEDEX = str_replace(',', '.', $valorSEDEX);

            $resultado .= '
		<p style="font-family:Arial, Helvetica, sans-serif;">PAC - R$ ' . decimal($valorPAC) . '<span style="font-size:11px; font-family:Arial, Helvetica, sans-serif; font-weight:normal;">&nbsp;(7 a 15 dias úteis após a confirmação de pagamento e postagem)*</span></p>
		<p style="font-family:Arial, Helvetica, sans-serif;">Sedex - R$ ' . decimal($valorSEDEX) . '<span style="font-size:11px; font-family:Arial, Helvetica, sans-serif; font-weight:normal;">&nbsp;(2 a 5 dias úteis após a confirmação de pagamento e postagem)*</span></p>
		';
        }
    }
    //####### FIM CALCULO DE FRETE PELOS CORREIOS ###########################################################################################################

} //FIM //quando o frete for preenchido

if (isset($_POST['calculafrete']) && empty($_POST['cep']))
    $msgCep = 'Informe um CEP!';
else if ($erroCorreio == '1' && $erroTrans == '1' && $erroJadLog == '1')
    $msgCep = 'Não foi possível calcular!';
else
    $msgCep = '';
/////
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Calcule o valor e o prazo de entrega</title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        <!--
        .neutro {
            padding: 8px;
            border: #CCC solid 1px;
            background: #F2f2f2;
            color: #666;
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .right {
            float: right;
        }

        .left {
            float: left;
        }

        .clear {
            clear: both;
        }
        -->
    </style>
</head>

<body style="background:#FFF;">

    <!-- Calculo Frete -->
    <div style="width:590px; margin:5px;">

        <?php if (!empty($msgCep)) { ?>
            <h4 style="color:#C00; text-align:center; margin-bottom:10px;"><?php echo $msgCep; ?></h4>
        <?php } ?>

        <fieldset>
            <h2>Calcule seu frete</h2>
            <hr style="margin-top:-1px;">
            <form action="" method="post" name="cart" id="cart">
                <div style="float:left; width:200px; margin-right:10px;">
                    <label class="gold">Digite seu CEP:</label><br>
                    <input type="text" name="cep" value="<?php echo $cep; ?>" class="" alt="cep" style="width:167px; margin-top:-10px;">
                </div>
                <div style="float:left; width:100px;  margin-right:10px;">
                    <label class="gold">Qtde. Produto:</label><br>
                    <input type="text" name="qtd" id="qtd" value="<?php if (!empty($qtd)) echo $qtd;
                                                                    else echo '1'; ?>" class="" style="width:40px; margin-top:-10px;">
                </div>
                <input type="submit" name="calculafrete" id="botao-calcula-frete" class="btn" value="CALCULAR FRETE" style="margin-top:28px; height:35px;">
                <div class="clear"></div>
            </form>
        </fieldset>

        <div class="neutro">
            <h5 style="margin-top:0;">Prazo de entrega<br><br>
                <?php
                if ($cep != '') {

                    echo $resultado;
                } else {
                ?>
                    <span style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">Digite o CEP desejado e clique em <strong>Calcular Frete</strong> para obter o prazo e o valor do envio*</span>
                <?php } ?>
            </h5>
            <span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; font-weight:normal;">* Prazo a partir da confirmação de pagamento, sujeito a alterações</span>
        </div>
    </div>
    <!-- Fim Calculo Frete -->


    <script type="text/javascript" src="js/jquery-1.9.1.js"></script>
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
            $('#qtd').filter_input({
                regex: '[0-9]'
            });
        });
    </script>
</body>

</html>