<?php

namespace Storm\Api;

class EntityNotFoundException extends \Storm\Core\StormException {
    public function __construct($EntityType, array $IdentityValues) {
        parent::__construct(
                'Count not find %s with identity: ', 
                $EntityType,
                count($IdentityValues) === 0 ? 'null' : implode(', ', $IdentityValues));
    }
}

?>
