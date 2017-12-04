<?php
// Função para de Uploads de Fotos no Banco de Dados.
function uploadDeFotos($arquivo){
	
	$url = "http://www.infofatec.hol.es/fotos/"; //Altera para o meu BD
	$image_name = "img_".date("Y-m-d-H-m-s")."_".uniqid().".jpg"; 
	$path = $url .$image_name;
	$arquivo = str_replace(' ', '+', $arquivo);
	$arquivo = str_replace('\n', '', $arquivo);
	$binary = base64_decode($arquivo);
	
	file_put_contents("/home/u487200405/public_html/fotos/" . $image_name, $binary);// Criar pasta no Hostinger para armazenar fotos receitas e trocar o nome
	
	return $path;
}
?>