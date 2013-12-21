<?php

namespace Storm\Drivers\Intelligent\Object\Code\Tokens;

use \Storm\Drivers\Intelligent\Object\Pinq\Exceptions\InvalidTokenException;

final class Token {
    private $Code;
    private $HasString;
    private $String;
    
    public function __construct($Code, $String = null) {
        $this->Code = $Code;
        $this->String = $String;
        $this->HasString = $this->String !== null;
    }
    
    public static function From($Token) {
        if(is_array($Token)) {
            if(count($Token) < 2) {
                throw new InvalidTokenException();
            }
            return new self($Token[0], $Token[1]);
        }
        else if(is_string($Token)) {
            return new self($Token);
        }
        else 
            throw new InvalidTokenException(); 
    }
    
    public function GetCode() {
        return $this->Code;
    }
    
    public function Is($Code) {
        return $this->Code === $Code;
    }
    
    public function HasString() {
        return $this->HasString;
    }

    public function GetString() {
        return $this->String;
    }
}

?>