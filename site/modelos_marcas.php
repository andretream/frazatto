<?php
require('config.php'); 
/////////////////////////////////////////

$marca = secure($_GET['marca']);

$sql = mysql_query("
SELECT codigo, titulo 
FROM produtos_modelos 
WHERE cod_mar='$marca' AND ativa='S' 
ORDER BY titulo
");
$tot = mysql_num_rows($sql);
	
if($tot=='0') 
{
	echo '<option value="">Sem modelo</option>';
} else {
	echo '<option value="">Selecione o modelo</option>';
}

while($row = mysql_fetch_array($sql))
{
	echo '<option value="'.$row['codigo'].'">'.$row['titulo'].'</option>';
}
