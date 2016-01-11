<?php

namespace AdminBundle\Controller;

use GenericBundle\Entity\Licencedef;
use GenericBundle\Entity\Licence;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LicenceDefController extends Controller
{
    public function addlicenceAction()
    {
        return $this->render('AdminBundle:Admin:ajoutelicence.html.twig');
    }
    public function licenceaddedAction()
    {
        $licence = new Licencedef();
        $licence->setLibelle($_REQUEST['_Libelle']);
        $licence->setDescription($_REQUEST['_Description']);
        $licence->setDuree($_REQUEST['_Duree']);
        $em=$this->getDoctrine()->getManager();
        $em->persist($licence);
        $em->flush();
        return $this->render("AdminBundle:Admin:iFrameContent.html.twig");
    }

    public function associerAction(Request $request)
    {
        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findAll();
        $ecole = $this->getDoctrine()->getRepository('GenericBundle:Ecole')->find($request->get('idecole'));
        if($ecole->getLogo())
        {
            $ecole->setLogo(base64_encode(stream_get_contents($ecole->getLogo())));
        }
        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licenceecole')->findBy(array('ecoleecole'=>$ecole ));
        $users = $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('ecole'=>$ecole ));

        return $this->render('AdminBundle:Admin:instanceLicence.html.twig',array('licencedef'=>$licencedef,
            'ecole'=>$ecole,'libs'=>$licences,'usersecole'=>$users));


    }
    public function associatedAction(Request $request)
    {
            $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->find($request->get('Licencedef'));
            $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_idetab'));

            $licence = new Licence();
            $licence->setLibelle($licencedef->getLibelle());
            $licence->setDescription($licencedef->getDescription());
            $licence->setTier($etablissement->getTier());
            $date=date_create($request->get('_Date'));
            $licence->setDatedebut(date_format($date,"d/m/Y"));

            date_add($date,date_interval_create_from_date_string($licencedef->getDuree()." days"));
            $datef = date_format($date,"d/m/Y");
            $licence->setDatefin($datef);
            $em=$this->getDoctrine()->getManager();
            $em->persist($licence);
            $em->flush();
            return $this->forward('AdminBundle:Default:affichage',array('id'=>$request->get('_idetab')));
    }
}
