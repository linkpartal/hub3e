<?php

namespace EcoleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RecruteurController extends Controller
{
    public function loadRecruteurAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($user->getPhotos() and !is_string($user->getPhotos()))
        {
            $user->setPhotos(base64_encode(stream_get_contents($user->getPhotos())));
        }

        $messages = $this->getDoctrine()->getRepository('GenericBundle:Message')->findBy(array('destinataire'=>$user));
        $messageNonLu = 0;
        foreach($messages as $msg){
            if(!$msg->getStatut()==1 and !$msg->getStatut()==-1){
                $messageNonLu++;
            }
        }

        /*$notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findBy(array('user'=>$user));
        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($notifications, 'json');*/

        $apprenants = $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$user->getEtablissement()));

        foreach($apprenants as $key => $value)
        {
            if(!$value->hasRole('ROLE_APPRENANT'))
            {
                unset($apprenants[$key]);
            }
        }


        $mes_missions = array();
        $societes =array();
        $formations = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findBy(array('etablissement'=>$user->getEtablissement()));
        foreach($formations as $formation)
        {
            $diffusions = $this->getDoctrine()->getRepository('GenericBundle:Diffusion')->findBy(array('formation'=>$formation));
            foreach($diffusions as $diffusion)
            {
                if(    (($diffusion->getMission()->getStatut() == 1 or $diffusion->getMission()->getStatut() == 2) and $diffusion->getMission()->getTier() == $user->getEtablissement()->getTier())
                    or ($diffusion->getMission()->getStatut() == 2 and in_array($user->getEtablissement()->getTier(),$diffusion->getMission()->getTier()->getTier1()->toArray()) )
                     ) {
                    if ($diffusion->getStatut() == 5 and !in_array($diffusion->getMission(), $mes_missions)) {
                        array_push($mes_missions, $diffusion->getMission());
                    }
                    if (!in_array($diffusion->getMission()->getEtablissement(), $societes)) {
                        array_push($societes, $diffusion->getMission()->getEtablissement());
                    }
                }
            }
        }


        $MissionPublic = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('statut'=>3));

        foreach($MissionPublic as $missPub)
        {
            if ( !in_array($missPub, $mes_missions)) {
                        array_push($mes_missions, $missPub);
            }


        }



        $societes = array_merge($societes,$user->getReferenciel()->toArray());
        $uniquesocietes = array_unique($societes);

        return $this->render('EcoleBundle:Recruteur:index.html.twig', array(/*'notifications'=>$jsonContent ,*/'apprenants'=>$apprenants,'societes'=>$uniquesocietes,'missions'=>$mes_missions,
            'image'=>$user->getPhotos(),'formations'=>$formations,'messages'=>$messageNonLu));
    }



}
