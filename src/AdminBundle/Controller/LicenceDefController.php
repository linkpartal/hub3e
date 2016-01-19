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
            $licence->setDatedebut(date_format($date,"d/m/Y"));

            date_add($date,date_interval_create_from_date_string($licencedef->getDuree()." days"));
            $datef = date_format($date,"d/m/Y");
            $licence->setDatefin($datef);
            $em=$this->getDoctrine()->getManager();
            $em->persist($licence);
            $em->flush();
            return $this->redirect($this->generateUrl('metier_user_affiche',array('id'=>$request->get('_idetab'))));
    }
}
