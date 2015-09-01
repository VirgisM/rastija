<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Rastija\Owl;

/**
 * Description of LmfClassInterface
 *
 * @author Virginijus
 */
interface LmfClassInterface {
    
    /**
     * Class will be converted to LMF OWL string
     */
    public function toLmfString();
}
