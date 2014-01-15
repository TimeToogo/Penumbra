<?php

namespace Storm\Drivers\Platforms\Mysql\Syncing;

use \Storm\Drivers\Base\Relational\Syncing;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Base\Relational\Traits;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Platforms\Mysql\QueryBuilderExtensions;
use \Storm\Drivers\Platforms\Mysql;

class TableTraitManager extends Syncing\Traits\TableTraitManager {
    final protected function Initialize() {
        $MakeAddFunction = function (callable $DefinitionFunction, $Add = false) {
            return function(IConnection $Connection, 
                    QueryBuilder $QueryBuilder, 
                    Table $Table, 
                    \Storm\Drivers\Base\Relational\TableTrait $Trait) 
                    use (&$DefinitionFunction, &$Add) {
                QueryBuilderExtensions::AppendAlterTable($QueryBuilder, $Table);
                if($Add) {
                    $QueryBuilder->Append('ADD ');
                }
                $DefinitionFunction($QueryBuilder, $Table, $Trait);
            };
        };
        
        $this->Register(Mysql\Tables\CharacterSet::GetType(), 
                [$this, 'CharacterSet'], 
                $MakeAddFunction([$this, 'CharacterSet']), [$this, 'DropCharacterSet']);
                
        $this->Register(Mysql\Tables\Collation::GetType(), 
                [$this, 'Collation'], 
                $MakeAddFunction([$this, 'CharacterSet']), [$this, 'DropCollation']);
        
        $this->Register(Traits\Comment::GetType(), 
                [$this, 'Comment'], 
                $MakeAddFunction([$this, 'Comment']), [$this, 'DropComment']);
        
        $this->Register(Mysql\Tables\Engine::GetType(), 
                [$this, 'Engine'], 
                $MakeAddFunction([$this, 'Engine']), [$this, 'DropEngine']);
        
        $this->Register(Traits\ForeignKey::GetType(), 
                [$this, 'ForeignKey'], 
                $MakeAddFunction([$this, 'ForeignKey'], true), [$this, 'DropForeignKey']);
        
        $this->Register(Mysql\Tables\Index::GetType(), 
                [$this, 'Index'], 
                $MakeAddFunction([$this, 'Index'], true), [$this, 'DropIndex']);
        
        $this->Register(Traits\PrimaryKey::GetType(), 
                [$this, 'PrimaryKey'], 
                $MakeAddFunction([$this, 'PrimaryKey'], true), [$this, 'DropPrimaryKey']);
    }

    // <editor-fold defaultstate="collapsed" desc="Character Set">
    public function CharacterSet(
            QueryBuilder $QueryBuilder, Table $Table, Mysql\Tables\CharacterSet $Trait) {
        $QueryBuilder->AppendIdentifier('CHARACTER SET #', [$Trait->GetName()]);
    }

    public function DropCharacterSet(IConnection $Connection, 
            QueryBuilder $QueryBuilder, Table $Table, Mysql\Tables\CharacterSet $Trait) {
        $DefaultCharacterSet = $Connection->FetchValue('SELECT @@character_set_database');
        QueryBuilderExtensions::AppendAlterTable($QueryBuilder, $Table);
        $QueryBuilder->AppendIdentifier('CHARACTER SET #', [$DefaultCharacterSet]);
    }
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Collation">
    public function Collation(
            QueryBuilder $QueryBuilder, Table $Table, Mysql\Tables\Collation $Trait) {
        $QueryBuilder->AppendIdentifier('COLLATE #', [$Trait->GetName()]);
    }

    public function DropCollation(IConnection $Connection,
            QueryBuilder $QueryBuilder, Table $Table, Mysql\Tables\Collation $Trait) {
        $DefaultCollation = $Connection->FetchValue('SELECT @@collation_database');
        QueryBuilderExtensions::AppendAlterTable($QueryBuilder, $Table);
        $QueryBuilder->AppendIdentifier('COLLATE #', [$DefaultCollation]);
    }
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Comment">
    public function Comment(
            QueryBuilder $QueryBuilder, Table $Table, Traits\Comment $Trait) {
        $QueryBuilder->AppendEscaped('COMMENT #', [$Trait->GetValue()]);
    }

    public function DropComment(IConnection $Connection, 
            QueryBuilder $QueryBuilder, Table $Table, Traits\Comment $Trait) {
        QueryBuilderExtensions::AppendAlterTable($QueryBuilder, $Table);
        $QueryBuilder->Append('COMMENT \'\'');
    }
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Engine">
    public function Engine(
            QueryBuilder $QueryBuilder, Table $Table, Mysql\Tables\Engine $Trait) {
        $QueryBuilder->AppendIdentifier('ENGINE #', [$Trait->GetName()]);
    }

    public function DropEngine(IConnection $Connection, 
            QueryBuilder $QueryBuilder, Table $Table, Mysql\Tables\Engine $Trait) {
        $DefaultEngine = $Connection->FetchValue('SELECT @@storage_engine');
        QueryBuilderExtensions::AppendAlterTable($QueryBuilder, $Table);
        $QueryBuilder->AppendIdentifier('ENGINE #', [$DefaultEngine]);
    }
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Foreign Key">
    public function ForeignKey(
            QueryBuilder $QueryBuilder, Table $Table, Traits\ForeignKey $Trait) {
        $ColumnNameMap = $Trait->GetReferencedColumnNameMap();
        $PrimaryColumns = array_keys($ColumnNameMap);
        $ForeignColumns = array_values($ColumnNameMap);
        
        $QueryBuilder->AppendIdentifier('CONSTRAINT # ', [$Trait->GetName()]);
        $QueryBuilder->AppendIdentifiers('FOREIGN KEY (#) ', $PrimaryColumns, ',');
        $QueryBuilder->AppendIdentifier('REFERENCES # ', [$Trait->GetReferencedTable()->GetName()]);
        $QueryBuilder->AppendIdentifiers('(#) ', $ForeignColumns, ',');
        $QueryBuilder->Append('ON UPDATE ' . $this->MapForeignKeyMode($Trait->GetUpdateMode()) . ' ');
        $QueryBuilder->Append('ON DELETE ' . $this->MapForeignKeyMode($Trait->GetDeleteMode()) . ' ');
    }

    private function MapForeignKeyMode($Mode) {
        switch ($Mode) {
            case Traits\ForeignKeyMode::NoAction:
                return 'NO ACTION';
            case Traits\ForeignKeyMode::Cascade:
                return 'CASCADE';
            case Traits\ForeignKeyMode::SetNull:
                return 'SET NULL';
            case Traits\ForeignKeyMode::Restrict:
                return 'RESTRICT';
            default:
                throw new \InvalidArgumentException();
        }
    }

    public function DropForeignKey(IConnection $Connection, 
            QueryBuilder $QueryBuilder, Table $Table, Traits\ForeignKey $Trait) {
        QueryBuilderExtensions::AppendAlterTable($QueryBuilder, $Table);
        $QueryBuilder->AppendIdentifier('DROP FOREIGN KEY #', [$Trait->GetName()]);
    }
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Index">
    public function Index(
            QueryBuilder $QueryBuilder, Table $Table, Mysql\Tables\Index $Trait) {
        $QueryBuilder->AppendIdentifier($this->MapIndexType($Trait->GetType()) . ' INDEX # ', [$Trait->GetName()]);
        $QueryBuilder->Append('(');
        $First = true;
        foreach ($Trait->GetColumns() as $Column) {
            if ($First)
                $First = false;
            else
                $QueryBuilder->Append(',');


            $ColumnName = $Column->GetName();
            $QueryBuilder->AppendIdentifier('#', [$ColumnName]);
            $QueryBuilder->Append(' ' . $this->MapIndexDirection($Trait->GetColumnDirection($QueryBuilder)));
        }
        $QueryBuilder->Append(')');
        $StorageType = $Trait->GetStorageType();
        if($StorageType !== null)
            $QueryBuilder->Append('USING' . $this->MapIndexStorageType($Trait->GetStorageType()));
    }

    private function MapIndexType($Type) {
        switch ($Type) {
            case Traits\IndexType::Plain:
                return '';
            case Traits\IndexType::Unique:
                return 'UNIQUE';
            case Traits\IndexType::FullText:
                return 'FULLTEXT';
            default:
                throw new \InvalidArgumentException();
        }
    }

    private function MapIndexDirection($Direction) {
        switch ($Direction) {
            case Traits\IndexDirection::Ascending:
                return 'ASC';
            case Traits\IndexDirection::Descending:
                return 'DESC';
            default:
                throw new \InvalidArgumentException();
        }
    }

    private function MapIndexStorageType($StorageType) {
        switch ($StorageType) {
            case Mysql\Tables\IndexStorageType::BTree:
                return 'BTREE';
            case Mysql\Tables\IndexStorageType::RTree:
                return 'RTREE';
            case Mysql\Tables\IndexStorageType::Hash:
                return 'HASH';
            default:
                throw new \InvalidArgumentException();
        }
    }

    public function DropIndex(IConnection $Connection, 
            QueryBuilder $QueryBuilder, Table $Table, Mysql\Tables\Index $Trait) {
        QueryBuilderExtensions::AppendAlterTable($QueryBuilder, $Table);
        $QueryBuilder->AppendIdentifier('DROP INDEX #', [$Trait->GetName()]);
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Primary Key">
    public function PrimaryKey(
            QueryBuilder $QueryBuilder, Table $Table, Traits\PrimaryKey $Trait) {
        $QueryBuilder->AppendIdentifiers('PRIMARY KEY (#)', $Trait->GetColumnNames(), ',');
    }

    public function DropPrimaryKey(IConnection $Connection, 
            QueryBuilder $QueryBuilder, Table $Table, Traits\PrimaryKey $Trait) {
        QueryBuilderExtensions::AppendAlterTable($QueryBuilder, $Table);
        $QueryBuilder->Append('DROP PRIMARY KEY');
    }
    // </editor-fold>
}

?>