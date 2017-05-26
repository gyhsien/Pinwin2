<?php
namespace Pinwin\Crypt;

use Zend\Crypt\BlockCipher;
use Pinwin\Crypt\CryptInterface;
use Pinwin\Debug\Debug;
use Zend\Validator\Explode;

class GrnetCrypt implements CryptInterface
{

    public static $key_path = 'data/persistent/security';
    
    protected $default_key = array(
        '؀',
        '؁',
        '؂',
        '؃',
        '؆',
        '؇',
        '؈',
        '؉',
        '؊',
        '؋',
        '،',
        '؍',
        '؎',
        '؏',
        'ؐ',
        'ؑ',
        'ؒ',
        'ؓ',
        'ؔ',
        'ؕ',
        'ؖ',
        'ؗ',
        'ؘ',
        'ؙ',
        'ؚ',
        '؛',
        '؞',
        '؟',
        'ء',
        'آ',
        'أ',
        'ؤ',
        'إ',
        'ئ',
        'ا',
        'ب',
        'ة',
        'ت',
        'ث',
        'ج',
        'ح',
        'خ',
        'د',
        'ذ',
        'ر',
        'ز',
        'س',
        'ش',
        'ص',
        'ض',
        'ط',
        'ظ',
        'ع',
        'غ',
        'ػ',
        'ؼ',
        'ؽ',
        'ؾ',
        'ؿ',
        'ـ',
        'ف',
        'ق',
        'ك',
        'ل',
        'م',
        'ن',
        'ه',
        'و',
        'ى',
        'ي',
        'ً',
        'ٌ',
        'ٍ',
        'َ',
        'ُ',
        'ِ',
        'ّ',
        'ْ',
        'ٓ',
        'ٔ',
        'ٕ',
        'ٖ',
        'ٗ',
        '٘',
        'ٙ',
        'ٚ',
        'ٛ',
        'ٜ',
        'ٝ',
        'ٞ',
        '٠',
        '١',
        '٢',
        '٣',
        '٤',
        '٥',
        '٦',
        '٧',
        '٨',
        '٩',
        '٪',
        '٫',
        '٬',
        '٭',
        'ٮ',
        'ٯ',
        'ٰ',
        'ٱ',
        'ٲ',
        'ٳ',
        'ٴ',
        'ٵ',
        'ٶ',
        'ٷ',
        'ٸ',
        'ٹ',
        'ٺ',
        'ٻ',
        'ټ',
        'ٽ',
        'پ',
        'ٿ',
        'ڀ',
        'ځ',
        'ڂ',
        'ڃ',
        'ڄ',
        'څ',
        'چ',
        'ڇ',
        'ڈ',
        'ډ',
        'ڊ',
        'ڋ',
        'ڌ',
        'ڍ',
        'ڎ',
        'ڏ',
        'ڐ',
        'ڑ',
        'ڒ',
        'ړ',
        'ڔ',
        'ڕ',
        'ږ',
        'ڗ',
        'ژ',
        'ڙ',
        'ښ',
        'ڛ',
        'ڜ',
        'ڝ',
        'ڞ',
        'ڟ',
        'ڠ',
        'ڡ',
        'ڢ',
        'ڣ',
        'ڤ',
        'ڥ',
        'ڦ',
        'ڧ',
        'ڨ',
        'ک',
        'ڪ',
        'ګ',
        'ڬ',
        'ڭ',
        'ڮ',
        'گ',
        'ڰ',
        'ڱ',
        'ڲ',
        'ڳ',
        'ڴ',
        'ڵ',
        'ڶ',
        'ڷ',
        'ڸ',
        'ڹ',
        'ں',
        'ڻ',
        'ڼ',
        'ڽ',
        'ھ',
        'ڿ',
        'ۀ',
        'ہ',
        'ۂ',
        'ۃ',
        'ۄ',
        'ۅ',
        'ۆ',
        'ۇ',
        'ۈ',
        'ۉ',
        'ۊ',
        'ۋ',
        'ی',
        'ۍ',
        'ێ',
        'ۏ',
        'ې',
        'ۑ',
        'ے',
        'ۓ',
        '۔',
        'ە',
        'ۖ',
        'ۗ',
        'ۘ',
        'ۙ',
        'ۚ',
        'ۛ',
        'ۜ',
        '۝',
        '۞',
        '۟',
        '۠',
        'ۡ',
        'ۢ',
        'ۣ',
        'ۤ',
        'ۥ',
        'ۦ',
        'ۧ',
        'ۨ',
        '۩',
        '۪',
        '۫',
        '۬',
        'ۭ',
        'ۮ',
        'ۯ',
        '۰',
        '۱',
        '۲',
        '۳',
        '۴',
        '۵',
        '۶',
        '۷',
        '۸',
        '۹',
        'ۺ',
        'ۻ',
        'ۼ',
        '۽',
        '۾',
        'ۿ',
        'ݐ',
        'ݑ',
        'ݒ',
        'ݓ',
        'ݔ',
        'ݕ',
        'ݖ',
        'ݗ',
        'ݘ',
        'ݙ',
        'ݚ',
        'ݛ',
        'ݜ',
        'ݝ',
        'ݞ',
        'ݟ',
        'ݠ',
        'ݡ',
        'ݢ',
        'ݣ',
        'ݤ',
        'ݥ',
        'ݦ',
        'ݧ',
        'ݨ',
        'ݩ',
        'ݪ',
        'ݫ',
        'ݬ',
        'ݭ',
        'ݮ',
        'ݯ',
        'ݰ',
        'ݱ',
        'ݲ',
        'ݳ',
        'ݴ',
        'ݵ',
        'ݶ',
        'ݷ',
        'ݸ',
        'ݹ',
        'ݺ',
        'ݻ',
        'ݼ',
        'ݽ',
        'ݾ',
        'ݿ'
    );

    protected $key;

    /**
     *
     * @param array $key            
     */
    protected function keySpaceClear($key)
    {
        $key = trim($key);
        $key = str_replace("\n", '', $key);
        $key = str_replace("\r", '', $key);
        $key = preg_replace('/\s(?=\s)/', '', $key);
        $key = explode(",", $key);
        
        return $key;
    }

    /**
     *
     * @param string $key            
     */
    public function __construct($key = null, $key_path = '')
    {
        
        if ($key_path === '') {
            $key_path = static::$key_path.DIRECTORY_SEPARATOR. md5(__CLASS__);
        }
        
        $key_path = str_replace('\\', '/', $key_path);
        
        if (is_array($key)) {
            $this->key = $key;
        }
        
        if (is_string($key)) {
            $this->key = $this->keySpaceClear($key);
        }
        
        if (null === $this->key) {
            if (! is_file($key_path)) {
                shuffle($this->default_key);
                $_key = array_slice($this->default_key, 0, 256);
                if(!is_dir($key_path))
                {                   
                   $path = explode('/', $key_path);
                   $tmp = '';
                   foreach ($path as $p)
                   {
                       if($p != md5(__CLASS__))
                       {
                           $tmp .= $p.DIRECTORY_SEPARATOR;
                            
                           if(!is_dir($tmp) )
                           {
                               
                               mkdir($tmp);
                           }                            
                       }    
                   }    
                }    
                file_put_contents($key_path, implode(",", $_key));
            }
            $key = trim(file_get_contents($key_path));
            $key = $this->keySpaceClear($key);
            
            $this->setKey($key);
        }
    }

    public function encrypt($data)
    {
        return $this->dataEncode($data);
    }

    public function decrypt($data)
    {
        return $this->dataDecode($data);
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    protected function utf8StrSplit($str, $splitLen = 1)
    {
        if (! preg_match('/^[0-9]+$/', $splitLen) || $splitLen < 1) {
            
            return false;
        }
        
        $len = mb_strlen($str, 'UTF-8');
        if ($len <= $splitLen) {
            
            return array(
                $str
            );
        }
        
        preg_match_all('/.{' . $splitLen . '}|[^\x00]{1,' . $splitLen . '}$/us', $str, $ar);
        
        return $ar[0];
    }

    /**
     * 將 16 進制的數字字串轉為 64 進制的數字字串
     *
     * @access private
     *        
     * @param string $m
     *            16 進制的數字字串
     * @param integer $len
     *            返回字串長度，如果長度不夠用 0 填充，0 為不填充
     *            
     * @return string
     */
    protected function hex16to64($m, $len = 0)
    {
        $hex2 = array();
        for ($i = 0, $j = strlen($m); $i < $j; ++ $i) {
            
            $hex2[] = str_pad(base_convert($m[$i], 16, 2), 4, '0', STR_PAD_LEFT);
        }
        $hex2 = implode('', $hex2);
        $hex2 = str_split($hex2, 8); // 2 ^ 8 = 256
        foreach ($hex2 as $one) {
            
            $hex64[] = $this->key[bindec($one)];
        }
        
        $return = preg_replace('/^0*/', '', implode('', $hex64));
        if ($len) {
            
            $clen = strlen($return);
            
            if ($clen >= $len) {
                
                return $return;
            } else {
                
                return str_pad($return, $len, '0', STR_PAD_LEFT);
            }
        }
        return $return;
    }

    /**
     * 將 64 進制的數字字串轉為 10 進制的數字字串
     *
     * @access private
     *        
     * @param string $m
     *            64 進制的數字字串
     * @param integer $len
     *            回傳字串長度，如果長度不夠用 0 填充，0 為不填充
     *            
     * @return string
     */
    protected function hex64to10($m, $len = 0)
    {
        $m = $this->utf8StrSplit($m);
        $hex2 = '';
        $keyCode = array_flip($this->key);
        for ($i = 0, $l = count($m); $i < $l; $i ++) {
            
            if (! isset($keyCode[$m[$i]])) {
                // error_log(self::$db_name . ' database encryption key is be changed!!!', 0);
                // exit;
                break;
            }
            
            $hex2 .= str_pad(decbin($keyCode[$m[$i]]), 8, '0', STR_PAD_LEFT);
        }
        $return = bindec($hex2);
        
        if ($len) {
            
            $clen = strlen($return);
            
            if ($clen >= $len) {
                
                return $return;
            } else {
                
                return str_pad($return, $len, '0', STR_PAD_LEFT);
            }
        }
        
        return $return;
    }

    /**
     * 資料編碼
     *
     * @access public
     *        
     * @param string $value
     *            欲編碼之內容
     *            
     * @return string
     */
    protected function dataEncode($value)
    {
        if (empty($value)) {
            
            return $value;
        } elseif (is_array($value)) {
            
            return array_map(array(
                $this,
                __METHOD__
            ), $value);
        } else {
            
            $output = '';
            foreach (str_split(bin2hex(mb_convert_encoding($value, 'UTF-16', 'UTF-8')), 4) as $val) {
                
                $output .= $this->hex16to64($val, 2);
            }
            
            return $output;
        }
    }

    /**
     * 資料解碼
     *
     * @access public
     *        
     * @param string $value
     *            欲編碼之內容
     *            
     * @return string
     */
    protected function dataDecode($value)
    {
        if (empty($value)) {
            
            return $value;
        } elseif (is_array($value)) {
            
            return array_map(array(
                $this,
                __METHOD__
            ), $value);
        } else {
            
            $output = '';
            foreach (self::utf8StrSplit($value, 2) as $val) {
                
                $output .= str_pad(base_convert($this->hex64to10($val), 10, 16), 4, '0', STR_PAD_LEFT);
            }
            
            return mb_convert_encoding(pack('H*', $output), 'UTF-8', 'UTF-16');
        }
    }
}