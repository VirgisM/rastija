<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Service;

/**
 * Description of LkiisResource
 *
 * @author Virginijus
 */
class LkiisResource
{
    private $_resourceId = Null;
    private $_lkiisClient = Null;
    
    public function __construct($resourceId)
    {
        $this->_lkiisClient = new LkiisSoapClient();
                
        $this->_resourceId = $resourceId;
    }
    
    
    /**
     * Get data from service and save it to file
     * 
     * @param string $filename
     * @param int $count (if count not set function will get all records)
     */
    public function getRecords($filename, $count = 0)
    {
        $lkiisClient = $this->_lkiisClient;
        $resourceId = $this->_resourceId;
        
        // Empty file
        $file = fopen($filename, 'w');
        fwrite($file, '');

        $rowsCount = 5000;
        if (!$count) {
            $count = $lkiisClient->countRecords($resourceId, '2012-01-01', date('Y-m-d'));
        } else {
            if ($count < $rowsCount) {
                $rowsCount = $count;
            }
        }

        $i = 1;
        while ($i <= $count) { 
            // Modify rowcount 
            if (($i + $rowsCount) >= $count ){
                $rowsCount = $count - $i + 1;
            }            
            
            $xml = $lkiisClient->getRecords($resourceId, '2012-01-01', date('Y-m-d'), $i, $rowsCount);
            fwrite($file, $xml);
            

            $i += $rowsCount;
            echo $i . '-' . $rowsCount;
        }
        fclose($file);

        // Merge different SOAP enveloples
        $file = fopen($filename, 'r');
        $content = fread($file, filesize($filename));
        fclose($file);

        $content = str_replace('</ns2:getRecordsResponse></soap:Body></soap:Envelope><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><ns2:getRecordsResponse xmlns:ns2="http://servicebus.lki/">',
                '', $content);

        $file = fopen($filename, 'w');
        fwrite($file, $content);
        fclose($file);
    }
}
