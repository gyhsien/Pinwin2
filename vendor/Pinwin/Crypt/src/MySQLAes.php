<?php
namespace Pinwin\Crypt;

use Zend\Debug\Debug;
use Zend\Crypt\BlockCipher;
use Pinwin\Crypt\CryptInterface;

class MySQLAes implements CryptInterface
{

    //protected $default_key = 'Pinwin_Crypt_Aes';
    protected $default_key = 'Aofx3WghFWcxfusQBJ/dVVzwd+RHmhZTil';
    
    protected $key;

    protected $MySQLKey;
    
    protected $key_path = 'data/persistent/security/';
    
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
        // $key = explode(",", $key);
        
        return $key;
    }

    /**
     *
     * @param string $key            
     */
    public function __construct($key = null, $key_path = '')
    {
        if ($key_path === '') {
            $key_path = $this->key_path/*. md5(__CLASS__)*/;
        }
        
        if (is_array($key)) {
            $this->key = $key;
        }
        
        if (is_string($key)) {
            $this->key = $this->keySpaceClear($key);
        }
        
        if (null === $this->key) {
            if (! is_file($key_path. md5(__CLASS__))) {
                $_key = \Zend\Math\Rand::getString(rand(16, 32));
                $tmp = '';
                $key_path = str_replace('\\', '/', $key_path);
                foreach (explode('/', $key_path) as $p)
                {
                    $tmp.= ($p.'/');
                    if(!is_dir($tmp))
                    {
                        mkdir($tmp);
                    }    
                }    
                file_put_contents($key_path. md5(__CLASS__), $_key);
            }
            
            $key = trim(file_get_contents($key_path. md5(__CLASS__)));
            
            $key = $this->keySpaceClear($key);
            
            $this->setKey($key);
        }
    }

    /**
     *
     * @param string $key            
     */
    public function encrypt($data)
    {
        $key = $this->key;
        $pad_value = 16 - (strlen($data) % 16);
        $data = str_pad($data, (16 * (floor(strlen($data) / 16) + 1)), chr($pad_value));
        return mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128, 
            $key, 
            $data, 
            MCRYPT_MODE_ECB, 
            mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), 
            MCRYPT_DEV_URANDOM
        ));
    }

    public function decrypt($data)
    {
        $key = $this->key;
        $data = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128, 
            $key, 
            $data, 
            MCRYPT_MODE_ECB, 
            mcrypt_create_iv(
                mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), 
                MCRYPT_DEV_URANDOM
            )
        );
        //參考  https://zh.wikipedia.org/wiki/ASCII
        $data = rtrim($data,  "\x00..\x08"); 
        $data = rtrim($data,  "\x0B..\x0C");
        return $data;
    }

    public function setKey($key)
    {
        $new_key = str_repeat(chr(0), 16);
        
        for ($i = 0, $len = strlen($key); $i < $len; $i ++) {
            $new_key[$i % 16] = $new_key[$i % 16] ^ $key[$i];
        }
        
        $this->key = $new_key;
    }

    public function getKey()
    {
        return $this->key;
    }
}