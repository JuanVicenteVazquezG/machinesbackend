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
                    'status'=>'sucess',
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
                    'status'=>'sucess',
                    'code'=> 200,
                    'message'=> 'User created successfully!',
                    'user'=> $user
                    ];
            } else {
                $data = [
                    'status'=>'sucess',
                    'code'=> 400,
                    'message'=> 'User already exist!'
                    ];
            }
        }
        // make response in json
        return new JsonResponse($data);
    }
}
