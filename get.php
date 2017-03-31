<?php

namespace Rastija;
use Rastija\Resource;

require_once( dirname( __FILE__ ) . '/Service/SearchServices.class.php' );
require_once( dirname( __FILE__ ) . '/Service/LkiisSoapClient.php');
require_once( dirname( __FILE__ ) . '/Service/LkiisResource.php');
require_once( dirname( __FILE__ ) . '/Owl/Uri/AbstractUri.php');
require_once( dirname( __FILE__ ) . '/Owl/Uri/UriFactoryInterface.php');
require_once( dirname( __FILE__ ) . '/Owl/Uri/UriFactory.php');
require_once( dirname( __FILE__ ) . '/Owl/Uri/LemmaUri.php');
require_once( dirname( __FILE__ ) . '/Owl/Uri/DefaultClassUri.php');
require_once( dirname( __FILE__ ) . '/Owl/Uri/LexicalEntryUri.php');
require_once( dirname( __FILE__ ) . '/Owl/Uri/DefaultClassUri.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfClassInterface.php');
require_once( dirname( __FILE__ ) . '/Owl/AbstractLmfClass.php');
require_once( dirname( __FILE__ ) . '/Owl/AbstractLmfForm.php');
require_once( dirname( __FILE__ ) . '/Owl/AbstractRepresentation.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfLexicalEntry.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfLemma.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfWordForm.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfSense.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfSenseRelation.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfSenseExample.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfEquivalent.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfTextRepresentation.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfDefinition.php');
require_once( dirname( __FILE__ ) . '/Resource/DictionaryInterface.php');
require_once( dirname( __FILE__ ) . '/Resource/AbstractDictionary.php');
require_once( dirname( __FILE__ ) . '/Resource/EnLtDictionary.php');
require_once( dirname( __FILE__ ) . '/Resource/LkiMainCard.php');
require_once( dirname( __FILE__ ) . '/Resource/LkiSecondCard.php');
require_once( dirname( __FILE__ ) . '/Resource/LltiRiddleCard.php');
require_once( dirname( __FILE__ ) . '/Resource/LltiSongCard.php');
require_once( dirname( __FILE__ ) . '/Resource/LltiBeliefCard.php');
require_once( dirname( __FILE__ ) . '/Resource/LkiPhraseological.php');
require_once( dirname( __FILE__ ) . '/Resource/LtEnDictionary.php');
require_once( dirname( __FILE__ ) . '/Resource/LatLtDictionary.php');
require_once( dirname( __FILE__ ) . '/Resource/GrLtDictionary.php');
require_once( dirname( __FILE__ ) . '/Resource/LkiComparison.php');
require_once( dirname( __FILE__ ) . '/Resource/LkiClassified.php');
require_once( dirname( __FILE__ ) . '/Resource/LkiAntonym.php');
require_once( dirname( __FILE__ ) . '/Resource/LkiSurname.php');

// Maximum memory with is allowed by php
ini_set('memory_limit', '1048M');
ini_set('upload_max_filesize', '1024M');

/* Anglų-Lietuvių kalbų žodynas 62733 įrašai, realiai dabar 70593*/
//$dic = new Resource\EnLtDictionary();
//echo $dic->generateLmfOwl();

/* Pagrindinė kartoteka deklaruojama 55933 įrašai, realiai 48823*/
//$card = new Resource\LkiMainCard();
//echo $card->generateLmfOwl();

/* Papildymų kartoteka deklaruojama 560829 įrašai, realiai ??? */
//$card = new Resource\LkiSecondCard();
//echo $card->generateLmfOwl();

/* Mįslių kartoteka deklaruojama 80029 įrašai, realiai ??? */
//$card = new Resource\LltiRiddleCard();
//echo $card->generateLmfOwl();

/* Pokario partizanų dainų kartoteka deklaruojama 3697 įrašai, realiai ??? */
//$card = new Resource\LltiSongCard();
//echo $card->generateLmfOwl();

/* Pokario partizanų dainų kartoteka deklaruojama 8275 įrašai, realiai ??? */
//$card = new Resource\LltiBeliefCard();
//echo $card->generateLmfOwl();

/* Get resources */
//$lkiisClient = new Service\LkiisSoapClient();
//echo $lkiisClient->getResources();

/* Frazeologijos žodynas 3311 įrašai*/
//$dic = new Resource\LkiPhraseological();
//echo $dic->generateLmfOwl();

/* Lietuvių anglų žodynas  78874 įrašai*/
//$dic = new Resource\LtEnDictionary();
//echo $dic->generateLmfOwl();

/* Lotynų Lietuvių anglų žodynas 36265 įrašai*/
//$dic = new Resource\LatLtDictionary();
//echo $dic->generateLmfOwl();

/* Senovės graikų Lietuvių anglų žodynas 22803 įrašai*/
//$dic = new Resource\GrLtDictionary();
//echo $dic->generateLmfOwl();

/* Palyginimų žodynas 7093 įrašai, o realiai 7092*/
//$dic = new Resource\LkiComparison();
//echo $dic->generateLmfOwl();

/* Sisteminis lietuvių kalbos žodynas 1614 įrašai (viskas sudėta i 1), o realiai xxx */
$dic = new Resource\LkiClassified();
echo $dic->generateLmfOwl();

/* Antonimų žodynas 5401 įrašai, o realiai 4243 */
//$dic = new Resource\LkiAntonym();
//echo $dic->generateLmfOwl();

/* Antonimų žodynas 47122 įrašai, o realiai xxx */
//$dic = new Resource\LkiSurname();
//echo $dic->generateLmfOwl();
