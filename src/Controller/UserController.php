<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\component\HttpKernel\Request;
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
}
