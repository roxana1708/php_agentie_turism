<?php

	function db_Connect() {
		define('DB_SERVER', 'sql203.epizy.com');
	    define('DB_USERNAME', 'epiz_27106493');
	    define('DB_PASSWORD', 'xGHEC6jV1X');
	    define('DB_NAME', 'epiz_27106493_agentie_turism');

	    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
     
	    if (!$link) {
	        echo "Error: Unable to connect to MySQL.";
	        exit;
	    }

	    return $link;
	}

	function db_insert_rezervare($facuta_de, $tip, $nr_pers, $id_client) {
		//echo $facuta_de . $tip . $nr_pers . $id_client;

		$link = db_Connect();
		$sql = "INSERT INTO Rezervari (facuta_de, tip, nr_pers, data_rezervare, id_client) VALUES (?, ?, ?, ?, ?)";
             
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "sssss", $param_by, $param_tip, $param_nr, $param_date, $param_clientID);
      
            $param_by = $facuta_de;
            $param_tip = $tip;
            $param_nr = $nr_pers;
            $param_date = $data_ = date("Y-m-d H:i:s");
            $param_clientID = $id_client;
                
            if(mysqli_stmt_execute($stmt)){
                //echo "Rezervare finalizata. Multumim!";
            } else{
                echo "Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
        
        //intoarcem id-ul rezervarii pe care tocmai am facut-o
        $last_id = mysqli_insert_id($link);
        return $last_id;
	}


	function db_select_istoric_comenzi($id_client) {
		$link = db_Connect();
		$query = "SELECT id_rezervare, facuta_de, tip, nr_pers, data_rezervare FROM Rezervari WHERE id_client = $id_client";
    	$result = $link->query($query);
    	return $result;
	}


	function db_select_istoric_comenzi_facute_de($id_client) {
		$link = db_Connect();
		$query = "SELECT id_rezervare, tip, nr_pers, data_rezervare, id_client FROM Rezervari WHERE facuta_de = $id_client";
    	$result = $link->query($query);
    	return $result;
	}

	function db_select_nume_prenume($id_client) {
		//echo "aaaaaa";
		$link = db_Connect();
		$query = "SELECT nume, prenume FROM Users WHERE user_id = $id_client";
		$result = $link->query($query);
		$row = $result->fetch_assoc();
		//echo $row['nume'];
		//echo $row['prenume'];
		return array($row['nume'], $row['prenume']);
	}

	function get_aeroport($dest) {
		$link = db_Connect();

		$query = "SELECT aeroport FROM Destinatii WHERE nume_destinatie = '$dest'";
		$result = $link->query($query);
		$row = $result->fetch_assoc();

		return $row['aeroport'];
	}

	function db_select_id_cursa($plecare, $destinatie, $data_plecare) {
		$link = db_Connect();

		$aeroport_plecare = get_aeroport($plecare);
		$aeroport_dest = get_aeroport($destinatie);


		$query = "SELECT id_cursa FROM Curse WHERE data_decolare = concat('$data_plecare', ' 00:00:00') and aeroport_plecare = '$aeroport_plecare' and aeroport_sosire = '$aeroport_dest'";
		$result = $link->query($query);
		$row = $result->fetch_assoc();

		return $row['id_cursa'];
	}

	function verify_rzv_avion($plecare, $destinatie, $data_plecare, $id_client) {
		$link = db_Connect();

		$id_cursa = db_select_id_cursa($plecare, $destinatie, $data_plecare);

		$query = "SELECT r.id_client from Rezervari r, BileteAvion b where r.id_rezervare = b.id_rezervare and b.id_cursa = '$id_cursa' and r.id_client = '$id_client'";
		$result = $link->query($query);
		//$row = $result->fetch_assoc();

		if($result->num_rows > 0) {
			return 0;
		} else {
			return 1;
		}
	}

	function verify_availability_avion($plecare, $destinatie, $data_plecare, $nr_pers) {
		$id_cursa = db_select_id_cursa($plecare, $destinatie, $data_plecare);

		$link = db_Connect();

		$query = "SELECT sum(nr_pers) from Rezervari r, BileteAvion b where r.id_rezervare = b.id_rezervare and b.id_cursa = '$id_cursa'";
		$result = $link->query($query);
		$row = $result->fetch_assoc();

		if($row['sum(nr_pers)'] + $nr_pers <= 250) {
			return 1;
		} else {
			return 0;
		}
	}

	function db_insert_bilete_avion($id_rezervare, $id_client, $plecare, $destinatie, $data_plecare, $data_intoarcere) {

		//echo $id_rezervare . $id_client . $plecare . $destinatie . $data_plecare . $data_intoarcere;

		$link = db_Connect();
		
		$nume_prenume = db_select_nume_prenume($id_client);
		$nume = $nume_prenume[0];
		$prenume = $nume_prenume[1];
		$id_cursa = db_select_id_cursa($plecare, $destinatie, $data_plecare);

		//ECHO $id_cursa;

		$sql = "INSERT INTO BileteAvion (id_rezervare, id_cursa, nume_pasager, prenume_pasager) VALUES (?, ?, ?, ?)";


		if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ssss", $param_id_rezervare, $param_id_cursa, $param_nume, $param_prenume);
      
            $param_id_rezervare = $id_rezervare;
            $param_id_cursa = $id_cursa;
            $param_nume = $nume;
            $param_prenume = $prenume;
                
            if(mysqli_stmt_execute($stmt)){
                //echo "Rezervare finalizata. Multumim!";
            } else{
                echo "Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }

	}

	function get_id_dest($dest) {
		$link = db_Connect();

		$query = "SELECT id_destinatie FROM Destinatii WHERE nume_destinatie = '$dest'";
		$result = $link->query($query);
		$row = $result->fetch_assoc();
		return $row['id_destinatie'];
	}

	function get_id_hotel($id_dest, $nr_stele) {
		$link = db_Connect();

		$query = "SELECT id_hotel FROM Hoteluri WHERE id_destinatie = '$id_dest' AND nr_stele = '$nr_stele'";
		$result = $link->query($query);
		$row = $result->fetch_assoc();
		return $row['id_hotel'];
	}

	function verify_rzv_hotel($destinatie, $data_plecare, $data_intoarcere, $id_client, $nr_stele) {
		$link = db_Connect();

		//echo "fffffff";
		$id_dest = get_id_dest($destinatie);
		$id_hotel = get_id_hotel($id_dest, $nr_stele);

		//echo $id_hotel;

		$query = "SELECT r.id_client, s.data_checkin, s.data_checkout from Rezervari r, SejururiCazare s where r.id_rezervare = s.id_rezervare and s.id_hotel = '$id_hotel' and r.id_client = '$id_client' and (s.data_checkin between '$data_plecare' and '$data_intoarcere' or s.data_checkout between '$data_plecare' and '$data_intoarcere' or (s.data_checkin < '$data_plecare' and s.data_checkout > '$data_intoarcere'))";

		//s.data_checkin = concat('$data_plecare', ' 00:00:00') and s.data_checkout = concat('$data_intoarcere', ' 00:00:00'))

		$result = $link->query($query);
		//$row = $result->fetch_assoc();

		if($result->num_rows > 0) {
			return 0;
		} else {
			return 1;
		}
	}

	function verify_availability_hotel($destinatie, $data_plecare, $data_intoarcere, $nr_pers, $nr_stele) {
		$link = db_Connect();

		$id_dest = get_id_dest($destinatie);
		$id_hotel = get_id_hotel($id_dest, $nr_stele);

		//echo "     qqqq   " . $id_hotel . $destinatie;
		$query = "select nr_max_pers from Hoteluri where id_hotel = '$id_hotel'";
		$result = $link->query($query);
		$row = $result->fetch_assoc();

		$query_2 = "SELECT sum(r.nr_pers) from Rezervari r, SejururiCazare s where r.id_rezervare = s.id_rezervare and s.id_hotel = '$id_hotel' and (s.data_checkin between '$data_plecare' and '$data_intoarcere' or s.data_checkout between '$data_plecare' and '$data_intoarcere' or (s.data_checkin < '$data_plecare' and s.data_checkout > '$data_intoarcere'))";
		$result_2 = $link->query($query_2);
		$row_2 = $result_2->fetch_assoc();

		if($row_2['sum(r.nr_pers)'] + $nr_pers <= $row['nr_max_pers']) {
			return 1;
		} else {
			return 0;
		}
	}

	function db_insert_sejur($id_rezervare, $data_checkin, $data_checkout, $destinatie, $nr_stele) {
		$link = db_Connect();

		$id_dest = get_id_dest($destinatie);
		$id_hotel = get_id_hotel($id_dest, $nr_stele);

		echo $id_rezervare . $data_checkin . $data_checkout . $destinatie . $id_hotel;
		

		$sql = "INSERT INTO SejururiCazare (data_checkin, data_checkout, id_hotel, id_rezervare) VALUES (?, ?, ?, ?)";


		if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ssss", $param_data_checkin, $param_data_checkout, $param_id_hotel, $param_rezervare);
      
            $param_rezervare = $id_rezervare;
            $param_data_checkin = $data_checkin;
            $param_data_checkout = $data_checkout;
            $param_id_hotel = $id_hotel;
                
            if(mysqli_stmt_execute($stmt)){
                echo "Rezervare finalizata. Multumim!";
            } else{
                echo "Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
        
	}

	function get_tur_id ($oras, $data_) {

		$link = db_Connect();

		$id_oras = get_id_dest($oras);

		$query = "SELECT id_tur FROM Tururi WHERE id_destinatie = '$id_oras' AND data_ora = concat('$data_', ' 10:30:00')";

		$result = $link->query($query);
		$row = $result->fetch_assoc();

		//echo "ccc";
		
		return $row['id_tur'];
	}

	function verify_rzv_tur($oras, $data_, $id_client) {
		$link = db_Connect();

		$id_tur = get_tur_id($oras, $data_);

		$query = "SELECT r.id_client from Rezervari r, TururiRzv b where r.id_rezervare = b.id_rezervare and b.id_tur = '$id_tur' and r.id_client = '$id_client'";
		$result = $link->query($query);
		//$row = $result->fetch_assoc();

		if($result->num_rows > 0) {
			return 0;
		} else {
			return 1;
		}
	}

	function verify_availability_tur($oras, $data_, $nr_pers) {
		$id_tur = get_tur_id($oras, $data_);

		$link = db_Connect();

		echo $oras . $data_ . $nr_pers . $id_tur;
		$query = "SELECT sum(r.nr_pers) from Rezervari r, TururiRzv b where r.id_rezervare = b.id_rezervare and b.id_tur = '$id_tur'";
		$result = $link->query($query);
		$row = $result->fetch_assoc();

		echo "aa";

		if($row['sum(r.nr_pers)'] + $nr_pers <= 30) {
			return 1;
		} else {
			return 0;
		}
	}

	function db_insert_tur($nr_pers, $oras, $data_, $id_rezervare, $id_client) {
		//echo "aaa";
		$id_tur = get_tur_id($oras, $data_);
		//echo $id_tur;

		$link = db_Connect();
		

		$sql = "INSERT INTO TururiRzv (id_tur, id_rezervare, id_client, nr_pers) VALUES (?, ?, ?, ?)";


		if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ssss", $param_id_tur, $param_id_rezervare, $param_id_client, $param_nr_pers);
      
            $param_id_tur = $id_tur;
            $param_id_rezervare = $id_rezervare;
            $param_id_client = $id_client;
            $param_nr_pers = $nr_pers;
                
            if(mysqli_stmt_execute($stmt)){
                echo "Rezervare finalizata. Multumim!";
            } else{
                echo "Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
	}

	function db_insert_pachet($id_rezervare, $id_client, $nr_pers, $data_plecare, $data_intoarcere, $plecare, $destinatie, $nr_stele, $data_) {
		db_insert_bilete_avion($id_rezervare, $id_client, $plecare, $destinatie, $data_plecare, $data_intoarcere);
		db_insert_sejur($id_rezervare, $data_plecare, $data_intoarcere, $destinatie, $nr_stele);
		db_insert_tur($nr_pers, $destinatie, $data_, $id_rezervare, $id_client);
	}

	function show_dest_list() {
		$link = db_Connect();

	    $query = "SELECT nume_destinatie FROM Destinatii"; 
	    return $link->query($query);
	}

	

?>