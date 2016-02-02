<?php
/**
 * Created by PhpStorm.
 * User: ABDELLAH
 * Date: 21/01/2016
 * Time: 11:19
 */
namespace SocieteBundle\Controller;


use GenericBundle\Entity\Formation;
use GenericBundle\Entity\Qcmdef;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use \JMS\Serializer\SerializerBuilder;


class FormationController extends Controller
{
    public function ajouterFormationAction(Request $request)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $formation = new Formation();

        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_idetab'));


        $formation->setEtablissement($etablissement);
        $formation->setDescriptif($request->get('_Description'));

        $formation->setNom($request->get('_Nom'));
        $formation->setNomDoc($_FILES['_PDF']['name']);
        $em->persist($formation);
        $em->flush();
        $emplacementFinal = "./formation_pdf/". $formation->getId() .".pdf" ;
        move_uploaded_file($_FILES['_PDF']['tmp_name'] , $emplacementFinal);


        return $this->forward('EcoleBundle:Default:affichage',array('id'=>$request->get('_idetab')));


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
        $em = $this->getDoctrine()->getEntityManager();
        $formation = $em->getRepository('GenericBundle:Formation')->find($id);
        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($formation->getQcmdef(), 'json');
        $reponse = new JsonResponse();
        return $reponse->setData(array('adresses'=>$jsonContent));
    }



    public function formationAddQcmAction($idformation, $idqcm){
        $em = $this->getDoctrine()->getEntityManager();
        $formation = $em->getRepository('GenericBundle:Formation')->find($idformation);
        $qcm = $em->getRepository('GenericBundle:Qcmdef')->find($idqcm);
        $qcm->addFormationformation($formation);
        $em->persist($qcm);
        $em->flush();

        $reponse = new JsonResponse();
        return $reponse->setData(array('succes'=>'0'));
    }

    public function formationRemoveQcmAction($idformation, $idqcm){
        $em = $this->getDoctrine()->getEntityManager();
        $formation = $em->getRepository('GenericBundle:Formation')->find($idformation);
        $qcm = $em->getRepository('GenericBundle:Qcmdef')->find($idqcm);
        $qcm->removeFormationformation($formation);
        $em->persist($qcm);
        $em->flush();

        $reponse = new JsonResponse();
        return $reponse->setData(array('succes'=>'0'));
    }

}