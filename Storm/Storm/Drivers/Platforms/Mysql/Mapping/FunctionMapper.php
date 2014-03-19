<?php

namespace Storm\Drivers\Platforms\Mysql\Mapping;

use \Storm\Drivers\Platforms\Standard\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

class FunctionMapper extends Mapping\FunctionMapper {
    
    protected function MatchingFunctions() {
        return [
            'strtoupper' => 'UPPER',
            'strtolower' => 'LOWER',
            'strlen' => 'LENGTH',
            'strrev' => 'REVERSE',
            'chr' => 'CHAR',
            'ord' => 'ASCII',
            'base64_encode' => 'TO_BASE64',
            'base64_encode' => 'TO_BASE64',
            'bin2hex' => 'HEX',
            'hex2bin' => 'UNHEX',
            'soundex' => 'SOUNDEX',
            
            'time' => 'UNIX_TIMESTAMP',
            
            'abs' => 'ABS',
            'pow' => 'POW',
            'ceil' => 'CEIL',
            'floor' => 'FLOOR',
            'sqrt' => 'SQRT',
            'base_convert' => 'CONV',
            'deg2rad' => 'RADIANS',
            'rad2deg' => 'DEGREES',
            'acos' => 'ACOS',
            'asin' => 'ASIN',
            'atan' => 'ATAN',
            'atan2' => 'ATAN2',
            'cos' => 'COS',
            'log' => 'LOG',
            'log10' => 'LOG10',
            'fmod' => 'MOD',
            'sin' => 'SIN',
            'tan' => 'TAN',
            
            'crc32' => 'CRC32',
            'crypt' => 'ENCRYPT',
        ];
    }
    
    // <editor-fold defaultstate="collapsed" desc="String functions">
    
    public function substr(&$MappedName, array &$ArgumentExpressions) {
        $MappedName = 'SUBSTR';
        
        $ArgumentExpressions[1] = $this->Add($ArgumentExpressions[1], 1);
    }

    public function strpos(&$MappedName, array &$ArgumentExpressions, $CaseSensitive = true) {
        $MappedName = 'LOCATE';


        $HaystackStringExpression = $ArgumentExpressions[0];
        $NeedleStringExpression = $ArgumentExpressions[1];
        //Flip order
        $ArgumentExpressions[1] = $HaystackStringExpression;
        $ArgumentExpressions[0] = $NeedleStringExpression;
        if ($CaseSensitive) {
            $ArgumentExpressions[1] = $this->Binary($ArgumentExpressions[1]);
        }
        if (isset($ArgumentExpressions[2])) {
            $ArgumentExpressions[2] = $this->Add($ArgumentExpressions[2], 1);
        }

        return $this->Subtract(R\Expression::FunctionCall($MappedName, $ArgumentExpressions), 1);
    }
    
    public function stripos(&$MappedName, array &$ArgumentExpressions) {
        return $this->strpos($MappedName, $ArgumentExpressions, false);
    }

    public function trim(&$MappedName, array &$ArgumentExpressions, $Direction = 'BOTH') {
        $MappedName = 'TRIM';
        $TrimCharacters = isset($ArgumentExpressions[1]) ?
                $ArgumentExpressions[1] : R\Expression::BoundValue(" \t\n\r\0\x0B");


        if (!($TrimCharacters instanceof EE\ConstantExpression) ||
                strlen($TrimCharacters->GetValue()) !== 1) {
            
            throw new PlatformException(
                    'Mysql does not support trimming a set of characters');
        }

        $ArgumentExpressions = [
            R\Expression::Multiple(
                    [R\Expression::Keyword($Direction),
                        $TrimCharacters,
                        R\Expression::Keyword('FROM'),
                        $ArgumentExpressions[0]]
            )
        ];
    }


    public function rtrim(&$MappedName, array &$ArgumentExpressions) {
        return $this->trim($MappedName, $ArgumentExpressions, 'LEADING');
    }


    public function ltrim(&$MappedName, array &$ArgumentExpressions) {
        return $this->trim($MappedName, $ArgumentExpressions, 'TRAILING');
    }


    public function preg_match(&$MappedName, array &$ArgumentExpressions) {
        if(count($ArgumentExpressions) > 2) {
            throw new PlatformException(
                    'function preg_match cannot be called with more than two arguments');
        }
        
        return R\Expression::BinaryOperation(
                        $ArgumentExpressions[1], 
                        R\Operators\Binary::MatchesRegularExpression, 
                        $this->Binary($ArgumentExpressions[0]));

    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Date and time functions">
    
    public function strftime(&$MappedName, array &$ArgumentExpressions) {
        $MappedName = 'DATE_FORMAT';
        
        if(!isset($ArgumentExpressions[1])) {
            $this->MapFunctionCallExpression('time');
        }        
        $ArgumentExpressions[1] = R\Expression::FunctionCall('FROM_UNIXTIME', [$ArgumentExpressions[1]]);
        
        $ArgumentExpressions = array_reverse($ArgumentExpressions);
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Security functions">
    
    private function RawOutput(&$MappedName, array &$ArgumentExpressions, $RawOutputIndex) {
        if(isset($ArgumentExpressions[$RawOutputIndex])) {
            $RawOutputExpression = $ArgumentExpressions[$RawOutputIndex];
            unset($ArgumentExpressions[$RawOutputIndex]);
            
            $Md5FunctionCall = R\Expression::FunctionCall($MappedName, $ArgumentExpressions);
            
            return R\Expression::Conditional(
                    $RawOutputExpression,
                    R\Expression::FunctionCall('UNHEX', [$Md5FunctionCall]),
                    $Md5FunctionCall);
        }
    }
    
    public function hash(&$MappedName, array &$ArgumentExpressions) {
        $HashNameExpression = $ArgumentExpressions[0];
        unset($ArgumentExpressions[0]);
        
        if(!($HashNameExpression instanceof EE\ConstantExpression)) {
            throw new PlatformException('Hash algorithm must be a constant value');
        }
        $HashName = $HashNameExpression->GetValue();
        $DataExpression = $ArgumentExpressions[1];
        
        switch ($HashName) {
            case 'md5':
                return $this->MapFunctionCallExpression('md5', $ArgumentExpressions);
                
            case 'sha1':
                return $this->MapFunctionCallExpression('sha1', $ArgumentExpressions);
                
            case 'sha224':
            case 'sha256':
            case 'sha384':
            case 'sha512':
                $MappedName = 'SHA2';
                $ShaLength = (int)substr($HashName, 3);
                $ArgumentExpressions[0] = $DataExpression;
                $ArgumentExpressions[1] = R\Expression::BoundValue($ShaLength);
                break;
            
            default:
                throw new PlatformException('Unsupported hash algorithm: must be one of md5, sha1, sha224, sha256, sha384 or sha512, %s given ', $HashName);
        }
        
        
        return $this->RawOutput($MappedName, $ArgumentExpressions, 2);
    }
            
    public function md5(&$MappedName, array &$ArgumentExpressions) {
        $MappedName = 'MD5';
        
        return $this->RawOutput($MappedName, $ArgumentExpressions, 1);
    }
    public function sha1(&$MappedName, array &$ArgumentExpressions) {
        $MappedName = 'SHA1';
        
        return $this->RawOutput($MappedName, $ArgumentExpressions, 1);
    }
    
    
    public function ParseMcryptArguments(array &$ArgumentExpressions) {
        if(!($ArgumentExpressions[0] instanceof EE\ConstantExpression)) {
            throw new PlatformException('Cipher algorithm must be constant');
        }
        
        if(!($ArgumentExpressions[3] instanceof EE\ConstantExpression)) {
            throw new PlatformException('Cipher mode must be constant');
        }
        
        if(isset($ArgumentExpressions[4])) {
            throw new PlatformException('Mysql does not support a custom IV');
        }
        
        $Algorithm = $ArgumentExpressions[0]->GetValue();
        $Mode = $ArgumentExpressions[3]->GetValue();
        
        if($Mode !== MCRYPT_MODE_ECB) {
            throw new PlatformException('Mysql only support ECB cipher mode');
        }
        
        unset($ArgumentExpressions[0]);
        unset($ArgumentExpressions[3]);
        //Flip key/data order
        $ArgumentExpressions = array_reverse($ArgumentExpressions);
        
        return [$Algorithm, $Mode];
    }
    
    private static $EncryptCipherAlgorithms = [
        MCRYPT_RIJNDAEL_128 => 'AES_ENCRYPT',
        MCRYPT_TRIPLEDES => 'DES_ENCRYPT',
    ];
    private static $DecryptCipherAlgorithms = [
        MCRYPT_RIJNDAEL_128 => 'AES_DECRYPT',
        MCRYPT_TRIPLEDES => 'DES_DECRYPT',
    ];
    public function mcrypt_encrypt(&$MappedName, array &$ArgumentExpressions) {
        list($Algorithm, $Mode) = $this->ParseMcryptArguments($ArgumentExpressions);
        
        if(isset(self::$EncryptCipherAlgorithms[$Algorithm])) {
            $MappedName = self::$EncryptCipherAlgorithms[$Algorithm];
        }
        else {
            throw new PlatformException(
                    'Unsupported cipher algorithm: must be MCRYPT_RIJNDAEL_128 or MCRYPT_TRIPLEDES, %s given', 
                    $Algorithm);
        }
    }
    
    public function mcrypt_decrypt(&$MappedName, array &$ArgumentExpressions) {
        list($Algorithm, $Mode) = $this->ParseMcryptArguments($ArgumentExpressions);
        
        if(isset(self::$DecryptCipherAlgorithms[$Algorithm])) {
            $MappedName = self::$DecryptCipherAlgorithms[$Algorithm];
        }
        else {
            throw new PlatformException(
                    'Unsupported cipher algorithm: must be MCRYPT_RIJNDAEL_128 or MCRYPT_TRIPLEDES, %s given', 
                    $Algorithm);
        }
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Numeric functions">
    
    public function pi(&$MappedName, array &$ArgumentExpressions) {
        return R\Expression::BinaryOperation(
                R\Expression::FunctionCall('PI'), 
                R\Operators\Binary::Addition,
                R\Expression::Literal('0.0000000000000'));
    }
    
    public function round(&$MappedName, array &$ArgumentExpressions) {
        $MappedName = 'ROUND';
        if(isset($ArgumentExpressions[2])) {
            throw new PlatformException('Mysql does not support rounding modes');
        }
    }
    
    private function RandomInt(CoreExpression $Minimum, CoreExpression $Maximum) {
        //Add one due to flooring the random value
        $Maximum = $this->Add($Maximum, 1);
                
        $DifferenceExpression = R\Expression::BinaryOperation(
                $Maximum, 
                R\Operators\Binary::Subtraction, 
                $Minimum);

        return $this->MapFunctionCallExpression('floor', 
                [R\Expression::BinaryOperation(
                        $Minimum, 
                        R\Operators\Binary::Addition, 
                        R\Expression::BinaryOperation(
                                R\Expression::FunctionCall('RAND'), 
                                R\Operators\Binary::Multiplication, 
                                $DifferenceExpression))]);
    }
    public function RandomIntFromArguments(array &$ArgumentExpressions, $DefaultMinimum, $DefaultMaximum) {
        $Minimum = null;
        $Maximum = null;
        if(count($ArgumentExpressions) === 0) {
            $Minimum = new EE\ConstantExpression($DefaultMinimum);
            $Maximum = new EE\ConstantExpression($DefaultMaximum);
        }
        else {
            $Minimum = $ArgumentExpressions[0];
            $Maximum = $ArgumentExpressions[1];
        }
        return $this->RandomInt($Minimum, $Maximum);
    }
    
    public function rand(&$MappedName, array &$ArgumentExpressions) {
        return $this->RandomIntFromArguments($ArgumentExpressions, 0, getrandmax());
    }
    
    public function mt_rand(&$MappedName, array &$ArgumentExpressions) {
        return $this->RandomIntFromArguments($ArgumentExpressions, 0, mt_getrandmax());
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Misc functions">
    
    public function in_array(&$MappedName, array &$ArgumentExpressions) {
        return R\Expression::BinaryOperation(
                $ArgumentExpressions[0], 
                R\Operators\Binary::In, 
                $ArgumentExpressions[1]);
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Helpers">
    
    private function Add(CoreExpression $Expression, $Value) {
        return R\Expression::BinaryOperation(
                $Expression, 
                R\Operators\Binary::Addition, 
                R\Expression::BoundValue($Value));
    }

    private function Subtract(CoreExpression $Expression, $Value) {
        return R\Expression::BinaryOperation(
                $Expression, 
                R\Operators\Binary::Subtraction, 
                R\Expression::BoundValue($Value));
    }

    private function Binary(CoreExpression $Expression) {
        return R\Expression::Multiple([R\Expression::Keyword('BINARY'), $Expression]);
    }

    // </editor-fold>
}

?>