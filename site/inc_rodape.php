<footer>
    <div id="faixa-footer">
        <span class="swis">ENTREGA EXPRESSA EM TODO O BRASIL</span>
    </div>
    <div id="container" class="pt15 mb15">
        <div id="pagamentos" class="text-center">
            <div class="swis text-center f14 cinCA">FORMAS DE PAGAMENTO</div>
            <img src="img/pagamentos.jpg" border="0" alt="Formas de pagamento">
        </div>

        <div id="envio" class="ml35 text-center">
            <div class="swis text-center f14 cinCA">FORMA DE ENVIO</div>
            <img src="img/envio.jpg" border="0" alt="Formas de envio">
        </div>

        <div id="seguranca" class="text-center">
            <div class="swis text-center f14 cinCA">SEGURANÇA</div>
            <img src="img/seguranca.jpg" border="0" alt="Segurança">
        </div>
    </div>

    <div id="footer">
        <div id="container" class="pt35">
            <div id="cont-menu">
                <div class="menu-footer">
                    <a href="pagina/envioeentrega">
                        <li>- Envio e entrega</li>
                    </a>
                    <a href="pagina/trocasedevolucoes">
                        <li>- Trocas e devoluções</li>
                    </a>
                    <a href="pagina/politicadeprivacidade">
                        <li>- Política de privacidade</li>
                    </a>
                    <a href="pagina/garantia">
                        <li style="border:0;">- Garantia</li>
                    </a>
                </div>
                <div class="menu-footer ml30">
                    <a href="pagina/sobrenos">
                        <li>- Sobre nós</li>
                    </a>
                    <a href="meuspedidos">
                        <li>- Minha conta</li>
                    </a>
                    <a href="atendimento">
                        <li>- Fale conosco</li>
                    </a>
                    <a href="naoencontrei">
                        <li style="border:0;">- Produtos por encomenda</li>
                    </a>
                </div>
            </div>

            <div id="endereco">
                <p>
                    FRAZATTO & FRAZATTO BIRIGUI LTDA - CNPJ: 01.993.099/0001-32<br>
                    <?php echo $rowMeta['endereco']; ?><br>
                    <strong>Televendas e SAC: <?php echo $rowMeta['telefone']; ?></strong>
                    <?php if (!empty($rowMeta['whatsapp'])) { ?> - <strong>WhatsApp: <?php echo $rowMeta['whatsapp']; ?></strong><?php } ?><br>
                    Email: <?php echo $rowMeta['email_contato']; ?>
                </p>
            </div>

            <div id="redes">
                <!-- AddThis Button BEGIN -->
                <div class="mb30 right addthis_toolbox addthis_default_style addthis_32x32_style">
                    <a class="addthis_button_facebook"></a>
                    <a class="addthis_button_twitter"></a>
                    <a class="addthis_button_email"></a>
                    <a class="addthis_button_compact"></a>
                </div>
                <!-- AddThis Button END -->

                <div class="clear">&nbsp;</div>


            </div>

            <div class="clear border-bottom pt15">&nbsp;</div>

            <p class="copy mt20">
                Os Preços e Condições de Pagamento divulgados neste site, são exclusivos para compras através deste site, não valendo necessariamente para as vendas por catálogos e televendas .<br>
                Ofertas válidas até o término de nossos estoques. Vendas sujeitas à análise e confirmação de dados.<br><br>

                Copyright © <?php echo date("Y"); ?> Frazatto & Frazatto Birigui Ltda. Todos os direitos reservados. É vetada a sua reprodução, total ou parcial, sem a expressa autorização da administradora do site
            </p>
        </div>
    </div>
</footer>

<?php if ($whatsapp) {
    $whatsapp = str_replace(" ", "", $whatsapp);
    $whatsapp = str_replace("(", "", $whatsapp);
    $whatsapp = str_replace(")", "", $whatsapp);
    $whatsapp = str_replace("-", "", $whatsapp);
?>
    <!-- WhatsHelp.io widget -->
    <div style="position: fixed;bottom: -4px;right: 10px;">
        <a href="https://api.whatsapp.com/send?l=pt_BR&phone=55<?php echo $whatsapp; ?>" target="_blank"><img src="atendimento_on_line.png" border="0" width="250" /></a>
    </div>
    <!-- /WhatsHelp.io widget -->
<?php } ?>