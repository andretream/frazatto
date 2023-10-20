<?php
require('config.php');

$onde = secure($_GET['w']);

if ($onde != 'a' && $onde != 'l') {
  header('Location:/login');
  exit;
}
if ($onde == 'l') {
  if (!isset($_SESSION['usuario']['ss_codigo'])) {
    header('Location:/login');
    exit;
  }
}
if ($onde == 'a') {
  if (!isset($_SESSION['ss_adm']['codigo'])) {
    header('Location:/login');
    exit;
  }
}

$chave = secure($_GET['k']);

$sql = "SELECT * FROM pedidos WHERE chave='$chave' LIMIT 1";
$res = mysql_query($sql, $conecta);
$row = mysql_fetch_array($res);

if ($row['pagamento'] == 'Boleto' || $row['pagamento'] == 'Deposito' && $row['valor_desconto'] != '0.00') { //desconto no boleto
  $total_geral = $row['total'] - $row['valor_desconto'];
  $valor_desconto = $row['valor_desconto'];
  $desconto = '1';
} else { //sem desconto
  $desconto = '0';
  $valor_desconto = 0.00;
} //FIM //sem desconto
//////////////
?>
<!DOCTYPE HTML>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Detalhe do pedido</title>
  <link href="css/bootstrap.css" type="text/css" rel="stylesheet">
  <style type="text/css" media="print">
    .no-print {
      display: none;
    }
  </style>
</head>

<body>
  <div style="width:700px; margin:0 auto;">
    <div style="margin-left:0;">
      <div class="bs-docs-example">

        <div class="span3 pull-left" style="margin:10px 0 25px 0; width:265px;">
          <img src="img/logo_impressao.jpg" alt="<?php echo NOMEDOSITE; ?>" border="0" style="border:none;">
        </div>

        <div class="pull-right">
          <div class="bs-docs-example" style="text-align:right;">
            <h4 style="margin:10px 0;">Pedido Nº <?php echo $row['codigo']; ?> - <?php echo sql2date($row['data']); ?></h4>
            <p><strong>Forma de pagamento:</strong> <?php if ($row['pagamento'] == 'Cielo') echo "Pagar.me";
                                                    else echo $row['pagamento']; ?></p>
            <?php
            if ($row['frete'] != 'gratis') {
              $frete = $row['frete'];
            ?>
              <p><strong>Modalidade de envio:</strong> <span style="text-transform:uppercase;"><?php echo $frete; ?></span></p>
            <?php } ?>
          </div>
        </div>

        <div class="clearfix"></div>

        <div class="navbar">
          <div class="navbar-inner">
            <h4 class="pull-left">Dados do cliente:</h4>
          </div>
        </div>

        <table class="table table-striped table-condensed table-hover">
          <tr>
            <th width="26%" align="left"><strong>Nome / Razão social:</strong></th>
            <td width="74%" align="left"><?php echo $row['nome']; ?></td>
          </tr>
          <tr>
            <th width="26%" align="left"><strong>CPF / CNPJ:</strong></th>
            <td width="74%" align="left"><?php echo $row['cpf']; ?></td>
          </tr>
          <tr>
            <th width="26%" align="left"><strong>RG / IE:</strong></th>
            <td width="74%" align="left"><?php echo $row['rg']; ?></td>
          </tr>
          <tr>
            <th width="26%" align="left"><strong>Endereço completo:</strong></th>
            <td width="74%" align="left"><?php echo $row['endereco']; ?>, <? echo $row['numero']; ?></td>
          </tr>
          <tr>
            <th width="26%" align="left"><strong>Complemento / Bairro:</strong></th>
            <td width="74%" align="left"><?php if (!empty($row['complemento'])) echo $row['complemento'] . ' - '; ?><?php echo $row['bairro']; ?></td>
          </tr>
          <tr>
            <th width="26%" align="left"><strong>Cidade / Estado:</strong></th>
            <td width="74%" align="left"><?php echo $row['cidade']; ?> / <?php echo $row['estado']; ?></td>
          </tr>
          <tr>
            <th width="26%" align="left"><strong>CEP:</strong></th>
            <td width="74%" align="left"><?php echo $row['cep']; ?></td>
          </tr>
          <tr>
            <th width="26%" align="left"><strong>Telefones:</strong></th>
            <td width="74%" align="left"><?php echo $row['telefone']; ?><?php if (!empty($row['celular'])) echo ' - ' . $row['celular']; ?></td>
          </tr>
          <tr>
            <th width="26%" align="left"><strong>Email:</strong></th>
            <td width="74%" align="left"><?php echo $row['email']; ?></td>
          </tr>
        </table>

        <div class="clearfix">&nbsp;</div>

        <div class="navbar">
          <div class="navbar-inner">
            <h4 class="pull-left">Iten(s) do pedido:</h4>
          </div>
        </div>

        <div class="clearfix">&nbsp;</div>

        <table class="table table-striped table-condensed table-hover">
          <thead>
            <tr>
              <th height="38" align="left"><strong>Produto</strong></th>
              <th width="90" align="center"><strong>Quantidade</strong></th>
              <th width="120" align="right"><strong>Valor Unit.</strong></th>
              <th width="120" align="right"><strong>Valor Total</strong></th>
            </tr>
          </thead>
          <tbody>
            <?php
            //itens na cesta
            $subValor = 0;
            $itens = mysql_query("SELECT * FROM pedidos_itens WHERE pedido='" . $row['codigo'] . "' ");
            while ($it = mysql_fetch_array($itens)) {

              $subTotal += $it['valor_qtd'];

              //consulta variação
              $Var = mysql_query("SELECT codigo, titulo, estoque FROM variacoes WHERE codigo='" . $it['cod_variacao'] . "' LIMIT 1");
              $totVar = mysql_num_rows($Var);
              $rowVar = mysql_fetch_array($Var);

              //foto para exibição
              $sqlFoto = mysql_query("SELECT codigo, pp FROM fotos WHERE cod_pagina='" . $rowProd['codigo'] . "' AND pp='produtos' ORDER BY codigo DESC LIMIT 1");
              $totFoto = mysql_num_rows($sqlFoto);
              $rowFoto = mysql_fetch_array($sqlFoto);

              if ($totFoto > 0 && file_exists(SITEPATH . 'upload/imagens/paginas/fotos_' . $rowFoto['codigo'] . '_' . $rowFoto['pp'] . '.jpg'))
                $fotoProd = SITEPATH . 'upload/imagens/paginas/fotos_medio_' . $rowFoto['codigo'] . '_' . $rowFoto['pp'] . '.jpg';
              else
                $fotoProd = "img/semfoto.jpg";
            ?>
              <tr>
                <td align="left" valign="middle">
                  <strong><?php echo $it['titulo_produto']; ?> </strong>
                  <?php if (!empty($rowVar['titulo'])) echo '<br>' . $rowVar['titulo']; ?>
                  <?php if (!empty($rowMeta['prazo_envio'])) echo '<br>Prazo de envio: ' . $rowMeta['prazo_envio']; ?>
                </td>
                <td align="center"><? echo $it['qtd']; ?></td>
                <td align="right">R$ <?php echo decimal($it['valor_unitario']); ?></td>
                <td align="right">R$ <?php echo decimal($it['valor_qtd']); ?></td>
              </tr>
            <?php
            } //fecha while
            ?>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td align="right"><strong>Sub-total:</strong></td>
              <td align="right"><strong>R$ <?php echo decimal($subTotal); ?></strong></td>
            </tr>
            <tr>
              <th>&nbsp;</th>
              <th>&nbsp;</th>
              <th align="right"><strong>Frete:</strong></th>
              <td align="right"><strong><?php
                                        if ($row['frete'] == 'gratis') {
                                          echo 'GRÁTIS';
                                        } else {
                                          echo 'R$ ' . decimal($row['valor_frete']);
                                        }
                                        ?></strong>
              </td>
            </tr>
            <tr>
              <th>&nbsp;</th>
              <th>&nbsp;</th>
              <th align="right"><strong>Total do pedido:</strong></th>
              <td align="right"><strong>R$ <?php echo decimal($row['total']); ?></strong></td>
            </tr>
            <?php
            if ($desconto == 1) { //com desconto
            ?>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong>Desconto</strong></td>
                <td align="right"><strong>R$ <?php echo decimal($valor_desconto); ?></strong></td>
              </tr>
              <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right"><strong>Total com desconto</strong></th>
                <td align="right"><strong>R$ <?php echo decimal($total_geral); ?></strong></td>
              </tr>
            <?php
            } //FIM //com desconto
            ?>
          </tbody>
        </table>
      </div>

      <div align="center" class="no-print"><a href="#" onclick="window.print();" class="btn"><i class="icon icon-print"></i> Imprimir</a></div>

      <div class="clearfix">&nbsp;</div>

    </div>
  </div>
</body>

</html>