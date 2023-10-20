<?php
          include('url_response.php'); 
          
		  $urlpatterns = array(

				////////////////////////////////////////////url´s simples
				
				//capa
                '/home'=>'index.php',
				'/home/'=>'index.php',
				
				//paginas
				'/pagina/(?P<alias>\w+)'=>'pagina.php',
				'/pagina/(?P<alias>\w+)/'=>'pagina.php',
				
				//cadastro
				'/cadastro'=>'cadastro.php',
				'/cadastro/'=>'cadastro.php',
				'/cadastro/(?P<response>\w+)'=>'cadastro.php',
				'/cadastro/(?P<response>\w+)/'=>'cadastro.php',
				'/cadastro/(?P<response>\w+)/(?P<pagina>\w+)'=>'cadastro.php',
				'/cadastro/(?P<response>\w+)/(?P<pagina>\w+)/'=>'cadastro.php',
				
				//meu cadastro
				'/meucadastro'=>'meucadastro.php',
				'/meucadastro/'=>'meucadastro.php',
				'/meucadastro/(?P<response>\w+)'=>'meucadastro.php',
				'/meucadastro/(?P<response>\w+)/'=>'meucadastro.php',

				//meus pedidos
				'/meuspedidos'=>'meuspedidos.php',
				'/meuspedidos/'=>'meuspedidos.php',
				
				//busca
				'/busca'=>'busca.php',
				'/busca/'=>'busca.php',

				//login
				'/login'=>'login.php',
				'/login/'=>'login.php',
				'/login/(?P<op>\w+)'=>'login.php',
				'/login/(?P<op>\w+)/'=>'login.php',
				'/login/(?P<op>\w+)/(?P<pagina>\w+)'=>'login.php',
				'/login/(?P<op>\w+)/(?P<pagina>\w+)/'=>'login.php',

				//faleconosco
				'/atendimento'=>'contato.php',
				'/atendimento/'=>'contato.php',
				'/atendimento/(?P<response>\w+)'=>'contato.php',
				'/atendimento/(?P<response>\w+)/'=>'contato.php',
				
				//naoencontrei
				'/naoencontrei'=>'naoencontrei.php',
				'/naoencontrei/'=>'naoencontrei.php',
				'/naoencontrei/(?P<response>\w+)'=>'naoencontrei.php',
				'/naoencontrei/(?P<response>\w+)/'=>'naoencontrei.php',
				
				//newsletter
				'/newsletter/(?P<response>\w+)'=>'newsletter.php',
				'/newsletter/(?P<response>\w+)/'=>'newsletter.php',
				
				//produtos
				'/produtos'=>'produtos.php',
				'/produtos/'=>'produtos.php',
				'/produtos/(?P<tipo>\w+)'=>'produtos.php',
				'/produtos/(?P<tipo>\w+)/'=>'produtos.php',
				'/produtos/(?P<tipo>\w+)/(?P<cat>\w+)'=>'produtos.php',
				'/produtos/(?P<tipo>\w+)/(?P<cat>\w+)/'=>'produtos.php',
				'/produtos/(?P<tipo>\w+)/(?P<cat>\w+)/(?P<sub>\w+)'=>'produtos.php',
				'/produtos/(?P<tipo>\w+)/(?P<cat>\w+)/(?P<sub>\w+)/'=>'produtos.php',
				'/produtos/(?P<tipo>\w+)/(?P<cat>\w+)/(?P<sub>\w+)/(?P<mar>\w+)'=>'produtos.php',
				'/produtos/(?P<tipo>\w+)/(?P<cat>\w+)/(?P<sub>\w+)/(?P<mar>\w+)/'=>'produtos.php',
				  
				//detalhes do produto
			    '/produto/(?P<alias>\w+)/(?P<codigo>\w+)'=>'detalhes.php',
			    '/produto/(?P<alias>\w+)/(?P<codigo>\w+)/'=>'detalhes.php',
			    '/produto/(?P<alias>\w+)/(?P<codigo>\w+)/(?P<cat>\w+)'=>'detalhes.php',
			    '/produto/(?P<alias>\w+)/(?P<codigo>\w+)/(?P<cat>\w+)/'=>'detalhes.php',
			    '/produto/(?P<alias>\w+)/(?P<codigo>\w+)/(?P<cat>\w+)/(?P<sub>\w+)'=>'detalhes.php',
			    '/produto/(?P<alias>\w+)/(?P<codigo>\w+)/(?P<cat>\w+)/(?P<sub>\w+)/'=>'detalhes.php',
				'/produto/(?P<alias>\w+)/(?P<codigo>\w+)/(?P<cat>\w+)/(?P<sub>\w+)/(?P<mar>\w+)'=>'detalhes.php',
			    '/produto/(?P<alias>\w+)/(?P<codigo>\w+)/(?P<cat>\w+)/(?P<sub>\w+)/(?P<mar>\w+)/'=>'detalhes.php',
				
				//detalhes do produto teste
			    '/produto2/(?P<alias>\w+)/(?P<codigo>\w+)'=>'detalhes2.php',
			    '/produto2/(?P<alias>\w+)/(?P<codigo>\w+)/'=>'detalhes2.php',
			    '/produto2/(?P<alias>\w+)/(?P<codigo>\w+)/(?P<cat>\w+)'=>'detalhes2.php',
			    '/produto2/(?P<alias>\w+)/(?P<codigo>\w+)/(?P<cat>\w+)/'=>'detalhes2.php',
			    '/produto2/(?P<alias>\w+)/(?P<codigo>\w+)/(?P<cat>\w+)/(?P<sub>\w+)'=>'detalhes2.php',
			    '/produto2/(?P<alias>\w+)/(?P<codigo>\w+)/(?P<cat>\w+)/(?P<sub>\w+)/'=>'detalhes2.php',
				'/produto2/(?P<alias>\w+)/(?P<codigo>\w+)/(?P<cat>\w+)/(?P<sub>\w+)/(?P<mar>\w+)'=>'detalhes2.php',
			    '/produto2/(?P<alias>\w+)/(?P<codigo>\w+)/(?P<cat>\w+)/(?P<sub>\w+)/(?P<mar>\w+)/'=>'detalhes2.php',
				
				//cesta de compras
				'/carrinho'=>'carrinho.php',
				'/carrinho/'=>'carrinho.php',
				  
				//pagamento
				'/pagamento/(?P<k>\w+)'=>'pagamento.php',
				'/pagamento/(?P<k>\w+)/'=>'pagamento.php',
				//pagamento de teste
				'/pagamento2/(?P<k>\w+)'=>'pagamento2.php',
				'/pagamento2/(?P<k>\w+)/'=>'pagamento2.php',
				
				//confirmação de pagamento
				'/confirmacaodepagamento/(?P<k>\w+)'=>'confirmarpagamento.php',
				'/confirmacaodepagamento/(?P<k>\w+)/'=>'confirmarpagamento.php',
				'/confirmacaodepagamento/(?P<k>\w+)/(?P<response>\w+)'=>'confirmarpagamento.php',
				'/confirmacaodepagamento/(?P<k>\w+)/(?P<response>\w+)/'=>'confirmarpagamento.php',
				
				//não apagar
				'/'=>'index.php'
								                
           );
          
		  url_response($urlpatterns);
?>