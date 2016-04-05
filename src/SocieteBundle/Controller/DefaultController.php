<?php

namespace SocieteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function loadAdminAction()
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user->getPhotos() and !is_string($user->getPhotos()))
        {
            $user->setPhotos(base64_encode(stream_get_contents($user->getPhotos())));
        }
        /*$notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findBy(array('user'=>$user));
        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($notifications, 'json');*/

        $ecoles = array();
        $ecoles = array_merge($ecoles,$this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findAdressesOfEcole($user->getTier()->getId()))  ;
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
        $societes = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findSocietes();
        foreach($societes as $key => $societe)
        {
            if($societe->getSuspendu())
            {
                unset($societes[$key]);
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

        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$user->getTier()));



        $missions = array();
        foreach($societes as $s){
            $missions =  array_merge($missions,$this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('etablissement'=>$s)));

        }



        return $this->render('SocieteBundle:AdminSoc:index.html.twig', array('ecoles'=>$ecoles, /*'notifications'=>$jsonContent,*/'users'=>$notapprenant,
            'AllLicences'=>$licences,'societes'=>$societes,'image'=>$user->getPhotos(),'missions'=>$missions));
    }
}
