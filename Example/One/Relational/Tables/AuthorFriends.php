<?php

namespace PenumbraExamples\One\Relational\Tables;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Core\Relational\Database;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Core\Relational\ITable;
use \Penumbra\Drivers\Constant\Relational;

class AuthorFriends extends Relational\Relations\JoinTable {
    protected function Name() {
        return 'AuthorFriends';
    }
    
    public $AuthorId;
    public $FriendId;
    
    protected function CreateTableStructure(IColumnSet $Column) {
        $this->AuthorId = $Column->Int32('AuthorId', true);
        $this->FriendId = $Column->Int32('FriendId', true);
    }
    
    protected function Table1(Database $Database) {
        return $Database->Authors;
    }
    protected function MapForeignKey1(Map $Map, ITable $Authors) {
        $Map->Map($this->AuthorId, $Authors->Id);
    }

    protected function Table2(Database $Database) {
        return $Database->Authors;
    }
    protected function MapForeignKey2(Map $Map, ITable $Authors) {
        $Map->Map($this->FriendId, $Authors->Id);
    }
}

?>
