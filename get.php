<?php

namespace Rastija;
use Rastija\Service;
use Rastija\Owl;
use Rastija\Resource;

require_once( dirname( __FILE__ ) . '/Service/SearchServices.class.php' );
require_once( dirname( __FILE__ ) . '/Service/LkiisSoapClient.php');
require_once( dirname( __FILE__ ) . '/Service/LkiisResource.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfClassInterface.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfLexicalEntry.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfSense.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfEquivalent.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfWordForm.php');
require_once( dirname( __FILE__ ) . '/Resource/DictionaryInterface.php');
require_once( dirname( __FILE__ ) . '/Resource/DictionaryAbstract.php');
require_once( dirname( __FILE__ ) . '/Resource/EnLtDictionary.php');
require_once( dirname( __FILE__ ) . '/Resource/LkiMainCard.php');


/* Anglų-Lietuvių kalbų žodynas 62733 įrašai*/
//$dic = new Resource\EnLtDictionary();
//$dic->generateLmfOwl();

/* Pagrindinė kartoteka deklaruojama 55933 įrašai, realiai 48823*/
$card = new Resource\LkiMainCard();
$card->generateLmfOwl();