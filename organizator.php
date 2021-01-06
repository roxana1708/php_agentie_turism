<?php 
    include 'db_connections.php';
 	session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    } else if ($_SESSION["user_type"] != "org") {
        echo "Nu ai drepturi pentru a vizualiza aceasta pagina.";
        exit;
    }

    if(isset($_SESSION['err_msg'])) {
        $tip_serv_err = $_SESSION['err_msg']['tip_serv_err'];
        $nr_pers_err = $_SESSION['err_msg']['nr_pers_err'];
        $date_err = $_SESSION['err_msg']['date_err'];
        $departure_err = $_SESSION['err_msg']['departure_err'];
        $arrival_err = $_SESSION['err_msg']['arrival_err'];
        unset($_SESSION['err_msg']);
    }
 	
    $link = db_Connect();

    $query = "SELECT nume_destinatie FROM Destinatii"; 
    $result = $link->query($query);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Organizator</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  	<link rel="stylesheet" href="/resources/demos/style.css">

	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
	<div>
        <h1>Buna, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Speram ca grupul tau este gata de calatorie!</h1>
    </div>
    <p>
        <a href="logout.php">Sign Out of Your Organizer Account</a>
    </p>

    <form action="client_form.php" method="post">
    	<div>
            <label>Alege tipul de serviciu dorit de grupul tau:</label>
            <select name="tip">
                <option value="b_a">Bilete de avion</option>
                <option value="h">Sejur Hotel</option>
                <option value="t">Tur</option>
                <option value="p">Pachet</option>
            </select>
            <span style="color:red"><?php echo $tip_serv_err ?></span>
        </div>

        <div>
            <label>Alege orasul din care pleaca grupul tau:</label>
            <select id="plecare" name="plecare" class="col-xs-2 px-lg-5">
    			<option value="Bucuresti">Bucuresti</option>
    			<option value="Brasov">Brasov</option>
    			<option value="Cluj">Cluj</option>
    			<option value="Timisoara">Timisoara</option>
    			<option value="Iasi">Iasi</option>
    		</select>
            <span style="color:red"><?php echo $departure_err ?></span>
        </div>

        <div>
            <label>Alege destinatia dorita de grupul tau:</label>
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
            <span style="color:red"><?php echo $arrival_err ?></span>
        </div>
		
        <div>
            <label>Alege data plecarii:</label>
            <input type="text" name="data_plecare" placeholder="Perioada" id="startDatePicker" class="col-xs-2 px-lg-5">
        </div>
        
        <div>
            <label>Alege data plecarii:</label>
    		<input type="text" name="data_intoarcere" placeholder="Perioada" id="endDatePicker" class="col-xs-2 px-lg-5">
            <span style="color:red"><?php echo $date_err ?></span>
        </div>

        <div>
            <label>Alege numarul de participanti:</label>
            <?php
            	echo "<select name='nr_pers'>";
                $range = range(6, 40);
                foreach ($range as $nr) {
                    echo "<option value=$nr>$nr</option>";
                }
                echo "</select>";
            ?>
            <span style="color:red"><?php echo $nr_pers_err ?></span>
        </div>

		<div class="form-group">
                <input type="submit" class="btn btn-primary" value="Rezerva">
        </div>
    </form>
    <script src="agentieturism.js"></script>
</body>
</html>