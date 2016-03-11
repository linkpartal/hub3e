<?php

namespace UserBundle\Controller;
use GenericBundle\Entity\Candidature;
use GenericBundle\Entity\Diplome;
use GenericBundle\Entity\Document;
use GenericBundle\Entity\Experience;
use GenericBundle\Entity\ImportCandidat;
use GenericBundle\Entity\Formation;
use GenericBundle\Entity\Hobbies;
use GenericBundle\Entity\Infocomplementaire;
use GenericBundle\Entity\Langue;
use GenericBundle\Entity\Mission;
use GenericBundle\Entity\Parents;
use GenericBundle\Entity\Recommandation;
use GenericBundle\Entity\User;
use GenericBundle\Entity\Etablissement;
use GenericBundle\Entity\Tier;
use GenericBundle\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SASController extends Controller
{
    public function affichageUserSASAction($id){


        $user = $this->get('security.token_storage')->getToken()->getUser();

        $importCandidatid = $this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->find($id);

        $info = $importCandidatid->getInfo();

        $Parents = $this->getDoctrine()->getRepository('GenericBundle:Parents')->findBy(array('importCandidat'=>$importCandidatid));

        $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findAll();
        $Langue = $this->getDoctrine()->getRepository('GenericBundle:Langue')->findAll();
        $Hobbies = $this->getDoctrine()->getRepository('GenericBundle:Hobbies')->findAll();
        // chargement des images
        if($importCandidatid->getPhotos() and !is_string($importCandidatid->getPhotos()))
        {
            $importCandidatid->setPhotos(base64_encode(stream_get_contents($importCandidatid->getPhotos())));
        }


        $Experience = $this->getDoctrine()->getRepository('GenericBundle:Experience')->findBy(array('importCandidat'=>$importCandidatid));

        $Recommandation = $this->getDoctrine()->getRepository('GenericBundle:Recommandation')->findBy(array('importCandidat'=>$importCandidatid));

        $Diplome = $this->getDoctrine()->getRepository('GenericBundle:Diplome')->findBy(array('importCandidat'=>$importCandidatid));

        $Document = $this->getDoctrine()->getRepository('GenericBundle:Document')->findBy(array('importCandidat'=>$importCandidatid));

        $type = 'Utilisateur';

        $candidatures = $this->getDoctrine()->getRepository('GenericBundle:Candidature')->findBy(array('importcandidat'=>$importCandidatid));

        if ($importCandidatid->getEtablissement()) {


            return $this->render('UserBundle:Gestion:iFrameContentSAS.html.twig', array('ImportCandidat' => $importCandidatid,
                'Infocomplementaire' => $info, 'Parents' => $Parents, 'Experience' => $Experience, 'Recommandation' => $Recommandation, 'Diplome' => $Diplome, 'Document' => $Document,
                'Langue' => $Langue, 'Hobbies' => $Hobbies,'candidatures' => $candidatures, 'QCMs' => $importCandidatid->getEtablissement()->getQcmdef(),'formations'=>$formation));
        }
        else{
            return $this->render('UserBundle:Gestion:iFrameContentSAS.html.twig',array('User'=>$importCandidatid,'Infocomplementaire'=>$info,'Parents'=>$Parents,'Experience'=>$Experience,'Recommandation'=>$Recommandation,
                'Diplome'=>$Diplome,'Document'=>$Document,'Langue'=>$Langue,'Hobbies'=>$Hobbies,'candidatures'=>$candidatures,'formations'));
        }


    }

    public function SASModifAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $ImportCandidat = $em->getRepository('GenericBundle:ImportCandidat')->findOneBy(array('id'=>$request->get('_ID')));


        $ImportCandidat->setCivilite($request->get('_Civilite'));
        $ImportCandidat->setNom($request->get('_Nom'));
        $ImportCandidat->setPrenom($request->get('_Prenom'));

        if($_FILES && $_FILES['_Photos']['size'] >0)
        {
            $ImportCandidat->setPhotos(file_get_contents($_FILES['_Photos']['tmp_name']));
            $em->flush();
        }

        $ImportCandidat->setTelephone($request->get('_Tel'));

        $ImportCandidat->setEmail($request->get('_Mail'));
        $em->flush();

        $info = $em->getRepository('GenericBundle:infocomplementaire')->find(array('id'=>$request->get('_IdInfo')));

        if($info)
        {
            if($request->get('_Datenaissance'))
            {
                $info->setDatenaissance(date_create($request->get('_Datenaissance')) );
            }
            $info->setCpnaissance($request->get('_Cpnaissance'));
            $info->setLieunaissance($request->get('_Lieunaissance'));
            $info->setAdresse($request->get('_Adresse'));
            $info->setFacebook($request->get('_Facebook'));
            $info->setLinkedin($request->get('_Linkedin'));
            $info->setDatemodification(date_create());
            if($request->get('_Mobilite'))
            {
                $info->setMobilite($request->get('_Mobilite'));
            }
            if($request->get('_Fratrie'))
            {
                $info->setFratrie($request->get('_Fratrie'));
            }
            $em->flush();
        }
        else{
            $info = new Infocomplementaire();

            if($request->get('_Datenaissance'))
            {
                $info->setDatenaissance(date_create_from_format('d/m/Y', $request->get('_Datenaissance')) );
            }
            $info->setCpnaissance($request->get('_Cpnaissance'));
            $info->setLieunaissance($request->get('_Lieunaissance'));
            $info->setAdresse($request->get('_Adresse'));
            $info->setFacebook($request->get('_Facebook'));
            $info->setLinkedin($request->get('_Linkedin'));
            $info->setDatecreation(date_create());
            if($request->get('_Mobilite'))
            {
                $info->setMobilite($request->get('_Mobilite'));
            }
            if($request->get('_Fratrie'))
            {
                $info->setFratrie($request->get('_Fratrie'));
            }
            $em->persist($info);
            $em->flush();
        }

        return $this->forward('UserBundle:SAS:affichageUserSAS',array('id'=>$request->get('_ID')));
    }

    public function ajouterHobbieSASAction($id,$IdImport)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $hobbie = $em->getRepository('GenericBundle:Hobbies')->find($id);
        $ImportCondidat = $em->getRepository('GenericBundle:ImportCandidat')->find($IdImport);

        $hobbie->addImportCandidat($ImportCondidat);

        $em->flush();

        $reponse = new JsonResponse();
        return $reponse->setData(array('succes'=>1));

    }

    public function supprimerHobbieSASAction($id,$IdImport)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $hobbie = $em->getRepository('GenericBundle:Hobbies')->find($id);
        $importCandidat = $em->getRepository('GenericBundle:ImportCandidat')->find($IdImport);
        $hobbie->removeImportCandidat($importCandidat);

        $em->flush();

        $reponse = new JsonResponse();
        return $reponse->setData(array('succes'=>'0'));


    }

    public function ajouterLangueSASAction($id,$IdNiveau,$IdImport)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $ImportCandidat = $em->getRepository('GenericBundle:ImportCandidat')->find($IdImport);
        $reponse = new JsonResponse();
        foreach ($em->getRepository('GenericBundle:Langue')->findBy(array('langue' => $id)) as $langue_dup){
            if (in_array($langue_dup, $ImportCandidat->getLangue()->toArray())) {
                return $reponse->setData(array('success' => 0));
            }
        }

        $langue = $em->getRepository('GenericBundle:Langue')->findOneBy(array('langue'=>$id,'niveau'=>$IdNiveau));
        $langue->addImportCandidat($ImportCandidat);

        $em->flush();


        return $reponse->setData(array('success'=>1));

    }

    public function supprimerLangueSASAction($id,$IdImport)
    {



        $em = $this->getDoctrine()->getEntityManager();
        $langue = $em->getRepository('GenericBundle:Langue')->find($id);
        $ImportCandidat = $em->getRepository('GenericBundle:ImportCandidat')->find($IdImport);
        $langue->removeImportCandidat($ImportCandidat);

        $em->flush();

        $reponse = new JsonResponse();
        return $reponse->setData(array('succes'=>'0'));


    }

    public function AjouterCandidatureSASAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $importCandidat= $this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->find($request->get('_idUser'));

        if($request->get('formations'))
        {
            foreach($request->get('formations') as $idFormation){
                $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->find($idFormation);
                $cand = $this->getDoctrine()->getRepository('GenericBundle:Candidature')->findOneBy(array('importcandidat'=>$importCandidat,'formation'=>$formation));
                if(!$cand)
                {
                    $candidature = new Candidature();
                    $candidature->setImportcandidat($importCandidat);
                    $candidature->setFormation($formation);
                    $candidature->setStatut(2);
                    $date = new \DateTime();
                    $candidature->setDatecandidature($date);
                    $em->persist($candidature);
                    $em->flush();
                }

            }
        }



            return $this->redirect($this->generateUrl('Afficher_Sas',array('id'=>$request->get('_idUser'))));


    }

    public function AjouterParentSASAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();

        $parent = new Parents();

        $parent->setImportCandidat($this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->find($request->get('_idUser')));


        $parent->setNom($request->get('_Civiliteparent').' '.$request->get('_Nomparent'));
        $parent->setPrenom($request->get('_Prenomparent'));
        $parent->setMetier($request->get('_Metierparent'));
        $parent->setProfession($request->get('_Professionparent'));
        $parent->setTelephone($request->get('_Telephoneparent'));
        $parent->setAdresse($request->get('_Adresseparent').' '.$request->get('_Villeparent').' '.$request->get('_CodePostaleparent'));
        $parent->setEmail($request->get('_Emailparent'));

        $em->persist($parent);
        $em->flush();

            return $this->redirect($this->generateUrl('Afficher_Sas',array('id'=>$request->get('_idUser'))));



    }

    public function AjouterExperienceSASAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $experience = new Experience();
        $experience->setImportCandidat($this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->find($request->get('_idUser')));

        $experience->setNomsociete($request->get('_Nomsociete'));
        $experience->setActivite($request->get('_Activite'));
        $experience->setLieu($request->get('_Lieu'));
        $experience->setPoste($request->get('_Poste'));
        $experience->setNbreannee($request->get('_Nbreannee'));
        $experience->setDescription($request->get('_Descriptionexp'));

        $em->persist($experience);
        $em->flush();



            return $this->redirect($this->generateUrl('Afficher_Sas',array('id'=>$request->get('_idUser'))));


    }

    public function AjouterRecommandationSASAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $recommandation = new Recommandation();
        $recommandation->setImportCandidat($this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->find($request->get('_idUser')));

        $recommandation->setNom($request->get('_Nomrec').' '.$request->get('_Prenomrec'));
        $recommandation->setFonction($request->get('_Fonctionrec'));
        $recommandation->setTelephone($request->get('_Telephonerec'));
        $recommandation->setEmail($request->get('_Emailrec'));
        $recommandation->setText($request->get('_Text'));

        $em->persist($recommandation);
        $em->flush();


            return $this->redirect($this->generateUrl('Afficher_Sas',array('id'=>$request->get('_idUser'))));



    }

    public function AjouterDiplomeSASAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $diplome = new Diplome();
        $diplome->setImportCandidat($this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->find($request->get('_idUser')));

        $diplome->setLibelle($request->get('_Libelle'));
        $diplome->setObtention($request->get('_Obtention'));
        $diplome->setEcole($request->get('_Ecole'));

        $em->persist($diplome);
        $em->flush();

        return $this->redirect($this->generateUrl('Afficher_Sas',array('id'=>$request->get('_idUser'))));

    }

    public function AjouterDocumentSASAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $document = new Document();
        $document->setImportCandidat($this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->find($request->get('_idUser')));

        $document->setType($request->get('_Type'));
        $document->setExtension($_FILES['_Document']['type']);
        $document->setName($_FILES['_Document']['name']);
        $document->setTaille($_FILES['_Document']['size']);
        $document->setDocument(file_get_contents($_FILES['_Document']['tmp_name']));

        $em->persist($document);
        $em->flush();

        return $this->redirect($this->generateUrl('Afficher_Sas',array('id'=>$request->get('_idUser'))));
    }

    public function supprimeruserSASAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $ImportCandidat = $em->getRepository('GenericBundle:ImportCandidat')->find($id);

        if(!$ImportCandidat)
        {
            throw new Exception('Aucun personne de la table SAS ne posséde l\'id ' . $id);
        }


        $em->remove($ImportCandidat);
        $em->flush();
        $reponse = new JsonResponse();

            return $reponse->setData(array('Succes'=>$this->generateUrl('Afficher_Sas')));


    }
}