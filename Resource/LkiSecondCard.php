<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Resource;
use Rastija\Owl;
use Rastija\Owl\Uri;
use Rastija\Service;

/**
 * Description of LkiSecondCard
 *
 * @author Virginijus
 */
class LkiSecondCard extends LkiMainCard
{
    private $_cacheDir = 'cache/LKI_SECOND_CARD/';
    private $_ontologyFile = 'config/rastija_owl_v3_2015_07_30VM.owl';
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->setResourceId('LKIKartoteka/2');
        $this->setResourceName('LKŽ papildymų kartoteka');
        
        /** @var Uri\AbstractUri $uriFactory */
        $uriFactory = new Owl\Uri\UriFactory();
        $uriFactory->setUriBase('&lmf;zodynas.LKŽ_papildymų_kartoteka');
        
        $this->setUriFactory($uriFactory);
    }
    
    /**
     *  Main function for owl generations
     * 
     * @return string - dictionary ID encoded with MD5
     */
    public function generateLmfOwl() {
        $test = false;
        if ($test) {
            $filename  = $this->_cacheDir . md5($this->getResourceId()) . '_1.txt';
            $fileOfIndividuals = $this->_cacheDir . md5($this->getResourceId()) . '_individuals_1' . '.txt';
            $resourceOwlFile = $this->_cacheDir . md5($this->getResourceId()) . '_ontology_1' . '.owl';
        } else {
            $filename  = $this->_cacheDir . md5($this->getResourceId()) . '.txt';
            $fileOfIndividuals = $this->_cacheDir . md5($this->getResourceId()) . '_individuals' . '.txt';
            $resourceOwlFile = $this->_cacheDir . md5($this->getResourceId()) . '_ontology' . '.owl';
        }
        // Get resource information from the service
        //$resource = new Service\LkiisResource($this->getResourceId());
        //$resource->getRecords($filename, 0); // records appear from ~190001
        
        // Build individal for LMF ontology
        // LKISecond car file is very big so we split it it to parts
        // Part size 50 MB
        $partSize = 25 *1024 * 1024;        
        if (filesize($filename) > $partSize) {
            
            // Splitting data file
            $file = fopen($filename, 'r');
            $content = fread($file, filesize($filename));
            fclose($file);
   
            $parts = floor(filesize($filename) / $partSize) + 1;
            $startPoss = 0;
            for ($i = 1; $i <= $parts; $i++) {
                $partFileName = $filename . '_part_' . $i . '.txt';
                $partFileOfIndividuals = $fileOfIndividuals  . '_part_' . $i . '.txt';
                
                $partText = substr($content, $startPoss, $partSize);
                $content = substr($content, $partSize);
                $length = (strlen($content) < 1024 * 1024) ? strlen($content) : 1024 * 1024;
                $tmpStr = substr($content, 0, $length);

                $endOfRecord = strpos($tmpStr, '</return>') + 9;
                unset($tmpStr);
                
                $partText .= substr($content, 0, $endOfRecord);
                if ($i != $parts) {
                    $partText .= '</ns2:getRecordsResponse></soap:Body></soap:Envelope>' . "\n";
                }
                // remove unecessary tags
                $partText = str_replace('</ns2:getRecordsResponse></soap:Body></soap:Envelope><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><ns2:getRecordsResponse xmlns:ns2="http://servicebus.lki/">',
                    '', $partText);
                $partFile = fopen($partFileName, 'w');
                fwrite($partFile, $partText);
                fclose($partFile);
                unset($partText);

                $content = substr($content, $endOfRecord);
                $content = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><ns2:getRecordsResponse xmlns:ns2="http://servicebus.lki/">'
                        . $content;
            } 
           
            // Building of individuals and OWL
            for ($i = 1 ; $i <= $parts; $i++) {
                $partFileName = $filename . '_part_' . $i . '.txt';
                $partFileOfIndividuals = $fileOfIndividuals  . '_part_' . $i . '.txt';
                $partResourceOwlFile = $resourceOwlFile . '_part_' . $i . '.owl';
                
                $this->buildLmfIndividuals($partFileName, $partFileOfIndividuals);
                
                $this->createOwl($partFileOfIndividuals, $partResourceOwlFile);
            }
        } else {            
            $this->buildLmfIndividuals($filename, $fileOfIndividuals);
            
            // Make owl of dictionary
            $this->createOwl($fileOfIndividuals, $resourceOwlFile);            
        }

        return md5($this->getResourceId());
    }
}
