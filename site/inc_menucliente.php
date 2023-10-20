<div class="span2">
    <div class="lateral-pedido">
    <div class="cont-menu">
        <div class="topo <?php if(preg_match("/meuspedidos/", $_SERVER['REQUEST_URI'])) echo 'in'; ?>">
            <a href="meuspedidos"><h3>Meus pedidos</h3></a>
        </div>
        <div class="topo <?php if(preg_match("/meucadastro/", $_SERVER['REQUEST_URI'])) echo 'in'; ?>">
            <a href="meucadastro"><h3>Meu cadastro</h3></a>
        </div>
        <div class="topo">
            <a href="autentica.php?op=sair"><h3>Sair</h3></a>
        </div>
        <br>
    </div>
    </div>
</div>