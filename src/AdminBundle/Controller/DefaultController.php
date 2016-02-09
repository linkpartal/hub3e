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
