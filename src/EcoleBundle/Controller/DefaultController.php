<?php

namespace EcoleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function loadAdminAction()
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

        $ecoles = array();
        $ecoles = array_merge($ecoles,$this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findAdressesOfEcole($user->getTier()->getId()));

        foreach($user->getTier()->getTier1() as $partenaire) {
            $ecoles = array_merge($ecoles, $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findAdressesOfEcole($partenaire->getId()));
        }

        foreach($ecoles as $key => $ecole)
        {
            if($ecole->getSuspendu())
            {
                unset($ecoles[$key]);
            }
        }

        $missions_propose = array();
        $mes_missions = array();
        foreach($ecoles as $ecole)
        {
            $formations = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findBy(array('etablissement'=>$ecole));
            foreach($formations as $formation)
            {
                $diffusions = $this->getDoctrine()->getRepository('GenericBundle:Diffusion')->findBy(array('formation'=>$formation));
                foreach($diffusions as $diffusion)
                {
                    if((($diffusion->getMission()->getStatut() == 1 or $diffusion->getMission()->getStatut() == 2) and $diffusion->getMission()->getTier() == $user->getTier())
                        or ($diffusion->getMission()->getStatut() == 2 and in_array($user->getTier(),$diffusion->getMission()->getTier()->getTier1()->toArray()) ) or $diffusion->getMission()->getStatut() == 3){
                        if($diffusion->getStatut()==5 and !in_array($diffusion->getMission(), $mes_missions))
                        {
                            array_push($mes_missions,$diffusion->getMission());
                        }
                        elseif($diffusion->getStatut()==1 and !in_array($diffusion->getMission(), $mes_missions)){
                            array_push($missions_propose,$diffusion->getMission());
                        }
                    }

                }
            }
        }

        $users = $this->getDoctrine()->getRepository('GenericBundle:User')->getUserofTier($user->getTier());
        $apprenants =array();
        $notapprenant = array();
        foreach($users as $userd)
        {
            if($userd->hasRole('ROLE_APPRENANT'))
            {
                array_push($apprenants,$userd);
            }
            else{
                array_push($notapprenant,$userd);
            }
        }

        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$user->getTier(),'suspendu'=>false));
        //$missions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('suspendu'=>false),array('date'=>'DESC'));



        return $this->render('EcoleBundle:Adminecole:index.html.twig', array('ecoles'=>$ecoles,/*'notifications'=>$jsonContent ,*/'users'=>$notapprenant,'AllLicences'=>$licences,
            'societes'=>$user->getReferenciel(),'missions'=>$mes_missions,'missions_propose'=>$missions_propose,'apprenants'=>$apprenants,'image'=>$user->getPhotos(),'messages'=>$messageNonLu));
    }

    public function affichageLicenceAction($id)
    {
        $licence = $this->getDoctrine()->getRepository('GenericBundle:Licence')->find($id);

        return $this->render('EcoleBundle:Adminecole:afficheLicence.html.twig',array('licence'=>$licence));
    }

    public function adressesAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $etablissement = $em->getRepository('GenericBundle:Etablissement')->find($id);
        $etablissements = $em->getRepository('GenericBundle:Etablissement')->findBy(array('tier'=>$etablissement->getTier()));
        $adresses = array();
        foreach($etablissements as $value)
        {
            $adresse = array('id'=>$value->getId(),'adresse' => $value->getAdresse());
            array_push($adresses, json_encode($adresse) );
        }
        $reponse = new JsonResponse();
        return $reponse->setData(array('adresses'=>$adresses));
    }

    public function loadQCMAction($id)
    {

            $qcm = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->find($id);
            $questions = $this->getDoctrine()->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef'=>$qcm));
            usort($questions,array('\GenericBundle\Entity\Questiondef','sort_questions_by_order'));
            $reponses = array(array());
            for($i = 0; $i < count($questions); $i++)
            {
                $reps = $this->getDoctrine()->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef'=>$questions[$i]));
                usort($reps,array('\GenericBundle\Entity\Reponsedef','sort_reponses_by_order'));
                $reponses[] = $reps;
            }
            return $this->render('EcoleBundle:Adminecole:LoadQCM.html.twig', array('QCM'=>$qcm ,'Questions'=>$questions,'reponses'=>$reponses));
        }


}
