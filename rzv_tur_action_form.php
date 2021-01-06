<?php
	include 'functions.php';
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
        verify_nr_pers(/*$_SESSION['err_msg']['nr_pers_err'], */$_SESSION['user_type'], $nr_pers);
        if(!empty($_SESSION['err_msg']['nr_pers_err'])) {
            header('location: rezervare_tur.php');
            exit;
        }
    } else {
            $_SESSION['err_msg']['nr_pers_err'] = "Please select a valid value";
            header('location: rezervare_tur.php');
                exit;
    }


    //verif oras
    $oras = '';

    if(isset($_POST['destinatie'])) {
        if(verify_destinatie($_POST['destinatie'])) {
        	$oras = $_POST['destinatie'];
        } else {
        	$_SESSION['err_msg']['departure_err'] = "Va rugam alegeti un oras valid";
        	header('location: rezervare_tur.php');
            exit;
        }
    } else {
        $_SESSION['err_msg']['departure_err'] = "Va rugam alegeti un oras valid";
            header('location: rezervare_tur.php');
            exit;
    }


    //verif date calendaristice

    $data_ = '';
   
    if(isset($_POST['data_'])) {
        $aux = new DateTime($_POST['data_']);
        if(!verify_date($aux)) {
        	$_SESSION['err_msg']['date_err'] = "Va rugam alegeti o data calendaristica valida";// . " " . $now;
            //echo $_POST['data_'];
            header('location: rezervare_tur.php');
            exit;
        } else {
        	$data_ = $_POST['data_'];
        }
    } else {
        $_SESSION['err_msg']['date_err'] = "Va rugam alegeti o data calendaristica valida mm";
            header('location: rezervare_tur.php');
            exit;
    }
    //echo "aaaa";

    //verif daca clientul a mai facut rzv pt acelasi zbor
    if(verify_rzv_tur($oras, $data_, $id_client)) {
        //
    } else {
        $_SESSION['err_msg']['duplicate_err'] = "Ati mai facut rezervare pentru acest tur";
            header('location: rezervare_tur.php');
            exit;
    }

    //verif nr max turisti tur
    //echo $oras . $data_ . $nr_pers;
    if(verify_availability_tur($oras, $data_, $nr_pers)) {
        //
    } else {
        $_SESSION['err_msg']['availability_err'] = "Nu mai sunt suficiente locuri disponibile pentru acest tur";
            header('location: rezervare_tur.php');
            exit;
    }

    //facem rezervarea
    $id_rezervare = db_insert_rezervare($facuta_de, 't', $nr_pers, $id_client);
    db_insert_tur($nr_pers, $oras, $data_, $id_rezervare, $id_client);


    $desc = " Tur " . $oras . " " . $data_;

    
    $d = date("Y/m/d");
    //echo "kkkkkkkk";
    $type = "Tur";

    $europa = ["Stockholm", "Hanovra", "Roma", "Geneva", "Londra", "Oslo", "Copenhaga", "Budapesta", "Viena", "Atena", "Amsterdam", "Lyon", "Luxemburg"];
    $romania = ["Bucuresti", "Cluj", "Brasov", "Iasi", "Timisoara"];
    $america = ["New York"];

    if(in_array($oras, $europa)) {
        $price_per_pers = 15;
    } elseif (in_array($oras, $america)) {
        $price_per_pers = 20;
    } elseif (in_array($oras, $romania)) {
        $price_per_pers = 10;
    } 

    make_invoice($id_client, $d, $id_rezervare, $type, $desc, $nr_pers, $price_per_pers);

?>