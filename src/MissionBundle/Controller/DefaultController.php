<?php

namespace MissionBundle\Controller;

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
        $mission->setEtat($request->get('_Etat'));
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
        //$mission->setFormation($request->get('_idform'));

        $us = $this->getDoctrine()->getRepository('GenericBundle:User')->find($request->get('userselect'));
        if($us)
        {
            $mission->setTuteur($us);
        }


        // var_dump($mission);die;
        /*  $user = $this->getDoctrine()->getRepository('GenericBundle:User')->find($request->get('_iduse'));
          $mission->setTuteur($user);*/
        /* $qb=$em->createQueryBuilder();
         $qb->select('mission')
            ->from('EcoleBundle:Default:affichage','mission')
             ->orderBy('mission.date','ASC');
         $query = $qb->getQuery();
         $mission = $query->getResult();*/

        //$mission=$this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('date', 'DESC'));


        $em->persist($mission);
        $em->flush();

        $mission->genererCode();
        $em->flush();



        return $this->redirect($this->generateUrl('affiche_etab',array('id'=>$etablissement->getId())));


    }

    public function affichageMissionAction($id)
    {
        $mission = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($id);
        $Users = $this->getDoctrine()->getRepository('GenericBundle:User')->findAll();


        if($mission->getEtablissement()->getTier()->getLogo())
        {
            $mission->getEtablissement()->getTier()->setLogo(base64_encode(stream_get_contents($mission->getEtablissement()->getTier()->getLogo())));
        }
        if($mission->getEtablissement()->getTier()->getFondecran())
        {
            $mission->getEtablissement()->getTier()->setFondecran(base64_encode(stream_get_contents($mission->getEtablissement()->getTier()->getFondecran())));
        }



        // var_dump($mission);die;
        return $this->render('MissionBundle::afficheMission.html.twig',array('mission'=>$mission,'Users'=>$Users));



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

    public function modifierAction($id)
    {
        $missionid = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($id);
        return $this->render('AdminBundle:Admin:modifierMission.html.twig',array('mission'=>$missionid));
    }
    public function missionModifAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $mission = $em->getRepository('GenericBundle:Mission')->findOneBy(array('id'=>$request->get('_ID')));
        $mission->setProfil($request->get('_Profil'));
        $mission->setCodemission($request->get('_Codemission'));
        $mission->setDescriptif($request->get('_Descriptif'));

        $mission->setEtat($request->get('_Etat'));
        $mission->setTypecontrat($request->get('_TypeContrat'));
        $mission->setNomcontact($request->get('_NomContact'));
        $mission->setPrenomContact($request->get('_PrenomContact'));
        $mission->setFonctionContact($request->get('_FonctionContact'));
        $mission->setTelContact($request->get('_TelContact'));
        $mission->setEmailcontact($request->get('_Emailcontact'));
        $mission->setIntitule($request->get('_Intitule'));
        $mission->setDomaine($request->get('_Domaine'));
        $mission->setDatedebut(date_create($request->get('_Datedebut')) );
        $mission->setDatefin(date_create($request->get('_Datefin')) );
        $mission->setEmploi($request->get('_Emploi'));
        $mission->setRemuneration($request->get('_Remuneration'));
        $mission->setHoraire($request->get('_Horaire'));


        $em->flush();

        return $this->redirect($this->generateUrl('admin_afficheMission',array('id'=>$request->get('_ID'))));
    }
}
