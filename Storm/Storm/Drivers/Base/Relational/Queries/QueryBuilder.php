<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Drivers\Base\Relational;
use \Storm\Core\Relational\Request;
use \Storm\Core\Relational\Procedure;
use \Storm\Core\Relational\Constraints\Predicate;
use \Storm\Core\Relational\Expressions\Expression;

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
    
    final public function AppendExpression(Expression $Expression) {
        $this->ExpressionCompiler->Append($this, $Expression);
    }
    
    final public function AppendExpressions(array $Expressions, $Delimiter) {
        $First = true;
        foreach ($Expressions as $Expression) {
            if($First) $First = false;
            else
                $this->QueryString .= $Delimiter;
            
            $this->AppendExpression($Expression);
        }
    }
    
    final public function AppendRequest(Request $Request) {
        $this->RequestCompiler->AppendRequest($this, $Request);
    }
    
    final public function AppendOperation(Procedure $Operation) {
        $this->RequestCompiler->AppendProcedure($this, $Operation);
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

        $this->AppendEscapedIdentifiers($QueryStringFormat, $EscapedIdentifiers, $Delimiter, $ValuePlaceholder);
    }


    final public function AppendColumn($QueryStringFormat, Relational\Columns\Column $Column, $Alias = null, $ValuePlaceholder = self::DefaultPlaceholder) {
        $EscapedIdentifier = $this->GetColumnIdentifier($Column, $Alias);
        
        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $EscapedIdentifier);
    }

    private function AppendEscapedIdentifiers($QueryStringFormat, array $Identifiers, $Delimiter, $ValuePlaceholder) {
        $QueryIdentifiers = implode($Delimiter, $Identifiers);
        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $QueryIdentifiers);
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Identifier Helpers">    
    private function GetColumnIdentifier(Relational\Columns\Column $Column, $Alias = null) {
        $EscapedIdentifier = $this->IdentifierEscaper->Escape([$Column->GetTable()->GetName(), $Column->GetName()]);
        if($Alias !== null) {
            $EscapedIdentifier = $this->IdentifierEscaper->Alias($EscapedIdentifier, $Alias);
        }
        
        return $EscapedIdentifier;
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
                Relational\Expressions\Expression::PersistData($Column,
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
