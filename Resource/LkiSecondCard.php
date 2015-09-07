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
        $this->setResourceName('Papildymų kartoteka');
        
        /** @var Uri\AbstractUri $uriFactory */
        $uriFactory = new Owl\Uri\UriFactory();
        $uriFactory->setUriBase('&lmf;kartoteka.Papildymų_kartoteka');
        
        $this->setUriFactory($uriFactory);
    }
    
    /**
     *  Main function for owl generations
     * 
     * @return string - dictionary ID encoded with MD5
     */
    public function generateLmfOwl() {
        $test = true;
        if ($test) {
            $filename  = $this->_cacheDir . md5($this->getResourceId()) . '_1.txt';
            $fileOfIndividuals = $this->_cacheDir . md5($this->getResourceId()) . '_individuals_1' . '.owl';
            $resourceOwlFile = $this->_cacheDir . md5($this->getResourceId()) . '_ontology_1' . '.owl';
        } else {
            $filename  = $this->_cacheDir . md5($this->getResourceId()) . '.txt';
            $fileOfIndividuals = $this->_cacheDir . md5($this->getResourceId()) . '_individuals' . '.owl';
            $resourceOwlFile = $this->_cacheDir . md5($this->getResourceId()) . '_ontology' . '.owl';
        }
        // Get resource information from the service
        $resource = new Service\LkiisResource($this->getResourceId());
        $resource->getRecords($filename, 0);
        
        // Build individal for LMF ontology
        $this->buildLmfIndividuals($filename, $fileOfIndividuals);
        
        
        // Make owl of dictionary
        $this->createOwl($fileOfIndividuals, $resourceOwlFile);
        
        return md5($this->getResourceId());
    } 
}
