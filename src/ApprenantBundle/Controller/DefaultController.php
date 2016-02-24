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

        $societes = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findSocietes();
        foreach($societes as $key => $societe)
        {
            if($societe->getSuspendu())
            {
                unset($societes[$key]);
            }
        }


        $image = base64_encode(stream_get_contents($user->getPhotos()));

        $missions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('suspendu'=>false),array('date'=>'DESC'));
        return $this->render('ApprenantBundle:Default:index.html.twig', array('notifications'=>$jsonContent ,'societes'=>$societes,'missions'=>$missions,'image'=>$image));
    }
}
