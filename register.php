<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'includes/session.php';
include 'includes/Enigma.php';

if (isset($_POST['signup'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];

    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['email'] = $email;
// if(!empty($_POST)){
    if (!isset($_SESSION['captcha'])) {
//			require('recaptcha/src/autoload.php');		
//			$recaptcha = new \ReCaptcha\ReCaptcha('6Lc5O6UZAAAAAJQZu2QDKPHe3AMgSLlkQD7GAXgX', new \ReCaptcha\RequestMethod\SocketPost());
//			$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
//
//			if (!$resp->isSuccess()){
//		  		$_SESSION['error'] = 'Please answer recaptcha correctly';
//		  		header('location: signup');	
//		  		exit();	
//		  	}	
//		  	else{
//		  		$_SESSION['captcha'] = time() + (10*60);
//		  	}


        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $repassword = $_POST['repassword'];
        $captcha = $_POST['g-recaptcha-response'];
        $secret = '6Lc5O6UZAAAAAM9Wz7gYlvBfSTWWciXORlkCyepe';
        if (!$captcha) {
            $_SESSION['error'] = 'Por favor resuelva el captcha antes de continuar';
            header('location: signup');
            exit();	
        }
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha");

        $arr = json_decode($response, TRUE);

        if ($arr['success']) {
            
        } else {
            $_SESSION['error'] = 'Se ha producido un error al verificar el captcha';
            header('location: signup');
            exit();	
        }
    }


// } 

    
    if ($password != $repassword) {
        $_SESSION['error'] = 'Passwords did not match';
        header('location: signup.php');
    } else {
        $conn = $pdo->open();

        $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        if ($row['numrows'] > 0) {
            $_SESSION['error'] = 'Este email ya se encuentra en uso';
            header('location: signup.php');
        } else {
            $now = date('Y-m-d');
            $password = password_hash($password, PASSWORD_DEFAULT);

            //generate code
            $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = substr(str_shuffle($set), 0, 12);

            try {
                $stmt = $conn->prepare("INSERT INTO users (email, password, type, firstname, lastname, activate_code, created_on) VALUES (:email, :password, :type, :firstname, :lastname, :code, :now)");
                $stmt->execute(['email' => $email, 'password' => $password, 'type' => 0, 'firstname' => $firstname, 'lastname' => $lastname, 'code' => $code, 'now' => $now]);
                $userid = $conn->lastInsertId();
                $idencrypt = Enigma::encryption($userid);
                $message = "
						<h2>Bienvenido a la familia TechUIO.</h2>
						<p>Los datos de tu cuenta son:</p>
						<p>Email: " . $email . "</p>
						<p>Clave: " . $_POST['password'] . "</p>
						<p>Para activar tu cuenta entra al siguiente enlace.</p>
						<a href='http://localhost/ecommerce/activate?token=" . $code . "&u=" . $idencrypt . "'>Activar Cuenta</a>
					";

                //Load phpmailer
                require 'vendor/autoload.php';

                $mail = new PHPMailer(true);
                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'fchicaiza.g1990@gmail.com';
                    $mail->Password = '1989Y1990g';
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port = 465;

                    $mail->setFrom('fchicaiza.g1990@gmail.com');

                    //Recipients
                    $mail->addAddress($email);
                    $mail->addReplyTo('fchicaiza.g1990@gmail.com');

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = 'ECommerce Site Sign Up';
                    $mail->Body = $message;

                    $mail->send();

                    unset($_SESSION['firstname']);
                    unset($_SESSION['lastname']);
                    unset($_SESSION['email']);

                    $_SESSION['success'] = 'Account created. Check your email to activate.';
                    header('location: signup.php');
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
                    header('location: signup.php');
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = $e->getMessage();
                header('location: register.php');
            }

            $pdo->close();
        }
    }
} else {
    $_SESSION['error'] = 'Fill up signup form first';
    header('location: signup');
}
?>