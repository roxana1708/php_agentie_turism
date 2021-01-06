<?php 
 	session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

 	define('DB_SERVER', 'sql203.epizy.com');
    define('DB_USERNAME', 'epiz_27106493');
    define('DB_PASSWORD', 'xGHEC6jV1X');
    define('DB_NAME', 'epiz_27106493_agentie_turism');
     
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
     
    if (!$link) {
        echo "Error: Unable to connect to MySQL.";
        exit;
    }

    $query = "SELECT * FROM Destinatii"; 
    $result = $link->query($query);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Client</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  	<link rel="stylesheet" href="/resources/demos/style.css">

	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
	<div>
        <h1>Buna, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Bine ai venit!</h1>
    </div>
    <p>
        <a href="logout.php">Sign Out of Your Account</a>
    </p>

    <form action="client_form.php" method="post">
    	<div>
            <label><h3>Alege tipul de serviciu pe care vrei sa il rezervi:</h3></label>
            <select name="tip">
                <option value="b_a">Bilete de avion</option>
                <option value="h">Sejur Hotel</option>
                <option value="t">Tur</option>
                <option value="p">Pachet</option>
            </select>
        </div>

        <select id="plecare" name="plecare" class="col-xs-2 px-lg-5">
			<option value="Bucuresti">Bucuresti</option>
			<option value="Brasov">Brasov</option>
			<option value="Cluj">Cluj</option>
			<option value="Timisoara">Timisoara</option>
			<option value="Iasi">Iasi</option>
		</select>

        <select id="destinatie" name="destinatie" class="col-xs-2 px-lg-5">
			<option value="">Alege destinatie</option>
			<?php
				if ($result->num_rows > 0) {
					echo "string";
					while($row = $result->fetch_assoc()) {
						echo '<option value="' . $row['nume_destinatie'] . '">' . $row['nume_destinatie'] . '</option>';
					}
				} else {
					echo 'Eroare';
				}
			?>
		</select>
		

        <input type="text" name="data_plecare" placeholder="Perioada" id="startDatePicker" class="col-xs-2 px-lg-5">
		<input type="text" name="data_intoarcere" placeholder="Perioada" id="endDatePicker" class="col-xs-2 px-lg-5">

		<select name="nr_pers">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
		</select>

		<div class="form-group">
                <input type="submit" class="btn btn-primary" value="Rezerva">
        </div>
    </form>
    <script src="agentieturism.js"></script>
</body>
</html>