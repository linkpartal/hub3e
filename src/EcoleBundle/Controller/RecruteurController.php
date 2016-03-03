<?php

namespace EcoleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RecruteurController extends Controller
{
    public function loadRecruteurAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($user->getPhotos())
        {
            $user->setPhotos(base64_encode(stream_get_contents($user->getPhotos())));
        }


        // les formation de l'etablissement
        $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findBy(array('etablissement'=>$user->getEtablissement() ));
        $hobbie = $this->getDoctrine()->getRepository('GenericBundle:Hobbies')->findAll();

        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findBy(array('user'=>$user));
        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($notifications, 'json');

        $apprenants = $this->getDoctrine()->getRepository('GenericBundle:User')->getUserofTier($user->getEtablissement()->getTier());
        $import_apprenant = $this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->findBy(array('user'=>$user));

        foreach($apprenants as $key => $value)
        {
            if(!$value->hasRole('ROLE_APPRENANT'))
            {
                unset($apprenants[$key]);
            }
        }
        $missions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('suspendu'=>false),array('date'=>'DESC'));
        return $this->render('EcoleBundle:Recruteur:index.html.twig', array('notifications'=>$jsonContent ,'users'=>$apprenants,'import_apprenants'=>$import_apprenant,
            'societes'=>$user->getReferenciel(),'missions'=>$missions,'image'=>$user->getPhotos(),'formations'=>$formation,'hobbies' =>$hobbie,
            ));
    }



}
