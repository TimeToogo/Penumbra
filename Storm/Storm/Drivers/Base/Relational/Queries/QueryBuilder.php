<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Drivers\Base\Relational;
use \Storm\Core\Relational\Expression;
use \Storm\Core\Relational\Criterion;
use \Storm\Core\Relational\Select;
use \Storm\Core\Relational\Update;

class QueryBuilder {
    const DefaultPlaceholder = '#';
    private $Connection;
    private $ParameterPlaceholder;
    private $Bindings;
    private $QueryString = '';
    private $ExpressionCompiler;
    private $CriterionCompiler;
    private $QueryCompiler;
    private $ProcedureCompiler;
    private $IdentifierEscaper;
    
    public function __construct(
            IConnection $Connection,
            $ParameterPlaceholder,
            Bindings $Bindings,
            IExpressionCompiler $ExpressionCompiler,
            ICriterionCompiler $CriterionCompiler,
            IQueryCompiler $QueryCompiler,
            IUpdateCompiler $ProcedureCompiler,
            IIdentifierEscaper $IdentifierEscaper) {
        $this->Connection = $Connection;
        $this->ParameterPlaceholder = $ParameterPlaceholder;
        $this->Bindings = $Bindings;
        $this->ExpressionCompiler = $ExpressionCompiler;
        $this->CriterionCompiler = $CriterionCompiler;
        $this->QueryCompiler = $QueryCompiler;
        $this->ProcedureCompiler = $ProcedureCompiler;
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
     * @return ICriterionCompiler
     */
    final public function GetCriterionCompiler() {
        return $this->CriterionCompiler;
    }
    
    /**
     * @return IQueryCompiler
     */
    final public function GetQueryCompiler() {
        return $this->QueryCompiler;
    }
    
    /**
     * @return IQuery
     */
    final public function Build() {
        return $this->Connection->Prepare($this->QueryString, $this->Bindings);
    }
    
    final public function Delimit($Iteratable, $Delimiter) {
        $Iterator = is_array($Iteratable) ? 
                new \ArrayIterator($Iteratable) : new \IteratorIterator($Iteratable);
        $First = true;
        return new \CallbackFilterIterator($Iterator, 
                function () use (&$First, &$Delimiter) {
                    if($First) $First = false;
                    else 
                        $this->QueryString .= $Delimiter;
                    
                    return true;
                });
    }
    
    final public function Append($QueryString) {
        $this->QueryString .= $QueryString;
    }
    
    // <editor-fold defaultstate="collapsed" desc="Expresion appenders">
    
    final public function AppendExpression(Expression $Expression) {
        $this->ExpressionCompiler->Append($this, $Expression);
    }


    final public function AppendExpressions(array $Expressions, $Delimiter) {
        $First = true;
        foreach ($Expressions as $Expression) {
            if ($First)
                $First = false;
            else
                $this->QueryString .= $Delimiter;


            $this->AppendExpression($Expression);
        }
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Query Appenders">

    final public function AppendSelect(Select $Select) {
        $this->QueryCompiler->AppendSelect($this, $Select);
    }

    final public function AppendUpdate(Update $Update) {
        $this->QueryCompiler->AppendUpdate($this, $Update);
    }
    
    final public function AppendTableDefinition(Criterion $Criterion) {
        $this->CriterionCompiler->AppendTableDefinition($this, $Criterion);
    }

    final public function AppendWhere(Criterion $Criterion) {
        $this->CriterionCompiler->AppendWhere($this, $Criterion);
    }

    final public function AppendOrderBy(Criterion $Criterion) {
        $this->CriterionCompiler->AppendOrderBy($this, $Criterion);
        
    }
    
    final public function AppendRange(Criterion $Criterion) {
        $this->CriterionCompiler->AppendRange($this, $Criterion);
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Placeholders">
    
    final public function AppendParameterPlaceholder($QueryStringFormat, $ValuePlaceholder = self::DefaultPlaceholder) {
        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $this->ParameterPlaceholder);
    }

    final public function AppendParameterPlaceholders($QueryStringFormat, $Count, $Delimiter, $ValuePlaceholder = self::DefaultPlaceholder) {
        $Placeholders = implode($Delimiter, array_fill(0, $Count, $this->ParameterPlaceholder));
        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $Placeholders);
    }

    // </editor-fold>
    
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
        $this->Bindings->DefaultParameterType($ParameterType, $Value);
        $EscapedValue = $this->Connection->Escape($Value, $ParameterType);


        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $EscapedValue);
    }


    final public function AppendAllEscaped($QueryStringFormat, array $Values, $Delimiter, $ValuePlaceholder = self::DefaultPlaceholder) {
        $EscapedValues = [];
        foreach ($Values as $Value) {
            $ParameterType = null;
            $this->Bindings->DefaultParameterType($ParameterType, $Value);
            $EscapedValues[] = $this->Connection->Escape($Value, $ParameterType);
        }


        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, implode($Delimiter, $EscapedValues));
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Bound Value Appenders">
    
    final public function AppendValue($QueryStringFormat, $Value, $ParameterType = null, $ValuePlaceholder = self::DefaultPlaceholder) {
        $PlaceholderCount = substr_count($QueryStringFormat, $ValuePlaceholder);
        for($Count = 0; $Count < $PlaceholderCount; $Count++) {
            $this->Bindings->Bind($Value, $ParameterType);
        }
        
        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $this->ParameterPlaceholder);
    }
    
    final public function AppendSingleValue($Value, $ParameterType = null) {
        $this->Bindings->Bind($Value, $ParameterType);
        
        $this->QueryString .= $this->ParameterPlaceholder;
    }


    final public function AppendValues($QueryStringFormat, array $Values, $Delimiter, $ValuePlaceholder = self::DefaultPlaceholder) {
        $PlaceholderCount = substr_count($QueryStringFormat, $ValuePlaceholder);
        foreach ($Values as $Value) {
            for ($Count = 0; $Count < $PlaceholderCount; $Count++) {
                $this->Bindings->Bind($Value);
            }
        }

        $QueryPlaceholders = implode($Delimiter, array_fill(0, count($Values), $this->ParameterPlaceholder));
        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $QueryPlaceholders);
    }
    
    final public function AppendColumnData(Relational\Columns\Column $Column, $Value) {     
        $ValueExpression = Relational\Expressions\Expression::Constant($Value);
        $PersistExpression = $Column->GetDataType()->GetPersistExpression($ValueExpression);
        
        if($ValueExpression === $PersistExpression) {
            $this->AppendSingleValue($Value);
        }
        else {
            $this->ExpressionCompiler->Append($this, $PersistExpression);
        }
    }
    
    final public function AppendAllColumnData(\Storm\Core\Relational\ColumnData $Data, $Delimiter) {     
        $First = true;
        foreach($Data->GetData() as $ColumnIdentifier => $Value) {
            if($First) $First = false;
            else
                $this->QueryString .= $Delimiter;
            
            $Column = $Data->GetColumn($ColumnIdentifier);
            $this->AppendColumnData($Column, $Value);
        }
    }
    
    // </editor-fold>
    
    final private function ReplacePlaceholder($QueryStringFormat, $Placeholder, $Value) {
        return str_replace($Placeholder, $Value, $QueryStringFormat);
    }
}

?>
