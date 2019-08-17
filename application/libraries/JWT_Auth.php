<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require('vendor/autoload.php');
use \Firebase\JWT\JWT;

class JWT_Auth{

    private $key = 'rXtqdgZ2qHr2DxqfhEIzTbo1OKsjfGLem+sXdD5lY2s=';

    function encode($user_id, $email){
        $date = new DateTime();
        $timestamp = $date->getTimestamp();

        $token = array(
            'user_id'=> $user_id,
            'email'=> $email,
            'iat'=> $timestamp,
            'exp'=> $timestamp + 5
        );
        $data['token'] = JWT::encode($token, $this->key);
        return $data;
    }

    function decode($jwt){
        $decoded = JWT::decode($jwt, $this->key, array('HS256'));
        return $decoded;
    }

}