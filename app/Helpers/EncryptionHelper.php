<?php

namespace App\Helpers;

use phpseclib3\Crypt\RSA;
use Illuminate\Support\Str;

class EncryptionHelper
{
    // Generate a random 32-character key (alpha-numeric)
    public static function generateKeyIv()
    {
        return Str::random(32);
    }

    // Replace special characters in the encrypted string
    public static function replaceSpecialChars($data)
    {
        return str_replace(
            ['=', '+', '/'],
            [',', '-', '_'],
            $data
        );
    }

    // Encrypt payload using AES-128-CBC
    public static function encryptPayload($input, $keyIv)
    {
        $key = substr($keyIv, 0, 16);
        $iv = substr($keyIv, 16, 16);

        $encrypted = openssl_encrypt($input, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($encrypted);
    }

    // Encrypt AES Key/IV using RSA Public Key
    public static function encryptAesKeyIv($keyIv)
    {
        // new
        // $publicKey = <<<EOD
        // -----BEGIN PUBLIC KEY-----
        // MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnBBNCJJXHIRS8aAqmRVyAvxhzxcrp/Ic5/n9NEHb+1WT7KrC5skG3jhIuWviSOohMdagtcPvEe4Y6U5mxuegFUIa1fw7a8Uck3rQIa/TcssJvCOdQbj4l+DAeDzLmwNiy9mL56Cuf7ivLePfl7GETFZB50vRHbexjyIg+IdP5AJKoYxV8erzHTStXqZdLp2b1KG9/sny69Cb4yr/duwyTn+eT1VEBKJBeGUh4UTGt6owdAh7UIZ4Al9aZH3NPMJktZfumGGft5ZEXEpCsqFT68XkWiTQDl8mWAv8zVQ10Kt6gKx5AAY3277Nk9DnWsygqYSag/IGzjzFFaqYx4WLEQIDAQAB
        // -----END PUBLIC KEY-----
        // EOD;
        
        
        // old
         $publicKey = <<<EOD
        -----BEGIN PUBLIC KEY-----
        MIIBCgKCAQEAukSqgXt9DsAJuwvrRrDhHwWzSRDwjCmRlPc5ssafWAZnB8ab2gfLRABv0MBwKtCxNrMbncS4Ic8/W05ISBGtkkphVbt4JM22yZAGWqD+Nszk8ESfPMbhWaLF64Egt/vGWZFwa4qbdrXEhiW5nb8jrc4wE+pv4eDOGziALoBtEU0cjeGWQhUMsb1behS0Tzbq0XY39e3pru1jBBK3c/PCp8tuPUl336AopK+8chIqDipCDoNg2WUXjQ6IAgWnc4O44q9mo7naU2nHigUCtdarTfoeOLdKMUAQTY05NGNuN5G+0ma9aXjuIJGzX9vQCBy9GnchJaHvtMWXWMRWTiucdQIDAQAB
        -----END PUBLIC KEY-----
        EOD;

        $rsa = RSA::loadPublicKey($publicKey);
        $encrypted = $rsa->encrypt($keyIv);
        return base64_encode($encrypted);
    }

    
    // Restore special characters for decryption
    /*public static function restoreSpecialChars($data)
    {
        return str_replace(
            [',', '-', '_'],
            ['=', '+', '/'],
            $data
        );
    } */

    // Decrypt AES-encrypted payload
    /* public static function decryptPayload($encryptedText, $keyIv)
    {
        $key = substr($keyIv, 0, 16);
        $iv = substr($keyIv, 16, 16);

        $decoded = base64_decode(self::restoreSpecialChars($encryptedText));
        $decrypted = openssl_decrypt($decoded, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        
        return $decrypted ? json_decode($decrypted, true) : null;
    } */

}
