<?php

function InserirUsuario(){
	
	//Recupera conteudo recebido na request
	$conteudo = file_get_contents("php://input");
	$resposta = array();
	//Verifica se o conteudo foi recebido
	if(empty($conteudo)){
		$resposta = mensagens(2);
	}
	else{
		//Converte o json recebido pra array
		$dados = json_decode($conteudo,true);
		
		//Verifica se as infromações esperadas foram recebidas
		if(!isset($dados["Nome"]) || !isset($dados["Email"])){
			$resposta = mensagens(3);
		}
		else{
			include("conectar.php");
			$emailCadastrado = false;
			
			//Evita SQL injection
			$Nome = mysqli_real_escape_string($conexao,$dados["Nome"]);
			$Email = mysqli_real_escape_string($conexao,$dados["Email"]);
			
			//Consulta usuário no banco
			$query = mysqli_query($conexao,"SELECT idUsuario, Nome, Email FROM Usuario WHERE Email='" .$Email ."'") or die(mysqli_error($conexao));
			
			//Verifica se foi retornado algum registro
			while($dados = mysqli_fetch_array($query))
			{
			  $emailCadastrado = true;
			  break;
			}
			
			if($emailCadastrado){
				$resposta = mensagens(8);
			}else{
				//Recupera o próximo ID de usuário
				$idUsuario = 0;
				$query = mysqli_query($conexao, "SELECT idUsuario FROM Usuario ORDER BY idUsuario DESC LIMIT 1") or die(mysqli_error($conexao));
				while($dados = mysqli_fetch_array($query)){
					$idUsuario = $dados["idUsuario"];
				}
				$idUsuario++;
				
				//Insere usuário
				$query = mysqli_query($conexao,"INSERT INTO Usuario VALUES(" .$idUsuario .",'" .$Nome ."','" .$Email ."')") or die(mysqli_error($conexao));
				$resposta = mensagens(4);
			}
		}
	}
	return $resposta;
}

function RecuperarUsuario(){
	
	//Recupera conteudo recebido na request
	$conteudo = file_get_contents("php://input");
	$resposta = array();
	//Verifica se o conteudo foi recebido
	if(empty($conteudo)){
		$resposta = mensagens(2);
	}
	else{
		//Converte o json recebido pra array
		$dados = json_decode($conteudo,true);
		
		//Verifica se as infromações esperadas foram recebidas
		if(!isset($dados["Email"])){
			$resposta = mensagens(3);
		}
		else{
			include("conectar.php");
			$emailCadastrado = false;
			
			//Evita SQL injection
			$Email = mysqli_real_escape_string($conexao,$dados["Email"]);
			
			//Consulta usuário no banco
			$query = mysqli_query($conexao,"SELECT idUsuario, Nome, Email FROM Usuario WHERE Email='" .$Email ."'") or die(mysqli_error($conexao));
			
			//Verifica se foi retornado algum registro
			while($dados = mysqli_fetch_array($query))
			{
				$resposta = array('idUsuario' => $dados['idUsuario'],
					'Nome' => $dados['Nome'],
					'Email' => $dados['Email']);
				
			  $emailCadastrado = true;
			  break;
			}
			
			//Verifica se o usuário foi encontrado
			if(!$emailCadastrado){
				$resposta = mensagens(8);
			}
		}
	}
	return $resposta;
}

function AtualizarUsuario($id){
	
	//Recupera conteudo recebido na request
	$conteudo = file_get_contents("php://input");
	$resposta = array();
	//Verifica se o id foi recebido
	if($id == 0){
		$resposta = mensagens(5);
	}
	else{
		//Verifica se o conteudo foi recebido
		if(empty($conteudo)){
			$resposta = mensagens(2);
		}
		else{
			//Converte o json recebido pra array
			$dados = json_decode($conteudo,true);
			
			//Verifica se as infromações esperadas foram recebidas
			if(!isset($dados["Nome"]) || !isset($dados["Email"])){
				$resposta = mensagens(3);
			}
			else{
				include("conectar.php");
				
				//Evita SQL injection
				$id = mysqli_real_escape_string($conexao,$id);
				$Nome = mysqli_real_escape_string($conexao,$dados["Nome"]);
				$Email = mysqli_real_escape_string($conexao,$dados["Email"]);
				
				//Consulta usuário no banco
				$query = mysqli_query($conexao, "UPDATE Usuario SET Nome = '" .$Nome ."', Email = '" .$Email ."' WHERE idUsuario=" .$id) or die(mysqli_error($conexao));
				$resposta = mensagens(6);
			}
		}
	}
	return $resposta;
}

function ExcluirUsuario($id){
	
	//Recupera conteudo recebido na request
	$resposta = array();
	//Verifica se o id foi recebido
	if($id == 0){
		$resposta = mensagens(5);
	}
	else{
		include("conectar.php");
		
		//Evita SQL injection		
		$id = mysqli_real_escape_string($conexao,$id);
		
		//Exclui usuário no banco
		$query = mysqli_query($conexao, "DELETE FROM Usuario WHERE idUsuario=" .$id) or die(mysqli_error($conexao));
		$resposta = mensagens(7);
	}
	return $resposta;
}
?>