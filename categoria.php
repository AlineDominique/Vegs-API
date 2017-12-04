<?php
 
function InserirCategoria(){
	
	//Recupera conteudo recebido na request
	$conteudo = file_get_contents("php://input");
	//Variavel que recebe as mensagens
	$resposta = array();
	
	//Verifica se o conteudo foi recebido
	if(empty($conteudo)){
		$resposta = mensagens(2);
	}
	else{
		//Converte o json recebido pra array
		$dados = json_decode($conteudo,true);
		
		//Verifica se as infromações esperadas foram recebidas
		if(!isset($dados["Nome"]))
		{
			$resposta = mensagens(3);
		}
		else{
			include("conectar.php");
			
			//Evita SQL injection
			$Nome = mysqli_real_escape_string($conexao,$dados["Nome"]);
			
			//Recupera idIngrediente para incrementar 1
			$idCategoria = 0;
			$query = mysqli_query($conexao, "SELECT idCategoria FROM Categoria ORDER BY idCategoria DESC LIMIT 1") or die(mysqli_error($conexao));
			while($dados = mysqli_fetch_array($query)){
				$idCategoria = $dados["idCategoria"];
			}
			$idCategoria++;
			
			//Insere uma Categoria
			$query = mysqli_query($conexao,"INSERT INTO Categoria VALUES(" .$idCategoria .",'" .$Nome ."')") or die(mysqli_error($conexao));
			$resposta = mensagens(4);
		}
	}
	return $resposta;
}

function ExcluirCategoria($id){
	
	//Recupera conteudo recebido na request
	$resposta = array();
	
	//Verifica se o id foi recebido
	if($id == 0){
		$resposta = mensagens(5);
	}
	else{
		include("conectar.php");
		
		//Evita SQL injection
		$idCategoria = mysqli_real_escape_string($conexao,$id);		
				
		//Exclui Categoria
		$query = mysqli_query($conexao, "DELETE FROM Categoria WHERE idCategoria = ".$idCategoria) or die(mysqli_error($conexao));
		
		$resposta = mensagens(7);
	}
	return $resposta;
}

function ListarCategorias($idCategoria){
	include("conectar.php");
	
	//Recebe a resposta das mensagens
	$resposta = array();
	$idCategoria = mysqli_real_escape_string($conexao,$idCategoria);
	
	//Consulta Tabela Favorito no BD
	if($idCategoria == 0){
		$query = mysqli_query($conexao,"SELECT idCategoria, Nome FROM Categoria") or die(mysqli_error($conexao));
	}else{ 
		$query = mysqli_query($conexao,"SELECT idCategoria, Nome FROM Categoria WHERE idCategoria = ". $idCategoria ."") or die(mysqli_error($conexao));
	}
	
	//faz um looping e cria um array com os campos da consulta
	while($dados = mysqli_fetch_array($query))
	{
		$resposta[] = array('idCategoria' => $dados['idCategoria'],
							'Nome' => $dados['Nome']); 
	}
	return $resposta;
}
?>