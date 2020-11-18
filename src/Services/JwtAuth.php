<?php
namespace App\Services;

use Firebase\JWT\JWT;
use App\Entity\User;

class JwtAuth
{
    public $manager;
    public $key;

    public function __construct($manager)
    {
        $this->manager = $manager;
        $this->key = "Hi, this is only a practice";
    }

    public function signup($email, $password, $gettoken=null)
    {
        // Check if the user exists
        $user = $this->manager->getRepository(User::class)->findOneBy([
           'email'=>$email,
           'password'=>$password
       ]);
        $signup = false;
        if (is_object($user)) {
            //If $user is an object is because has found the email and the password
            $signup = true;
        }

    
        // If exist, generate the token jwt
        if ($signup) {
            $token = [
               'sub' => $user->getId(),
               'name'=> $user->getName(),
               'surname'=> $user->getSurname(),
               'email'=> $user->getemail(),
               'iat'=> time(),
               'exp'=> time() + (7 * 24 * 60 *60)
           ];

            //  Check the flag gettoken, condition
            $jwt = JWT::encode($token, $this->key, 'HS256');
            if (!empty($gettoken)) {
                $data = $jwt;
            } else {
                $decoded = JWT::decode($jwt, $this->key, ['HS256']);
                $data = $decoded;
            }
        } else {
            $data = [
                'status'=> 'error',
                'code'=>400,
                'message'=> 'The email or password is not correct!'
            ];
        }
        
        // return data

        return $data;
    }

    public function checkToken($jwt, $identity = false)
    {
        $auth = false;
        try {
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }
        if (isset($decoded) && !empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth= true;
        } else {
            $auth=false;
        }

        if ($identity!=false) {
            return $decoded;
        } else {
            return $auth;
        }
    }
}
