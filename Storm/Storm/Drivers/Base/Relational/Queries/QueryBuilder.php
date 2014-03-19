<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Drivers\Base\Relational;
use \Storm\Core\Relational\Expression;
use \Storm\Core\Relational\Select;
use \Storm\Core\Relational\Update;
use \Storm\Core\Relational\Delete;

class QueryBuilder {
    const DefaultPlaceholder = '#';
    private $Connection;
    private $ParameterPlaceholder;
    private $Bindings;
    private $QueryString = '';
    
    private $ExpressionCompiler;
    private $QueryCompiler;
    private $IdentifierEscaper;
    
    public function __construct(
            IConnection $Connection,
            $ParameterPlaceholder,
            Bindings $Bindings,
            IExpressionCompiler $ExpressionCompiler,
            IQueryCompiler $QueryCompiler,
            IIdentifierEscaper $IdentifierEscaper) {
        $this->Connection = $Connection;
        $this->ParameterPlaceholder = $ParameterPlaceholder;
        $this->Bindings = $Bindings;
        
        $this->ExpressionCompiler = $ExpressionCompiler;
        $this->QueryCompiler = $QueryCompiler;
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
    
    /**
     * Returns an iterator which will append the delimiter inbetween every iteration.
     * NOTE: Do not use this if the iteration can be interrupted (break, continue), the
     * delimiter will not be appended properly.
     * 
     * @param array|Traversable $Iteratable
     * @param string $Delimiter
     * @return \CallbackFilterIterator
     */
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
    
    /**
     * @var Relational\Expressions\ExpressionWalker[]
     */
    private $ExpressionWalkers = [];
    
    final public function AddExpressionWalker(Relational\Expressions\ExpressionWalker $Walker) {
        if(!in_array($Walker, $this->ExpressionWalkers, true)) {
            $this->ExpressionWalkers[] = $Walker;
        }
    }
    
    final public function RemoveExpressionWalker(Relational\Expressions\ExpressionWalker $Walker) {
        foreach ($this->ExpressionWalkers as $Key => $OtherWalker) {
            if($Walker === $OtherWalker) {
                unset($this->ExpressionWalkers[$Key]);
                return;
            }
        }
    }
    
    final public function AppendExpression(Expression $Expression) {
        foreach($this->ExpressionWalkers as $Walker) {
            $Expression = $Walker->Walk($Expression);
        }
        $this->ExpressionCompiler->Append($this, $Expression);
    }
    
    final public function AppendExpressions(array $Expressions, $Delimiter) {
        foreach($this->ExpressionWalkers as $Walker) {
            $Expressions = $Walker->WalkAll($Expressions);
        }
        
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

    final public function AppendDelete(Delete $Delete) {
        $this->QueryCompiler->AppendDelete($this, $Delete);
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

    private function AppendEscapedIdentifiers($QueryStringFormat, array $Identifiers, $Delimiter, $ValuePlaceholder) {
        $QueryIdentifiers = implode($Delimiter, $Identifiers);
        $this->QueryString .= $this->ReplacePlaceholder($QueryStringFormat, $ValuePlaceholder, $QueryIdentifiers);
    }
    
    private function EscapeIdentifiers(array $Identifiers) {
        foreach ($Identifiers as &$IdentifierSegments) {
            if (is_string($IdentifierSegments)) {
                $IdentifierSegments = [$IdentifierSegments];
            }
        }
        
        return $this->IdentifierEscaper->EscapeAll($Identifiers);
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Escaped Value Appenders">
    
    final public function AppendSingleEscapedValue($Value, $ParameterType = null) {
        $this->Bindings->DefaultParameterType($ParameterType, $Value);
        $EscapedValue = $this->Connection->Escape($Value, $ParameterType);
        
        $this->QueryString .= $EscapedValue;
    }
    
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
    
    // </editor-fold>
    
    final private function ReplacePlaceholder($QueryStringFormat, $Placeholder, $Value) {
        return str_replace($Placeholder, $Value, $QueryStringFormat);
    }
}

?>
