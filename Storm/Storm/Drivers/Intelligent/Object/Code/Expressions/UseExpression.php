<?php

namespace Storm\Drivers\Intelligent\Object\Code\Expressions;

class UseExpression extends Expression {
    private $UsedName;
    private $HasAlias;
    private $Alias;
    
    public function __construct($UsedName, $Alias = null) {
        $this->UsedName = $UsedName;
        $this->HasAlias = $Alias !== null;
        $this->Alias = $Alias;
    }
    
    public function GetUsedName() {
        return $this->UsedName;
    }

    public function HasAlias() {
        return $this->HasAlias;
    }

    public function GetAlias() {
        return $this->Alias;
    }

    protected function CompileCode(&$Code) {
        $Code .= 'use ' . $this->UsedName;
        if($this->HasAlias)
            $Code .= ' as ' . $this->Alias;
    }
}

?>