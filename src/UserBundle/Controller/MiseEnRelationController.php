<?php

namespace UserBundle\Controller;

use GenericBundle\Entity\CompteRendu;
use GenericBundle\Entity\Message;
use GenericBundle\Entity\Postulation;
use GenericBundle\Entity\RDV;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MiseEnRelationController extends Controller
{
    public function envoiMessageMRAction($idDest,$idEnv,$mission,Request $request){
        //var_dump($request);die;
        $reponsejson = new JsonResponse();

        $em = $this->getDoctrine()->getEntityManager();
        $expediteur = $em->getRepository('GenericBundle:User')->find($idEnv);
        $destinataire = $em->getRepository('GenericBundle:User')->find($idDest);
        $mission = $em->getRepository('GenericBundle:Mission')->find($mission);
        $MRduplica = $em->getRepository('GenericBundle:Message')->findBy(array('mission'=>$mission,'destinataire'=>$destinataire));


        if(!$MRduplica){
            $message = new Message();
            $message->setMessage($request->get('_Descriptif'));
            $date = new \DateTime();
            $message->setDate($date);
            $message->setExpediteur($expediteur);
            $message->setDestinataire($destinataire);
            $message->setMission($mission);
            $em->persist($message);
            $em->flush();

            $messageRetour = new Message();
            $messageRetour->setMessage("En attente d'une réponse");
            $date = new \DateTime();
            $messageRetour->setDate($date);
            $messageRetour->setExpediteur($destinataire);
            $messageRetour->setDestinataire($expediteur);
            $messageRetour->setMission($mission);
            $messageRetour->setStatut(2);
            $em->persist($messageRetour);
            $em->flush();
        }
        else{
            return $reponsejson->setData(-1);
        }

        $mail = \Swift_Message::newInstance()
            ->setSubject('Postulation')
            ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
            ->setTo($destinataire->getEmail())
            ->setBody($this->renderView('GenericBundle:Mail:MessageMiseEnRelation.html.twig',array('message'=>$message->getMessage(),'mission'=>$mission))
                ,'text/html'
            );
        $this->get('mailer')->send($mail);





        return $reponsejson->setData(1);
    }

    public function RepondreMessageAction($idmessage,Request $request){
        $reponsejson = new JsonResponse();

        $em = $this->getDoctrine()->getEntityManager();
        $userConnecte = $this->get('security.token_storage')->getToken()->getUser();
        $messagereponse = $em->getRepository('GenericBundle:Message')->find($idmessage);
        $messageDup = $em->getRepository('GenericBundle:Message')->findOneBy(array('expediteur'=>$userConnecte,'destinataire'=>$messagereponse->getExpediteur(),'mission'=>$messagereponse->getMission()));

        if(!$messageDup){
            $message = new Message();
            $date = new \DateTime();
            $message->setDate($date);
            $message->setExpediteur($userConnecte);
            $message->setDestinataire($messagereponse->getExpediteur());
            $message->setMission($messagereponse->getMission());
            if($request->get('MessageRefus')){
                $message->setMessage($request->get('MessageRefus'));
                $message->setStatut(-1);
                $messagereponse->setStatut(-1);
                $em->persist($message);
                $em->flush();
            }
            elseif($request->get('MessageAcceptation')) {
                if ($request->get('LienDoodle')) {
                    $lien = '<a href="' . $request->get('LienDoodle') . '" target="_blank">' . $request->get('LienDoodle') . '</a>';
                    if ($request->get('MessageAcceptation')) {
                        $message->setMessage($request->get('MessageAcceptation') . ' Rendez-vous sur : ' . $lien);
                    }
                } else {
                    $message->setMessage($request->get('MessageAcceptation'));
                }

                $message->setStatut(1);
                $messagereponse->setStatut(1);
                $em->persist($message);
                $em->flush();
            }
        }
        else{
            $date = new \DateTime();
            $messageDup->setDate($date);
            if($request->get('MessageRefus')){
                $messageDup->setMessage($request->get('MessageRefus'));
                $messageDup->setStatut(-1);
                $messagereponse->setStatut(-1);
                $em->flush();
            }
            elseif($request->get('MessageAcceptation')) {
                if ($request->get('LienDoodle')) {
                    $lien = '<a href="' . $request->get('LienDoodle') . '" target="_blank">' . $request->get('LienDoodle') . '</a>';
                    if ($request->get('MessageAcceptation')) {
                        $messageDup->setMessage($request->get('MessageAcceptation') . ' Rendez-vous sur : ' . $lien);
                    }
                } else {
                    $messageDup->setMessage($request->get('MessageAcceptation'));
                }

                $messageDup->setStatut(1);
                $messagereponse->setStatut(1);
                $em->flush();
            }
            $message = $messageDup;
            $em->flush();
        }


        if($request->get('MessageRefus')){
            if($this->get('security.token_storage')->getToken()->getUser()->hasRole('ROLE_APPRENANT')){
                $mail = \Swift_Message::newInstance()
                    ->setSubject('Postulation')
                    ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                    ->setTo($messagereponse->getExpediteur()->getEmail())
                    ->setBody($this->renderView('GenericBundle:Mail:RefusMissionApprenant.html.twig',array('message'=>$message->getMessage(),'apprenant'=>$message->getExpediteur(),'mission'=>$message->getMission()))
                        ,'text/html'
                    );
                $this->get('mailer')->send($mail);
            }
            else{
                $mail = \Swift_Message::newInstance()
                    ->setSubject('Postulation')
                    ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                    ->setTo($messagereponse->getExpediteur()->getEmail())
                    ->setBody($this->renderView('GenericBundle:Mail:ApprenantRefusé.html.twig',array('message'=>$message->getMessage(),'mission'=>$message->getMission()))
                        ,'text/html'
                    );
                $this->get('mailer')->send($mail);
            }

            return $reponsejson->setData(-1);
        }
        elseif($request->get('MessageAcceptation')){
            $rdv = $em->getRepository('GenericBundle:RDV')->findOneBy(array('mission'=>$messagereponse->getMission(),'tuteur'=>$messagereponse->getMission()->getTuteur(),'apprenant'=>$messagereponse->getExpediteur()));
            if($rdv)
            {
                for( $i = 0; $i < count($request->get('dateRDV')) and $i<3;$i++)
                {
                    $datetimeRDV = $request->get('dateRDV')[$i].' '.$request->get('timeRDV')[$i];
                    if($i == 2){$rdv->setDate1(date_create_from_format('d/m/Y H:i',$datetimeRDV));}
                    if($i == 1){$rdv->setDate2(date_create_from_format('d/m/Y H:i',$datetimeRDV));}
                    if($i == 0){$rdv->setDate3(date_create_from_format('d/m/Y H:i',$datetimeRDV));}
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
                    if($i == 0){$rdv->setDate1(date_create_from_format('d/m/Y H:i',$datetimeRDV));}
                    if($i == 1){$rdv->setDate2(date_create_from_format('d/m/Y H:i',$datetimeRDV));}
                    if($i == 2){$rdv->setDate3(date_create_from_format('d/m/Y H:i',$datetimeRDV));}

                }
                $em->persist($rdv);
                $em->flush();
            }

            if(!$this->get('security.token_storage')->getToken()->getUser()->hasRole('ROLE_APPRENANT')){
                $mail = \Swift_Message::newInstance()
                    ->setSubject('Postulation')
                    ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
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
        $messagereponse = $em->getRepository('GenericBundle:Message')->find($idmessage);
        $messagedup = $em->getRepository('GenericBundle:Message')->findOneBy(array('destinataire'=>$messagereponse->getMission()->getTuteur(),'expediteur'=>$this->get('security.token_storage')->getToken()->getUser(),'mission'=>$messagereponse->getMission()));
        if(!$messagedup) {
            //Message RP
            $messageRP = $em->getRepository('GenericBundle:Message')->findOneBy(array('expediteur'=>$this->get('security.token_storage')->getToken()->getUser(),'destinataire'=>$messagereponse->getExpediteur(),'mission'=>$messagereponse->getMission()));
            $messageRP->setStatut(1);
            $messageRP->getMessage("L'apprenant a donné suite à votre mise en relation");
            $em->flush();
            //Message au Tuteur
            $messageTuteur = new Message();
            $date = new \DateTime();
            $messageTuteur->setDate($date);
            $messageTuteur->setExpediteur($this->get('security.token_storage')->getToken()->getUser());
            $messageTuteur->setDestinataire($messagereponse->getMission()->getTuteur());
            $messageTuteur->setMission($messagereponse->getMission());
            $messageTuteur->setMessage('Cet apprenant correspond au profil demandé');
            $messagereponse->setStatut(1);
            $em->persist($messageTuteur);
            $em->flush();
            $messagedup = $messageTuteur;
        }
        else{
            $messagedup->setMessage('Cet apprenant correspond au profil demandé');
            $messagedup->setStatut(1);
            $messagereponse->setStatut(1);
            $em->flush();
        }
        $postdup = $em->getRepository('GenericBundle:Postulation')->findOneBy(array('user'=>$this->get('security.token_storage')->getToken()->getUser(),'mission'=>$messagereponse->getMission()));
        if(!$postdup) {
            //Creation de la postulation
            $postulation = new Postulation();
            $postulation->setUser($this->get('security.token_storage')->getToken()->getUser());
            $postulation->setMission($messagereponse->getMission());
            $postulation->setStatut(1);
            $em->persist($postulation);
            $em->flush();

            if($this->get('security.token_storage')->getToken()->getUser()->hasRole('ROLE_APPRENANT')){
                $mail = \Swift_Message::newInstance()
                    ->setSubject('Postulation')
                    ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                    ->setTo($messagereponse->getMission()->getTuteur()->getEmail())
                    ->setCc($messagereponse->getExpediteur()->getEmail())
                    ->setBody($this->renderView('GenericBundle:Mail:PostulationMessage.html.twig',array('message'=>$messagedup->getMessage(),'apprenant'=>$messagedup->getExpediteur(),'mission'=>$messagedup->getMission()))
                        ,'text/html'
                    );
                $this->get('mailer')->send($mail);
            }
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
            $rdv->setStatut(2);
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

                if($i == 0){
                    $rdv->setDate1(date_create_from_format('d/m/Y H:i',$datetimeRDV));
                }
                if($i == 1){
                    $rdv->setDate2(date_create_from_format('d/m/Y H:i',$datetimeRDV));
                }
                if($i == 2){
                    $rdv->setDate3(date_create_from_format('d/m/Y H:i',$datetimeRDV));
                }

                array_push($date,$datetimeRDV);
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
        return $rep->setdata(0);
    }

    public function envoiMailRemplirCRAction($idUser,$idRDV){
        $rdv = $this->getDoctrine()->getRepository('GenericBundle:RDV')->find($idRDV);
        $user = $this->getDoctrine()->getRepository('GenericBundle:User')->find($idUser);
        $reponse = new JsonResponse();

        if($user == $rdv->getApprenant()){
            $message = \Swift_Message::newInstance()
                ->setSubject('Email')
                ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                ->setTo($rdv->getApprenant()->getEmail())
                ->setBody($this->render('GenericBundle:Mail:EmailRemplirCompteRendu.html.twig',array('rdv'=>$rdv,'tuteur'=>false))
                    ,'text/html'
                );
            $this->get('mailer')->send($message);
            $reponse->setData(1);
        }
        elseif($user == $rdv->getTuteur()){
            $message = \Swift_Message::newInstance()
                ->setSubject('Email')
                ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                ->setTo($rdv->getTuteur()->getEmail())
                ->setBody($this->render('GenericBundle:Mail:EmailRemplirCompteRendu.html.twig',array('rdv'=>$rdv,'tuteur'=>true))
                    ,'text/html'
                );
            $this->get('mailer')->send($message);
            $reponse->setData(1);
        }
        else{
            $reponse->setData(0);
        }
        return $reponse;

    }

    public function ConsulterCompteRenduAction($idRDV){
        $rdv = $this->getDoctrine()->getRepository('GenericBundle:RDV')->find($idRDV);
        $Arraycomptes = array();
        $rep = new JsonResponse();
        foreach ($this->getDoctrine()->getRepository('GenericBundle:CompteRendu')->findBy(array('rendezvous'=>$rdv)) as $item) {
            array_push($Arraycomptes,array($item->getAuteur()->getCivilite().' '.$item->getAuteur()->getNom().' '.$item->getAuteur()->getPrenom(),$item->getCompterendu(),date_format($item->getDate(),'d/m/Y H:i') ));
        }
        return $rep->setData($Arraycomptes);
    }
}
