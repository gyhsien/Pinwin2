<?php
namespace Pinwin\Crypt;

use Zend\Crypt\BlockCipher;
//use Pinwin\Crypt\CryptInterface;

class Aes implements CryptInterface
{

    //protected $default_key = 'Pinwin_Crypt_Aes';
    protected $key = 'Pinwin_Crypt_Aes';

    /**
     *
     * @var BlockCipher
     */
    protected $cipher;

    /**
     *
     * @param string $key            
     */
    public function __construct($key = null)
    {
        $this->cipher = BlockCipher::factory('mcrypt', array(
            'algo' => 'aes'
        ));
        
        if($key !== null)
        {
            $this->key = $key;
        }
        
        $this->setKey($this->key);
    }

    public function encrypt($data)
    {
        return $this->cipher->encrypt($data);
    }

    public function decrypt($data)
    {
        return $this->cipher->decrypt($data);
    }

    public function setKey($key)
    {
        $this->key = $key;
        $this->cipher->setKey($this->key);
    }
}

// $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
// $blockCipher->setKey('encryption key');