<?php

namespace UserBundle\Controller;

use GenericBundle\Entity\Message;
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
}
