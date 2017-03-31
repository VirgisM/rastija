<?php
// Skaityti SK_BUG
// Dokumentacija https://sk.raštija.lt/rastija/rest/

namespace Rastija;
use Rastija\Service;

require_once( dirname( __FILE__ ) . '/Service/SkService.php"' );


$sk = new Service\SkService();

//print($sk->isWsOnline());
//var_dump($sk->getDictionaries());
//var_dump($sk->getOntologyList());

var_dump($sk->getMainOntology('zodynas'));

