<?php

namespace EcoleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \JMS\Serializer\SerializerBuilder;

class DefaultController extends Controller
{
    public function loadAdminAction()
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findBy(array('user'=>$user));


        $ecoles = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findAdressesOfEcole($user->getTier()->getId());
        $societes = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findSocietes();


        $users = $this->getDoctrine()->getRepository('GenericBundle:User')->getUserofTier($user->getTier());

        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$user->getTier()));
        $qcms = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->findAll();
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($notifications, 'json');
        return $this->render('EcoleBundle:Adminecole:index.html.twig', array('ecoles'=>$ecoles,'notifications'=>$jsonContent ,'users'=>$users,
            'AllLicences'=>$licences,'societes'=>$societes,'qcms'=>$qcms));

    }
    public function loadiframeAction()
    {
        return $this->render('AdminBundle:Admin:iFrameContent.html.twig');
    }

    public function affichageAction($id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($id);
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
            'libs'=>$licences,'tiers'=>$tiers,'users'=>$users));
    }

    public function affichageUserAction($id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        $userid = $this->getDoctrine()->getRepository('GenericBundle:User')->find($id);





        $tiers = $this->getDoctrine()->getRepository('GenericBundle:Tier')->findAll();
        return $this->render('AdminBundle:Admin:iFrameContentUser.html.twig',array('licencedef'=>$licencedef,'User'=>$userid
        ));
    }

    public function affichageLicenceAction($id)
    {
        $licence = $this->getDoctrine()->getRepository('GenericBundle:Licence')->find($id);

        return $this->render('EcoleBundle:Adminecole:afficheLicence.html.twig',array('licence'=>$licence));
    }

    public function creeNewModeleAction()
    {
        $modeles = array();
        if ($handle = opendir('../src/GenericBundle/Resources/views/Mail')) {

            while (false !== ($entry = readdir($handle))) {

                if ($entry != "." && $entry != "..") {

                    array_push($modeles,$entry);
                }
            }

            closedir($handle);
        }
        return $this->render("AdminBundle:Admin:creeNewModele.html.twig",array('modeles'=>$modeles));
    }
    public function saveNewModeleAction(Request $request)
    {
        $myfile = fopen("./templates/". $request->get('_filename') .".html.twig","w");
        fwrite($myfile,$this->render('GenericBundle:Mail:'. $request->get('_modele'),array('Textarea'=>$request->get('_newtext')))->getContent());
        fclose($myfile);
        return $this->render("AdminBundle:Admin:iFrameContent.html.twig");
    }
}
