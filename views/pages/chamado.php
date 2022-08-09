<?php
	$token = $_GET['token'];
?>
<!DOCTYPE html>
<html>
<head>

	<title>SuportPlug</title>
	<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="fonts-6/css/all.css">
	<link href="css3/style.css" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="Keywords" content="soporte para site">
	<meta name="description" content="tirar qualquer dúvida com suporte personalizado">
	<meta charset="utf-8">
	<meta name="author" content="Raul Nascimento Cruz">
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	
</head>
<body>

<div class="box-center">
	<div class="container">
		<h2>Visualizando chamado</h2>
		<h1>Token: <?php echo $token; ?></h1>

		<hr>

		<h1>Pergunta do suporte: <?php echo $info['pergunta'] ?></h1>

	

<?php
	$puxarInteracoes = \MySql::conectar()->prepare("SELECT * FROM interacao_chamado WHERE id_chamado = ?");
	echo '<hr>';

	$puxarInteracoes->execute(array($token));
	$puxarInteracoes = $puxarInteracoes->fetchAll();
	foreach ($puxarInteracoes as $key => $value) {
		if($value['admin'] == 1){
			echo '<h1><b>Admin: </b>'.$value['mensagem'].'</h1>';
		}else{
			echo '<h1><b>Você: </b>'.$value['mensagem'].'</h1>';
		}
		echo '<hr>';
	}
?>

<?php
	if(isset($_POST['responder_chamado'])){
		$mensagem = $_POST['mensagem'];
		$sql = \MySql::conectar()->prepare("INSERT INTO interacao_chamado VALUES (null,?,?,?,0)");
		$sql->execute(array($token,$mensagem,-1));

		echo '<script>alert("Sua resposta foi enviada com sucesso! Aguarde o admin responde-lo(a) :)")</script>';
		echo '<script>location.href="'.BASE.'chamado?token='.$token.'"</script>';
		die();
	}

	$sql = \MySql::conectar()->prepare("SELECT * FROM interacao_chamado WHERE id_chamado = ? ORDER BY id DESC");
	$sql->execute(array($token));
	if($sql->rowCount() == 0){
		echo '<h1>Aguarde até ter uma resposta do admin para continuar com o suporte!</h1>';
	}else{
		$info = $sql->fetchAll();
		if($info[0]['admin'] == -1){
			//A última interação foi feita por quem abriu o suporte. Não pode interagir até ter uma resposta.
			echo '<h1>Aguarde até ter uma resposta do admin para continuar com o suporte!</h1>';
		}else{
			echo '<form method="post">
				<textarea name="mensagem" placeholder="Sua resposta..."></textarea><br />
				<input type="submit" name="responder_chamado" value="Enviar" />
			</form>';
		}
	}
?>

	<div class="clear"></div>
	</div>
</div>
</body>
</html>
