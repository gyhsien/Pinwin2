<?php
namespace Pinwin\Crypt;

interface CryptInterface
{

    /**
     *
     * @param string $key            
     */
    public function setKey($key);

    /**
     *
     * @param string $data            
     * @return string
     */
    public function encrypt($data);

    /**
     *
     * @param string $data            
     * @return string
     */
    public function decrypt($data);
}