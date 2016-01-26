<?php
/**
 * Created by PhpStorm.
 * User: ABDELLAH
 * Date: 21/01/2016
 * Time: 11:19
 */
namespace EcoleBundle\Controller;

use GenericBundle\Entity\Etablissement;
use GenericBundle\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


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


        return $this->render('EcoleBundle:Adminecole:iFrameContent.html.twig');


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


}