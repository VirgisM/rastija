<?php
namespace Rastija;
use Rastija\Service;
use Rastija\Owl;


die();

require_once( dirname( __FILE__ ) . '/Service/SearchServices.class.php' );
require_once( dirname( __FILE__ ) . '/Service/LkiisSoapClient.php');
require_once( dirname( __FILE__ ) . '/Service/LkiisResource.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfLexicalEntry.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfSense.php');
require_once( dirname( __FILE__ ) . '/Owl/LmfEquivalent.php');

$lkiisClient = new Service\LkiisSoapClient();
//$lkiisClient->getRecords('LKIKartoteka/1', '2013-04-25T10:02:39.953Z', '2015-08-17', 1, 10);
//$lkiisClient->getRecords('LKIKartoteka/1', '2013-04-25T10:02:39.953Z', date('Y-m-d'), 1, 10);
//echo $lkiisClient->countRecords('LKIKartoteka/1', '2013-04-25T10:02:39.953Z', date('Y-m-d'));
//$lkiisClient->getRecord('LKIKartoteka/2', 2717848);

//$resource = new Service\LkiisResource('VU/10485995');
// Anglų lietuvių -- VU/10485716
//$resourceId = 'VU/10485995';
$resourceId = 'LKIKartoteka/1';
//VU_LatviuLietuviu_zodynas
//$resourceId = 'VU/7557550';
//$resourceName = 'Anglų-Lietuvių kalbų žodynas';
//$resourceName = 'Lietuvių-Anglų kalbų žodynas';
$resourceName = 'Pagrindin4 kartoteka';

$ontologyFile = 'config/rastija_owl_v3_2015_07_30VM.owl';
$test = true;
if ($test) {
    $filename  = 'cache/' . md5($resourceId) . '_1.txt';
    $fileOfIndividuals = 'cache/' . md5($resourceId) . '_individuals_1' . '.owl';
    $resourceOwlFile = 'cache/' . md5($resourceId) . '_ontology_1' . '.owl';
} else {
    $filename  = 'cache/' . md5($resourceId) . '.txt';
    $fileOfIndividuals = 'cache/' . md5($resourceId) . '_individuals' . '.owl';
    $resourceOwlFile = 'cache/' . md5($resourceId) . '_ontology' . '.owl';
}

/*---------------------- Get data ----------------------*/
// Empty file

$file = fopen($filename, 'w');
fwrite($file, '');

echo $count = $lkiisClient->countRecords($resourceId, '2012-01-01', date('Y-m-d'));

$xml = $lkiisClient->getRecords($resourceId, '2012-01-01', date('Y-m-d'), 1, 100);
/*
$i = 0;
while ($i <= $count) { 
    $xml = $lkiisClient->getRecords($resourceId, '2012-01-01', date('Y-m-d'), $i+1, 5000);
 
    //$xml = $lkiisClient->getRecords($resourceId, '2012-01-01', date('Y-m-d'), 1, 100);

    fwrite($file, $xml);
    $i += 5000;
    echo '<br/>' . $i;
}
 * 
 */
fwrite($file, $xml);
fclose($file);

die();
// Merge different SOAP enveloples
/*
$file = fopen($filename, 'r');
$content = fread($file, filesize($filename));
fclose($file);

$content = str_replace('</ns2:getRecordsResponse></soap:Body></soap:Envelope><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><ns2:getRecordsResponse xmlns:ns2="http://servicebus.lki/">',
        '', $content);

$file = fopen($filename, 'w');
fwrite($file, $content);
fclose($file);
*/
// Add LMF ontology to file

// Read individuals
$fileIndividuals = fopen($fileOfIndividuals, "r");
$individuals = fread($fileIndividuals, filesize($fileOfIndividuals));
fclose($fileIndividuals);    

// Read ontology
$fileLmfOntology = fopen($ontologyFile, "r");
$ontology = fread($fileLmfOntology, filesize($ontologyFile));
fclose($fileLmfOntology);    

// Create resource owl
$fileResourceOwl = fopen($resourceOwlFile, "w");
fwrite($fileResourceOwl, $ontology);
fwrite($fileResourceOwl, $individuals);
$individuals = NULL;

$resourceAnnotationStr = "
    
    <!-- 
    ///////////////////////////////////////////////////////////////////////////////////////
    //
    // Annotations
    //
    ///////////////////////////////////////////////////////////////////////////////////////
     -->

    <rdf:Description rdf:about=\"&j.1;zodynas.Anglų-Lietuvių_kalbų_žodynas.Resource\">
        <rdfs:label>Anglų-lietuvių kalbų žodynas</rdfs:label>
    </rdf:Description>
</rdf:RDF>
    ";
fwrite($fileResourceOwl, $resourceAnnotationStr);
fclose($fileResourceOwl);




 
$file = fopen($filename, 'r');
$xml = fread($file, filesize($filename));
fclose($file);

$dom = new \DOMDocument('1.0', 'UTF-8');
$dom->loadXML($xml);

$data = array();

$fileIndividuals = fopen($fileOfIndividuals, "w+");
$recordNr = 1;
foreach($dom->getElementsByTagName('return') as $domRecord) {
    /* @var $domRecord \DOMElement */
    $nodes = $domRecord->childNodes;
    
    $arr = array();
    foreach ($nodes as $node) {
        /* @var $node \DOMElement */
        if ($node->nodeName == 'metadata') {
            $metadata = new \DOMDocument('1.0', 'UTF-8');
            $metadata->loadXML($node->nodeValue);
            
            $ins = array();
            // Taking a record
            /* @var $record \DOMElement */
            $record = $metadata->getElementsByTagName('record')->item(0);
            
            foreach ($record->getElementsByTagName('el') as $el) {
                /* @var $el \DOMElement */
                if ($el->getAttribute('value') || $el->getAttribute('name') == 'Reiksme') {
                    // Lemma
                    if ($el->getAttribute('name') == 'AntrastinisZodis') {
                        $ins['lemma'] = $el->getAttribute('value');  
                    }
                    // Forms
                    if ($el->getAttribute('name') == 'Forma') {
                        $ins['wordForms'][] = $el->getAttribute('value');
                    }
                    // Pronunciation
                    if ($el->getAttribute('name') == 'Tarimas') {
                        $ins['pronunciation'] = $el->getAttribute('value');
                    }
                    
                    // Senses
                    if ($el->getAttribute('name') == 'Reiksme') {
                        $senseArr = array();

                        foreach ($el->childNodes as $sense) {
                            
                            // There are some DOMTExt nodes, so we will ignore them
                            if (get_class($sense) == 'DOMElement') {
                                /* @var $sense \DOMElement */
                                
                                // PartOfSpeach
                                if ($sense->getAttribute('name') == 'KalbosDalis') {
                                    $senseArr['partOfSpeach'] = $sense->getAttribute('value');
                                }

                                // Equivalents
                                if ($sense->getAttribute('name') == 'Atitikmuo') {
                                    $senseArr['equivalent'][] = $sense->getAttribute('value');
                                }
                            }
                        }
                        $ins['senses'][] = $senseArr; 
                    }
                }
            }
            $arr[$node->nodeName] = $ins;
        } else {
            $arr[$node->nodeName] = $node->nodeValue;
        }
    }
    $data[] = $arr;
    // TODO pridėti tarimą ir wordFormas
    // Array to lexical entry
    /* array contains
     * - id
     * - header
     * - status
     * - metadata
     *      - lemma
     *      - pronunciation - nepridėta
     *      - wordForms  - nepridėta
     *      - senses
     *          - partOfSpeach
     *          - equivalent
     */
    //print_r($arr);         
    if ($arr['metadata']['lemma']) {
        $lexicalEntries = array();


        $isFirst = TRUE;
        foreach ($arr['metadata']['senses'] as $sense) {
            $lmfSense = new Owl\LmfSense();

            if ($isFirst) {
                $lexicalEntry = new Owl\LmfLexicalEntry($resourceName);
                $lexicalEntry->setSeed($arr['id']);
                $lexicalEntry->setLemma($arr['metadata']['lemma']);

                $lexicalEntry->setPartOfSpeech($sense['partOfSpeach']);
                array_push($lexicalEntries, $lexicalEntry);
                $isFirst = FALSE;
            } else {
                reset($lexicalEntries);
                $lexicalEntry = NULL;
                // Check if lexical entry with specified part of speech exists
                foreach($lexicalEntries as $lexEntry) {
                    /* @var $lexEntry Owl\LmfLexicalEntry */
                    if ($lexEntry->getPartOfSpeech() == $sense['partOfSpeach']) {
                        $lexicalEntry = $lexEntry;
                    }
                }
                // Creation of new entity of lexical entry
                if (!$lexicalEntry) {
                    $lexicalEntry = new Owl\LmfLexicalEntry($resourceName);
                    $lexicalEntry->setSeed($arr['id']);
                    $lexicalEntry->setLemma($arr['metadata']['lemma'] . '-' . (sizeof($lexicalEntries)+1));

                    $lexicalEntry->setPartOfSpeech($sense['partOfSpeach']);
                    array_push($lexicalEntries, $lexicalEntry);
                }
            }
            $lmfSense->setUriBase($lexicalEntry->getUriBase());
            $lmfSense->setLemmaWrittenForm($lexicalEntry->getLemma());

            $equivalents = $sense['equivalent'];
            $rank = 1;
            foreach ($equivalents as $equivalent) {
                 $lmfEquivalent = new Owl\LmfEquivalent();
                 $lmfEquivalent->setLanguage('Anglų');
                 $lmfEquivalent->setWrittenForm($equivalent);
                 $lmfEquivalent->setUriBase($lexicalEntry->getUriBase());
                 $lmfEquivalent->setRank($rank++);

                 $lmfSense->addEquivalent($lmfEquivalent);
             }
             $lexicalEntry->addSense($lmfSense);
        }
            
        // When is more than one sense
        foreach($lexicalEntries as $lexicalEntry) {
            //$owlStr .= $lexicalEntry->toLmfString();
            fwrite($fileIndividuals, $lexicalEntry->toLmfString());
        }
    }
    echo '<br />' . $recordNr++ . '-' . $arr['id'] . '-' .  $arr['metadata']['lemma'];
}

fclose($fileIndividuals);


//$lkiisClient->getResources();

$str = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
   <soap:Body>
      <ns2:getResourcesResponse xmlns:ns2="http://servicebus.lki/">
         <return>
            <firstRecordDate>2013-04-25T10:02:39.953Z</firstRecordDate>
            <id>LKIKartoteka/1</id>
            <name>Kartoteka 1</name>
         </return>
         <return>
            <firstRecordDate>2013-04-04T10:20:18.016Z</firstRecordDate>
            <id>LKIKartoteka/2</id>
            <name>Kartoteka 2</name>
         </return>
         <return>
            <firstRecordDate>2013-11-08T12:53:15.663Z</firstRecordDate>
            <id>LKIKartoteka/5</id>
            <name>Kartoteka 5</name>
         </return>
p
         <return>
            <firstRecordDate>2015-08-04T10:55:38.976Z</firstRecordDate>
            <id>LKIVocabulary/10043942</id>
            <name>Lietuvos_vietovardziu_geoinformacine_duomenu_baze</name>
         </return>
         <return>
            <firstRecordDate>2015-07-24T12:52:30.915Z</firstRecordDate>
            <id>LKIVocabulary/10123931</id>
            <name>Geografiniai_Objektai</name>
         </return>
         <return>
            <firstRecordDate>2015-08-12T21:48:27.627Z</firstRecordDate>
            <id>LKIVocabulary/11209855</id>
            <name>Testinis_importavimas</name>
         </return>
         <return>
            <firstRecordDate>2014-12-08T17:23:23.604Z</firstRecordDate>
            <id>LKIVocabulary/1682743</id>
            <name>Antonimu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2015-06-02T09:37:36.736Z</firstRecordDate>
            <id>LKIVocabulary/1682745</id>
            <name>Frazeologizmu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2015-06-07T12:11:22.082Z</firstRecordDate>
            <id>LKIVocabulary/1682747</id>
            <name>Palyginimu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2014-02-19T11:29:53.644Z</firstRecordDate>
            <id>LKIVocabulary/1682749</id>
            <name>Dabartinis_lt_zodynas</name>
            <resourceClassificators>
               <createDate>2012-12-31T22:00:00Z</createDate>
               <id>LKIVocabulary/3</id>
               <lastModificationDate>2012-12-31T22:00:00Z</lastModificationDate>
               <name>Dabartinio lt pažymos</name>
            </resourceClassificators>
         </return>
         <return>
            <firstRecordDate>2013-08-02T15:47:48.205Z</firstRecordDate>
            <id>LKIVocabulary/7114173</id>
            <name>Valentingumo_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2013-08-16T16:42:23.079Z</firstRecordDate>
            <id>LKIVocabulary/7115150</id>
            <name>Sisteminis_zodynas</name>
            <resourceClassificators>
               <createDate>2012-12-31T22:00:00Z</createDate>
               <id>LKIVocabulary/1</id>
               <lastModificationDate>2013-12-17T22:00:00Z</lastModificationDate>
               <name>Sisteminio_zodyno_struktura</name>
            </resourceClassificators>
         </return>
         <return>
            <firstRecordDate>2014-02-19T10:49:44.234Z</firstRecordDate>
            <id>LKIVocabulary/960440</id>
            <name>Sinonimu_zodynas</name>
            <resourceClassificators>
               <createDate>2012-12-31T22:00:00Z</createDate>
               <id>LKIVocabulary/2</id>
               <lastModificationDate>2012-12-31T22:00:00Z</lastModificationDate>
               <name>Sinonimų pažymos</name>
            </resourceClassificators>
         </return>
         <return>
            <firstRecordDate>2015-03-07T16:37:09.447Z</firstRecordDate>
            <id>LKIVocabulary/9952625</id>
            <name>Istoriniai_vietovardziai</name>
         </return>
         <return>
            <firstRecordDate>2015-08-07T18:10:45.801Z</firstRecordDate>
            <id>LKIVocabulary/9975325</id>
            <name>Pavardziu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2015-08-12T22:15:08.337Z</firstRecordDate>
            <id>LKIVocabulary/9988134</id>
            <name>Tarmiu_archyvas</name>
         </return>
         <return>
            <firstRecordDate>2014-04-08T17:53:39+03:00</firstRecordDate>
            <id>LLTI/daina</id>
            <name>daina</name>
         </return>
         <return>
            <firstRecordDate>2014-04-08T16:24:08+03:00</firstRecordDate>
            <id>LLTI/tikejimas</id>
            <name>tikejimas</name>
         </return>
         <return>
            <firstRecordDate>2014-03-21T02:12:39+02:00</firstRecordDate>
            <id>LLTI/misle</id>
            <name>misle</name>
         </return>
         <return>
            <firstRecordDate>2015-07-28T08:37:01.177Z</firstRecordDate>
            <id>VU/10485716</id>
            <name>VU_Anglu-Lietuviu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2015-07-28T08:09:08.212Z</firstRecordDate>
            <id>VU/10485795</id>
            <name>VU_Lotynu-Lietuviu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2015-07-28T07:23:08.357Z</firstRecordDate>
            <id>VU/10485895</id>
            <name>VU_senoves_Graiku-Lietuviu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2015-07-28T09:06:28.114Z</firstRecordDate>
            <id>VU/10485995</id>
            <name>VU_Lietuviu-Anglu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2014-02-21T08:09:13.798Z</firstRecordDate>
            <id>VU/7557054</id>
            <name>VU_LietuviuLenku_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2014-11-24T11:28:52.244Z</firstRecordDate>
            <id>VU/7557058</id>
            <name>VU_LietuviuLatviu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2014-11-28T14:17:44.164Z</firstRecordDate>
            <id>VU/7557550</id>
            <name>VU_LatviuLietuviu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2014-05-26T07:04:00.993Z</firstRecordDate>
            <id>VU/7557554</id>
            <name>VU_LietuviuVokieciu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2014-11-12T11:27:30.678Z</firstRecordDate>
            <id>VU/7557558</id>
            <name>VU_VokieciuLietuviu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2014-12-01T07:41:36.301Z</firstRecordDate>
            <id>VU/7557562</id>
            <name>VU_LenkuLietuviu_zodynas</name>
         </return>
         <return>
            <firstRecordDate>2014-05-28T07:43:03.903Z</firstRecordDate>
            <id>VU/7568279</id>
            <name>VU_Ispanu-Lietuviu_zodynas</name>
         </return>
      </ns2:getResourcesResponse>
   </soap:Body>
</soap:Envelope>';