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

        if($user->getPhotos())
        {
            $user->setPhotos(base64_encode(stream_get_contents($user->getPhotos())));
        }

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
            elseif(!$item->getTier()->getEcole() && !$item->getSuspendu()){
                array_push($societes,$item);
            }
        }
        $users = $this->getDoctrine()->getRepository('GenericBundle:User')->findAll();
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

        $import_apprenant = $this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->findBy(array('user'=>$user));
        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        $missions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array(),array('datecreation'=>'DESC'));
        $qcms = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->findAll();
        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($notifications, 'json');

        //modele
        $modeles = array();
        if ($handle = opendir('../src/GenericBundle/Resources/views/Mail')) {

            while (false !== ($entry = readdir($handle))) {

                if ($entry != "." && $entry != ".." && $entry != "templates" ) {

                    array_push($modeles,$entry);
                }
            }

            closedir($handle);
        }
        if ($handle = opendir('../src/GenericBundle/Resources/views/Mail/templates')) {

            while (false !== ($entry = readdir($handle))) {

                if ($entry != "." && $entry != ".." && $entry!="README.txt") {

                    array_push($modeles,$entry);
                }
            }

            closedir($handle);
        }

        return $this->render('AdminBundle::AdminHome.html.twig',array('ecoles'=>$ecoles,'notifications'=>$jsonContent ,'users'=>$notapprenant,'modeles'=>$modeles,
            'AllLicences'=>$licences,'societes'=>$societes,'qcms'=>$qcms,'missions'=>$missions,'apprenants'=>$apprenants,'import_apprenants'=>$import_apprenant,'image'=>$user->getPhotos()));
    }

    public function loadiframeAction()
    {
        return $this->render('AdminBundle:Admin:iFrameContent.html.twig');
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
            if(file_get_contents('../src/GenericBundle/Resources/views/Mail/'. $id))
            {
                @$d->loadHTML(file_get_contents('../src/GenericBundle/Resources/views/Mail/'. $id));
            }
            elseif(file_get_contents('../src/GenericBundle/Resources/views/Mail/templates/'. $id)){
                @$d->loadHTML(file_get_contents('../src/GenericBundle/Resources/views/Mail/templates/'. $id));
            }
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
