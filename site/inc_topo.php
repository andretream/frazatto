<header><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<div id="faixa-topo">
    	<div id="container">
        	<div id="menu-topo">
                <div class="left">
                    <li><a href="home">Home</a></li>
                    <li><a href="pagina/sobrenos">Sobre nós</a></li>
                    <li><a href="meuspedidos">Minha conta</a></li>
                    <li><a href="atendimento">Atendimento</a></li>
                </div>
                <div class="right">
                	<li><a href="naoencontrei">Não achou o produto? Clique aqui</a></li>
                    
                	<li><a href="login">Login</a></li>
				</div>
            </div>
		</div>
    </div>
    <div id="faixa-logo">
    	<div id="container">
        	<div class="left mt20">
            	<a href="home"><img src="img/logo.png" border="0" alt="Frazzato"></a>
            </div>
            
            <div id="meio-topo" class="mt35">
            	<div id="bem-vindo">
                	<div class="left mt5 f13 cin6">
                    <?php if(!empty($_SESSION['usuario']['ss_codigo'])) { ?>
                    Bem-vindo(a) <?php echo $_SESSION['usuario']['ss_nome']; ?>! <a href="autentica.php?op=sair"><br><strong>Se não for você, clique aqui.</strong></a>
                    <?php } else { ?>
                    Já é cadastrado? <a href="login"><strong>Faça o login aqui</strong></a>
                    <?php } ?>
                    </div>
                    <?php if(!empty($rowMeta['whatsapp'])) { ?><div class="right swisb f20" style="margin-left:20px;"><img src="img/whatsapp.png" border="0" alt="WhatsApp"> <?php echo $rowMeta['whatsapp']; ?></div><?php } ?>
					<?php if(!empty($rowMeta['telefone'])) { ?><div class="right swisb f20"><img src="img/tel.png" border="0" alt="Telefone"> <?php echo $rowMeta['telefone']; ?></div><?php } ?>
                    
                </div>
                <div id="busca" class="mt15">
                    <form name="busca" action="busca" method="post">
                        <input type="text" name="busca" placeholder="Busque o produto desejado" value="" required>
                        <input type="image" src="img/bt-busca.png">
                    </form>
                    
					<?php 
					if($_SESSION['carrinho']['itensCesta'] > '0') { 
						$itensCesta = $_SESSION['carrinho']['itensCesta']; 
						$valorCesta = decimal($_SESSION['carrinho']['valorCesta']);
						
						if($itensCesta=='1') $itensCesta = ' (1 item)'; else $itensCesta = ' ('.$itensCesta.' itens)'; 
					} else { 
						$itensCesta = ''; 
						$valorCesta = '0,00';
					}
					?>
					<a href="carrinho">
					<div class="cesta">
                    	<div class="mt10 ml10">
                            <img src="img/carrinho.png" border="0" alt="Carrinho">
                            <span class="f13 ml15">R$ <?php echo $valorCesta.$itensCesta; ?></span>
                    	</div>
                    </div>                
                    </a>
                </div>
                <div class="sombra-busca"></div>	
            </div>
        </div>
    </div>
    <div id="faixa-preta">
    	<div id="container" style="text-align:center;">
        	<img class="mt15" src="img/faixatopo-nova.png" width="548" height="26" border="0" alt="Ambiente Seguro - Entrega expressa em todo o Brasil">
        </div>
    </div> 
    
    <?php
	$sql = mysql_query("SELECT codigo, titulo, alias FROM produtos_marcas WHERE ativa='S' AND destaque='1' ORDER BY RAND()");
	$totMarcas = mysql_num_rows($sql);
	
	if($totMarcas > '0') {//existindo marcas
	?>
    <div id="faixa-marcas">
    	<div id="container" class="pt10">
            <ul class="slidermarcas">
            	<?php
                while($row = mysql_fetch_array($sql)) 
                {//while
                    $logo = SITEPATH."upload/imagens/marcas/marca_".$row['codigo'].".png";
                    if(file_exists($logo)) {//se tiver imagem
                ?>
                <li class="slide">
                    <a href="produtos/marca/<?php echo $row['alias']; ?>">
                    <img src="<?php echo $logo; ?>" alt="<?php echo $row['titulo']; ?>" width="55" height="55" border="0">                
                    </a>
                </li>
                <?php 
                    } //FIM se tiver imagem
                }//FIM //while
                ?>
            </ul>
        </div>
    </div>
    <?php }//FIM //existindo marcas ?>
    
</header>