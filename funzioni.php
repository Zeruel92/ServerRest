<?php

//Funzione richiamata quando arriva una richiesta di tipo GET
function processGET($url_array, $db_link) {
    $tabella = $url_array[2]; //tabella a cui facciamo riferimento per il db
    $idrisorsa = $url_array[3]; //id della risorsa di cui vogliamo i dettagli
    $query = "SELECT * FROM $tabella";
    //in base al tipo di tabella richiesta la query viene modificata per ottenere i dati richiesti
    if ($tabella == "Utente") {
        $query = "SELECT email, idUtente, Cognome, Nome FROM $tabella";
        $token = $url_array[4];
        $query = $query . " WHERE token='$token';";
    } else if ($tabella == "Preferenze") {
        $query = "SELECT distinct O.Nome as NomeOpera, A.Nome as Nome, A.Cognome as Cognome, O.idOpera as idOpera, G.Nome as NomeGenere, G.idGenere as idGenere";
        $query = $query . " FROM Opera O, Autore A, Preferenze P, Utente U, Genere G";
        $query = $query . " WHERE P.Utente_idUtente=$idrisorsa AND P.Genere_idGenere=G.idGenere AND O.Genere_idGenere=G.idGenere AND O.Autore_idAutore=A.idAutore;";
    } else if ($tabella == "Login") {
        $email = $url_array[3];
        $password = $url_array[4];
        $query = "SELECT Nome, Cognome, token ";
        $query = $query . "FROM Utente ";
        $query = $query . " WHERE email='$email' AND password='$password'";
    } else {
        if ($idrisorsa != 0) {
            $query = $query . " WHERE id$tabella=$idrisorsa;";
        }
        if ($idrisorsa == 0) {
            $query = $query . ";";
        }
    }
    $res = mysqli_query($db_link, $query)or die("mysql error"); //eseguo la query sul database
    if ($res == null) {
        header('HTTP/1.1 404 NOT FOUND');
    } else {
        //rispondo alla richiesta GET con i dati in formato JSONArray
        header('HTTP/1.1 200 OK');
        header('Content-type: application/json');
        while ($current = mysqli_fetch_assoc($res)) {
            $json[] = $current;
        }
        echo json_encode($json);
    }
}

function processPUT($url_array, $db_link) {
    echo "è stato chiamato un metodo put"; //Non Usata
}

//Funzione richiamata quando il server riceve una richiesta POST
//La query in questa funzione viene generata dinamicamente a partire dai dati inseriti nella POST
//nomi dei campi e valori vengono inviati al server sotto forma di POST
function processPOST($url_array, $db_link, $post_data) {
    $tabella = $url_array[2]; //tabella in cui verranno inseriti i dati
    //inizio generazione query
    $query = "INSERT INTO $tabella";
    $query_field = "(";
    $query_value = "VALUES(";
    $arraysize = count($post_data) - 1;
    $iteration = 0;
    foreach ($post_data as $item_name => $value) {
        if ($arraysize != $iteration) {//se non è l'ultima iterazione aggiungo una virgola per separare i campi
            $query_field = "$query_field $item_name,";
            $query_value = "$query_value '$value',";
        } else {// non inserisco la virgola all'ultima iterazione
            $query_field = "$query_field $item_name";
            $query_value = "$query_value '$value'";
        }
        $iteration++;
    }
    $query_field = "$query_field) ";
    $query_value = "$query_value);";
    $query = $query . $query_field . $query_value; //assemblo la query unendo campi e valori
    mysqli_query($db_link, $query)or die("Error processing Post"); //eseguo la query
    header('HTTP/1.1 200 OK');
    echo ""; //rispondo con un echo vuoto per dire che è tutto ok
}

//Funzione che viene richiamata quando al server arriva una richiesta DELETE
//viene richiamata solo dalla rimozione dei generi dalle preferenze di un utente
function processDELETE($url_array, $db_link) {
    $tabella = $url_array[2]; //Tabella dal quale rimuovere un elemento
    $idUtente = $url_array[3]; //id dell'utente richiedente
    $idGenere = $url_array[4]; //id della risorsa da eliminare
    $query = "DELETE FROM $tabella WHERE Utente_idUtente='$idUtente' AND Genere_idGenere='$idGenere'";
    mysqli_query($db_link, $query)or die("Error processing Delete");
    header('HTTP/1.1 200 OK');
    echo "DELETE OK";
}

?>