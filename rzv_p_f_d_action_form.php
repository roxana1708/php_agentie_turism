<?php
    include 'functions.php';
	//include 'db_connections.php';

	session_start();

	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

    $facuta_de = $_SESSION["id"];
    $user_type = $_SESSION["user_type"];

    $link = db_Connect();


    //verif id client
    if (isset($_POST['username'])) {
        $id_client = 0;
        $client_user_type = '';//filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        verify_username_client(filter_var($_POST['username'], FILTER_SANITIZE_STRING), $id_client, $user_type, $client_user_type);

        if(!empty($_SESSION['err_msg']['username_client_err'])){
            //
            header('location: rzv_pachet_f_d.php');
                exit;
        }
    } else {
            $_SESSION['err_msg']['username_client_err'] = "Please select a valid value";
            header('location: rzv_pachet_f_d.php');
                exit;
    }

    //verif nr pasageri
    if (isset($_POST['nr_pers'])) {
        $nr_pers = filter_var($_POST['nr_pers'], FILTER_SANITIZE_STRING);
        verify_nr_pers(/*$_SESSION['err_msg']['nr_pers_err'], */$client_user_type, $nr_pers);
        if(!empty($_SESSION['err_msg']['nr_pers_err'])) {
            header('location: rzv_pachet_f_d.php');
            exit;
        }
    } else {
            $_SESSION['err_msg']['nr_pers_err'] = "Please select a valid value";
            header('location: rzv_pachet_f_d.php');
                exit;
    }


    //verif oras plecare && sosire
    $plecare = $destinatie = '';

    if(isset($_POST['plecare']) && isset($_POST['destinatie'])) {
        verify_orase($_POST['plecare'], $_POST['destinatie'], $plecare, $destinatie/*, $_SESSION['err_msg']['departure_err'], $_SESSION['err_msg']['arrival_err']*/);
        if(!empty($_SESSION['err_msg']['departure_err']) || !empty($_SESSION['err_msg']['arrival_err'])) {
            header('location: rzv_pachet_f_d.php');
            exit;
        }
    } else {
        $_SESSION['err_msg']['date_err'] = "Va rugam alegi date calendaristice valide";
            header('location: rzv_pachet_f_d.php');
            exit;
    }


    //verif date calendaristice
    $data_plecare = $data_intoarcere = '';
   
    if(isset($_POST['data_plecare']) && isset($_POST['data_intoarcere'])) {
        verify_dates($_POST['data_plecare'], $_POST['data_intoarcere'], $data_plecare, $data_intoarcere/*, $_SESSION['err_msg']['date_err']*/);

        if(!empty($_SESSION['err_msg']['date_err'])) {
            header('location: rzv_pachet_f_d.php');
            exit;
        }
    } else {
        $_SESSION['err_msg']['date_err'] = "Va rugam alegi date calendaristice valide";
            header('location: rzv_pachet_f_d.php');
            exit;
    }

    if (isset($_POST['nr_stele'])) {
        $nr_stele = filter_var($_POST['nr_stele'], FILTER_SANITIZE_STRING);
        if($nr_stele != 3 && $nr_stele != 5) {
            $_SESSION['err_msg']['nr_stele_err'] = "Please select a valid value";
            header('location: rzv_pachet_f_d.php');
            exit;
        }
    } else {
            $_SESSION['err_msg']['nr_stele_err'] = "Please select a valid value";
            header('location: rzv_pachet_f_d.php');
                exit;
    }

    //verif date calendaristice tur

    $data_ = '';
   
    if(isset($_POST['data_'])) {
        $aux = new DateTime($_POST['data_']);
        if(!verify_date($aux)) {
            $_SESSION['err_msg']['date_err'] = "Va rugam alegeti o data calendaristica valida";// . " " . $now;
            //echo $_POST['data_'];
            header('location: rzv_pachet_f_d.php');
            exit;
        } else {
            $data_ = $_POST['data_'];
        }
    } else {
        $_SESSION['err_msg']['date_err'] = "Va rugam alegeti o data calendaristica valida mm";
            header('location: rzv_pachet_f_d.php');
            exit;
    }

    //VERIFICARI VALABILITATE AVION
    //verif daca clientul a mai facut rzv pt acelasi zbor
    if(verify_rzv_avion($plecare, $destinatie, $data_plecare, $id_client) && verify_rzv_avion($destinatie, $plecare, $data_intoarcere, $id_client)) {
        //
    } else {
        $_SESSION['err_msg']['duplicate_err'] = "Ati mai facut rezervare pentru acest zbor";
            header('location: rzv_pachet_f_d.php');
            exit;
    }

    //verif nr max pasageri zbor
    if(verify_availability_avion($plecare, $destinatie, $data_plecare, $nr_pers) && verify_availability_avion($destinatie, $plecare, $data_intoarcere, $nr_pers)) {
        //
    } else {
        $_SESSION['err_msg']['availability_err'] = "Nu mai sunt suficiente locuri disponibile pentru acest zbor";
            header('location: rzv_pachet_f_d.php');
            exit;
    }


    //VERIFICARI VALABILITATE HOTEL
    //verif daca clientul a mai facut rzv pt acelasi hotel in aceeasi perioada
    echo "\n\n\nzzzzzzz";
    echo $oras . $data_plecare . $data_intoarcere . $id_client . $nr_stele;
    if(verify_rzv_hotel($destinatie, $data_plecare, $data_intoarcere, $id_client, $nr_stele)) {
        //
    } else {
        $_SESSION['err_msg']['duplicate_err'] = "Ati mai facut rezervare pentru acest hotel in aceasta perioada";
            header('location: rzv_pachet_f_d.php');
            exit;
    }
    echo "aaa";

    //verif nr max oaspeti hotel in acea perioada
    echo $oras . $data_plecare . $data_intoarcere . $nr_pers . $nr_stele. "a a a a ";
    if(verify_availability_hotel($destinatie, $data_plecare, $data_intoarcere, $nr_pers, $nr_stele)) {
        //
    } else {
        
        $_SESSION['err_msg']['availability_err'] = "Nu mai sunt suficiente locuri disponibile pentru acest hotel in perioada aleasa";
            header('location: rzv_pachet_f_d.php');
            exit;
        
    }


    //VERIFICARI VALABILITATE TUR
    //verif daca clientul a mai facut rzv pt acelasi zbor
    if(verify_rzv_tur($destinatie, $data_, $id_client)) {
        //
    } else {
        $_SESSION['err_msg']['duplicate_err'] = "Ati mai facut rezervare pentru acest tur";
            header('location: rzv_pachet_f_d.php');
            exit;
    }

    //verif nr max turisti tur
    //echo $oras . $data_ . $nr_pers;
    if(verify_availability_tur($destinatie, $data_, $nr_pers)) {
        //
    } else {
        $_SESSION['err_msg']['availability_err'] = "Nu mai sunt suficiente locuri disponibile pentru acest tur";
            header('location: rzv_pachet_f_d.php');
            exit;
    }


    //facem rezervarea
    $id_rezervare = db_insert_rezervare($facuta_de, 'p', $nr_pers, $id_client);
    db_insert_pachet($id_rezervare, $id_client, $nr_pers, $data_plecare, $data_intoarcere, $plecare, $destinatie, $nr_stele, $data_);

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

    if(in_array($destinatie, $europa)) {
        if($nr_stele == 3){
            $price_per_pers += 80;
        } else {
            $price_per_pers += 120;
        }
    } elseif (in_array($destinatie, $america)) {
        if($nr_stele == 3){
            $price_per_pers += 100;
        } else {
            $price_per_pers += 200;
        }
    } elseif (in_array($destinatie, $romania)) {
        if($nr_stele == 3){
            $price_per_pers += 30;
        } else {
            $price_per_pers += 90;
        }
    }

    if(in_array($destinatie, $europa)) {
        $price_per_pers += 15;
    } elseif (in_array($destinatie, $america)) {
        $price_per_pers += 20;
    } elseif (in_array($destinatie, $romania)) {
        $price_per_pers += 10;
    }
    
    $price_per_pers = $price_per_pers * 90 / 100;
    make_invoice($id_client, $d, $id_rezervare, $type, $desc, $nr_pers, $price_per_pers);

?>