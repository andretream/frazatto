<?php 
require('config.php'); 
//////////////////////

function coeficiente_pagseguro($valor,$maximo){
	
$parc[2] = '0.52255';
$parc[3] = '0.35347';
$parc[4] = '0.26898';
$parc[5] = '0.21830';
$parc[6] = '0.18453';
$parc[7] = '0.16044';
$parc[8] = '0.14240';
$parc[9] = '0.12838';
$parc[10] = '0.11717';
$parc[11] = '0.10802';
$parc[12] = '0.10040';

$var = '';
for($i = 2; $i <= 12; $i++){
	
$conf = ($valor * $parc[$i]);

if($i % 2 == 0) $sombra =  "style=background:#EEEEEE; border=0;"; else $sombra = ''; 

$tot = $conf * $i ;

if($conf > $maximo){
	
$var.= '
<div class="cont-tab" '.$sombra.'>
	<div class="float-left">'.$i.'x de</div>
	<div> &nbsp; R$ '.decimal($conf).' (com juros)</div>
	<div align="right" style="margin-top:-15px;">Total: R$ '.decimal($tot).'</div>
</div>
';
}
}
return $var;

}

$codigo = secure($_GET['codigo']);
$sql = mysql_query("SELECT valor, valor_promocao FROM produtos WHERE codigo='$codigo'");
$row = mysql_fetch_array($sql);

if($row['valor_promocao']!='0.00') $valor_do_produto = $row['valor_promocao']; 
else $valor_do_produto = $row['valor'];
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Formas de pagamento e parcelamento</title>
<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!-- 
.cont-tab {
	width:300px;
	display:block;
	margin:0 10px 5px 10px;
	padding:4px;
}
.float-left { float:left; }
.float-right { float:right; }
.clear { clear:both; }
-->
</style>
</head>

<body style="background:#FFF;">
	
<!-- Como Pagar -->    
<div style="margin:10px;">
    <h2 style="margin-bottom:30px;">Formas de pagamento e parcelamento</h2>

    <?php 
	if($rowMeta['FPboleto']=='1') {//boleto 
	
		//se tiver desconto
		if($rowMeta['desconto_boleto'] > '0') {
			
			$desc = ($rowMeta['desconto_boleto'] / 100); 
			$TotalDesc =  $valor_do_produto - ($desc * $valor_do_produto);
			
			$frase_desc = ' <span style="font-weight:normal;">('.$rowMeta['desconto_boleto'].'% de desconto)</span>';
			
		}//FIM //se tiver desconto
		
		else $TotalDesc = $valor_do_produto;

	?>
  	<h5 style="font-size:14px;"><strong>Pagamento à vista no boleto bancário:</strong> <span style="color:#000;">R$ <?php echo decimal($TotalDesc); ?></span><?php echo $frase_desc; ?></h5>
	<?php }//FIM //boleto ?>
    
    <?php 
	if($rowMeta['FPdeposito']=='1') {//deposito 
	
		//se tiver desconto
		if($rowMeta['desconto_deposito'] > '0') {
			
			$desc2 = ($rowMeta['desconto_deposito'] / 100); 
			$TotalDesc2 =  $valor_do_produto - ($desc2 * $valor_do_produto);
			
			$frase_desc2 = ' <span style="font-weight:normal;">('.$rowMeta['desconto_deposito'].'% de desconto)</span>';
			
		}//FIM //se tiver desconto
		
		else $TotalDesc2 = $valor_do_produto;
	?>
    <h5 style="font-size:14px;"><strong>Pagamento à vista no depósito bancário:</strong> <span style="color:#000;">R$ <?php echo decimal($TotalDesc2); ?></span><?php echo $frase_desc2; ?></h5>
    <?php }//FIM //deposito ?>
    
    <hr style="margin-bottom:0;">
    
	<?php if($rowMeta['FPpagseguro']=='1') {//pagseguro ?>
    <div style="float:left; width:340px;">
        <h5 style="font-size:14px;"><strong>Pagamento com PagSeguro:</strong><br><br> <img src="img/pag.png" border="0" width="140" ></h5><br>
        <div class="cont-tab">
            <div class="float-left">1x sem juros de</div>
            <div></div>
            <div align="right">R$ <?php echo decimal($valor_do_produto); ?></div>
        </div>
        <?php
        $valor = $valor_do_produto; //valor total do produto
        $max = '20.00'; //valor minimo da parcela

        $var = coeficiente_pagseguro($valor,$max); //imprimime todas as parcelas
        echo $var;
        ?>
    </div>
    <?php }//FIM //pagseguro ?>
    



    <?php if($rowMeta['FPpaypal']=='1') {//paypal ?>
    <div style="float:left; width:350px;">
        <h5 style="font-size:14px;"><strong>Pagamento com PayPal:</strong><br><br> <img src="img/pay.png" border="0" width="100"></h5><br>
        <?php
        for($i = 1; $i <= 12; $i++)
        {
        $valor_tot = $valor_do_produto / $i;
        
        if($i % 2 == 0) $sombra =  "style=background:#EEEEEE; border=0;"; else $sombra = ''; 
        ?>
        <div class="cont-tab" <?php echo $sombra; ?>>
            <div class="float-left"><?php echo $i; ?>x de &nbsp;</div>
            <div> R$ <?php echo decimal($valor_tot); ?> (sem juros)</div>
            <div align="right" style="margin-top:-15px;">Total: R$ <?php echo decimal($valor_do_produto); ?></div>
        </div>
        <?php 
        }
        ?>
    </div>
    <?php }//FIM //paypal ?>
    
    


    <?php if($rowMeta['FPcielo']=='1') {//cielo ?>
    <div style="float:left; margin-left:30px; width:350px;">
        <h5 style="font-size:14px;"><strong>Pagamento com Cartão de Crédito:</strong><br><br> <img src="img/cartoes_cielo.jpg" width="344" height="60" border="0"></h5><br>
        <?php
        //aqui faz o for para $i que é igual a 1 e $i <= $maxParcelas, para limitar ao numero de parcelas
        for($i = 1; $i <= MAXPARCELAS; $i++) {//for
        
        $valorParcela = ($valor_do_produto / $i);
        $valorParcela = round($valorParcela, 1);
        
        //se o valor da parcela for maior ou igual à parcela mínima, printa a parcela, senão não printa.
        if($valorParcela >= VALORMIN) {//if
        
        if($i % 2 == 0) $sombra =  "style=background:#EEEEEE; border=0;"; else $sombra = ''; 
        ?>
        <div class="cont-tab" <?php echo $sombra; ?>>
            <div class="float-left"><?php echo $i; ?>x de &nbsp;</div>
            <div> R$ <?php echo decimal($valorParcela); ?> (sem juros)</div>
            <div align="right" style="margin-top:-15px;">Total: R$ <?php echo decimal($valor_do_produto); ?></div>
        </div>
        <?php
        	}//FIM //if
        }//FIM //for
        ?>
    </div>
    <?php }//FIM //cielo ?>
    
    <div class="clear"></div>

</div>
<!-- Fim Como Pagar -->        
</body>
</html>