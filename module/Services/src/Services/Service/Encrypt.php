<?php
namespace Services\Service;


class Encrypt
{
    private $key = 'konokoshino2002';

    public function HashPass($pass, $salt)
    {
        return md5(md5($pass . hash('sha512', $this->key) . $salt . 'hash_pass'));
    }

    public function HashSalt($salt)
    {
        return md5(md5(hash('sha512', $this->key) . $salt . 'hast_salt'));
    }

    public function HashKeyRequestNewPass()
    {
        $strRandom = rand(1, 9999999999);
        return md5(md5(hash('sha512', $this->key) . $strRandom . 'hash_key_request_pass'));
    }

}