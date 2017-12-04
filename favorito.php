<?php
 
function InserirFavorito(){
	
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
		if(!isset($dados["idUsuario"]) || !isset($dados["idReceita"]))
		{
			$resposta = mensagens(3);
		}
		else{
			include("conectar.php");
			
			//Evita SQL injection
			$idUsuario = mysqli_real_escape_string($conexao,$dados["idUsuario"]);
			$idReceita = mysqli_real_escape_string($conexao,$dados["idReceita"]);
			
			//Consulta favorito no banco
			$query = mysqli_query($conexao,"SELECT idFavorito, idUsuario, idReceita FROM Favorito WHERE idUsuario=" .$idUsuario ." AND idReceita=" .$idReceita ."") or die(mysqli_error($conexao));
			
			//Verifica se foi retornado algum registro
			while($dados = mysqli_fetch_array($query))
			{
			  $favoritoCadastrado = true;
			  break;
			}
			
			if($favoritoCadastrado){
				$resposta = mensagens(11);
			}else{
				//Recupera o próximo ID de favorito
				$idFavorito = 0;
				$query = mysqli_query($conexao, "SELECT idFavorito FROM Favorito ORDER BY idFavorito DESC LIMIT 1") or die(mysqli_error($conexao));
				while($dados = mysqli_fetch_array($query)){
					$idFavorito = $dados["idFavorito"];
				}
				$idFavorito++;
				
				//Insere uma Receita aos favoritos dos Usuario
				$query = mysqli_query($conexao,"INSERT INTO Favorito VALUES(" .$idFavorito ."," .$idUsuario ."," .$idReceita .")") or die(mysqli_error($conexao));
				$resposta = mensagens(4);
			}
		}
	}
	return $resposta;
}

function ExcluirFavorito($id){
	
	//Recupera conteudo recebido na request
	$resposta = array();
	
	//Verifica se o id foi recebido
	if($id == 0){
		$resposta = mensagens(5);
	}
	else{
		include("conectar.php");
		
		//Evita SQL injection
		$idFavorito = mysqli_real_escape_string($conexao,$id);		
				
		//Exclui Favorito
		$query = mysqli_query($conexao, "DELETE FROM Favorito WHERE idFavorito = ".$idFavorito) or die(mysqli_error($conexao));
		
		$resposta = mensagens(7);
	}
	return $resposta;
}

//Usuario poder ver sua lista de receitas Favoritas
function ListarFavoritoPorUsuario(){

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
		if(!isset($dados["Email"]))
		{
			$resposta = mensagens(3);
		}
		else {
			include("conectar.php");
			
			//Evita SQL injection
			$Email = mysqli_real_escape_string($conexao,$dados["Email"]);
			
			$query = mysqli_query($conexao,"SELECT F.idFavorito, F.idUsuario, F.idReceita, R.Nome, R.TempoPreparo, R.Porcoes, R.ModoPreparo, 
											R.Dicas, R.Foto, R.idCategoria, U.Nome as 'NomeUsuario', U.Email  
											FROM Favorito as F LEFT JOIN Receita as R on F.idReceita = R.idReceita 
											LEFT JOIN Usuario as U on F.idUsuario = U.idUsuario
											WHERE U.Email = '" .$Email."'") or die(mysqli_error($conexao));
			
			//faz um looping e cria um array com os campos da consulta
			while($dados = mysqli_fetch_array($query))
			{
				$resposta[] = array('idFavorito' => $dados['idFavorito'],
									'idUsuario' => $dados['idUsuario'],
									'idReceita' => $dados['idReceita'],
									'Nome' => $dados['Nome'],
									'TempoPreparo' => $dados['TempoPreparo'],
									'Porcoes' => $dados['Porcoes'],
									'ModoPreparo' => $dados['ModoPreparo'],
									'Dicas' => $dados['Dicas'],
									'Foto' => $dados['Foto'],
									'idCategoria' => $dados['idCategoria'],
									'NomeUsuario' => $dados['NomeUsuario'],
									'Email' => $dados['Email']); 
			}
		}
	}
	return $resposta;
}
?>