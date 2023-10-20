<?php
///
//curl abrir url
function get_data($url)
{
$ch = curl_init();
$timeout = 15;
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
$data = curl_exec($ch);
curl_close($ch);
return $data;
}
///////

$soapXMLResult = get_data('http://www.jadlog.com.br:8080/JadlogEdiWs/services/ValorFreteBean?method=valorar&vModalidade=3&Password=2T0c1M4p&vSeguro=N&vVlDec=100,00&vVlColeta=0,00&vCepOrig=16204-017&vCepDest=16300-000&vPeso=40.00&vFrap=N&vEntrega=D&vCnpj=49580707000103');

print_r($soapXMLResult);
////
?>