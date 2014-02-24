<?php

namespace Storm\Tests\Integration\ObjectRelationalModels\Blog\Entities;

class Blog extends Entity {
    public $Id;
    public $Name;
    public $Description;
    public $CreatedDate;
    public $Posts;
    
    public function GetName() {
        return $this->Name;
    }
    
    public function SetName($Name) {
        $this->Name = $Name;
    }
}

?>
