<?php include 'includes/session.php'; ?>
<?php
include 'includes/Enigma.php';
	$output = '';
	if(!isset($_GET['token']) OR !isset($_GET['u'])){
		$output .= '
			<div class="alert alert-danger">
                <h4><i class="icon fa fa-warning"></i> Error!</h4>
                Este código ya fué usado para activar una cuenta
            </div>
            <h4>Intenta <a href="signup.php">Iniciar Sesión</a> o volver a la página de<a href="index.php">Inicio</a>.</h4>
		'; 
	}
	else{
		$conn = $pdo->open();
                $iddecrypt = Enigma::decryption($_GET['u']);
		$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE activate_code=:token AND id=:id");
		$stmt->execute(['token'=>$_GET['token'], 'id'=>$iddecrypt]);
		$row = $stmt->fetch();

		if($row['numrows'] > 0){
			if($row['status']){
				$output .= '
					<div class="alert alert-danger">
		                <h4><i class="icon fa fa-warning"></i> Error!</h4>
		                Ésta cuenta ya fué activada.
		            </div>
		            <h4>Intenta <a href="signup.php">Iniciar Sesión</a> o volver a la página de<a href="index.php">Inicio</a>.</h4>
				';
			}
			else{
				try{
					$stmt = $conn->prepare("UPDATE users SET status=:status WHERE id=:id");
					$stmt->execute(['status'=>1, 'id'=>$row['id']]);
					$output .= '
						<div class="alert alert-success">
			                <h4><i class="icon fa fa-check"></i> Listo!</h4>
			               La cuenta fué activada con el email: <b>'.$row['email'].'</b>.
			            </div>
			            <h4>Ahora puedes <a href="login.php">Iniciar Sesión</a> o volver a la pagina de <a href="index.php">Inicio</a>.</h4>
					';
				}
				catch(PDOException $e){
					$output .= '
						<div class="alert alert-danger">
			                <h4><i class="icon fa fa-warning"></i> Eror!</h4>
			                '.$e->getMessage().'
			            </div>
			            <h4>Intenta  <a href="signup.php">Iniciar Sesiónm</a> o volver a la página de <a href="index.php">Inicio</a>.</h4>
					';
				}

			}
			
		}
		else{
			$output .= '
				<div class="alert alert-danger">
	                <h4><i class="icon fa fa-warning"></i> Error!</h4>
	                Imposible activar la cuenta, el código de activación es inválido.
	            </div>
	        <h4>Intenta <a href="signup.php">Iniciar Sesión</a> o volver a la página de<a href="index.php">Inicio</a>.</h4>
			';
		}

		$pdo->close();
	}
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

	<?php include 'includes/navbar.php'; ?>
	 
	  <div class="content-wrapper">
	    <div class="container">

	      <!-- Main content -->
	      <section class="content">
	        <div class="row">
	        	<div class="col-sm-9">
	        		<?php echo $output; ?>
	        	</div>
	        	<div class="col-sm-3">
	        		<?php include 'includes/sidebar.php'; ?>
	        	</div>
	        </div>
	      </section>
	     
	    </div>
	  </div>
  
  	<?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>