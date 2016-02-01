<?php

include "funzioni.php"; //carico i metodi GET POST DELETE dal file funzioni.php
$method = $_SERVER['REQUEST_METHOD']; //assume il valore del tipo di richiesta ricevuta (GET/POST/DELETE)
$url = $_SERVER['REQUEST_URI']; //indirizzo della richiesta 
//
//dati database
$username = 'museo';
$pass = 'museo';
$server = 'awsdbistance.cl04jmikh4zc.eu-west-1.rds.amazonaws.com';
$database = "Museo";

$link = mysqli_connect($server, $username, $pass, $database)or die("connessione fallita"); //connessione al DB
$stringa = explode('/', $url); //separo l'indirizzo in un array usando come separatore il /
if ($method == 'GET') {
    processGET($stringa, $link); //metodo gestione richieste GET 
} elseif ($method == 'POST') {
    $post = $_POST; //array contenente i dati da inserire nel DB
    processPOST($stringa, $link, $post); //metodo gestione richieste POST
} elseif ($method == 'DELETE') {
    processDELETE($stringa, $link); //metodo gestione richieste DELETE
}
mysqli_close($link);
?>
