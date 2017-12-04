<?php
$ServerBD = "mysql.hostinger.com.br";
$UserBD = "u487200405_vegs";
$PassBD = "r3c31t45v3g";
$BD = "u487200405_bdveg";

if(!isset($PortaBD)){
    $conexao = mysqli_connect($ServerBD, $UserBD, $PassBD);
}else{
    $conexao = mysqli_connect($ServerBD .":" .$PortaBD, $UserBD, $PassBD);   
}

// Caso a conexão seja reprovada, exibe na tela uma mensagem de erro
if (!$conexao) die ('<div id="erro2">Falha na conexao com o Banco de Dados!</div>');

// Caso a conexão seja aprovada, então conecta o Banco de Dados.	
$db = mysqli_select_db($conexao, $BD);

?>