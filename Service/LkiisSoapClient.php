<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rastija\Service;

class LkiisSoapClient
{
    private $_wsdlUrl = "https://vlkiis.lki.lt/mule/resources?wsdl";
    private $_serviceUrl = "https://vlkiis.lki.lt/mule/resources";
    private $_soapClient = Null;
    private $_soapVersion = SOAP_1_1;
    

    /**
     * Contructor
     */
    public function __construct()
    {
        $this->_soapClient = new \SoapClient($this->_wsdlUrl, array(
                'soap_version' => $this->_soapVersion,
                'trace'        => 1,
                'exceptions'   => true
            ));
        $this->_soapClient->__setLocation($this->_serviceUrl);
    }
    
    public function getResources() 
    {
        $client = $this->_soapClient;
        try {
            $result = @$client->__soapCall("getResources", array());
            //var_dump($result);
        } catch (SoapFault $soapFault) {
            var_dump($soapFault);
            var_dump($client->__getLastRequest());
            echo $soapFault->xdebug_message;
        }
        return $client->__getLastResponse();
    }
    
    public function getRecords($resourceId, $dateFrom, $dateTo, $rowFrom, $rowsCount = 100)
    {
        $client = $this->_soapClient;
        $params = array(
            'resourceId' => $resourceId,
            'modifiedDateFrom' => $dateFrom,
            'modifiedDateTo' => $dateTo,
            'rowFrom' => $rowFrom,
            'rowCount' => $rowsCount
        );

        try {
            $result = @$client->__soapCall("getRecords", array($params));
            //var_dump($result);
        } catch (SoapFault $soapFault) {
            //var_dump($client->__getLastRequest());
            throw new \Exception($soapFault->xdebug_message);
        }
        return $client->__getLastResponse();  
    }
    
    public function getRecord($resourceId, $recordId)
    {
        $client = $this->_soapClient;
        $params = array(
            'resourceId' => $resourceId,
            'recordId' => $recordId,
        );

        try {
            $result = @$client->__soapCall("getRecord", array($params));
        } catch (SoapFault $soapFault) {
            //var_dump($client->__getLastRequest());
            throw new \Exception($soapFault->xdebug_message);
            return NULL;
        }
        return $result->return;        
    }
    
    public function countRecords($resourceId, $dateFrom, $dateTo)
    {
        $client = $this->_soapClient;
        $params = array(
            'resourceId' => $resourceId,
            'modifiedDateFrom' => $dateFrom,
            'modifiedDateTo' => $dateTo,
        );

        try {
            $result = @$client->__soapCall("countRecords", array($params));
        } catch (SoapFault $soapFault) {
            //var_dump($client->__getLastRequest());
            throw new \Exception($soapFault->xdebug_message);
        }
        return (int) $result->return;         
    }
}
        