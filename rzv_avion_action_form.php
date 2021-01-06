<?php
	include 'functions.php';
    include 'factura.php';
	//include 'db_connections.php';

	session_start();

	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

    $id_client = $_SESSION["id"];
    $facuta_de = $_SESSION["id"];

    $link = db_Connect();


    //verif nr pasageri
    if (isset($_POST['nr_pers'])) {
        $nr_pers = filter_var($_POST['nr_pers'], FILTER_SANITIZE_STRING);
        verify_nr_pers(/*$_SESSION['err_msg']['nr_pers_err'],*/ $_SESSION['user_type'], $nr_pers);
        if(!empty($_SESSION['err_msg']['nr_pers_err'])) {
            header('location: rezervare_avion.php');
            exit;
        }
    } else {
            $_SESSION['err_msg']['nr_pers_err'] = "Please select a valid value";
            header('location: rezervare_avion.php');
                exit;
    }

    //ECHO "ADAFBKHDSJL";
    //verif oras plecare && sosire
    $plecare = $destinatie = '';

    if(isset($_POST['plecare']) && isset($_POST['destinatie'])) {
        verify_orase($_POST['plecare'], $_POST['destinatie'], $plecare, $destinatie/*, $_SESSION['err_msg']['departure_err'], $_SESSION['err_msg']['arrival_err']*/);
        if(!empty($_SESSION['err_msg']['departure_err']) || !empty($_SESSION['err_msg']['arrival_err'])) {
            header('location: rezervare_avion.php');
            exit;
        }
    } else {
        $_SESSION['err_msg']['date_err'] = "Va rugam alegi date calendaristice valide";
            header('location: rezervare_avion.php');
            exit;
    }
    //echo $plecare;
    //echo $destinatie;

    //verif date calendaristice
    $data_plecare = $data_intoarcere = '';
   
    if(isset($_POST['data_plecare']) && isset($_POST['data_intoarcere'])) {
        verify_dates($_POST['data_plecare'], $_POST['data_intoarcere'], $data_plecare, $data_intoarcere/*, $_SESSION['err_msg']['date_err']*/);

        if(!empty($_SESSION['err_msg']['date_err'])) {
            header('location: rezervare_avion.php');
            exit;
        }
    } else {
        $_SESSION['err_msg']['date_err'] = "Va rugam alegi date calendaristice valide";
            header('location: rezervare_avion.php');
            exit;
    }

    //verif daca clientul a mai facut rzv pt acelasi zbor
    if(verify_rzv_avion($plecare, $destinatie, $data_plecare, $id_client) && verify_rzv_avion($destinatie, $plecare, $data_intoarcere, $id_client)) {
    	//
    } else {
    	$_SESSION['err_msg']['duplicate_err'] = "Ati mai facut rezervare pentru acest zbor";
            header('location: rezervare_avion.php');
            exit;
    }

    //verif nr max pasageri zbor
    if(verify_availability_avion($plecare, $destinatie, $data_plecare, $nr_pers) && verify_availability_avion($destinatie, $plecare, $data_intoarcere, $nr_pers)) {
    	//
    } else {
    	$_SESSION['err_msg']['availability_err'] = "Nu mai sunt suficiente locuri disponibile pentru acest zbor";
            header('location: rezervare_avion.php');
            exit;
    }

    //facem rezervarea
    $id_rezervare = db_insert_rezervare($facuta_de, 'b_a', $nr_pers, $id_client);
   
    db_insert_bilete_avion($id_rezervare, $id_client, $plecare, $destinatie, $data_plecare, $data_intoarcere);
    db_insert_bilete_avion($id_rezervare, $id_client, $destinatie, $plecare, $data_intoarcere, $data_plecare);

    $desc = " " . $plecare . "->" . $destinatie . "  " . $data_plecare . "  -  " . $data_intoarcere . " ";

    
    $d = date("Y/m/d");
    //echo "kkkkkkkk";
    $type = "Bilete avion";

    $europa = ["Stockholm", "Hanovra", "Roma", "Geneva", "Londra", "Oslo", "Copenhaga", "Budapesta", "Viena", "Atena", "Amsterdam", "Lyon", "Luxemburg"];
    $romania = ["Bucuresti", "Cluj", "Brasov", "Iasi", "Timisoara"];
    $america = ["New York"];

    if(in_array($plecare, $europa) && in_array($destinatie, $europa)) {
        $price_per_pers = 50;
    } elseif ((in_array($plecare, $europa) && in_array($destinatie, $romania)) || (in_array($plecare, $romania) && in_array($destinatie, $europa))) {
        $price_per_pers = 80;
    } elseif ((in_array($plecare, $europa) && in_array($destinatie, $america)) || (in_array($plecare, $america) && in_array($destinatie, $europa)) ) {
        $price_per_pers = 1000;
    } elseif ((in_array($plecare, $romania) && in_array($destinatie, $america)) || (in_array($plecare, $america) && in_array($destinatie, $romania))) {
        $price_per_pers = 1200;
    } elseif ((in_array($plecare, $romania) && in_array($destinatie, $romania)) || (in_array($plecare, $romania) && in_array($destinatie, $romania))) {
        $price_per_pers = 30;
    }
    

    make_invoice($id_client, $d, $id_rezervare, $type, $desc, $nr_pers, $price_per_pers);

?>