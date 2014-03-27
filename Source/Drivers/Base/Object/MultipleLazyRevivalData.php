<?php

namespace Penumbra\Drivers\Base\Object;

use Penumbra\Core\Object;

class MultipleLazyRevivalData {
    /**
     * @var Object\RevivalData 
     */
    private $AlreadyKnownRevivalData;
    
    /**
     * @var callable 
     */
    private $RevivalDataLoader;
    
    public function __construct(Object\RevivalData $AlreadyKnownRevivalData, callable $RevivalDataLoader) {
        $this->AlreadyKnownRevivalData = $AlreadyKnownRevivalData;
        $this->RevivalDataLoader = $RevivalDataLoader;
    }
    
    /**
     * @return Object\RevivalData 
     */
    public function GetAlreadyKnownRevivalData() {
        return $this->AlreadyKnownRevivalData;
    }

    /**
     * @return callable 
     */
    public function GetMultipleRevivalDataLoader() {
        return $this->RevivalDataLoader;
    }
}

?>