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

        $Hobbies = $this->getDoctrine()->getRepository('GenericBundle:Hobbies')->findAll();

        // les formation de l'etablissement
        $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findBy(array('etablissement'=>$user->getEtablissement() ));

        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findBy(array('user'=>$user));
        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($notifications, 'json');

        $apprenants = $this->getDoctrine()->getRepository('GenericBundle:User')->getUserofTier($user->getEtablissement());
        $import_apprenant = $this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->findBy(array('user'=>$user));


        $societes = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findSocietes();
        foreach($societes as $key => $societe)
        {
            if($societe->getSuspendu())
            {
                unset($societes[$key]);
            }
        }
        foreach($apprenants as $key => $value)
        {
            if(!$value->hasRole('ROLE_APPRENANT'))
            {
                unset($apprenants[$key]);
            }
        }


        $mes_missions = array();


        $formations = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findBy(array('etablissement'=>$user->getEtablissement()));
        foreach($formations as $formation)
        {
            $diffusions = $this->getDoctrine()->getRepository('GenericBundle:Diffusion')->findBy(array('formation'=>$formation));
            foreach($diffusions as $diffusion)
            {
                if($diffusion->getStatut()==5)
                {
                    array_push($mes_missions,$diffusion->getMission());
                }
                elseif($diffusion->getStatut()==1){
                    array_push($missions_propose,$diffusion->getMission());
                }
            }
        }

        return $this->render('SocieteBundle:Tuteur:index.html.twig', array('notifications'=>$jsonContent ,'apprenants'=>$apprenants,'import_apprenants'=>$import_apprenant,
            'societes'=>$user->getReferenciel(),'missions'=>$mes_missions,'image'=>$user->getPhotos(),'formations'=>$formation,'hobbies'=>$Hobbies,'societes'=>$societes));
    }



}
