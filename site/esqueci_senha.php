<?php
require('config.php'); 
?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Esqueci minha senha</title>
    <style type="text/css">
    <!--
    body {
        margin-left: 0px;
        margin-top: 0px;
        margin-right: 0px;
        margin-bottom: 0px;
    }
    -->
    .tituloAzul
    {
    font-family:
    Arial,
    Helvetica,
    sans-serif;
    font-size:
    16px;
    font-weight:
    bold;
    color:
    #333;
    text-decoration:
    none;
    }
    .textoNormalPreto
    {
    font-family:
    Arial,
    Helvetica,
    sans-serif;
    font-size:
    12px;
    font-weight:
    normal;
    color:
    #000000;
    text-decoration:
    none;
    }
    .formu
    {
    font-family:
    Verdana,
    Arial,
    Helvetica,
    sans-serif;
    font-size:
    15px;
    font-weight:
    normal;
    color:
    #333;
    text-decoration:
    none;
    background-color:
    #FFFFFF;
    border:
    1px
    solid
    #999;
    height:28px;
    }
    .textoCinza
    {
    font-family:
    Verdana,
    Arial,
    Helvetica,
    sans-serif;
    font-size:
    12px;
    font-weight:
    normal;
    color:
    #666666;
    text-decoration:
    none;
    }
    .sucesso
    {
    padding:20px;
    border:#9C0
    solid
    1px;
    background:#CFC;
    color:#060;
    font-size:14px;
    margin-top:10px;
    margin-bottom:10px;
    border-radius:5px;
    box-shadow:
    0px
    2px
    4px
    #ccc;
    }
    .erro
    {
    padding:20px;
    border:#C00
    solid
    1px;
    background:#FCC;
    color:#C00;
    font-size:14px;
    margin-top:10px;
    margin-bottom:10px;
    border-radius:5px;
    box-shadow:
    0px
    2px
    4px
    #ccc;
    }
    .neutro
    {
    padding:20px;
    border:#CCC
    solid
    1px;
    background:#F2f2f2;
    color:#666;
    font-size:14px;
    margin-top:10px;
    margin-bottom:10px;
    border-radius:5px;
    box-shadow:
    0px
    2px
    4px
    #ccc;
    }
    .botao
    {
    border:none;
    padding:5px;
    font-size:15px;
    color:#FFF;
    border-radius:
    5px;
    -moz-border-radius:
    5px;
    /*
    this
    works
    only
    in
    camino/firefox
    */
    -webkit-border-radius:
    5px;
    /*
    this
    is
    just
    for
    Safari
    */
    background-color:#666;
    }
    .botao:hover
    {
    cursor:pointer;
    background-color:#999;
    }
    </style>

</head>

<body>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="TextoPreto10verdana">
        <tr>
            <td height="60" align="center" class="tituloAzul">
                Recuperar senha
            </td>
        </tr>
        <?php if(!$_GET['op']) { ?>
        <tr>
            <td align="center">
                <script language="JavaScript" type="text/javascript">
                function valida(lembrete)

                {

                    if (lembrete.Login.value == "") {
                        alert("Erro! Voce deve informar seu usuario para continuar.");
                        return false;
                    }

                    if (lembrete.Email.value == "") {
                        alert("Erro! Voce deve informar o e-mail para continuar.");
                        return false;
                    }

                    return true;

                }
                </script>
                <form name="lembrete" method="post" action="?op=envia" onsubmit="return valida(this)">
                    <p class="textoNormalPreto">Informe seu email de cadastro e clique em enviar. <br>
                        Sua senha será enviada para o seu email.<br>
                        <br>
                    </p>
                    <p class="textoNormalPreto">
                        <strong>Email:</strong>
                        <input name="email" type="text" class="formu" size="35" maxlength="50">
                        <br>
                        <br>
                    </p>
                    <div class="buttons">
                        <button type="submit" class="botao">Enviar</button>
                    </div>
                    <p class="textoNormalPreto">&nbsp; </p>
                </form>
            </td>
        </tr>

        <?php } else { 
  
  	$email = secure($_POST['email']);

	$sql = "SELECT codigo, email, nome, senha FROM clientes WHERE email='$email' AND ativa='S' ";
	$res = mysql_query($sql, $conecta);
	if(mysql_num_rows($res)==0)
	$st = '0';
	else
	{
	$r = mysql_fetch_array($res);
	$codigo = $r['codigo'];
	$nome = $r['nome'];
	$email_cli = $r['email'];

	$senha = base64_decode($r['senha']);
	
	$st = '1';
	
	$assunto = "Senha de acesso a loja ".NOMEDOSITE."";
	
	$mensagem = "
	Prezado(a) <b>".utf8_decode($nome).",</b><br><br>
	Esta é uma mensagem automática com seus dados de acesso a loja ".NOMEDOSITE.". <br><br>
	Email: <b>$email_cli</b><br>
	Senha: <b>$senha</b><br><br>
	Caso tenha problemas em lembrar sua senha, recomendamos a troca da mesma. <br><br>
	Atenciosamente,<br><br>
	<b>".NOMEDOSITE."</b><br>
	".EMAILPADRAO."
	";
	
	$destinatario = "$email_adm";
	
	enviarcomsmtp($rowMeta['smtp_servidor'], $rowMeta['smtp_porta'], $rowMeta['smtp_senha'], $rowMeta['smtp_usuario'], $rowMeta['smtp_origem'], $rowMeta['smtp_origem'], NOMEDOSITE, $email_cli, utf8_decode($nome), $assunto, $mensagem);
	
	}
  ?>
        <tr>
            <td align="center" class="textoNormalPreto">
                <?php if($st=='0') { ?>
                <p><br>
                <div class="erro">Email incorreto ou n&atilde;o encontrado.</div>
                </p>
                <div class="buttons">
                    <button type="button" class="botao" onClick="javascript:history.back()">Voltar</button>
                </div>
                <p>&nbsp;</p>
                <?php } else { ?>
                <p>
                <div class="sucesso">Sua senha foi enviada para o seu email.</div><br>
                <br>
                <span class="textoCinza"><br>
                    <a href="javascript:window.close()" class="textoCinza">Fechar</a></span></p>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>

    </table>
</body>

</html>