<?php

namespace MissionBundle\Controller;

use GenericBundle\Entity\Diffusion;
use GenericBundle\Entity\Mission;
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
        $mission->setDate($date);
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
            $diffuser->setFormation($this->getDoctrine()->getRepository('GenericBundle:Formation')->find($request->get('formation')));
            $diffuser->setMission($mission);
            $diffuser->setStatut(5);
            $em->persist($diffuser);
            $em->flush();
        }



        return $this->redirect($this->generateUrl('affiche_etab',array('id'=>$etablissement->getId())));


    }

    public function affichageMissionAction($id)
    {
        $mission = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($id);
        $users = $this->getDoctrine()->getRepository('GenericBundle:User')->findByRole('ROLE_APPRENANT');

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
        return $this->render('MissionBundle::afficheMission.html.twig',array('mission'=>$mission,'users'=>$users,'formations_prop'=>$formations_prop,'informations_maps'=>$informations_maps));



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

        $mission = $em->getRepository('GenericBundle:Mission')->findOneBy(array('id'=>$request->get('_ID')));
        $mission->setProfil($request->get('_Profil'));
        $mission->setCodemission($request->get('_Codemission'));
        $mission->setDescriptif($request->get('_Descriptif'));
        $mission->setTypecontrat($request->get('_TypeContrat'));
        $mission->setEmailcontact($request->get('_Emailcontact'));
        $mission->setDomaine($request->get('_Domaine'));
        $mission->setDatedebut(date_create($request->get('_Datedebut')) );
        $mission->setDatefin(date_create($request->get('_Datefin')) );
        $mission->setRemuneration($request->get('_Remuneration'));


        $em->flush();

        return $this->redirect($this->generateUrl('admin_afficheMission',array('id'=>$request->get('_ID'))));
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

}
