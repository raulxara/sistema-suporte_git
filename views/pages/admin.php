<?php
		use PHPMailer\PHPMailer\PHPMailer;
		use PHPMailer\PHPMailer\Exception;

	if(isset($_POST['responder_novo_chamado'])){
		$token = $_POST['token'];
		$email = $_POST['email'];
		$mensagem = $_POST['mensagem'];

		$sql = \MySql::conectar()->prepare("INSERT INTO interacao_chamado VALUES (null,?,?,?,1)");
		$sql->execute(array($token,$mensagem,1));
		//envio de e-mail
		$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
		try {
		    //Server settings
		    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
		    $mail->isSMTP();                                      // Set mailer to use SMTP
		    $mail->Host = 'vps.dankicode.com';  // Specify main and backup SMTP servers
		    $mail->SMTPAuth = true;                               // Enable SMTP authentication
		    $mail->Username = 'testes@dankicode.com';                 // SMTP username
		    $mail->Password = 'gui123456';                           // SMTP password
		    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		    $mail->Port = 465;  
		                                      // TCP port to connect to

		    //Recipients
		    $mail->setFrom('testes@dankicode.com', 'Guilherme');
		    $mail->addAddress($email, '');

		    //Content
		    $mail->isHTML(true);                                  // Set email format to HTML
		    $mail->CharSet = "UTF-8";
		    $mail->Subject = 'Nova interação no chamado: '.$token;
		    $url = BASE.'chamado?token='.$token;
		    $informacoes = '
		   	Uma nova interação foi feita no seu chamado!<br />Utilize o link abaixo para interagir:<br />
		    <a href="'.$url.'">Acessar chamado!</a>
		    ';
		    $mail->Body    = $informacoes;

		    $mail->send();
		} catch (Exception $e) {
		    //echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
		}
		echo '<script>alert("Você respondeu o usuário!")</script>';
	}else if(isset($_POST['responder_nova_interacao'] )){
		$mensagem = $_POST['mensagem'];

		$token = $_POST['token'];

		$email = \MySql::conectar()->prepare("SELECT * FROM chamados WHERE token = ?");
		$email->execute(array($token));
		$email = $email->fetch()['email'];
		\MySql::conectar()->exec("UPDATE interacao_chamado SET status = 1 WHERE id = $_POST[id]");

		$sql = \MySql::conectar()->prepare("INSERT INTO `interacao_chamado` VALUES (null,?,?,1,1)");

		$sql->execute(array($token,$mensagem));

		//envio de e-mail
		$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
		try {
		    //Server settings
		    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
		    $mail->isSMTP();                                      // Set mailer to use SMTP
		    $mail->Host = 'vps.dankicode.com';  // Specify main and backup SMTP servers
		    $mail->SMTPAuth = true;                               // Enable SMTP authentication
		    $mail->Username = 'testes@dankicode.com';                 // SMTP username
		    $mail->Password = 'gui123456';                           // SMTP password
		    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		    $mail->Port = 465;  
		                                      // TCP port to connect to

		    //Recipients
		    $mail->setFrom('testes@dankicode.com', 'Guilherme');
		    $mail->addAddress($email, '');

		    //Content
		    $mail->isHTML(true);                                  // Set email format to HTML
		    $mail->CharSet = "UTF-8";
		    $mail->Subject = 'Nova interação no chamado: '.$token;
		    $url = BASE.'chamado?token='.$token;
		    $informacoes = '
		   	Uma nova interação foi feita no seu chamado!<br />Utilize o link abaixo para interagir:<br />
		    <a href="'.$url.'">Acessar chamado!</a>
		    ';
		    $mail->Body    = $informacoes;

		    $mail->send();
		} catch (Exception $e) {
		    //echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
		}

		echo '<script>alert("Você respondeu o usuário!")</script>';
	}
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
<h2>Novos chamados</h2>
<?php
	$pegarChamados = \MySql::conectar()->prepare("SELECT * FROM chamados ORDER BY id DESC");
	$pegarChamados->execute();
	$pegarChamados = $pegarChamados->fetchAll();
	foreach ($pegarChamados as $key => $value) {
	$verificaInteracao = \MySql::conectar()->prepare("SELECT * FROM interacao_chamado WHERE id_chamado = '$value[token]'");
	$verificaInteracao->execute();
	if($verificaInteracao->rowCount() >= 1)
		continue;
?>
	<h1>Dúvida: " <?php echo $value['pergunta']; ?> "</h1>
	<form method="post">
		<textarea name="mensagem" placeholder="Sua resposta"></textarea>
		<br />
		<br />
		<input type="submit" name="responder_novo_chamado" value="Responder">
		<input type="hidden" name="token" value="<?php echo $value['token']; ?>">
		<input type="hidden" name="email" value="<?php echo $value['email']; ?>">
	</form>
<?php } ?>
<hr>

<h1>Últimas interações:</h1>
<?php
	$pegarChamados = \MySql::conectar()->prepare("SELECT * FROM interacao_chamado WHERE admin = -1 AND status = 0 ORDER BY id DESC");
	$pegarChamados->execute();
	$pegarChamados = $pegarChamados->fetchAll();
	foreach ($pegarChamados as $key => $value) {
?>
	<h1><?php echo $value['mensagem']; ?></h1>
	<h1>Clique <a href="<?php echo BASE ?>chamado?token=<?php echo $value['id_chamado']; ?>">aqui</a> para visualizar este chamado!</h1>
	<form method="post">
		<textarea name="mensagem" placeholder="Sua resposta"></textarea>
		<br />
		<br />
		<input type="hidden" name="id" value="<?php echo $value['id']; ?>">
		<input type="submit" name="responder_nova_interacao" value="Responder">
		<input type="hidden" name="token" value="<?php echo $value['id_chamado']; ?>">
	</form>
<?php } ?>
<div class="clear"></div>
	</div>
</div>
</body>
</html>