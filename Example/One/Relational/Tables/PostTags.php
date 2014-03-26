<?php

namespace PenumbraExamples\One\Relational\Tables;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Core\Relational\Database;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Core\Relational\ITable;
use \Penumbra\Drivers\Constant\Relational;

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
    protected function MapForeignKey1(Map $Map, ITable $Posts) {
        $Map->Map($this->PostBlogId, $Posts->BlogId);
        $Map->Map($this->PostTitle, $Posts->Title);
    }

    protected function Table2(Database $Database) {
        return $Database->Tags;
    }
    protected function MapForeignKey2(Map $Map, ITable $Tags) {
        $Map->Map($this->TagId, $Tags->Id);
    }
}

?>
