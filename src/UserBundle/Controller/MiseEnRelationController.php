<?php

namespace UserBundle\Controller;

use GenericBundle\Entity\Message;
use GenericBundle\Entity\Postulation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MiseEnRelationController extends Controller
{
    public function envoiMessageMRAction($idDest,$idEnv,$mission,Request $request){
        //var_dump($request);die;
        $reponsejson = new JsonResponse();

        $em = $this->getDoctrine()->getEntityManager();
        $message = new Message();
        $message->setMessage($request->get('_Descriptif'));
        $date = new \DateTime();
        $message->setDate($date);
        $expediteur = $em->getRepository('GenericBundle:User')->find($idEnv);
        $message->setExpediteur($expediteur);
        $destinataire = $em->getRepository('GenericBundle:User')->find($idDest);
        $message->setDestinataire($destinataire);
        $mission = $em->getRepository('GenericBundle:Mission')->find($mission);
        $message->setMission($mission);
        $em->persist($message);
        $em->flush();
        $reponsejson = new JsonResponse();
        return $reponsejson->setData(1);
    }

    public function RepondreMessageAction($idmessage,Request $request){
        $reponsejson = new JsonResponse();

        $em = $this->getDoctrine()->getEntityManager();
        $message = new Message();
        $date = new \DateTime();
        $message->setDate($date);
        $message->setExpediteur($this->get('security.token_storage')->getToken()->getUser());
        $messagereponse = $em->getRepository('GenericBundle:Message')->find($idmessage);
        $message->setDestinataire($messagereponse->getExpediteur());
        $message->setMission($messagereponse->getMission());

        if($request->get('MessageRefus')){
            $message->setMessage($request->get('MessageRefus'));
            $message->setStatut(-1);
            $messagereponse->setStatut(-1);
            $em->persist($message);
            $em->flush();
            return $reponsejson->setData(-1);
        }
        elseif($request->get('MessageAcceptation')){
            $lien = '<a href="'.$request->get('LienDoodle').'" target="_blank">'. $request->get('LienDoodle').'</a>';
            $message->setMessage($request->get('MessageAcceptation') .' Rendez-vous sur : '. $lien);
            $message->setStatut(1);
            $messagereponse->setStatut(1);
            $em->persist($message);
            $em->flush();
            return $reponsejson->setData(1);
        }

    }

    public function PostulerMessagerieAction($idmessage){
        $reponsejson = new JsonResponse();

        $em = $this->getDoctrine()->getEntityManager();
        $message = new Message();
        $date = new \DateTime();
        $message->setDate($date);
        $message->setExpediteur($this->get('security.token_storage')->getToken()->getUser());
        $messagereponse = $em->getRepository('GenericBundle:Message')->find($idmessage);
        $message->setDestinataire($messagereponse->getExpediteur());
        $message->setMission($messagereponse->getMission());

        $message->setMessage('L\'apprenant a donné suite à votre message!');
        $messagereponse->setStatut(1);
        $em->persist($message);
        $em->flush();

        $postulation = new Postulation();
        $postulation->setUser($this->get('security.token_storage')->getToken()->getUser());
        $postulation->setMission($messagereponse->getMission());
        $postulation->setStatut(1);
        $em->persist($postulation);
        $em->flush();
        return $reponsejson->setData(1);

    }
}
