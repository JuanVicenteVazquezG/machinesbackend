<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;


use App\Entity\User;
use App\Entity\Machine;
use App\Services\JwtAuth;

// use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    private function resjson($data)
    {
        // Serialize data with service Serializer
        $json = $this->get('serializer')->serialize($data, 'json');
        // Response with httpFoundation
        $response = new Response();
        // Assign content to the response
        $response->setContent($json);
        // Indicate response format json
        $response->headers->set('Content-Type', 'application/json');
        // return response
        return $response;
    }

    public function index()
    {
        $user_repo = $this->getDoctrine()->getRepository(User::class);
        $machines_repo = $this->getDoctrine()->getRepository(Machine::class);
       
        $users = $user_repo->findAll();

        // foreach ($users as $user) {
        //     echo "<h1>{$user->getName()} {$user->getSurname()}</h1>";
        // }

        // die();

        return $this->resjson($users);
    }

    public function createUser(Request $request)
    {
        
        // Collect the post data
        $json = $request->get('json', null);
        // Decode the json
        $params = json_decode($json);
        // Default response
        $data = [
    'status'=>'error',
    'code'=> 200,
    'message'=> 'The user has not been created!'
    ];
        // check and validate data
        if ($json !== null) {
            $name = (!empty($params->name)) ? $params->name : null;
            $surname= (!empty($params->surname)) ? $params->surname : null;
            $email= (!empty($params->email)) ? $params->email : null;
            $password= (!empty($params->password)) ? $params->password : null;
            $validator = Validation::createValidator();
            $validate_email = $validator->validate($email, [ new Email()]);
            if (!empty($email) &&
             count($validate_email)==0 &&
             !empty($password) &&
             !empty($name) &&
             !empty($surname)) {
                $data = [
                    'status'=>'success',
                    'code'=> 200,
                    'message'=> 'VALIDATED!'
                    ];
            }
            // If the validation is correct we create the user object
            $user = new User();
            $user->setName($name);
            $user->setSurname($surname);
            $user->setEmail($email);
            $user->setCreatedAt(new \DateTime('now'));
            // $user->setPassword($password);

            // Encrypt password
            $pwd = hash('sha256', $password);
            $user->setPassword($pwd);
            // var_dump($user);
            // die();
            // Check if user exists (duplicates)

            // if it doesn't exist, save it in the database
            $doctrine = $this->getDoctrine();
            $em = $doctrine->getManager();
            $user_repo = $doctrine->getRepository(User::class);
            $isset_user = $user_repo->findBy(array(
                'email'=> $email
            ));
            if (count($isset_user)==0) {
                //saves the user
                
                $em->persist($user);
                $em->flush();
                $data = [
                    'status'=>'success',
                    'code'=> 200,
                    'message'=> 'User created successfully!',
                    'user'=> $user
                    ];
            } else {
                $data = [
                    'status'=>'error',
                    'code'=> 400,
                    'message'=> 'User already exist!'
                    ];
            }
        }
        // make response in json
        return new JsonResponse($data);
    }

    public function login(Request $request, JwtAuth $jwt_auth)
    {
        //Receive Data by Post
        $json = $request->get('json', null);

        // Decode the json
        $params = json_decode($json);

        //Array by Default to return
        $data = [
                'status'=>'success',
                'code'=>200,
                'message'=> 'User cannot be identified!'
            ];
        //check and validate data
        if ($json !== null) {
            $email = (!empty($params->email)) ? $params->email: null;
            $password = (!empty($params->password)) ? $params->password: null;
            $gettoken =  (!empty($params->gettoken)) ? $params->gettoken: null;

            $validator = Validation::createValidator();
            $validate_email = $validator->validate(
                $email,
                [ new Email()]
            );

            if (!empty($email) && !empty($password) && count($validate_email)==0) {
               
                //encode password
                $pwd = hash('sha256', $password);
                // if all is correct, call service to identify the user(jwt, token or an object)
                if ($gettoken) {
                    $signup = $jwt_auth->signup($email, $pwd, $gettoken);
                } else {
                    $signup = $jwt_auth->signup($email, $pwd);
                }
                return new JsonResponse($signup);
            }
        }
        // If all is ok response
        return $this->resjson($data);
    }

    public function edit(Request $request, JwtAuth $jwt_auth)
    {
        // get header of auth
        $token = $request->headers->get('Authorization');

        // Create a method to check if the token is correct
        $authCheck = $jwt_auth->checkToken($token);

        // default answer
        $data = [
            'status'=>'error',
            'code'=>400,
            'message'=>'User not updated',
            ];

        // If is correct, make the update of the user
        if ($authCheck) {
            //Update the user info
           
            // get entity manager
            $em = $this->getDoctrine()->getManager();
             
            // get Identified user data info

            $identity = $jwt_auth->checkToken($token, true);
            
            // Full Update User

            $user_repo = $this->getDoctrine()->getRepository(User::class);

            $user = $user_repo->findOneBy(['id'=>$identity->sub]);

            // Take data by Post

            $json = $request->get('json', null);
            $params = json_decode($json);

            // Check & validate Data

            if (!empty($json)) {
                $name = (!empty($params->name)) ? $params->name : null;
                $surname= (!empty($params->surname)) ? $params->surname : null;
                $email= (!empty($params->email)) ? $params->email : null;
            
                $validator = Validation::createValidator();
                $validate_email = $validator->validate($email, [ new Email()]);
                
                if (!empty($email) &&
                 count($validate_email)==0 &&
                 !empty($name) &&
                 !empty($surname)) {
                    $data = [
                        'status'=>'success',
                        'code'=> 200,
                        'message'=> 'VALIDATED!'
                        ];
                    // Assign new Data to the user Object
                    $user->setEmail($email);
                    $user->setName($name);
                    $user->setSurname($surname);
                  
                    // check duplicates data
                    $isset_user = $user_repo->findBy([
                        'email'=>$email
                    ]);
                    
                    if (count($isset_user)==0 || $identity->email==$email) {
                        
            
                    // save changes on db
                        $em->persist($user);
                        $em->flush();

                        $data = [
                            'status'=> 'success',
                            'code'=> 200,
                            'message'=> 'User updated successfully!',
                            'user'=>$user
                        ];
                    } else {
                        $data = [
                            'status'=> 'error',
                            'code'=> 400,
                            'message'=> 'You cannot use this email!',
                        ];
                    }
                }
            }
        }
        return $this->resjson($data);
    }
}
