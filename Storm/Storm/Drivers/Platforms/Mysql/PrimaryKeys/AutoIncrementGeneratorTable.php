<?php

namespace Storm\Drivers\Platforms\Mysql\PrimaryKeys;

use \Storm\Core\Containers\Registrar;
use \Storm\Core\Relational\Database;
use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Columns\Column;
use \Storm\Drivers\Base\Relational\Columns\DataType;
use \Storm\Drivers\Platforms\Mysql;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

class AutoIncrementGeneratorTable extends Relational\Table implements Relational\PrimaryKeys\IKeyGenerator {
    private $Name;
    
    private $TableNameColumn;
    private $IncrementColumn;
    
    public function __construct($Name) {
        $this->Name = $Name;
        
        $this->TableNameColumn = new Column('Table', new DataType('VARCHAR', [64]));
        $this->IncrementColumn = new Column('Increment', new DataType('INT', [11]), [new Columns\Traits\Increment()]);
        
        parent::__construct();
    }
    
    protected function KeyGenerator() {
        return new \Storm\Drivers\Platforms\Null\NullKeyGenerator();
    }
    
    protected function Name() {
        return $this->Name;
    }

    protected function RegisterColumns(Database $Context, Registrar $Registrar) {
        $Registrar->Register($this->TableNameColumn);
        $Registrar->Register($this->IncrementColumn);
    }

    protected function RegisterStructuralTraits(Registrar $Registrar) {
        $Registrar->Register(new Mysql\Tables\Engine('MYISAM'));
        
        $Registrar->Register(new Relational\Traits\PrimaryKey
                ([$this->TableNameColumn, $this->IncrementColumn]));
    }
    
    protected function RegisterRelationalTraits(Registrar $Registrar, Database $Context) { }

    protected function RegisterToManyRelations(Database $Context, Registrar $Registrar) { }
    protected function RegisterToOneRelations(Database $Context, Registrar $Registrar) { }

    public function FillPrimaryKeys(IConnection $Connection, Relational\Table $Table, array $PrimaryKeys, array $PrimaryKeyColumns) {
        if(count($PrimaryKeys) === 0)
            return;
        
        if(count($PrimaryKeyColumns) !== 1)
            throw new \InvalidArgumentException('Can only generate single increment per table');
        
        $TableName = $Table->GetName();
        
        $QueryBuilder = $Connection->QueryBuilder();
        $QueryBuilder->AppendIdentifier('INSERT INTO # ', [$this->Name]);
        $QueryBuilder->AppendIdentifier('(#)', $this, [$this->Name, $this->TableNameColumn->GetName()]);
        $QueryBuilder->Append(' VALUES ');
        
        $InsertRows = array_fill(0, count($PrimaryKeys), '(#)');
        $QueryBuilder->AppendValue(implode(',', $InsertRows), $TableName);
        
        $QueryBuilder->Build()->Execute();
        
        //Mysql will return the first inserted id
        $IncrementValue = $Connection->GetLastInsertIncrement();
        
        $PrimaryKeyColumn = reset($PrimaryKeyColumns);
        foreach($PrimaryKeys as $PrimaryKey) {
            $PrimaryKey[$PrimaryKeyColumn] = $IncrementValue;
            $IncrementValue++;
        }
    }
}

?>
