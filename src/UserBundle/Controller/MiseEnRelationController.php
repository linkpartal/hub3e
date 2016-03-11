<?php

namespace UserBundle\Controller;

use GenericBundle\Entity\CompteRendu;
use GenericBundle\Entity\Message;
use GenericBundle\Entity\Postulation;
use GenericBundle\Entity\RDV;
use JMS\Serializer\Serializer;
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
        $mail = \Swift_Message::newInstance()
            ->setSubject('Postulation')
            ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
            ->setTo($destinataire->getEmail())
            ->setBody($this->renderView('GenericBundle:Mail:MessageMiseEnRelation.html.twig',array('message'=>$message->getMessage(),'mission'=>$mission))
                ,'text/html'
            );
        $this->get('mailer')->send($mail);
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

            if($this->get('security.token_storage')->getToken()->getUser()->hasRole('ROLE_APPRENANT')){
                $mail = \Swift_Message::newInstance()
                    ->setSubject('Postulation')
                    ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
                    ->setTo($messagereponse->getExpediteur()->getEmail())
                    ->setBody($this->renderView('GenericBundle:Mail:RefusMissionApprenant.html.twig',array('message'=>$message->getMessage(),'apprenant'=>$message->getExpediteur(),'mission'=>$message->getMission()))
                        ,'text/html'
                    );
                $this->get('mailer')->send($mail);
            }
            else{
                $mail = \Swift_Message::newInstance()
                    ->setSubject('Postulation')
                    ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
                    ->setTo($messagereponse->getExpediteur()->getEmail())
                    ->setBody($this->renderView('GenericBundle:Mail:ApprenantRefusé.html.twig',array('message'=>$message->getMessage(),'mission'=>$message->getMission()))
                        ,'text/html'
                    );
                $this->get('mailer')->send($mail);
            }

            return $reponsejson->setData(-1);
        }
        elseif($request->get('MessageAcceptation')){
            if($request->get('LienDoodle'))
            {
                $lien = '<a href="'.$request->get('LienDoodle').'" target="_blank">'. $request->get('LienDoodle').'</a>';
                if($request->get('MessageAcceptation'))
                {
                    $message->setMessage($request->get('MessageAcceptation') .' Rendez-vous sur : '. $lien);
                }
            }
            else{
                $message->setMessage($request->get('MessageAcceptation'));
            }

            $message->setStatut(1);
            $messagereponse->setStatut(1);
            $em->persist($message);
            $em->flush();
            $rdv = $em->getRepository('GenericBundle:RDV')->findOneBy(array('mission'=>$messagereponse->getMission(),'tuteur'=>$messagereponse->getMission()->getTuteur(),'apprenant'=>$messagereponse->getExpediteur()));
            if($rdv)
            {
                for( $i = 0; $i < count($request->get('dateRDV')) and $i<3;$i++)
                {
                    $datetimeRDV = $request->get('dateRDV')[$i].' '.$request->get('timeRDV')[$i];
                    if($i == 2){$rdv->setDate1(date_create($datetimeRDV));}
                    if($i == 1){$rdv->setDate2(date_create($datetimeRDV));}
                    if($i == 0){$rdv->setDate3(date_create($datetimeRDV));}
                }
                $em->flush();
            }
            else{
                $rdv = new RDV();
                $rdv->setMission($messagereponse->getMission());
                $rdv->setTuteur($messagereponse->getMission()->getTuteur());
                $rdv->setApprenant($messagereponse->getExpediteur());
                $rdv->setChoixApprenant(true);
                for( $i = 0; $i < count($request->get('dateRDV')) and $i<3;$i++)
                {
                    $datetimeRDV = $request->get('dateRDV')[$i].' '.$request->get('timeRDV')[$i];
                    if($i == 0){$rdv->setDate1(date_create($datetimeRDV));}
                    if($i == 1){$rdv->setDate2(date_create($datetimeRDV));}
                    if($i == 2){$rdv->setDate3(date_create($datetimeRDV));}

                }
                $em->persist($rdv);
                $em->flush();
            }

            if(!$this->get('security.token_storage')->getToken()->getUser()->hasRole('ROLE_APPRENANT')){
                $mail = \Swift_Message::newInstance()
                    ->setSubject('Postulation')
                    ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
                    ->setTo($messagereponse->getExpediteur()->getEmail())
                    ->setBody($this->renderView('GenericBundle:Mail:ProposezRDV.html.twig',array('message'=>$message->getMessage(),'mission'=>$message->getMission(),'rdv'=>$rdv))
                        ,'text/html'
                    );
                $this->get('mailer')->send($mail);
            }
            return $reponsejson->setData(1);
        }
        else{
            return $reponsejson->setData(99);
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

        if($this->get('security.token_storage')->getToken()->getUser()->hasRole('ROLE_APPRENANT')){
            $mail = \Swift_Message::newInstance()
                ->setSubject('Postulation')
                ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
                ->setTo($messagereponse->getExpediteur()->getEmail())
                ->setBody($this->renderView('GenericBundle:Mail:PostulationMessage.html.twig',array('message'=>$message->getMessage(),'apprenant'=>$message->getExpediteur(),'mission'=>$message->getMission()))
                    ,'text/html'
                );
            $this->get('mailer')->send($mail);
        }
        return $reponsejson->setData(1);

    }

    public function choixDateAction($id,$numero){
        $em = $this->getDoctrine()->getManager();
        $rdv = $em->getRepository('GenericBundle:RDV')->find($id);
        $rep = new JsonResponse();

        if($rdv)
        {
            $rdv->setStatut(1);
            if($numero==2){
                $rdv->setDate1($rdv->getDate2());
            }elseif($numero==3){
                $rdv->setDate1($rdv->getDate3());
            }
            $rdv->setDate2(null);
            $rdv->setDate3(null);
            $em->flush();
            return $rep->setData(1);
        }
        else{
            return $rep->setData(-1);
        }
    }

    public function annulerRDVAction($id){
        $em = $this->getDoctrine()->getManager();
        $rdv = $em->getRepository('GenericBundle:RDV')->find($id);
        $rep = new JsonResponse();
        if($rdv)
        {
            $rdv->setDate2(null);
            $rdv->setDate3(null);
            $rdv->setStatut(-2);
            $em->flush();
            return $rep->setData(1);
        }
        else{
            return $rep->setData(-1);
        }
    }

    public function CompteRenduSaisieAction($id,Request $request){
        $rep = new JsonResponse();
        $em = $this->getDoctrine()->getManager();
        $rdv = $em->getRepository('GenericBundle:RDV')->find($id);
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($rdv){
            $compterendu = new CompteRendu();
            $compterendu->setDate(date_create());
            $compterendu->setAuteur($user);
            $compterendu->setRendezvous($rdv);
            if($request->get('CompteRenduAbsent')){
                $compterendu->setCompterendu($request->get('CompteRenduAbsent'));
                $compterendu->setHonorer(false);
            }
            elseif($request->get('CompteRendu')){
                $compterendu->setCompterendu($request->get('CompteRendu'));
                $compterendu->setHonorer(true);
            }
            else{
                return $rep->setData(2);
            }
            //return $rep->setData(array($request->get('CompteRendu'),$request->get('CompteRenduAbsent')));
            $em->persist($compterendu);
            $em->flush();
            return $rep->setData(1);
        }
        else{
            return $rep->setData(0);
        }
    }

    public function AjournementAction($id,Request $request){
        $rep = new JsonResponse();
        $em = $this->getDoctrine()->getManager();
        $rdv = $em->getRepository('GenericBundle:RDV')->find($id);
        $date = array();

        if($rdv){
            for( $i = 0; $i < count($request->get('dateRDV')) and $i<3;$i++)
            {
                $datetimeRDV = $request->get('dateRDV')[$i].' '.$request->get('timeRDV')[$i];

                if($i == 2){
                    $rdv->setDate1(date_create($datetimeRDV));
                }
                if($i == 1){
                    $rdv->setDate2(date_create($datetimeRDV));
                }
                if($i == 0){
                    $rdv->setDate3(date_create($datetimeRDV));
                }

                array_push($date,date_format(date_create($datetimeRDV),"d M Y à H:i"));
            }
            if($this->get('security.token_storage')->getToken()->getUser() == $rdv->getApprenant()){
                $rdv->setChoixApprenant(false);
            }
            else{
                $rdv->setChoixApprenant(true);
            }
            $rdv->setStatut(-1);
            $em->flush();
            return $rep->setData($date);
        }
    }
}
