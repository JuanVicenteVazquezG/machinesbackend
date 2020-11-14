<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;

use Knp\Component\Pager\PaginatorInterface;

use App\Entity\User;
use App\Entity\Machine;
use App\Services\JwtAuth;

class MachinesController extends AbstractController
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
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MachinesController.php',
        ]);
    }

    public function newMachine(Request $request, JwtAuth $jwt_auth)
    {
        $data = [
            'status'=>'error',
            'code'=>400,
            'message'=>'A new Machine could not be created!'
        ];

        //get the token

        $token = $request->headers->get('Authorization', null);

        // check if is correct the token

        $authCheck = $jwt_auth->checkToken($token);


        if ($authCheck) {
            
        // get data by post

            $json = $request->get('json', null);

            $params = json_decode($json);
            // get identified user object

            $identity = $jwt_auth->checkToken($token, true);

            // check and validate data

            if (!empty($json)) {
                $user_id = ($identity->sub !=null) ? $identity->sub : null;
                $brand = (!empty($params->brand)) ? $params->brand : null;
                $model = (!empty($params->model)) ? $params->model : null;
                $manufacturer = (!empty($params->manufacturer)) ? $params->manufacturer : null;
                $price = (!empty($params->price)) ? $params->price : null;
                $image_front_url = (!empty($params->image_front_url)) ? $params->image_front_url : null;
                $image_lateral_url = (!empty($params->image_lateral_url)) ? $params->image_lateral_url : null;
                $image_thumbnail_url = (!empty($params->image_thumbnail_url)) ? $params->image_thumbnail_url : null;
               
                if (!empty($user_id) && !empty($brand) && !empty($model) && !empty($manufacturer)) {
                    // Save the new Machine in database
                  
                    $em = $this->getDoctrine()->getManager();
                    $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
                'id'=> $user_id
                ]);

                    //Create and Save object
                    $machine = new Machine();
                    $machine->setUser($user);
                    $machine->setBrand($brand);
                    $machine->setModel($model);
                    $machine->setManufacturer($manufacturer);
                    $machine->setPrice($price);
                    $machine->setImageFrontUrl($image_front_url);
                    $machine->setImageLateralUrl($image_lateral_url);
                    $machine->setImageThumbnailUrl($image_thumbnail_url);
                    $createdAt = new \DateTime('now');
                    $updatedAt = new \DateTime('now');
                    $machine->setCreatedAt($createdAt);
                    $machine->setUpdatedAt($updatedAt);
                    // Save the new Machine in database
                    $em->persist($machine);
                    $em->flush();

                    $data=[
                        'status'=>'success',
                        'code'=>200,
                        'message'=>'The new machine was saved!',
                        'machine'=>$machine
                    ];
                }
            }
            return $this->resjson($data);
        }

        return $this->resjson($data);
    }

    public function myListMachine(Request $request, JwtAuth $jwt_auth, PaginatorInterface $paginator)
    {
        //get autentication headers
        $token = $request->headers->get('Authorization');
        
        //check token

        $authCheck = $jwt_auth->checkToken($token, true);

        // if is valid
        if ($authCheck) {
            // take user identity
            $identity = $jwt_auth->checkToken($token, true);
            $em = $this->getDoctrine()->getManager();
            // Make a request to paginate
            $dql = "SELECT m FROM App\Entity\Machine m WHERE m.user = {$identity->sub} ORDER BY m.id DESC";
            $query = $em->createQuery($dql);

            // get page parameter of Url

            $page = $request->query->getInt('page', 1);
            $items_per_page = 5;

            // invoke pagination

            $pagination = $paginator->paginate($query, $page, $items_per_page);
            $total = $pagination->getTotalItemCount();

            // Prepare Array Data to Return

            $data = array(
                'status' => 'success',
                'code'=> 200,
                'total_items_count' => $total,
                'page_actual' => $page,
                'items_per_page' => $items_per_page,
                'total_pages' => ceil($total / $items_per_page),
                'machines' => $pagination,
                'user_id' => $identity->sub
        );
        } else {
            // if fail return Error message
            $data=array(
            'status'=>'Error',
            'code'=>404,
            'message'=>'Machines created by you cannot be listed at this time '
        );
        }
        // Return array of data

        return $this->resjson($data);
    }
}
