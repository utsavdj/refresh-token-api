<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_rnd_iv($iv_len)
{
    $iv = '';
    while ($iv_len-- > 0) {
        $iv .= chr(mt_rand() & 0xff);
    }
    return $iv;
}

function md5_encrypt($plain_text, $password, $iv_len = 16)
{
    $plain_text .= "\x13";
    $n = strlen($plain_text);
    if ($n % 16) $plain_text .= str_repeat("\0", 16 - ($n % 16));
    $i = 0;
    $enc_text = get_rnd_iv($iv_len);
    $iv = substr($password ^ $enc_text, 0, 512);
    while ($i < $n) {
        $block = substr($plain_text, $i, 16) ^ pack('H*', md5($iv));
        $enc_text .= $block;
        $iv = substr($block . $iv, 0, 512) ^ $password;
        $i += 16;
    }
    return base64_encode($enc_text);
}

function md5_decrypt($enc_text, $password, $iv_len = 16)
{
    $enc_text = base64_decode($enc_text);
    $n = strlen($enc_text);
    $i = $iv_len;
    $plain_text = '';
    $iv = substr($password ^ substr($enc_text, 0, $iv_len), 0, 512);
    while ($i < $n) {
        $block = substr($enc_text, $i, 16);
        $plain_text .= $block ^ pack('H*', md5($iv));
        $iv = substr($block . $iv, 0, 512) ^ $password;
        $i += 16;
    }
    return preg_replace('/\\x13\\x00*$/', '',htmlentities($plain_text ,ENT_QUOTES , "UTF-8"));
}

/*****************************************
$plain_text = 'very secret string';
$password = 'test';
echo "plain text is: [${plain_text}]<br />\n";
echo "password is: [${password}]<br />\n";

$enc_text = md5_encrypt($plain_text, $password);
echo "encrypted text is: [${enc_text}]<br />\n";

$plain_text2 = md5_decrypt($enc_text, $password);
echo "decrypted text is: [${plain_text2}]<br />\n";
*/
?>