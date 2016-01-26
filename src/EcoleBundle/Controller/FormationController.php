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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class FormationController extends Controller
{


    public function ajouterFormationAction(Request $request)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $formation = new Formation();


        $id=$request->get('_idetab');

        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($id);


        $formation->setEtablissement($etablissement);
        $formation->setDescriptif($request->get('_Description'));
        if($_FILES && $_FILES['_PDF']['size'] >0)
        {
            $formation->setDocument(file_get_contents($_FILES['_PDF']['tmp_name']));
        }
        $formation->setNom($request->get('_Nom'));
        $em->persist($formation);
        $em->flush();

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


}