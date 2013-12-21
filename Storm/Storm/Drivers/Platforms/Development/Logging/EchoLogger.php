<?php

namespace Storm\Drivers\Platforms\Development\Logging;

class EchoLogger implements ILogger {
    public function Log($Output) {
        echo $Output . '<br />';
    }
}

?>