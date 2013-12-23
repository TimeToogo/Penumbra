<?php

namespace StormTests\One\Relational\Tables;

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
        $this->PostBlogId = new Column('PostBlogId', new Mysql\Columns\DataTypes\HexedBinaryDataType(16));
        $this->PostTitle = new Column('PostTitle', new DataType('VARCHAR', [50]));
        
        $this->TagId = new Column('TagId', new Mysql\Columns\DataTypes\HexedBinaryDataType(16));
    }
    
    protected function Table1(Database $Database) {
        return $Database->Posts;
    }
    protected function MapForeignKey1(Map $Map, Table $Table1) {
        $Map->Map($this->PostBlogId, $Table1->BlogId);
        $Map->Map($this->PostTitle, $Table1->Title);
    }

    protected function Table2(Database $Database) {
        return $Database->Tags;
    }
    protected function MapForeignKey2(Map $Map, Table $Table2) {
        $Map->Map($this->TagId, $Table2->Id);
    }
}

?>
