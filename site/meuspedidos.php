<?php
require('config.php');
//////////////////////

if (!isset($_SESSION['usuario']['ss_codigo']) || empty($_SESSION['usuario']['ss_codigo'])) {
  header('location:/login/acesso/meuspedidos');
  exit;
}
?>
<!DOCTYPE HTML>
<html>

<head>
  <meta charset="utf-8">
  <title><?php echo NOMEDOSITE; ?> | Meus pedidos</title>

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
        <h3>MEUS PEDIDOS</h3>
      </div>

      <?php include('inc_bemvindo.php'); ?>

      <div class="row">
        <?php include('inc_menucliente.php'); ?>
        <div class="span11 pull-right">

          <h3>Ver meus pedidos</h3>
          <hr>

          <?php
          $sql_Lan = "SELECT * FROM pedidos WHERE status='S' AND cliente='" . $_SESSION['usuario']['ss_codigo'] . "' ORDER by codigo DESC";
          $res_Lan = mysql_query($sql_Lan, $conecta);
          $tot_Lan = mysql_num_rows($res_Lan);

          if ($tot_Lan > 0) {
          ?>
            <table class="table table-striped table-hover" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th valign="middle" bgcolor="#EFEFEF"><strong>Número do pedido</strong></th>
                <th align="left" valign="middle" bgcolor="#EFEFEF"><strong>Data do pedido</strong></th>
                <th align="left" valign="middle" bgcolor="#EFEFEF"><strong>Valor</strong></th>
                <th align="left" valign="middle" bgcolor="#EFEFEF"><strong>Pagamento</strong></th>
                <th align="left" valign="middle" bgcolor="#EFEFEF"><strong>Status</strong></th>
                <th align="center" valign="middle" bgcolor="#EFEFEF"><strong>Detalhes</strong></th>
              </tr>
              <?php
              while ($row_Lan = mysql_fetch_array($res_Lan)) { //consulta pedidos

                if ($row_Lan['pagamento'] == 'Boleto' && $row_Lan['valor_desconto']) $total_pedido = $row_Lan['total'] - $row_Lan['valor_desconto'];
                else if ($row_Lan['pagamento'] == 'Deposito' && $row_Lan['valor_desconto']) $total_pedido = $row_Lan['total'] - $row_Lan['valor_desconto'];
                else $total_pedido = $row_Lan['total'];
              ?>
                <tr>
                  <td height="10" align="center" valign="middle"><?php echo $row_Lan['codigo']; ?></td>
                  <td valign="middle"><?php echo sql2date($row_Lan['data']); ?></td>
                  <td valign="middle">R$ <?php echo decimal($total_pedido); ?></td>
                  <td valign="middle"><?php if ($row_Lan['pagamento'] == 'Cielo') echo "Pagar.me";
                                      else echo $row_Lan['pagamento']; ?></td>
                  <td><?php
                      if ($row_Lan['situacao'] == '1') {
                        echo '<font color="#FF9900">Aguardando pagamento</font>';
                        echo '<br>
						<a href="pagamento/' . $row_Lan['chave'] . '" target="_blank" style="font-size:12px; color:#666; text-transform:uppercase;"><strong>Efetuar pagamento</strong></a>
						';
                      }
                      if ($row_Lan['situacao'] == '2') echo '<font color="#009900">Pagamento confirmado - em processamento</font>';
                      if ($row_Lan['situacao'] == '3') echo '<font color="#006699">Pedido enviado</font>';
                      if ($row_Lan['situacao'] == '4') echo '<font color="#FF0000">Cancelado</font>';
                      ?></td>
                  <td align="center" valign="middle"><a href="verpedido.php?w=l&k=<?php echo $row_Lan['chave']; ?>" onClick="window.open(this.href, this.target, 'width=750,height=850,scrollbars=yes'); return false;"><img src="img/lupa.png" width="22" height="22" border="0" alt="Ver detalhes" align="middle"></a></td>
                </tr>
              <?php } //finaliza consulta 
              ?>
            </table>
          <?php } else { ?>
            <div class="alert  alert-default  uppercase  fade  in">Nenhum pedido realizado.</div>
          <?php } ?>

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

  <script type="text/javascript" src="js/scripts-site.js"></script>
  <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-512d3f98721dd6fc"></script>

  <?php echo $analytics; ?>
</body>

</html>