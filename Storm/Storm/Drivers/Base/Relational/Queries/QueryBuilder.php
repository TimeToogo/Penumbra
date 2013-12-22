<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Drivers\Base\Relational;
use \Storm\Core\Relational\Request;
use \Storm\Core\Relational\Operation;
use \Storm\Core\Relational\Constraints\Predicate;

class QueryBuilder {
    const DefaultPlaceholder = '#';
    private $Connection;
    private $ParameterPlaceholder;
    private $Bindings;
    private $QueryString = '';
    private $ExpressionCompiler;
    private $RequestCompiler;
    private $PredicateCompiler;
    private $IdentifierEscaper;
    
    public function __construct(
            IConnection $Connection,
            $ParameterPlaceholder,
            Bindings $Bindings,
            IExpressionCompiler $ExpressionCompiler,
            IRequestCompiler $RequestCompiler,
            IPredicateCompiler $PredicateCompiler,
            IIdentifierEscaper $IdentifierEscaper) {
        $this->Connection = $Connection;
        $this->ParameterPlaceholder = $ParameterPlaceholder;
        $this->Bindings = $Bindings;
        $this->ExpressionCompiler = $ExpressionCompiler;
        $this->RequestCompiler = $RequestCompiler;
        $this->PredicateCompiler = $PredicateCompiler;
        $this->IdentifierEscaper = $IdentifierEscaper;
    }
    
    /**
     * @return Bindings
     */
    final public function GetBindings() {
        return $this->Bindings;
    }
    
    final public function GetParameterPlaceholder() {
        return $this->ParameterPlaceholder;
    }

    final public function GetQueryString() {
        return $this->QueryString;
    }
    
    /**
     * @return IIdentifierEscaper
     */
    final public function GetIdentifierEscaper() {
        return $this->IdentifierEscaper;
    }
    
    /**
     * @return IExpressionCompiler
     */
    final public function GetExpressionCompiler() {
        return $this->ExpressionCompiler;
    }
    
    /**
     * @return IRequestCompiler
     */
    final public function GetRequestCompiler() {
        return $this->RequestCompiler;
    }

    /**
     * @return IPredicateCompiler
     */
    final public function GetPredicateCompiler() {
        return $this->PredicateCompiler;
    }

       
    /**
     * @return IQuery
     */
    final public function Build() {
        return $this->Connection->Prepare($this->QueryString, $this->Bindings);
    }
    
    final public function Append($QueryString) {
        $this->QueryString .= $QueryString;
    }
    
    final public function AppendRequest(Request $Request) {
        $this->RequestCompiler->AppendRequest($this, $Request);
    }
    
    final public function AppendOperation(Operation $Operation) {
        $this->RequestCompiler->AppendOperation($this, $Operation);
    }
    
    final public function AppendPredicate(Predicate $Predicate) {
        $this->PredicateCompiler->Append($this, $Predicate);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Identifier Appenders">
    final public function AppendIdentifier($QueryStringFormat, array $IdentifierSegments, $ValuePlaceholder = self::DefaultPlaceholder) {
        $EscapedIdentifier = $this->IdentifierEscaper->Escape($IdentifierSegments);
        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $EscapedIdentifier);
    }

    final public function AppendIdentifiers($QueryStringFormat, array $Identifiers, $Delimiter, $ValuePlaceholder = self::DefaultPlaceholder) {
        $EscapedIdentifiers = $this->EscapeIdentifiers($Identifiers);

        $this->AppendEscapedIdenitefiers($QueryStringFormat, $EscapedIdentifiers, $Delimiter, $ValuePlaceholder);
    }


    final public function AppendColumn($QueryStringFormat, 
            Relational\Columns\Column $Column, $UseColumnFormat = true, $Alias = true, $ValuePlaceholder = self::DefaultPlaceholder) {
        
        $EscapedIdentifier = $this->GetColumnIdentifier($Column->GetTable()->GetName(), $Column->GetName(), $Column, 
                $UseColumnFormat, $Alias);
        
        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $EscapedIdentifier);
    }
    
    final public function AppendColumns($QueryStringFormat, array $Columns, $Delimiter, $ValuePlaceholder = self::DefaultPlaceholder) {
        $ColumnIdentifiers = $this->GetColumnIdentifiers($Columns);

        $this->AppendEscapedIdenitefiers($QueryStringFormat, $ColumnIdentifiers, $Delimiter, $ValuePlaceholder);
    }


    final public function AppendTableColumns($QueryStringFormat, Relational\Table $Table, $Delimiter, $ValuePlaceholder = self::DefaultPlaceholder) {
        $ColumnIdentifiers = $this->GetColumnIdentifiers($Table->GetColumns());

        $this->AppendEscapedIdenitefiers($QueryStringFormat, $ColumnIdentifiers, $Delimiter, $ValuePlaceholder);
    }

    private function AppendEscapedIdenitefiers($QueryStringFormat, array $Identifiers, $Delimiter, $ValuePlaceholder) {
        $QueryIdentifiers = implode($Delimiter, $Identifiers);
        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $QueryIdentifiers);
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Identifier Helpers">
    private function GetColumnIdentifiers(array $Columns) {
        $Identifiers = array();
        foreach ($Columns as $ColumnName => $Column) {
            $Identifiers[] = $this->GetColumnIdentifier($Column->GetTable()->GetName(), $ColumnName, $Column);
        }

        return $Identifiers;
    }
    
    private function GetColumnIdentifier($TableName, $ColumnName, Relational\Columns\Column $Column, $UseColumnFormat = true, $Alias = true) {
        $EscapedIdentifier = $this->IdentifierEscaper->Escape([$TableName, $ColumnName]);
        
        if($UseColumnFormat) {
            $this->ExpressionCompiler->Append($this, 
                    $Column->GetDataType()->GetReviveExpression(Relational\Expressions\Expression::Column($Column)));            
        }
        else {
            $FullIdentifier = $EscapedIdentifier;
        }
        
        if($Alias) {
            $FullIdentifier = $this->IdentifierEscaper->Alias($FullIdentifier, $ColumnName);
        }
        
        return $FullIdentifier;
    }

    private function EscapeIdentifiers(array $Identifiers) {
        foreach ($Identifiers as &$IdentifierSegments) {
            if (is_string($IdentifierSegments))
                $IdentifierSegments = [$IdentifierSegments];
        }
        
        return $this->IdentifierEscaper->EscapeAll($Identifiers);
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Escaped Value Appenders">
    final public function AppendEscaped($QueryStringFormat, $Value, $ParameterType = null, $ValuePlaceholder = self::DefaultPlaceholder) {
        $this->ParameterType($ParameterType, $Value);
        $EscapedValue = $this->Connection->Escape($Value, $ParameterType);


        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $EscapedValue);
    }


    final public function AppendAllEscaped($QueryStringFormat, array $Values, $Delimiter, $ValuePlaceholder = self::DefaultPlaceholder) {
        $EscapedValues = array();
        foreach ($Values as $Value) {
            $EscapedValues[] = $this->Connection->Escape($Value, $this->DefaultParameterType($Value));
        }


        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, implode($Delimiter, $EscapedValues));
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Bound Value Appenders">
    final public function AppendReference($QueryStringFormat, &$Value, $ParameterType = null, $ValuePlaceholder = self::DefaultPlaceholder) {
        $this->ParameterType($ParameterType, $Value);
        $this->Bindings->Bind($Value, $ParameterType);


        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $this->ParameterPlaceholder);
    }


    final public function AppendValue($QueryStringFormat, $Value, $ParameterType = null, $ValuePlaceholder = self::DefaultPlaceholder) {
        $this->ParameterType($ParameterType, $Value);
        $this->Bindings->Bind($Value, $ParameterType);


        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $this->ParameterPlaceholder);
    }


    final public function AppendAll($QueryStringFormat, array $Values, $Delimiter, $ValuePlaceholder = self::DefaultPlaceholder) {

        foreach ($Values as $Value) {
            $this->Bindings->Bind($Value, $this->DefaultParameterType($Value));
        }


        $QueryPlaceholders = implode($Delimiter, array_fill(0, count($Values), $this->ParameterPlaceholder));
        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $QueryPlaceholders);
    }
    
    final public function AppendColumnData(Relational\Columns\Column $Column, $Value) {     
        $this->ExpressionCompiler->Append($this, 
                $Column->GetDataType()->GetPersistExpression(
                        Relational\Expressions\Expression::Constant($Value)));
    }
    
    final public function AppendAllColumnData(\Storm\Core\Relational\ColumnData $Data, $Delimiter) {     
        $First = true;
        foreach($Data->GetColumnData() as $ColumnIdentifier => $Value) {
            if($First) $First = false;
            else
                $this->QueryString .= $Delimiter;
            
            $Column = $Data->GetColumn($ColumnIdentifier);
            $this->AppendColumnData($Column, $Value);
        }
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Parameter Type Helpers">
    final private function ParameterType(&$ParameterType, $Value) {
        if ($ParameterType === null)
            $ParameterType = $this->DefaultParameterType($Value);
    }


    final private function DefaultParameterType($Value) {
        if (is_string($Value))
            return ParameterType::String;
        else if (is_bool($Value))
            return ParameterType::Boolean;
        else if (is_integer($Value))
            return ParameterType::Integer;
        else if ($Value === null)
            return ParameterType::Null;
        else
            return ParameterType::Binary;
    }
    // </editor-fold>
    
    final private function ReplacePlaceholder($QueryStringFormat, $Placeholder, $Value) {
        return str_replace($Placeholder, $Value, $QueryStringFormat);
    }
}

?>
