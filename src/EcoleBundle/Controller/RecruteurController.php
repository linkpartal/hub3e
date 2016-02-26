<?php

namespace EcoleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RecruteurController extends Controller
{
    public function loadRecruteurAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();


        $image = base64_encode(stream_get_contents($user->getPhotos()));

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

        $apprenants = $this->getDoctrine()->getRepository('GenericBundle:User')->getUserofTier($user->getEtablissement()->getTier());

        foreach($apprenants as $key => $value)
        {
            if(!$value->hasRole('ROLE_APPRENANT'))
            {
                unset($apprenants[$key]);
            }
        }
        $missions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('suspendu'=>false),array('date'=>'DESC'));
        return $this->render('EcoleBundle:Recruteur:index.html.twig', array('notifications'=>$jsonContent ,'users'=>$apprenants,'societes'=>$societes,'missions'=>$missions,'image',$image));
    }
}
