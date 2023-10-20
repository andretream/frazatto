<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

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


//$soapXMLResult = get_data('http://www.jadlog.com.br:8080/JadlogEdiWs/services/ValorFreteBean?method=valorar&vModalidade=5&Password=R2s0C1r4&vSeguro=N&vVlDec=100,00&vVlColeta=0,00&vCepOrig=89062080&vCepDest=89062080&vPeso=30,30&vFrap=N&vEntrega=D&vCnpj=11703861000152');
$soapXMLResult = get_data('http://www.jadlog.com.br:8080/JadlogEdiWs/services/ValorFreteBean?method=valorar&vModalidade=3&Password=2T0c1M4p&vSeguro=N&vVlDec=0,00&vVlColeta=0,00&vCepOrig=16204017&vCepDest=16300000&vPeso=30,00&vFrap=N&vEntrega=D&vCnpj=49580707000103');

list($resultado, $xmlNFSE) = explode('<valorarResponse xmlns="">', $soapXMLResult);
$xmlNFSE = str_replace('&lt;','<',$xmlNFSE);
$xmlNFSE = str_replace('&gt;','>',$xmlNFSE);

function value_in($element_name, $xml, $content_only = true) {
    if ($xml == false) {
        return false;
    }
    $found = preg_match('#<'.$element_name.'(?:\s+[^>]+)?>(.*?)'.
            '</'.$element_name.'>#s', $xml, $matches);
    if ($found != false) {
        if ($content_only) {
            return $matches[1];  
        } else {
            return $matches[0];  
        }
    }
    return false;
}

echo $ValorFrete = value_in('Retorno', $xmlNFSE);

/////
?>