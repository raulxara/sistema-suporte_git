<?php
		use PHPMailer\PHPMailer\PHPMailer;
		use PHPMailer\PHPMailer\Exception;
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


<?php

	if(isset($_POST['acao'])){
		$email = $_POST['email'];
		$pergunta = $_POST['pergunta'];
		$token = md5(uniqid());
		$sql = \MySql::conectar()->prepare("INSERT INTO chamados VALUES (null,?,?,?)");
		$sql->execute(array($pergunta,$email,$token));
		//Enviar e-mail para o usuário dizendo que o chamado foi aberto.
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
		    $mail->Subject = 'Seu chamado foi aberto!';
		    $url = BASE.'chamado?token='.$token;
		    $informacoes = '
		    Olá, seu chamado foi criado com sucesso!<br />Utilize o link abaixo para interagir:<br />
		    <a href="'.$url.'">Acessar chamado!</a>
		    ';
		    $mail->Body    = $informacoes;

		    $mail->send();
		} catch (Exception $e) {
		    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
		}
		/*Fim do envio de e-mail*/
		echo '<script>alert("Seu chamado foi aberto com sucesso! Você receberá no e-mail as informações para interagir.")</script>';
	}
?>
<div class="box-center">
	<div class="container">
		<h2><i class="fa-regular fa-comments"></i> Suporte Personalizado - Tire sua dúvida aqui!</h2>
		<h1>*As perguntas são protegidas entre você e o suporte.</h1>
		<form method="post">
			<input type="email" name="email" placeholder="E-mail...">
			<br />
			<br />
			<textarea name="pergunta" placeholder="Qual sua dúvida?"></textarea>
			<br />
			<br />
			<input type="submit" name="acao" value="Enviar">
		</form>
	<div class="clear"></div>
	</div>
</div>

</body>
</html>



