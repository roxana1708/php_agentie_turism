<?php
	include 'db_connections.php';
	session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

    $id_client = $_SESSION["id"];

    $link = db_Connect();
    $result = db_select_istoric_comenzi($id_client);
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
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
	<table class="striped">
            <tr class="header">
                <td>ID Rezervare</td>
                <td>Tip Serviciu</td>
                <td>Facuta de</td>
                <td>Numar persoane</td>
                <td>Data rezervarii</td>
            </tr>
            <?php
               while ($row = $result->fetch_assoc()) {
                   echo "<tr>";
                   echo "<td>".$row['id_rezervare']."</td>";
                   if($row['tip'] == 'b_a') {
                      echo "<td>Bilete Avion</td>";
                   } elseif ($row['tip'] == 'h') {
                      echo "<td>Sejur Hotel</td>";
                   } elseif ($row['tip'] == 't') {
                     echo "<td>Tur</td>";
                   } else {
                    echo "<td>Pachet</td>";
                   }
                   echo "<td>".$row['facuta_de']."</td>";
                   echo "<td>".$row['nr_pers']."</td>";
                   echo "<td>".$row['data_rezervare']."</td>";
                   echo "</tr>";
               }
            ?>
        </table>

</body>
</html>