<?php

namespace Storm\Drivers\Intelligent\Object\Code\Tokens;

use \Storm\Drivers\Intelligent\Object\Pinq\Exceptions\InvalidTokenException;

final class TokenCollection extends \ArrayObject {
    public function __construct($Tokens = array()) {
        parent::__construct($Tokens);
    }
    
    public static function From($PHPSource) {
        $FullSource = '<?php ' . $PHPSource . ' ?>';
        $Tokens = token_get_all($FullSource);
        array_shift($Tokens);
        array_pop($Tokens);
        
        return new self($Tokens);
    }
    
    /**
     * @return Token[]
     */
    public function GetAll() {
        return $this->getArrayCopy();
    }
    
    /**
     * @return Token
     */
    public function First() {
        return reset($this->getArrayCopy());
    }
    
    /**
     * @return Token
     */
    public function Shift() {
        foreach($this->getArrayCopy() as $Key => $Value) {
            unset($this[$Key]);
            return $Value;
        }
        return null;
    }
    
    public function Unshift(Token $Token) {
        $Array = $this->getArrayCopy();
        array_unshift($Array, $Token);
        $this->exchangeArray($Array);
    }
    
    public function RemoveAll($Code) {
        $Tokens = $this->getArrayCopy();
        array_filter($Tokens, function ($Token) use ($Code) {
            return !$Token->Is($Code);
        });
        $this->exchangeArray($Tokens);
    }
    
    public function Contains($Code) {
        $Tokens = $this->getArrayCopy();
        array_filter($Tokens, function ($Token) use ($Code) {
            return $Token->Is($Code);
        });
        
        return count($Tokens) > 0;
    }
    
    /**
     * @return TokenCollection[]
     */
    public function ExplodeBy($Code) {
        $Tokens = $this->getArrayCopy();
        $NewTokenCollections = array(new self());
        $Count = 0;
        array_walk($Tokens, function ($Token) use (&$Code, &$NewTokenCollections, &$Count) {
            if($Token->Is($Code)) {
                if(count($NewTokenCollections[$Count]) > 0) {
                    $Count++;
                    $NewTokenCollections[$Count] = new self();
                }
                return;
            }
            $NewTokenCollections[$Count][] = $Token;
        });
        
        return $NewTokenCollections;
    }
    
    /**
     * @return TokenCollection
     */
    public function GetSection($StartIndex, $ScopeCode, $UnscopeCode) {
        $Tokens = new self();
        $Count = 0;
        foreach(array_slice($this->GetAll(), $StartIndex, null, true) as $Key => $Token) {
            if($Token->Is($ScopeCode))
                $Count++;
            else if($Token->Is($UnscopeCode))
                $Count--;
            
            $Tokens[$Key] = $Token;
            if($Count === 0)
                break;
        }
        
        return $Tokens;
    }
    
    /**
     * @return TokenCollection
     */
    public function SpliceSection($StartIndex, $ScopeCode, $UnscopeCode) {
        $Tokens = $this->GetSection($StartIndex, $ScopeCode, $UnscopeCode);
        foreach($Tokens->GetAll() as $Key => $Token) {
            unset($this[$Key]);
        }
        
        return $Tokens;
    }
    
    
    public function offsetSet($Index, $Token) {
        if(!($Token instanceof Token)) {
            throw new InvalidTokenException();
        }
        parent::offsetSet($Index, $Token);
    }
}

?>