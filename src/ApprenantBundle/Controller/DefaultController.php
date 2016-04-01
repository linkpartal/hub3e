<?php

namespace ApprenantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function loadApprenantAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findBy(array('user'=>$user));
        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($notifications, 'json');



        if($user->getPhotos() and !is_string($user->getPhotos()))
        {
            $user->setPhotos(base64_encode(stream_get_contents($user->getPhotos())));
        }

        $societes = array();
        $missions = array();
        foreach($this->getDoctrine()->getRepository('GenericBundle:Message')->findBy(array('destinataire'=>$user)) as $message){

            if(!$message->getMission()->getEtablissement()->getSuspendu())
            {
                array_push($missions,$message->getMission());
                array_push($societes,$message->getMission()->getEtablissement());

            }

        }


        return $this->render('ApprenantBundle:Default:index.html.twig', array('notifications'=>$jsonContent ,'societes'=>$societes,'missions'=>$missions,'image'=>$user->getPhotos()));
    }
}
