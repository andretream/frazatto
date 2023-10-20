<?php 
require('config.php'); 
 
//$senha_jadlog = base64_decode($rowMeta['senha_jadlog']);
$senha_jadlog = '2T0c1M4p'; #para teste manual
$total_pedido = '390,00';//valor decimal
$cep_origem = removerCaracter($rowMeta['cep_jadlog']);
$cep_destino = '16300000';//sem traço
$peso_total = '10,10';//valor decimal
//$cnpj_loja = removerCaracter($rowMeta['cnpj_jadlog']);
$cnpj_loja = '49580707000103'; #para teste manual

$resultado = calculaFreteJadLog($senha_jadlog, $total_pedido, $cep_origem, $cep_destino, $peso_total, $cnpj_loja);

echo $resultado;