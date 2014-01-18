<?php

namespace StormExamples\One\Relational\Tables;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational\Database;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Storm\Core\Relational\Table;
use \Storm\Drivers\Constant\Relational;
use \Storm\Drivers\Platforms\Mysql;
use \Storm\Drivers\Base\Relational\Columns\Column;
use \Storm\Drivers\Base\Relational\Columns\DataType;

class PostTags extends Relational\Relations\JoinTable {
    protected function Name() {
        return 'PostTags';
    }
 
    public $PostBlogId;
    public $PostTitle;
    public $TagId;

    protected function CreateTableStructure(IColumnSet $Column) {
        $this->PostBlogId = $Column->Int32('PostBlogId', true);
        $this->PostTitle = $Column->String('PostTitle', 50, true);
        
        $this->TagId = $Column->Int32('TagId', true);
    }
    
    protected function Table1(Database $Database) {
        return $Database->Posts;
    }
    protected function MapForeignKey1(Map $Map, Table $Posts) {
        $Map->Map($this->PostBlogId, $Posts->BlogId);
        $Map->Map($this->PostTitle, $Posts->Title);
    }

    protected function Table2(Database $Database) {
        return $Database->Tags;
    }
    protected function MapForeignKey2(Map $Map, Table $Tags) {
        $Map->Map($this->TagId, $Tags->Id);
    }
}

?>
