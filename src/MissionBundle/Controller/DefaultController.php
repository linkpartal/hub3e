<?php

namespace MissionBundle\Controller;

use GenericBundle\Entity\Diffusion;
use GenericBundle\Entity\Message;
use GenericBundle\Entity\Mission;
use GenericBundle\Entity\Postulation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function addMissionAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $mission = new Mission();
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_idetab'));
        $mission->setEtablissement($etablissement);

        $mission->setDescriptif($request->get('_Descriptif'));
        $mission->setProfil($request->get('_Profil'));
        $mission->setTypecontrat($request->get('_TypeContrat'));
        $mission->setDomaine($request->get('_Domaine'));
        $date = new \DateTime();
        $mission->setDatecreation($date);
        $mission->setRemuneration($request->get('_Remuneration'));
        $mission->setHoraire($request->get('_Horaire'));

        $mission->setDatedebut(date_create($request->get('_Datedebut')) );
        $mission->setDatefin(date_create($request->get('_Datefin')) );
        $mission->setNomcontact($request->get('_NomContact'));
        $mission->setPrenomContact($request->get('_PrenomContact'));
        $mission->setFonctionContact($request->get('_FonctionContact'));
        $mission->setTelContact($request->get('_TelContact'));
        $mission->setEmailContact($request->get('_EmailContact'));
        $mission->setIntitule($request->get('_Intitule'));
        $mission->setEmploi($request->get('_Emploi'));

        $em->persist($mission);
        $em->flush();
        $mission->genererCode();
        $em->flush();

        if($request->get('formation'))
        {
            $diffuser = new Diffusion();
            $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->find($request->get('formation'));
            $diffuser->setFormation($formation);
            $diffuser->setMission($mission);
            $diffuser->setStatut(5);
            $em->persist($diffuser);
            $em->flush();
        }
        return $this->render('GenericBundle::ReloadParent.html.twig');
    }

    public function affichageMissionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $mission = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($id);
        $users = array();
        $tuteurs = array();
        foreach($em->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$mission->getEtablissement())) as $users_etablissement)
        {
            if($users_etablissement->hasRole('ROLE_TUTEUR'))
            {
                array_push($tuteurs,$users_etablissement);
            }
        }
        foreach($em->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission)) as $diffusion)
        {
            if($this->get('security.authorization_checker')->isGranted('ROLE_ADMINSOC') and $diffusion->getStatut() == 2)
            {
                foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
                {
                    if($candidature->getUser()->getInfo()->getProfilcomplet()){
                        array_push($users,$candidature->getUser());
                    }

                }
            }
            else{
                $ecoleconnecte = $this->get('security.token_storage')->getToken()->getUser();
                if($ecoleconnecte->hasRole('ROLE_ADMINECOLE') and $ecoleconnecte->getTier() == $diffusion->getFormation()->getEtablissement()->getTier()){
                    foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
                    {
                        if($candidature->getUser()->getInfo()->getProfilcomplet()){
                            array_push($users,$candidature->getUser());
                        }
                    }
                }
                elseif($ecoleconnecte->hasRole('ROLE_RECRUTEUR') and $ecoleconnecte->getEtablissement() == $diffusion->getFormation()->getEtablissement())
                {
                    foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
                    {
                        if($candidature->getUser()->getInfo()->getProfilcomplet()){
                            array_push($users,$candidature->getUser());
                        }
                    }
                }
            }

        }

        foreach($users as $apprenant)
        {
            if($apprenant->getPhotos())
            {
                $apprenant->setPhotos(base64_encode(stream_get_contents($apprenant->getPhotos())));
            }
        }

        if($mission->getEtablissement()->getTier()->getLogo())
        {
            $mission->getEtablissement()->getTier()->setLogo(base64_encode(stream_get_contents($mission->getEtablissement()->getTier()->getLogo())));
        }
        if($mission->getEtablissement()->getTier()->getFondecran())
        {
            $mission->getEtablissement()->getTier()->setFondecran(base64_encode(stream_get_contents($mission->getEtablissement()->getTier()->getFondecran())));
        }
        $formations_prop = null;
        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMINSOC'))
        {
            $formations_prop = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findAll();
        }

        $informations_maps = array();
        foreach($users as $user)
        {
            array_push($informations_maps,[$user->getNom() .' '. $user->getPrenom(),$user->getInfo()->getAdresse()]);
        }


        // var_dump($mission);die;
        return $this->render('MissionBundle::afficheMission.html.twig',array('mission'=>$mission,'users'=>$users,'formations_prop'=>$formations_prop,'informations_maps'=>$informations_maps,'tuteur_etablissement'=>$tuteurs));



    }

    public function suppMissionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $mis = $em->getRepository('GenericBundle:Mission')->find($id);
        $mis->setSuspendu(true);
        $em->flush();
        $reponse = new JsonResponse();
        return $reponse->setData(array('Status'=>'Mission correctement supprimer'));
    }

    public function missionModifAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $mission = $em->getRepository('GenericBundle:Mission')->find($request->get('_ID'));
        $mission->setDescriptif($request->get('_Descriptif'));
        $mission->setProfil($request->get('_Profil'));
        $mission->setTypecontrat($request->get('_TypeContrat'));
        $mission->setNomcontact($request->get('_NomContact'));
        $mission->setCodemission($request->get('_Codemission'));
        $mission->setEmailcontact($request->get('_Emailcontact'));
        $mission->setDomaine($request->get('_Domaine'));
        $mission->setDatedebut(date_create($request->get('_Datedebut')) );
        $mission->setDatefin(date_create($request->get('_Datefin')) );
        $mission->setRemuneration($request->get('_Remuneration'));
        $mission->setDatemodification(date_create());

        $em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function DiffuserMissionAction($id ,Request $request){
        $em = $this->getDoctrine()->getManager();
        $mission = $em->getRepository('GenericBundle:Mission')->find($id);
        foreach($request->get('formation') as $idformation)
        {
            if($idformation == 'Recherche')
            {
                continue;
            }

            $formation = $em->getRepository('GenericBundle:Formation')->find($idformation);
            $duplicata = $em->getRepository('GenericBundle:Diffusion')->findOneBy(array('formation'=>$formation,'mission'=>$mission));
            if(!$duplicata)
            {
                $diffuser = new Diffusion();
                $diffuser->setFormation($formation);
                $diffuser->setMission($mission);
                $diffuser->setStatut(1);
                $em->persist($diffuser);
                $em->flush();
            }
        }
        $response = new JsonResponse();
        return $response->setData(array('status'=>1));
    }

    public function AfficherMissionProposeAction($id)
    {
        $mission = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($id);

        if($mission->getEtablissement()->getTier()->getLogo())
        {
            $mission->getEtablissement()->getTier()->setLogo(base64_encode(stream_get_contents($mission->getEtablissement()->getTier()->getLogo())));
        }
        if($mission->getEtablissement()->getTier()->getFondecran())
        {
            $mission->getEtablissement()->getTier()->setFondecran(base64_encode(stream_get_contents($mission->getEtablissement()->getTier()->getFondecran())));
        }
        $diffusions = $this->getDoctrine()->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission));

        // var_dump($mission);die;
        return $this->render('MissionBundle::MissionProposee.html.twig',array('mission'=>$mission,'diffusions'=>$diffusions));
    }

    public function ValiderDiffusionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $diffusion = $em->getRepository('GenericBundle:Diffusion')->find($id);
        if($diffusion)
        {
            $diffusion->setStatut(2);
            $em->flush();
        }

        $response = new JsonResponse();
        return $response->setData(array('status'=>1));
    }

    public function PostulerAction($id){
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $mission = $em->getRepository('GenericBundle:Mission')->find($id);
        $reponse = new JsonResponse();

        //$postdup = $em->getRepository('GenericBundle:Message')->findOneBy(array('expediteur'=>$user,'mission'=>$mission,'statut'=>7));
        $postdup = $em->getRepository('GenericBundle:Postulation')->findOneBy(array('user'=>$user,'mission'=>$mission));
        if($postdup)
        {
            return $reponse->setData(0);
        }
        else{

            $postulation = new Postulation();
            $postulation->setUser($this->get('security.token_storage')->getToken()->getUser());
            $postulation->setMission($mission);
            $postulation->setStatut(1);
            $em->persist($postulation);
            $em->flush();

            if($user->getEtablissement())
            {
                $destinataires = $em->getRepository('GenericBundle:User')->findByRoles(array('ROLE_RECRUTEUR','ROLE_ADMINECOLE'));
                foreach($destinataires as $destinataire)
                {
                    if(($destinataire->hasRole('ROLE_ADMINECOLE') and $destinataire->getTier() == $user->getEtablissement()->getTier()) or ($destinataire->hasRole('ROLE_RECRUTEUR') and $destinataire->getEtablissement() == $user->getEtablissement()))
                    {
                        $message = new Message();
                        $message->setStatut(7);
                        $message->setExpediteur($user);
                        $message->setMessage('Cette mission m\'intéresse ');
                        $message->setMission($mission);
                        $message->setDestinataire($destinataire);
                        $message->setDate(date_create());
                        $em->persist($message);
                        $em->flush();

                        $mail = \Swift_Message::newInstance()
                            ->setSubject('Postulation')
                            ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
                            ->setTo($destinataire->getEmail())
                            ->setBody($this->renderView('PostulationSpontanée.html.twig',array('apprenant'=>$user,'mission'=>$mission))
                                ,'text/html'
                            );
                        $this->get('mailer')->send($mail);
                    }
                }
                return $reponse->setData(1);

            }
        }




    }

    public function AssignerTuteurAction($id,Request $request){
        $mission = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($id);
        if($request->get('tuteur'))
        {
            $tuteur = $this->getDoctrine()->getRepository('GenericBundle:User')->find($request->get('tuteur'));
            if($tuteur)
            {
                $mission->setTuteur($tuteur);
            }
        }

        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

}
