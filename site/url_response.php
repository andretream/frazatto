<?php

        //diretório do projeto
        if(!defined('PROJECT_DIR'))
                define('PROJECT_DIR', './');
                
        // diretório da aplicacao
             if(!defined('APPLICATION_DIR'))    
                define('APPLICATION_DIR', './');

        // URL enviado
            if(!defined('REQUEST_URI')) 
                define('REQUEST_URI'    ,str_replace('/'.PROJECT_DIR,'',$_SERVER['REQUEST_URI']));

         /**
               * Função Resposável pelo tratamento da URL
               *
               * @author Camilo Teixeira de Melo
               * @link http://www.camilotx.com.br
               * @param string $urlpatterns array com os modelos de url
               * @return void
              **/
        function url_response($urlpatterns){
                        foreach($urlpatterns as $pcre=>$app){
                                if(preg_match("@^{$pcre}$@",REQUEST_URI,$_GET)){
                                                include(APPLICATION_DIR.'/'.$app);
                                                exit();
                                } else {

                                        $msg = '<meta http-equiv="refresh" content="0;URL=/404">';
                                }
                        }
                        echo $msg;
                return;         
        }

?>