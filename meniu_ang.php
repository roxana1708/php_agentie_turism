<?php
	session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    } else if ($_SESSION["user_type"] != "angajat") {
        echo "Nu ai drepturi pentru a vizualiza aceasta pagina.";
        exit;
    }
?>

<!DOCTYPE html>
<html>
<head>
	<title>Supervizor</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  	<link rel="stylesheet" href="/resources/demos/style.css">

	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

  	<style type="text/css">
  		.button {
		  font: bold 18px Arial;
		  text-decoration: none;
		  background-color: #EEEEEE;
		  color: #333333;
		  padding: 2px 6px 2px 6px;
		  border-top: 1px solid #CCCCCC;
		  border-right: 1px solid #333333;
		  border-bottom: 1px solid #333333;
		  border-left: 1px solid #CCCCCC;
		}
  	</style>
</head>
<body>
	<div>
        <h1>Buna, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Speram ca grupul tau este gata de calatorie!</h1>
    </div>
    <p>
        <a href="logout.php">Sign Out of Your Organizer Account</a>
    </p>
    <div>
    	<a href="rezerva.php" class="button">Rezerva calatorie</a>
    	<a href="rezerva_facuta_de.php" class="button">Rezerva calatorie pentru client/angajat</a>
    	<a href="istoric_comenzi.php" class="button">Istoric comenzi</a>
    	<a href="istoric_comenzi_facute_de.php" class="button">Istoric comenzi client/angajat facute de tine</a>
    </div>
    

</body>
</html>