<?php 
    include 'db_connections.php';
 	session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    } else if ($_SESSION["user_type"] != "admin") {
        echo "Nu ai drepturi pentru a vizualiza aceasta pagina.";
        exit;
    }

 	$link = db_Connect();

    $query = "SELECT username, user_type FROM Users WHERE user_type <> 'admin'"; 
    $result = $link->query($query);
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
            tr.header
            {
                font-weight:bold;
            }
            tr.alt
            {
                background-color: #777777;
            }
        </style>
        <script type="text/javascript">
            $(document).ready(function(){
               $('.striped tr:even').addClass('alt');
            });
        </script>
</head>
<body>
	<div>
        <h1>Buna, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Spor la treaba!</h1>
    </div>
    <p>
        <a href="logout.php">Sign Out of Your Supervisor Account</a>
    </p>

    <?php

    ?>
    <div>
        <label>Toti utilizatorii:</label>
        <table class="striped">
            <tr class="header">
                <td>Username</td>
                <td>Type</td>
            </tr>
            <?php
               while ($row = $result->fetch_assoc()) {
                   echo "<tr>";
                   echo "<td>".$row['username']."</td>";
                   echo "<td>".$row['user_type']."</td>";
                   echo "</tr>";
               }
               //$result->free();
            ?>
        </table>
    </div>
    <form action="drepturi.php" method="post">
        <div>
            <label>Introduceti username-ul utilizatorului caruia doriti sa-i schimbati rolul:</label>
            <input type="text" name="username" class="form-control">
        </div>
        <div>
            <label>Introduceti parola pentru a va verifica drepturile:</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div>
            <label>Alegeti noul rol al utilizatorului:</label>
            <select name="new_type">
                <option value="client">Client</option>
                <option value="org">Organizator</option>
                <option value="angajat">Angajat</option>
            </select>
        </div>
        <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Schimba rol">
        </div>
    </form>

<!--
    <form action="client_form.php" method="post">
    	<div>
            <label><h3>Alege tipul de serviciu dorit de grupul tau:</h3></label>
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
            /*
				if ($result->num_rows > 0) {
					echo "string";
					while($row = $result->fetch_assoc()) {
						echo '<option value="' . $row['nume_destinatie'] . '">' . $row['nume_destinatie'] . '</option>';
					}
				} else {
					echo 'Eroare';
				}
               */ 
			?>
		</select>
		

        <input type="text" name="data_plecare" placeholder="Perioada" id="startDatePicker" class="col-xs-2 px-lg-5">
		<input type="text" name="data_intoarcere" placeholder="Perioada" id="endDatePicker" class="col-xs-2 px-lg-5">

        <?php
        /*
        	echo "<select name='nr_pers'>";
            $range = range(1, 40);
            foreach ($range as $nr) {
                echo "<option value='". $nr . "'>". $nr . "</option>";
            }
            echo "</select>";
            */
        ?>

		<div class="form-group">
                <input type="submit" class="btn btn-primary" value="Rezerva">
        </div>
    </form>

    <script src="agentieturism.js"></script>
    -->
</body>
</html>