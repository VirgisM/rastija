<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Rastija\Owl\Uri;

/**
 * Description of UriFactory
 *
 * @author Virginijus
 */
class UriFactory implements UriFactoryInterface
{
    /**
     * Base uri. It is required for uri generation.
     * 
     * @var string
     */
    private $uriBase;
    
    /**
     * {@inheritdoc}
     */
    public function create($class, $writtenForm, $seed) {
        switch (strtolower($class))
        {
            case 'lexicalentry' :
                $uri = new LexicalEntryUri();
                $uri->setUriBase($this->getUriBase());
                $uri->buildUri(array(
                    'writtenForm' => $writtenForm,
                    'seed' => $seed,
                ));
                break;
            case 'lemma' :
                $uri = new LemmaUri();
                $uri->setUriBase($this->getUriBase());
                $uri->buildUri(array(
                    'writtenForm' => $writtenForm,
                    'seed' => $seed,
                ));
                break;            
            default:
                $uri = new DefaultClassUri();
                $uri->setUriBase($this->getUriBase());
                $uri->buildUri(array(
                    'className' => $class,
                    'writtenForm' => $writtenForm,
                    'seed' => $seed,
                ));
                break;
        }
        return $uri;
    }
/**
     * {@inheritdoc}
     */
    public function setUriBase($uriBase)
    {
        $this->uriBase = $uriBase;
    }
    
     /**
     * {@inheritdoc}
     */
    public function getUriBase()
    {
        return $this->uriBase;
    }
}
