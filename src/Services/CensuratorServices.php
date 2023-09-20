<?php

namespace App\Services;

class CensuratorServices {


    private array $motsInappriories = ['leMotQueJeNeVeuxPasVoir','leMotQueJeNeVeuxPasVoir2',
        'pute','con','enculé','encule','enculer',
        'enculée','enculées','connard','connards',
        'connasse','connasses','salope','salopes',
        'salop','salops','saloperie','saloperies',
        'merde', 'merdes','merdeux','merdeuse'];
    public function purify($content) : string
    {
        // check if $content contain any word from $motsInappriories
        // if yes, replace it with ****
        // return the new $content
        foreach ($this->motsInappriories as $mot) {
            $content = str_ireplace($mot, '****', $content);
        }
        return $content;
    }
    
    
    
    
}