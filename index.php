<?php 
	//Inclui arquivos com as funções da API.
	include('mensagem.php');
	include('usuario.php');
	include('receita.php');
	include('ingrediente.php');
	include('favorito.php');
	include('uploadDeFotos.php');
	include('conectar.php');
	include('categoria.php');
	
    //Recupera os valores da URL
	$metodo = $_SERVER['REQUEST_METHOD'];
	$caminho = $_SERVER['PATH_INFO'];
	$array = explode("/",$caminho);
	$funcao = $array[1];
	$id = 0;
	if(isset($array[2])){
		$id = $array[2];
	}
	$resposta = array();
	//Verifica se o método utilizado na request é POST
	if($metodo == "POST"){	
		//Verifica se a função informada existe.
		if(function_exists($funcao)){
			//Chama função.
			$resposta = $funcao($id);
		}
		else{
			//Caso a função nao exista retorna erro.
			$resposta = mensagens(1);
		}
	}
	else
	{
		$resposta = mensagens(1);
	}
    
    //Retorna a resposta
	header('Content-Type: application/json; charset=utf-8');
    echo json_encode($resposta);
?>