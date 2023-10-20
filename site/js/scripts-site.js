// JavaScript Document

<!--jumpmenu
function MM_jumpMenu(targ,selObj,restore, url){ //v3.0
if(url != undefined) open(url, '', '_blank');
else
   eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
if (restore) selObj.selectedIndex=0;
}
//-->


// confirma um comando de exclusão e chama o submitForm
function confirmar(msg, cmd) {
	if (confirm(msg)) 
	location.href=(cmd);
}


function abre(url,janela,larg,alt,scroll,pos1,pos2){
window.open(url,janela,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars='+scroll+',resizable=no,copyhistory=no,top='+pos1+',left='+pos2+',screenY='+pos1+',screenX='+pos2+',width='+larg+',height='+alt);
}


function menu_over(src,clrOver) {
 if (!src.contains(event.fromElement)) {
  src.style.cursor = 'hand';
  src.bgColor = clrOver;
 }
}
function menu_out(src,clrIn) {
 if (!src.contains(event.toElement)) {
  src.style.cursor = 'default';
  src.bgColor = clrIn;
 }
}

function janela(param,w,h,nome) {
   var nomearq = param;
   var windowvar = window.open(nomearq,nome,"scrollbars=yes,location=no,directories=no,status=yes,menubar=no,resizable=no,toolbar=no,top=0,left=0,width="+ w + ",height=" +h );
}

function erro(msg) {
	alert(msg);
	return false;
}

function erro_focus(msg, obj) {
	alert(msg);
	obj.focus();
	return false;
}

//valida data
function validDate(obj){
 date=obj.value
if (/[^\d/]|(\/\/)/g.test(date))  {obj.value=obj.value.replace(/[^\d/]/g,'');obj.value=obj.value.replace(/\/{2}/g,'/'); return }
if (/^\d{2}$/.test(date)){obj.value=obj.value+'/'; return }
if (/^\d{2}\/\d{2}$/.test(date)){obj.value=obj.value+'/'; return }
if (!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(date)) return

 test1=(/^\d{1,2}\/?\d{1,2}\/\d{4}$/.test(date))
 date=date.split('/')
 d=new Date(date[2],date[1]-1,date[0])
 test2=(1*date[0]==d.getDate() && 1*date[1]==(d.getMonth()+1) && 1*date[2]==d.getFullYear())
 if (test1 && test2) return true
 alert("Data inválida")
 obj.select();
 obj.focus()
 return false
}

// verifica o email digitado.
function emailValido(mail) {
	return mail.match(/[0-9A-Za-z\.\_\-]+\@{1,1}[0-9A-Za-z\_\-]+\.{1,1}[0-9A-Za-z\_\-]+/);
}

// retorna se um campo está vazio desconsiderando espaços no início e no final
function vazio(valor) {
	s = new String(valor);
	s = s.replace(/^ +/, '');
	s = s.replace(/ +$/, '');
	return s.length == 0;
}

// retorna se um identificador contém apenas caracteres válidos
function idValido(id) {
	s = new String(id);
	validos = new String('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_');
	valido = true;
	for (i = 0; i < s.length; i++) valido = valido && validos.indexOf(s.charAt(i)) != -1;
	return valido && !vazio(id);
}




/* limpa form */
function clearText(thefield){
if (thefield.defaultValue==thefield.value)
thefield.value = ""
} 


/* limpa form e restaura padrões se nada for inserido */
function clickclear(thisfield, defaulttext) {
if (thisfield.value == defaulttext) {
thisfield.value = "";
}
}
function clickrecall(thisfield, defaulttext) {
if (thisfield.value == "") {
thisfield.value = defaulttext;
}
}



/* valida email */

function valida_news (newsletter)

{

if (newsletter.nome.value=="")

{ alert ("Erro! Voce deve informar seu nome para continuar."); return false; }

if (newsletter.nome.value=="Informe seu nome")

{ alert ("Erro! Voce deve informar seu nome para continuar."); return false; }


if (newsletter.email.value=="")

{ alert ("Erro! Voce deve informar um e-mail para continuar."); return false; }

if (newsletter.email.value=="Seu email")

{ alert ("Erro! Voce deve informar um e-mail para continuar."); return false; }

if (newsletter.email.value.indexOf('@', 0) == -1 )

{ alert ("Erro! Informe o e-mail corretamente para continuar."); return false; }

return true;

}

/* valida busca */

function valida_busca (formBusca)
{

if (formBusca.busca.value=="")
{ alert ("Erro! Voce deve informar pelo menos uma palavra para continuar."); return false; }
if (formBusca.busca.value=="Buscar no site")
{ alert ("Erro! Voce deve informar pelo menos uma palavra para continuar."); return false; }

return true;

}


/* valida login */

function valida_login (login)
{

if (login.email.value=="")
{ alert ("Erro! Voce deve informar seu nome de usuario para continuar."); return false; }

if (login.email.value.indexOf('@', 0) == -1 )
{ alert ("Erro! Informe o email corretamente para continuar."); return false; }

if (login.senha.value=="")
{ alert ("Erro! Voce deve informar sua senha para continuar."); return false; }


return true;
}


/* valida endereco */

function valida_endereco (endereco)
					  
{

	if (endereco.endereco.value=="")
	
	{ alert ("Erro! Voce deve informar seu endereço para continuar."); return false; }
	
	if (endereco.numero.value=="")
	
	{ alert ("Erro! Informe o número para continuar."); return false; }
	
	 if (endereco.cidade.value=="")
	
	{ alert ("Erro! Informe a cidade para continuar."); return false; }
	
	 if (endereco.estado.value=="")
	
	{ alert ("Erro! Informe o Estado para continuar."); return false; }
	
	 if (endereco.cep.value=="")
	
	{ alert ("Erro! Informe o seu CEP para continuar."); return false; }
	
	return true;

}



/* valida cadastro */

function valida_cadastro (cadastro)
					  
{

	if (cadastro.nome.value=="")
	
	{ alert ("Erro! Voce deve informar seu nome para continuar."); return false; }

	if (cadastro.email.value=="")
	
	{ alert ("Erro! Voce deve informar seu email para continuar."); return false; }
	
	if (cadastro.email.value.indexOf('@', 0) == -1 )
	
	{ alert ("Erro! Informe o email corretamente para continuar."); return false; }
	
	
	
	return true;

}

/* abir popup de senha */
function senha(url)
{
	window.open(url, "senha",'top=100,left=100,height=280,width=450,location=no,resizable=no,scrollbars=no,status=no'); 
}

