<?php

namespace Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Domain\Maps;

use \Penumbra\Core\Object\Domain;
use \Penumbra\Drivers\Base\Object;
use \Penumbra\Drivers\Base\Object\Properties;
use \Penumbra\Drivers\Base\Object\Properties\Accessors;
use \Penumbra\Drivers\Constant\Object\EntityMap;
use \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Entities\Author;

class AuthorMap extends EntityMap {
    protected function EntityType() {
        return Author::GetType();
    }
    
    protected function EntityConstructor() {
        return new Object\Construction\EmptyConstructor();
    }
    
    public $Id;
    public $FirstName;
    public $LastName;
    
    protected function CreateProperties(Domain $Domain) {
        $this->Id = new Properties\DataProperty(new Accessors\Field('Id'), true);
        $this->FirstName = new Properties\DataProperty(new Accessors\Field('FirstName'));
        $this->LastName = new Properties\DataProperty(new Accessors\Field('LastName'));
    }

}

?>
