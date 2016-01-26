<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \JMS\Serializer\SerializerBuilder;

class DefaultController extends Controller
{
    public function loadAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findBy(array('user'=>$user));
        $modeles = $this->getDoctrine()->getRepository('GenericBundle:Modele')->findBy(array('user'=>$user));
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findAll();
        $ecoles = array();
        $societes = array();
        foreach($etablissement as $item)
        {
            if($item->getTier()->getEcole() && !$item->getSuspendu())
            {
                array_push($ecoles,$item);
            }
            else{
                array_push($societes,$item);
            }
        }
        $users = $this->getDoctrine()->getRepository('GenericBundle:User')->findAll();
        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        $qcms = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->findAll();
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($notifications, 'json');

        return $this->render('AdminBundle::AdminHome.html.twig',array('ecoles'=>$ecoles,'notifications'=>$jsonContent ,'users'=>$users,
            'AllLicences'=>$licences,'societes'=>$societes,'qcms'=>$qcms,'modeles'=>$modeles));
    }

    public function loadiframeAction()
    {
        return $this->render('AdminBundle:Admin:iFrameContent.html.twig');
    }

    public function affichageAction($id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->find($id);

        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($id);
        $modeles = $this->getDoctrine()->getRepository('GenericBundle:Modele')->findBy(array('user'=>$user));
        if($etablissement->getTier()->getEcole())
        {
            $type = 'Ecole';
        }
        else{
            $type = 'Societe';
        }
        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findOneBy(array('user'=>$user,'entite'=>$etablissement->getId(),'type'=>$type));
        if($notifications)
        {
            $this->getDoctrine()->getEntityManager()->remove($notifications);
            $this->getDoctrine()->getEntityManager()->flush();
        }

        if($etablissement->getTier()->getLogo())
        {
            $etablissement->getTier()->setLogo(base64_encode(stream_get_contents($etablissement->getTier()->getLogo())));
        }
        if($etablissement->getTier()->getFondecran())
        {
            $etablissement->getTier()->setFondecran(base64_encode(stream_get_contents($etablissement->getTier()->getFondecran())));
        }
        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$etablissement->getTier() ));
        $users = array();
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('tier'=>$etablissement->getTier() )));
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$etablissement )));
        $tiers = $this->getDoctrine()->getRepository('GenericBundle:Tier')->findAll();
        return $this->render('AdminBundle:Admin:iFrameContent.html.twig',array('licencedef'=>$licencedef,'etablissement'=>$etablissement,
            'libs'=>$licences,'tiers'=>$tiers,'users'=>$users,'formations'=>$formation));
    }
}
