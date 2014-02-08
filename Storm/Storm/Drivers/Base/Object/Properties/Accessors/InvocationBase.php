<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object;

class InvocationBase extends FunctionBase {
    protected $ConstantArguments;
    /**
     *
     * @var \ReflectionMethod 
     */
    protected $Reflection;
    public function __construct(array $ConstantArguments = array()) {
        $this->ConstantArguments = $ConstantArguments;
    }

    final public function Identifier(&$Identifier) {
        $Identifier .= $this->Format('__invoke', $this->ConstantArguments);
    }

    final public function SetEntityType($EntityType) {
        if(!method_exists($EntityType, '__invoke')) {
            throw new Object\ObjectException(
                    'The entity of type %s does not contain a valid method __invoke()',
                    $EntityType);
        }
        $this->Reflection = new \ReflectionMethod($EntityType, '__invoke');
    }
}

?>