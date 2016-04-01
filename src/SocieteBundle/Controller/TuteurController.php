<?php

namespace SocieteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TuteurController extends Controller
{
    public function loadTuteurAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($user->getPhotos())
        {
            $user->setPhotos(base64_encode(stream_get_contents($user->getPhotos())));
        }

        /*
        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findBy(array('user'=>$user));
        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($notifications, 'json');*/

        $mes_missions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('tuteur'=>$user));

        $apprenants = array();
        foreach($mes_missions as $mission){
            foreach($this->getDoctrine()->getRepository('GenericBundle:Message')->findBy(array('mission'=>$mission)) as $message)
            {
                if($message->getExpediteur()->hasRole('ROLE_APPRENANT') and !in_array($message->getExpediteur(),$apprenants)){
                    array_push($apprenants,$message->getExpediteur());
                }
                elseif($message->getDestinataire()->hasRole('ROLE_APPRENANT') and !in_array($message->getDestinataire(),$apprenants)){
                    array_push($apprenants,$message->getDestinataire());
                }

            }

            foreach($this->getDoctrine()->getRepository('GenericBundle:Postulation')->findBy(array('mission'=>$mission)) as $postulation)
            {
                if($postulation->getUser()->hasRole('ROLE_APPRENANT') and !in_array($postulation->getUser(),$apprenants)){
                    array_push($apprenants,$postulation->getUser());
                }

            }

        }

        return $this->render('SocieteBundle:Tuteur:index.html.twig', array(/*'notifications'=>$jsonContent ,*/'apprenants'=>$apprenants, 'missions'=>$mes_missions,'image'=>$user->getPhotos()));
    }



}
