<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GenericBundle\Entity\Mission;
use \JMS\Serializer\SerializerBuilder;


class DefaultController extends Controller
{
    public function loadAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findBy(array('user'=>$user));

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
        $missions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array(),array('date'=>'DESC'));
        $qcms = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->findAll();
        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($notifications, 'json');

        return $this->render('AdminBundle::AdminHome.html.twig',array('ecoles'=>$ecoles,'notifications'=>$jsonContent ,'users'=>$users,
            'AllLicences'=>$licences,'societes'=>$societes,'qcms'=>$qcms,'missions'=>$missions));
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
      //  $formaEtab = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findBy(array('etablissement'=>$etablissement));
        $userMiss = $this->getDoctrine()->getRepository('GenericBundle:User')->findByRole('ROLE_TUTEUR');

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
        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$etablissement->getTier(),'suspendu'=>false ));
        $users = array();
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('tier'=>$etablissement->getTier() )));
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$etablissement )));
        $tiers = $this->getDoctrine()->getRepository('GenericBundle:Tier')->findAll();
        $missions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('suspendu'=>false),array('date' => 'DESC'));
       //$mission = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($id);
        // var_dump($mission);die;


        return $this->render('AdminBundle:Admin:iFrameContent.html.twig',array('licencedef'=>$licencedef,'etablissement'=>$etablissement,
            'libs'=>$licences,'tiers'=>$tiers,'users'=>$users,'formations'=>$formation, 'missions'=>$missions ,'usermis'=>$userMiss));
    }

    public function creeNewModeleAction($id)
    {
        /*
        $modeles = array();
        if ($handle = opendir('../src/GenericBundle/Resources/views/Mail')) {

            while (false !== ($entry = readdir($handle))) {

                if ($entry != "." && $entry != "..") {

                    array_push($modeles,$entry);
                }
            }

            closedir($handle);
        }*/
        if($id == 'ajouter')
        {
            return $this->render("AdminBundle:Admin:creeNewModele.html.twig");
        }
        else{
            $d = new \DOMDocument;
            @$d->loadHTML(file_get_contents('../src/GenericBundle/Resources/views/Mail/templates/'. $id));
            $body = "";
            foreach($d->getElementsByTagName("body")->item(0)->childNodes as $child) {
                $body .= $d->saveHTML($child);
            }

            return $this->render("AdminBundle:Admin:creeNewModele.html.twig",array('modele'=>$id,'body'=>$body));
        }

    }

    public function saveNewModeleAction(Request $request)
    {
        if(".html.twig" === "" || (($temp = strlen($request->get('_filename')) - strlen('.html.twig')) >= 0 && strpos($request->get('_filename'), '.html.twig', $temp) !== FALSE))
        {
            $myfile = fopen("../src/GenericBundle/Resources/views/Mail/templates/". $request->get('_filename'),"w");
        }
        else{
            $myfile = fopen("../src/GenericBundle/Resources/views/Mail/templates/". $request->get('_filename') .".html.twig","w");
        }


        fwrite($myfile,$this->render('GenericBundle:Mail:Modele.html.twig',array('Textarea'=>$request->get('_newtext')))->getContent());
        fclose($myfile);
        return $this->redirect($this->generateUrl('admin_iframeload'));
    }


}
