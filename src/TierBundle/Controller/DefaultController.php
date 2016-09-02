<?php

namespace TierBundle\Controller;

use GenericBundle\Entity\Etablissement;
use GenericBundle\Entity\Qcmdef;
use GenericBundle\Entity\Notification;
use GenericBundle\Entity\Tier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function checkExistAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $reponse = new JsonResponse();

        $tier = $em->getRepository('GenericBundle:Tier')->findOneBy(array('siren'=>$request->get('_SIREN')));


        if ($tier) {
            if($tier->getEcole()){
                return $reponse->setData(array('status'=>'is_ecole'));
            }
            for($i = 0; $i< count($request->get('_SIRET'));$i++) {
                $etablissement = $em->getRepository('GenericBundle:Etablissement')->findOneBy(array('siret'=>$request->get('_SIRET')[$i]));
                if($etablissement)
                {
                    $etablissements = $em->getRepository('GenericBundle:Etablissement')->findAdressesOfSociete($tier->getId());
                    $serializer = $this->get('jms_serializer');
                    $jsonEtablissements = $serializer->serialize($etablissements, 'json');
                    $jsonTier = $serializer->serialize($tier,'json');
                    return $reponse->setData(array('status'=>'exist','Tier'=>$jsonTier,'Etablissements'=>$jsonEtablissements));
                }
            }
        }

        return $reponse->setData(array('status'=>'success'));

    }

    public function tieraddedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        var_dump($request->get('_SIREN'));die;

        $tier = $em->getRepository('GenericBundle:Tier')->findOneBy(array('siren'=>$request->get('_SIREN')));
        if(!$tier){
            $newtier = new Tier();
            $newtier->setSiren($request->get('_SIREN'));
            $newtier->setRaisonsoc($request->get('_RaisonSoc'));
            $newtier->setEcole(intval($request->get('_Ecole')));
            $newtier->setAxe($request->get('_Axe'));
            $newtier->setAvantage($request->get('_Avantages'));
            if(isset($_FILES['_Logo']) && $_FILES['_Logo']['size'] >0)
            {
                $newtier->setLogo(file_get_contents($_FILES['_Logo']['tmp_name']));
            }

            if(isset($_FILES['_image']) && $_FILES['_image']['size'] >0)
            {
                $newtier->setFondecran(file_get_contents($_FILES['_image']['tmp_name']));
            }
            $em->persist($newtier);
            $em->flush();
            $tier = $newtier;
        }



        for($i = 0; $i< count($request->get('_SIRET'));$i++) {



            $etablissement = new Etablissement();
            $etablissement->setSiret($request->get('_SIRET')[$i]);
            $etablissement->setAdresse($request->get('_Adresse')[$i]);
            $etablissement->setGeocode($request->get('_Geocode')[$i]);
            $etablissement->setCodepostal($request->get('_CodeP')[$i]);
            $etablissement->setTelephone($request->get('_Tel')[$i]);
            $etablissement->setFax($request->get('_Fax')[$i]);
            $etablissement->setVille($request->get('_Ville')[$i]);
            $etablissement->setNomResp($request->get('_Resp')[$i]);
            $etablissement->setPrenomResp($request->get('_RespPrenom')[$i]);
            $etablissement->setTelResponsable($request->get('_TelResp')[$i]);
            $etablissement->setMailResponsable($request->get('_MailResp')[$i]);
            $etablissement->setSite($request->get('_Site')[$i]);
            $etablissement->setType($request->get('_TypeSoc')[$i]);
            $etablissement->setTaille($request->get('_Taille')[$i]);
            $etablissement->setSecteur($request->get('_Secteur')[$i]);
            $etablissement->addUser($this->get('security.token_storage')->getToken()->getUser());
            $etablissement->setTier($tier);

            $em->persist($etablissement);
            $em->flush();
            if($tier->getEcole()){
                $em->getRepository('GenericBundle:Qcmdef')->findOneBy(array('nom'=>'QCMparDéfault'))->addEtablissement($etablissement) ;
                $em->flush();
            }
        }

        $em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function affichageAction($id)
    {
        // Utilisateur actuellement connecté.
        $user = $this->get('security.token_storage')->getToken()->getUser();
        // LicenceDef pour peupler le select de l'instanciation de la licence
        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();

        // Etablissement à afficher
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($id);

        // chargement des images
        if($etablissement->getTier()->getLogo() and !is_string($etablissement->getTier()->getLogo()))
        {
            $etablissement->getTier()->setLogo(base64_encode(stream_get_contents($etablissement->getTier()->getLogo())));
        }
        if($etablissement->getTier()->getFondecran() and !is_string($etablissement->getTier()->getFondecran()))
        {
            $etablissement->getTier()->setFondecran(base64_encode(stream_get_contents($etablissement->getTier()->getFondecran())));
        }

        // Recup des licences associer au tier de l'etablissement.
        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$etablissement->getTier(),'suspendu'=>false ));

        // Les utilisateurs de l'etablissement et du tier auquel il est lié
        $users = array();
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('tier'=>$etablissement->getTier() )));
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$etablissement )));
        $users = array_unique($users);
        $contactSociete = $this->getDoctrine()->getRepository('GenericBundle:ContactSociete')->findBy(array('etablissement' => $etablissement));

        //Suppression de la notification de l'utilisateur connecté concernant l'etablissement affiché
        if($etablissement->getTier()->getEcole())
        {
            $type = 'Ecole';
        }
        else{
            $type = 'Societe';
        }
        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findOneBy(array('user'=>$user,'entite'=>$etablissement->getId(),'type'=>$type));
        if($notifications)
        {
            $this->getDoctrine()->getEntityManager()->remove($notifications);
            $this->getDoctrine()->getEntityManager()->flush();
        }

        if($etablissement->getTier()->getEcole()){
            // Recuperation des differents QCM
            $qcmstest = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->findBy(array('affinite'=>false));

            $qcmsaffinite = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->findBy(array('affinite'=>true));

            // séparer les QCMs affinité deja associer à l'etablissement des autres
            $QcmNotEtab = array();
            foreach($qcmsaffinite as $item)
            {
                if(!in_array ($item,$etablissement->getQcmdef()->toArray()))
                {
                    array_push($QcmNotEtab,$item);
                }
            }

            // les formation de l'etablissement
            $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findBy(array('etablissement'=>$etablissement ));

            // les tiers pour peuplé l'association d'ecole
            $alltiers = $this->getDoctrine()->getRepository('GenericBundle:Tier')->findAllExcept($etablissement->getTier()->getId());
            $tiers = array();
            foreach ( $alltiers as $tier)
            {
                if(!in_array($tier,$etablissement->getTier()->getTier1()->toArray()))
                {
                    array_push($tiers,$tier);
                }
            }

            //
            $etablisementlier=array();
            foreach ($etablissement->getTier()->getTier1() as $value ){
                array_push($etablisementlier ,$this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findBy(array('tier'=>$value)));
            }

            return $this->render('TierBundle::iFrameContent.html.twig',array('licencedef'=>$licencedef,'etablissement'=>$etablissement,'tiers'=>$tiers,'users'=>$users,
                'formations'=>$formation,'libs'=>$licences,'QCMS'=>$qcmstest,'QCMSNOTETAB'=>$QcmNotEtab,'etablissementslier'=>$etablisementlier,'ContactSociete'=>$contactSociete));
        }
        else{
            // missions non suspendu
            if($user->hasRole('ROLE_SUPER_ADMIN')){
                $missions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('etablissement'=>$etablissement,'suspendu'=>false),array('datecreation' => 'DESC'));
            }
            else{
                $missions_etablissement = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('etablissement'=>$etablissement,'suspendu'=>false),array('datecreation' => 'DESC'));
                $missions = array();
                foreach($missions_etablissement as $mis){
                    foreach($this->getDoctrine()->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mis)) as $diffusion ){
                        if($user->getTier()){
                            if((($diffusion->getFormation()->getEtablissement()->getTier() == $user->getTier() or $diffusion->getFormation()->getEtablissement() == $user->getEtablissement())  or $user->getTier() == $mis->getTier()
                                or $user->getTier()==$mis->getEtablissement()->getTier()) and !in_array($mis,$missions)){
                                array_push($missions,$mis);
                            }
                        }
                        else if($user->getEtablissement()){
                            if((($diffusion->getFormation()->getEtablissement()->getTier() == $user->getTier() or $diffusion->getFormation()->getEtablissement() == $user->getEtablissement()  or $user->getEtablissement()->getTier() == $mis->getTier()
                                or $user->getEtablissement()->getTier()==$mis->getEtablissement()->getTier()) and !in_array($mis,$missions))){
                                array_push($missions,$mis);
                            }
                        }

                    }
                }
            }

            // QCM pour la création des mission
            $qcm = null;
            $questions = null;
            $reponses = null;

            $qcm = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->findOneBy(array('nom'=>'QCMparDéfault'));
            $questions = $this->getDoctrine()->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef' => $qcm));
            usort($questions, array('\GenericBundle\Entity\Questiondef', 'sort_questions_by_order'));
            $reponses = array();

            foreach ($questions as $keyqst => $qst) {
                $reps = $this->getDoctrine()->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef' => $qst));
                usort($reps, array('\GenericBundle\Entity\Reponsedef', 'sort_reponses_by_order'));
                $reponses[$keyqst] = $reps;
            }





           // var_dump($etablissement->getId());die;
            return $this->render('TierBundle::iFrameContent.html.twig',array('licencedef'=>$licencedef,'etablissement'=>$etablissement,'users'=>$users,
                'libs'=>$licences, 'missions'=>$missions,'ContactSociete'=>$contactSociete ,'QCMs' => $qcm, 'Questions' => $questions,
                'reponses' => $reponses,'formations'=>$this->getDoctrine()->getRepository('GenericBundle:Formation')->findAll()));
        }
        //$licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$etablissement->getTier(),'suspendu'=>false ));
    }

    public function supprimeretabAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $etab = $em->getRepository('GenericBundle:Etablissement')->find($id);

        $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user->hasRole('ROLE_SUPER_ADMIN')){
            $etab->setSuspendu(true);
            $em->flush();
        }
        elseif($user->hasRole('ROLE_ADMINECOLE') or $user->hasRole('ROLE_RECRUTEUR')){
            $etab->removeUser($user);
            $em->flush();
        }

        return $this->render('GenericBundle::ReloadParent.html.twig',array('clear'=>true));
    }

    public function ecolesassociatedAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $etab = $em->getRepository('GenericBundle:Etablissement')->find($id);
        $tier = $em->getRepository('GenericBundle:Tier')->find($etab->getTier());

        foreach($request->get('ecoles') as $value)
        {
            //get Question s'elle existe.
            $ecolelie = $em->getRepository('GenericBundle:Tier')->find($value);
            if(!$ecolelie)
            {
                throw new Exception("Une des écoles choisies n'existe plus");
            }

            else{
                $tier->addTier1($ecolelie);
                $ecolelie->addTier1($tier);
            }
            $em->flush();
        }

        return $this->redirect($this->generateUrl('affiche_etab',array('id'=>$id)) );
    }

    public function etabModifAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $etablissement = $em->getRepository('GenericBundle:Etablissement')->findOneBy(array('siret'=>$request->get('_SIRET')));

        $etablissement->setAdresse($request->get('_Adresse'));
        $etablissement->setGeocode($request->get('_Geocode'));
        $etablissement->setCodepostal($request->get('_CodeP'));
        $etablissement->setTelephone($request->get('_Tel'));
        $etablissement->setFax($request->get('_Fax'));
        $etablissement->setVille($request->get('_Ville'));
        $etablissement->setNomResp($request->get('_Resp'));
        $etablissement->setPrenomResp($request->get('_prenomResp'));
        $etablissement->setTelResponsable($request->get('_TelResp'));
        $etablissement->setMailResponsable($request->get('_MailResp'));
        $etablissement->setSite($request->get('_Site'));


        $etablissement->getTier()->setRaisonsoc($request->get('_RaisonSoc'));
        if($_FILES && $_FILES['_Logo']['size'] >0)
        {
            $etablissement->getTier()->setLogo(file_get_contents($_FILES['_Logo']['tmp_name']));
            $em->flush();
        }
        if($_FILES && $_FILES['_image']['size'] >0)
        {
            $etablissement->getTier()->setFondecran(file_get_contents($_FILES['_image']['tmp_name']));
            $em->flush();
        }

        $em->flush();

        return $this->forward('TierBundle:Default:affichage',array('id'=>$etablissement->getId()));
    }

    public function activateAction($id){
        $etab = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($id);
        $reponse = new JsonResponse();

        if($etab->getActive())
        {
            $etab->setActive(false);
            $reponse->setData(array('succes'=>'0'));
        }
        else{
            $etab->setActive(true);
            $reponse->setData(array('succes'=>'1'));
        }
        $this->getDoctrine()->getEntityManager()->flush();

        return $reponse;
    }

    public function adressesAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $etablissement = $em->getRepository('GenericBundle:Etablissement')->find($id);
        $etablissements = $em->getRepository('GenericBundle:Etablissement')->findBy(array('tier'=>$etablissement->getTier()));
        $adresses = array();
        foreach($etablissements as $value)
        {
            $adresse = array('id'=>$value->getId(),'adresse' => $value->getAdresse());
            array_push($adresses, json_encode($adresse) );
        }
        $reponse = new JsonResponse();
        return $reponse->setData(array('adresses'=>$adresses));
    }

    public function etablissementQcmAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $etablissement = $em->getRepository('GenericBundle:Etablissement')->find($id);
        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($etablissement->getQcmdef(), 'json');
        $reponse = new JsonResponse();

        return $reponse->setData(array('adresses'=>$jsonContent));
    }

    public function etablissementAddQcmAction($idetablissement, $idqcmaff){
        $em = $this->getDoctrine()->getEntityManager();
        $etablissement = $em->getRepository('GenericBundle:Etablissement')->find($idetablissement);
        $qcm = $em->getRepository('GenericBundle:Qcmdef')->find($idqcmaff);
        $etablissement->addQcmdef($qcm);
        $em->persist($etablissement);
        $em->flush();

        $reponse = new JsonResponse();
        return $reponse->setData(array('succes'=>'0'));
    }

    public function etablissementRemoveQcmAction($idetablissement, $idqcmaff){
        $em = $this->getDoctrine()->getEntityManager();
        $etablissement = $em->getRepository('GenericBundle:Etablissement')->find($idetablissement);
        $qcm = $em->getRepository('GenericBundle:Qcmdef')->find($idqcmaff);
        $etablissement->removeQcmdef($qcm);
        $em->persist($etablissement);
        $em->flush();

        $reponse = new JsonResponse();
        return $reponse->setData(array('succes'=>'0'));
    }

    public function ExistEtabAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        for( $i = 0; $i < count($request->get('etablissement')); $i++)
        {
            $etablissement = $em->getRepository('GenericBundle:Etablissement')->find($request->get('etablissement')[$i]);
            $etablissement->addUser($user);
            $em->flush();
        }
        return $this->redirect($_SERVER['HTTP_REFERER']);

    }

    public function suppLicenceAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $lic = $em->getRepository('GenericBundle:Licence')->find($id);
        $lic->setSuspendu(true);
        $em->flush();
        $reponse = new JsonResponse();
        return $reponse->setData(array('Status'=>'Licence correctement supprimer'));
    }

    public function suppEcoleAssocAction($id_ecole,$id_liee)
    {
        $em = $this->getDoctrine()->getManager();
        $tier = $em->getRepository('GenericBundle:Tier')->find($id_ecole);
        $tierliee = $em->getRepository('GenericBundle:Tier')->find($id_liee);
        $tier->removeTier1($tierliee);
        $tierliee->removeTier1($tier);
        $em->flush();
        $reponse = new JsonResponse();
        return $reponse->setData(array('Status'=>'Licence correctement supprimer'));
    }


    public function VerifierSiretAction($siret)
    {

        $em = $this->getDoctrine()->getManager();
        $reponse = new JsonResponse();
        $etablissement = $em->getRepository('GenericBundle:Etablissement')->findOneBy(array('siret'=>$siret));






        //getCodepostal();


        if($etablissement)
        {
            $rue=$etablissement->getAdresse();
            $codePostal=$etablissement->getCodepostal();
            $ville=$etablissement->getVille();
            $tel=$etablissement->getTelephone();
            $fax=$etablissement->getFax();
            $site=$etablissement->getSite();

            $nomResp=$etablissement->getNomResp();
            $prenomResp=$etablissement->getPrenomResp();
            $telResp=$etablissement->getTelresponsable();
            $mailResp=$etablissement->getMailresponsable();

            $type=$etablissement->getType();
            $taille=$etablissement->getTaille();
            $secteur=$etablissement->getSecteur();
           return $reponse->setData(array('status'=>'exist',
                'rue'=>$rue,
                'codePostal'=>$codePostal,
                'ville'=>$ville,
                'tel'=>$tel,
                'fax'=>$fax,
                'site'=>$site,

                'nomResp'=>$nomResp,
                'prenomResp'=>$prenomResp,
                'telResp'=>$telResp,
                'mailResp'=>$mailResp,

                'type'=>$type,
                'taille'=>$taille,
                'secteur'=>$secteur,

                ));
        }
        else
        {
            return $reponse->setData(array('status'=>'notexist'));

        }

    }

    public function EnregistrerSuiviAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $etab = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_id'));
        $etab->setSuivicommercial($request->get('_Suivi'));
        $em->flush();
        return $this->redirect($_SERVER['HTTP_REFERER']);


    }

    public function chercherContactAction($id){


        $reponse = new JsonResponse();
        //$contact = $this->getDoctrine()->getRepository('GenericBundle:ContactSociete')->findOneBy($id);
        $contact = $this->getDoctrine()->getRepository('GenericBundle:ContactSociete')->find($id);

        return $reponse->setData(array(
            'nom'=>$contact->getNom(),
            'prenom'=>$contact->getPrenom(),
            'fonction'=>$contact->getFonction(),
            'tel'=>$contact->getTelephone(),
            'mail'=>$contact->getMail(),



        ));


    }



}
