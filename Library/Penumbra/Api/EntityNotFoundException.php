<?php

namespace Penumbra\Api;

class EntityNotFoundException extends \Penumbra\Core\PenumbraException {
    public function __construct($EntityType, array $IdentityValues) {
        parent::__construct(
                'Count not find %s with identity: ', 
                $EntityType,
                count($IdentityValues) === 0 ? 'null' : implode(', ', $IdentityValues));
    }
}

?>
