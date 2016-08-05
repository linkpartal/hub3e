<?php

namespace TierBundle\Controller;

use GenericBundle\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FormationController extends Controller
{
    public function ajouterFormationAction(Request $request)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $formation = new Formation();

        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_idetab'));


        $formation->setEtablissement($etablissement);
        $formation->setDescriptif($request->get('_Description'));

        $formation->setNomResponsable($request->get('_NomResponsable'));
        $formation->setPrenomResponsable($request->get('_PrenomResponsable'));
        $formation->setTelResponsable($request->get('_TelResponsable'));
        $formation->setMailResponsable($request->get('_MailResponsable'));
        $formation->setFonctionResponsable($request->get('_FonctionResponsable'));
        if($request->get('_Metier1') and !$request->get('_Metier1') == '' )
        {
        $formation->setMetier1($request->get('_Metier1'));
        }
        if($request->get('_Metier2') and !$request->get('_Metier2') == '' ){
            $formation->setMetier2($request->get('_Metier2'));
        }
        if($request->get('_Metier3') and !$request->get('_Metier3') == '' ){
            $formation->setMetier3($request->get('_Metier3'));
        }








        $formation->setNom($request->get('_Nom'));
        $formation->setNomDoc($_FILES['_PDF']['name']);
        $em->persist($formation);
        $em->flush();
        $emplacementFinal = "./formation_pdf/". $formation->getId() .".pdf" ;
        move_uploaded_file($_FILES['_PDF']['tmp_name'] , $emplacementFinal);


        return $this->redirect($this->generateUrl('affiche_etab',array('id'=>$request->get('_idetab'))));


    }

    public function afficherDocumentAction($id)
    {

        $item = $this->getDoctrine()->getRepository('GenericBundle:Formation')->find($id);

        if (!$item) {
            throw $this->createNotFoundException("File with ID $id does not exist!");
        }

        $pdfFile = $item->getDocument(); //returns pdf file stored as mysql blob
        $response = new BinaryFileResponse($pdfFile);
        $response->trustXSendfileTypeHeader();
        $response->headers->set('Content-Type','application/pdf');
        return $response;



    }

    public function deleteFormationAction($id)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $formation = $em->getRepository('GenericBundle:Formation')->find($id);
        $em->remove($formation);
        $em->flush();
        unlink("./formation_pdf/".$id.'.pdf');

        $reponse = new JsonResponse();
        return $reponse->setData(array('succes'=>'1'));

    }

    public function formationQcmAction($id){
        $em = $this->getDoctrine()->getManager();
        $formation = $em->getRepository('GenericBundle:Formation')->find($id);
        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($formation->getQcmdef(), 'json');
        $reponse = new JsonResponse();
        return $reponse->setData(array('adresses'=>$jsonContent));
    }

    public function formationAddQcmAction($idformation, $idqcm){
        $em = $this->getDoctrine()->getManager();
        $formation = $em->getRepository('GenericBundle:Formation')->find($idformation);
        $qcm = $em->getRepository('GenericBundle:Qcmdef')->find($idqcm);
        $qcm->addFormation($formation);
        $em->persist($qcm);
        $em->flush();

        $reponse = new JsonResponse();
        return $reponse->setData(array('succes'=>'0'));
    }

    public function formationRemoveQcmAction($idformation, $idqcm){
        $em = $this->getDoctrine()->getManager();
        $formation = $em->getRepository('GenericBundle:Formation')->find($idformation);
        $qcm = $em->getRepository('GenericBundle:Qcmdef')->find($idqcm);
        $qcm->removeFormationformation($formation);
        $em->persist($qcm);
        $em->flush();

        $reponse = new JsonResponse();
        return $reponse->setData(array('succes'=>'0'));
    }

}
