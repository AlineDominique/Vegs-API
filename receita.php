<?php

function InserirReceita(){
	
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
		if(!isset($dados["Nome"]) || !isset($dados["TempoPreparo"]) || !isset($dados["Porcoes"]) || !isset($dados["ModoPreparo"]) 
			|| !isset($dados["idCategoria"]) || !isset($dados["idUsuario"]))
		{
			$resposta = mensagens(3);
		}
		else{
			include("conectar.php");
			
			//Evita SQL injection
			$Nome = mysqli_real_escape_string($conexao,$dados["Nome"]);
			$TempoPreparo = mysqli_real_escape_string($conexao,$dados["TempoPreparo"]);
			$Porcoes = mysqli_real_escape_string($conexao,$dados["Porcoes"]);
			$ModoPreparo = mysqli_real_escape_string($conexao,$dados["ModoPreparo"]);			
			$Dicas = mysqli_real_escape_string($conexao,$dados["Dicas"]);
			$Foto = mysqli_real_escape_string($conexao,$dados["Foto"]);
			$idCategoria = mysqli_real_escape_string($conexao,$dados["idCategoria"]);
			$idUsuario = mysqli_real_escape_string($conexao,$dados["idUsuario"]);
			
			//Faz upload da imagem
			$caminhoFoto = uploadDeFotos($Foto);

			//Recupera idIngrediente para incrementar 1
			$idReceita = 0;
			$query = mysqli_query($conexao, "SELECT idReceita FROM Receita ORDER BY idReceita DESC LIMIT 1") or die(mysqli_error($conexao));
			while($dados = mysqli_fetch_array($query)){
				$idReceita = $dados["idReceita"];
			}
			$idReceita++;
			
			//Insere Ingrediente
			$query = mysqli_query($conexao,"INSERT INTO Receita VALUES(" .$idReceita .",'" .$Nome."','" .$TempoPreparo ."','" .$Porcoes ."','" .$ModoPreparo ."','" .$Dicas ."','" .$caminhoFoto ."'," .$idCategoria ."," .$idUsuario .")") or die(mysqli_error($conexao));
			$resposta = mensagens(4);
		}
	}
	return $resposta;
}

function AtualizarReceita($id){
	
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
			if(!isset($dados["Nome"]) || !isset($dados["TempoPreparo"]) || !isset($dados["Porcoes"]) || !isset($dados["ModoPreparo"]) 
				|| !isset($dados["idCategoria"]) || !isset($dados["idUsuario"]))
		{
			$resposta = mensagens(3);
		}
			else{
				include("conectar.php");
			
				//Evita SQL injection
				$Nome = mysqli_real_escape_string($conexao,$dados["Nome"]);
				$TempoPreparo = mysqli_real_escape_string($conexao,$dados["TempoPreparo"]);
				$Porcoes = mysqli_real_escape_string($conexao,$dados["Porcoes"]);
				$ModoPreparo = mysqli_real_escape_string($conexao,$dados["ModoPreparo"]);			
				$Dicas = mysqli_real_escape_string($conexao,$dados["Dicas"]);
				$Foto = mysqli_real_escape_string($conexao,$dados["Foto"]);
				$idCategoria = mysqli_real_escape_string($conexao,$dados["idCategoria"]);
				$idUsuario = mysqli_real_escape_string($conexao,$dados["idUsuario"]);
						
				$update = "UPDATE Receita SET  Nome = '" .$Nome ."', TempoPreparo = '" .$TempoPreparo ."', Porcoes = '" .$Porcoes ."', ModoPreparo = '" .$ModoPreparo ."', Dicas = '" .$Dicas ."', idCategoria = " .$idCategoria . ", idUsuario = " .$idUsuario;
				
				if($Foto != ""){
					//Faz upload da imagem
					$caminhoFoto = uploadDeFotos($Foto);
					
					$update .= ", Foto = '" .$caminhoFoto ."'";
				}
				$update .= "WHERE idReceita = ".$id;
				
				//Atualiza Receita no banco
				$query = mysqli_query($conexao, $update) or die(mysqli_error($conexao));
				$resposta = mensagens(6);
			}
		}
	}
	return $resposta;
}

function ExcluirReceita($id){
	
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
		
		//Exclui Receita
		$query = mysqli_query($conexao, "DELETE FROM Receita WHERE idReceita=" .$id) or die(mysqli_error($conexao));
		
		$resposta = mensagens(7);
	}
	return $resposta;
}

function ListarReceitasPorCategoria($id){
	include("conectar.php");
	
	//Recebe a resposta das mensagens
	$resposta = array();
	$id = mysqli_real_escape_string($conexao,$id);
	
	//Consulta animal no banco
	if($id == 0){
		$query = mysqli_query($conexao,"SELECT R.idReceita, R.Nome, R.TempoPreparo, R.Porcoes, R.ModoPreparo, 
										R.Dicas, R.Foto, R.idCategoria, R.idUsuario, U.Nome as 'NomeUsuario', U.Email, C.Nome as 'NomeCategoria' 
										FROM Receita as R INNER JOIN Categoria as C on R.idCategoria = C.idCategoria INNER JOIN Usuario as U on R.idUsuario = U.idUsuario ORDER BY R.Nome") or die(mysqli_error($conexao));
	}else{
		$query = mysqli_query($conexao,"SELECT R.idReceita, R.Nome, R.TempoPreparo, R.Porcoes, R.ModoPreparo, 
										R.Dicas, R.Foto, R.idCategoria, R.idUsuario, U.Nome as 'NomeUsuario', U.Email, C.Nome as 'NomeCategoria'
										FROM Receita as R INNER JOIN Categoria as C on R.idCategoria = C.idCategoria INNER JOIN Usuario as U on R.idUsuario = U.idUsuario
										WHERE R.idCategoria = " .$id ." ORDER BY R.Nome") or die(mysqli_error($conexao));
	}
	
	//faz um looping e cria um array com os campos da consulta
	while($dados = mysqli_fetch_array($query))
	{
		$resposta[] = array('idReceita' => $dados['idReceita'],
							'Nome' => $dados['Nome'],
							'TempoPreparo' => $dados['TempoPreparo'],
							'Porcoes' => $dados['Porcoes'],
							'ModoPreparo' => $dados['ModoPreparo'],
							'Dicas' => $dados['Dicas'],
							'Foto' => $dados['Foto'],
							'idCategoria' => $dados['idCategoria'],
							'idUsuario' => $dados['idUsuario'],
							'NomeUsuario' => $dados['NomeUsuario'],
							'Email' => $dados['Email'],
							'NomeCategoria' => $dados['NomeCategoria'],); 
	}
	return $resposta;	
}

function ListarReceitasPorUsuario(){
	
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
		else{
			include("conectar.php");
			
			//Evita SQL injection
			$Email = mysqli_real_escape_string($conexao,$dados["Email"]);

			$query = mysqli_query($conexao,"SELECT R.idReceita, R.Nome, R.TempoPreparo, R.Porcoes, R.ModoPreparo, 
										R.Dicas, R.Foto, R.idCategoria, R.idUsuario, U.Nome as 'NomeUsuario', U.Email, C.Nome as 'NomeCategoria'
										FROM Receita as R INNER JOIN Categoria as C on R.idCategoria = C.idCategoria INNER JOIN Usuario as U on R.idUsuario = U.idUsuario
										WHERE U.Email = '" .$Email ."'") or die(mysqli_error($conexao));
			
			//faz um looping e cria um array com os campos da consulta
			while($dados = mysqli_fetch_array($query))
			{
				$resposta[] = array('idReceita' => $dados['idReceita'],
									'Nome' => $dados['Nome'],
									'TempoPreparo' => $dados['TempoPreparo'],
									'Porcoes' => $dados['Porcoes'],
									'ModoPreparo' => $dados['ModoPreparo'],
									'Dicas' => $dados['Dicas'],
									'Foto' => $dados['Foto'],
									'idCategoria' => $dados['idCategoria'],
									'idUsuario' => $dados['idUsuario'],
									'NomeUsuario' => $dados['NomeUsuario'],
									'Email' => $dados['Email'],
									'NomeCategoria' => $dados['NomeCategoria'],); 
			}
		}
	}
	return $resposta;
}


function RecuperarIdReceita(){
	
	//Recupera conteudo recebido na request
	$conteudo = file_get_contents("php://input");
	
	//Verifica se o conteudo foi recebido
	if(empty($conteudo)){
		$resposta = mensagens(2);
	}
	else{
		//Converte o json recebido pra array
		$dados = json_decode($conteudo,true);
		
		//Verifica se as infromações esperadas foram recebidas
		if(!isset($dados["Cod"])){
			$resposta = mensagens(3);
		}
		else{
			include("conectar.php");
			
			//Evita SQL injection
			$Cod = mysqli_real_escape_string($conexao,$dados["Cod"]);
			
			//Recupera idReceita
			$query = mysqli_query($conexao, "SELECT idReceita FROM Receita ORDER BY idReceita DESC LIMIT 1") or die(mysqli_error($conexao));
			while($dados = mysqli_fetch_array($query)){
				$idReceita = $dados["idReceita"];
			}
			
		}
	}
	return $idReceita;
}

function ListarReceitas($id){
	include("conectar.php");
	
	//Recebe a resposta das mensagens
	$resposta = array();
	$id = mysqli_real_escape_string($conexao,$id);
	
	//Consulta animal no banco
	if($id == 0){
		$query = mysqli_query($conexao,"SELECT R.idReceita, R.Nome, R.TempoPreparo, R.Porcoes, R.ModoPreparo, 
										R.Dicas, R.Foto, R.idCategoria, R.idUsuario, U.Nome as 'NomeUsuario', U.Email, C.Nome as 'NomeCategoria' 
										FROM Receita as R INNER JOIN Categoria as C on R.idCategoria = C.idCategoria INNER JOIN Usuario as U on R.idUsuario = U.idUsuario") or die(mysqli_error($conexao));
	}else{
		$query = mysqli_query($conexao,"SELECT R.idReceita, R.Nome, R.TempoPreparo, R.Porcoes, R.ModoPreparo, 
										R.Dicas, R.Foto, R.idCategoria, R.idUsuario, U.Nome as 'NomeUsuario', U.Email, C.Nome as 'NomeCategoria'
										FROM Receita as R INNER JOIN Categoria as C on R.idCategoria = C.idCategoria INNER JOIN Usuario as U on R.idUsuario = U.idUsuario
										WHERE R.idReceita = " .$id) or die(mysqli_error($conexao));
	}
	
	//faz um looping e cria um array com os campos da consulta
	while($dados = mysqli_fetch_array($query))
	{
		$resposta[] = array('idReceita' => $dados['idReceita'],
							'Nome' => $dados['Nome'],
							'TempoPreparo' => $dados['TempoPreparo'],
							'Porcoes' => $dados['Porcoes'],
							'ModoPreparo' => $dados['ModoPreparo'],
							'Dicas' => $dados['Dicas'],
							'Foto' => $dados['Foto'],
							'idCategoria' => $dados['idCategoria'],
							'idUsuario' => $dados['idUsuario'],
							'NomeUsuario' => $dados['NomeUsuario'],
							'Email' => $dados['Email'],
							'NomeCategoria' => $dados['NomeCategoria'],); 
	}
	return $resposta;	
}

function PesquisarReceitas($pesquisa){
	include("conectar.php");
	
	//Recebe a resposta das mensagens
	$resposta = array();
	$pesquisa = mysqli_real_escape_string($conexao,$pesquisa);
	$pesquisa = utf8_encode($pesquisa);
	
	//Consulta Tabela Receita no BD
	if($pesquisa == null){
		$resposta = mensagem(2);
	}else{
		$query = mysqli_query($conexao,"SELECT r.idReceita, r.Nome as 'NomeReceita', r.TempoPreparo, r.Porcoes, r.ModoPreparo, r.Dicas,
		r.Foto, r.idCategoria, c.Nome as 'NomeCategoria', u.idUsuario, u.Nome as 'NomeUsuario', i.Nome as 'NomeIngrediente' FROM Receita as r LEFT JOIN Ingrediente as i on r.idReceita = i.idReceita
		LEFT JOIN Usuario as u on r.idUsuario = u.idUsuario LEFT JOIN Categoria as c on r.idCategoria = c.idCategoria
		WHERE r.Nome LIKE '%". $pesquisa ."%' OR i.Nome LIKE '%". $pesquisa ."%' ORDER BY r.Nome") or die(mysqli_error($conexao));
	
	//faz um looping e cria um array com os campos da consulta
	while($dados = mysqli_fetch_array($query))
	{
		$resposta[] = array('idReceita' => $dados['idReceita'],
							'Nome' => $dados['NomeReceita'],
							'TempoPreparo' => $dados['TempoPreparo'],
							'Porcoes' => $dados['Porcoes'],
							'ModoPreparo' => $dados['ModoPreparo'],
							'Dicas' => $dados['Dicas'],
							'Foto' => $dados['Foto'],
							'idCategoria' => $dados['idCategoria'],
							'NomeCategoria' => $dados['NomeCategoria'],
							'idUsuario' => $dados['idUsuario'],
							'NomeUsuario' => $dados['Nome'],
							'NomeIngrediente' => $dados['NomeIngrediente']); 
	}
	return $resposta;
	}
}
?>