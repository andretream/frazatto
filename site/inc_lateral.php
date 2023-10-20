<div id="lateral">
    <div id="topo-lateral">
        <h3 class="swisb">DEPARTAMENTOS</h3>
    </div>
    <nav>
        <?php
		$sql = mysql_query("SELECT codigo, titulo, alias FROM produtos_categorias WHERE cod_cat='0' ORDER BY titulo");
		while($row = mysql_fetch_array($sql)) 
		{//while categoria
		
			if($_GET['cat']==$row['alias']) $active = ' active'; else $active = '';
		?>
        <a href="produtos/categoria/<?php echo $row['alias']; ?>"><div class="menu<?php echo $active ?>"><?php echo $row['titulo']; ?></div></a>
        	
            <?php 
			if($_GET['cat']==$row['alias']) {//quando tiver subcategoria na categoria clicada
			
				$sqlS = mysql_query("SELECT codigo, titulo, alias FROM produtos_categorias WHERE cod_cat='".$row['codigo']."' ORDER BY titulo");
				$totS = mysql_num_rows($sqlS);
				
				if($totS > '0') {//quando tiver subcategoria
			?>
            <div class="sub">
                <?php 
				$s = '0';
				while($rowS = mysql_fetch_array($sqlS)) 
				{//while subcategoria
					$s++;
					if($totS==$s) $class_sub = ' style="border:0;"'; else $class_sub = '';
				?>
                <a href="produtos/categoria/<?php echo $row['alias']; ?>/<?php echo $rowS['alias']; ?>"<?php echo $class_sub; ?>><?php echo $rowS['titulo']; ?></a>
                <?php }//FIM //while subcategoria ?>
            </div>
            <?php 
				}//FIM //quando tiver subcategoria
				
			}//FIM //quando tiver subcategoria na categoria clicada
			?>
        
        <?php }//FIM //while categoria ?>
    </nav>    

    <?php
	$sql = mysql_query("SELECT codigo, titulo, link FROM banners WHERE ativa='S' AND area='1' ORDER BY RAND() LIMIT 1");
	while($row = mysql_fetch_array($sql)) 
	{
		if(file_exists(SITEPATH."upload/imagens/banners/banner_".$row['codigo'].".jpg"))
		$banner = SITEPATH."upload/imagens/banners/banner_tumb_".$row['codigo'].".jpg";
		
		if(!empty($row['link'])) { 
		  $linkA = '<a href="http://'.$row['link'].'" target="_blank">'; 
		  $linkP = '</a>'; 
		} else { 
		  $linkA = ''; 
		  $linkP = ''; 
		}
	?>
    <div id="banner-lateral">
        <?php echo $linkA; ?><img src="<?php echo $banner; ?>" alt="<?php echo $row['titulo']; ?>" width="205" border="0"><?php echo $linkP; ?>
    </div>
  	<?php } ?>
    
    
    
    <div id="newsletter">
        <div id="topo-lateral">
            <h3>FRAZATTO NEWS</h3>
        </div>
        <p class="cin6 f12">Cadastre seu nome e email e receba nossas ofertas, grátis!</p>
        <form name="news" method="post" action="">
            <input name="tipoform" value="newsletter" type="hidden">
            <input type="text" name="nome" placeholder="Informe seu nome" required>
            <input type="text" name="email" placeholder="Seu email" required>
            <input type="submit" class="bt-cinza" value="CADASTRAR">
        </form>
    </div>
    <div class="mt40">
        <img src="img/ebit.jpg" border="0" alt="Ebit - Avaliado pelos consumidores">
    </div>
</div>