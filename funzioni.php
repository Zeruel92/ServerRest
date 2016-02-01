<?php

function processGET($url_array,$db_link){
    $tabella = $url_array[2];
    $idrisorsa = $url_array[3];
    $query = "SELECT * FROM $tabella";
    if($tabella=="Utente"){
	$query="SELECT email, idUtente, Cognome, Nome FROM $tabella";
        $token=$url_array[4];
        $query=$query. " WHERE token='$token';";
    }
    else if($tabella=="Preferenze"){
        $query="SELECT distinct O.Nome as NomeOpera, A.Nome as Nome, A.Cognome as Cognome, O.idOpera as idOpera, G.Nome as NomeGenere, G.idGenere as idGenere"; 
        $query=$query." FROM Opera O, Autore A, Preferenze P, Utente U, Genere G";
        $query=$query. " WHERE P.Utente_idUtente=$idrisorsa AND P.Genere_idGenere=G.idGenere AND O.Genere_idGenere=G.idGenere AND O.Autore_idAutore=A.idAutore;";
    }
    else if($tabella=="Login"){
	$email=$url_array[3];
	$password=$url_array[4];
	$query="SELECT Nome, Cognome, token ";
	$query=$query."FROM Utente ";
	$query=$query." WHERE email='$email' AND password='$password'";
    }
    else{
      if ($idrisorsa != 0) {
	  $query = $query . " WHERE id$tabella=$idrisorsa;";
      }
      if($idrisorsa==0){
	$query=$query.";";
      }
    }
    $res = mysqli_query($db_link, $query)or die("mysql error");
    if ($res == null) {
        header('HTTP/1.1 404 NOT FOUND');
    } else {
        header('HTTP/1.1 200 OK');
        header('Content-type: application/json');
        while ($current = mysqli_fetch_assoc($res)) {
            $json[] = $current;
        }
        echo json_encode($json);
    }
}

function processPUT($url_array, $db_link){
    echo "è stato chiamato un metodo put";
}
function processPOST($url_array, $db_link, $post_data){
    $tabella= $url_array[2];
    $query ="INSERT INTO $tabella";
    $query_field="(";
    $query_value="VALUES(";
    $arraysize=count($post_data)-1;
    $iteration=0;
    foreach($post_data as $item_name => $value){
        if($arraysize!=$iteration){
       $query_field="$query_field $item_name,";
        $query_value="$query_value '$value',";
        
        }
        else{
            $query_field="$query_field $item_name";
        $query_value="$query_value '$value'";
        }
        $iteration++;
    }
    $query_field="$query_field) ";
    $query_value="$query_value);";
    $query=$query.$query_field.$query_value;
    mysqli_query($db_link,$query)or die("Error processing Post");
    header('HTTP/1.1 200 OK');
    echo "";
}
function processDELETE($url_array,$db_link){
    $tabella=$url_array[2];
    $idUtente=$url_array[3];
    $idGenere=$url_array[4];
    $query="DELETE FROM $tabella WHERE Utente_idUtente='$idUtente' AND Genere_idGenere='$idGenere'";
    mysqli_query($db_link,$query)or die("Error processing Delete");
    header('HTTP/1.1 200 OK');
    echo "DELETE OK";
}
?>