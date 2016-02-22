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
        $licence->setMaxmission($_REQUEST['_maxmiss']);
        $licence->setMaxapp($_REQUEST['_maxapp']);
        $em=$this->getDoctrine()->getManager();
        $em->persist($licence);
        $em->flush();
        return $this->redirect($this->generateUrl('metier_user_admin'));
    }

    public function associatedAction(Request $request)
    {
        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->find($request->get('Licencedef'));
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_idetab'));

        $licence = new Licence();
        $licence->setLibelle($licencedef->getLibelle());
        $licence->setDescription($licencedef->getDescription());
        $licence->setMaxapp($licencedef->getMaxapp());
        $licence->setMaxmission($licencedef->getMaxmission());
        $licence->setTier($etablissement->getTier());
        $date=date_create($request->get('_Date'));

        $licence->setDatedebut($date);
        $datefin=date_create($request->get('_Date'));
        $licence->setDatefin(date_add($datefin,date_interval_create_from_date_string($licencedef->getDuree()." days")));
        $em=$this->getDoctrine()->getManager();
        $em->persist($licence);
        $em->flush();
        return $this->redirect($this->generateUrl('affiche_etab',array('id'=>$request->get('_idetab'))));
    }

    public function affichageLicenceAction($id)
    {
        $licence = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->find($id);
        return $this->render('AdminBundle:Admin:afficheLicence.html.twig',array('licence'=>$licence));
    }
}
