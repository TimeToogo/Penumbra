<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

use \Penumbra\Core\Object\Expressions;

abstract class InvocationBase extends MethodBase {
    
    public function __construct(array $ConstantArguments = []) {
        parent::__construct('__invoke', $ConstantArguments);
    }
    
    public function Identifier(&$Identifier) {
        $Identifier .= sprintf('(%s)',
                 implode(', ', array_map(function ($I) { return var_export($I, true); }, $this->ConstantArguments)));
    }
    
    final protected function MatchesInvokeMethodCall(Expressions\Expression $Expression) {
        return $Expression instanceof Expressions\MethodCallExpression
                && $this->MatchesName($Expression->GetNameExpression());
    }
}

?>