<?php
namespace Pinwin\Crypt;

use Zend\Crypt\Symmetric;
use Pinwin\Debug\Debug;
use Zend\Crypt\Hmac;
use Zend\Crypt\FileCipher as ZendFileCipher;
use Zend\Crypt\Key\Derivation\Pbkdf2;
use Zend\Crypt\Symmetric\Mcrypt;
use Zend\Crypt\Symmetric\SymmetricInterface;
use Zend\Crypt\Utils, Zend\Math\Rand;

class FileCipher extends ZendFileCipher
{

    public function decryptEval($fileIn)
    {
        if (empty($this->key)) {
            throw new Exception\InvalidArgumentException('No key specified for decryption');
        }
        
        $read = fopen($fileIn, "r");
        // $write = fopen($fileOut, "w");
        $hmacRead = fread($read, Hmac::getOutputSize($this->getHashAlgorithm()));
        $iv = fread($read, $this->cipher->getSaltSize());
        $tot = filesize($fileIn);
        $hmac = $iv;
        $size = strlen($iv) + strlen($hmacRead);
        $keys = Pbkdf2::calc($this->getPbkdf2HashAlgorithm(), $this->getKey(), $iv, $this->getKeyIteration(), $this->cipher->getKeySize() * 2);
        $padding = $this->cipher->getPadding();
        $this->cipher->setPadding(new Symmetric\Padding\NoPadding());
        $this->cipher->setKey(substr($keys, 0, $this->cipher->getKeySize()));
        $this->cipher->setMode('cbc');
        
        $blockSize = $this->cipher->getBlockSize();
        $hashAlgo = $this->getHashAlgorithm();
        $algorithm = $this->cipher->getAlgorithm();
        $saltSize = $this->cipher->getSaltSize();
        $keyHmac = substr($keys, $this->cipher->getKeySize());
        
        while ($data = fread($read, self::BUFFER_SIZE)) {
            $size += strlen($data);
            // Unpadding if last block
            if ($size + $blockSize >= $tot) {
                $this->cipher->setPadding($padding);
                $data .= fread($read, $blockSize);
            }
            $result = $this->cipher->decrypt($iv . $data);
            $hmac = Hmac::compute($keyHmac, $hashAlgo, $algorithm . $hmac . $data);
            $iv = substr($data, - 1 * $saltSize);
            /*
             * if (fwrite($write, $result) !== strlen($result)) {
             * return false;
             * }
             */
        }
        
        // fclose($write);
        fclose($read);
        
        // check for data integrity
        if (! Utils::compareStrings($hmac, $hmacRead)) {
            unlink($fileOut);
            return false;
        }
        
        $result = preg_replace("/<\?(php){0,1}\n{0,1}\r{0,1}/", '', $result);
        $result = preg_replace("/(?>){0,1}$/", '', $result);
        return true;
    }
}