<?php

namespace Storm\Drivers\Base\Object\Procedures;

use \Storm\Core\Object;
use \Storm\Drivers\Base\Object\Requests;
use \Storm\Core\Object\Expressions\AssignmentExpression;

class Procedure extends Requests\Request implements Object\IProcedure {
    private $AssignmentExpressions;
    public function __construct($EntityOrType, array $AssignmentExpressions, $IsSingleEntity = false) {
        $this->AssignmentExpressions = $AssignmentExpressions;
        $Properties = array_map(
                function (AssignmentExpression $Expression) {
                    return $Expression->GetProperty();
                }, $AssignmentExpressions);
        
        parent::__construct($EntityOrType, $Properties, $IsSingleEntity);
    }

    public function GetExpressions() {
        return $this->AssignmentExpressions;
    }
}

?>