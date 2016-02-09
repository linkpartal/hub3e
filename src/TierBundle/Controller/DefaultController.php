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
    public function checkExistAction($siren)
    {

        $em = $this->getDoctrine()->getManager();
        $reponse = new JsonResponse();

        $tier = $em->getRepository('GenericBundle:Tier')->findOneBy(array('siren'=>$siren));


        if ($tier) {
            return $reponse->setData(array('status'=>'exist'));
        }

        return $reponse->setData(array('status'=>'success'));

    }

    public function tieraddedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tier = new Tier();
        $tier->setSiren($request->get('_SIREN'));
        $tier->setRaisonsoc($request->get('_RaisonSoc'));
        $tier->setEcole(intval($request->get('_Ecole')));
        if($_FILES && $_FILES['_Logo']['size'] >0)
        {
            $tier->setLogo(file_get_contents($_FILES['_Logo']['tmp_name']));
        }
        if($_FILES && $_FILES['_image']['size'] >0)
        {
            $tier->setFondecran(file_get_contents($_FILES['_image']['tmp_name']));
        }
        $em->persist($tier);
        if($this->get('security.token_storage')->getToken()->getUser()->hasRole('ROLE_ADMINECOLE'))
        {
            $tier->addTier1($this->get('security.token_storage')->getToken()->getUser()->getTier());
            $this->get('security.token_storage')->getToken()->getUser()->getTier()->addTier1($tier);
            $em->flush();
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
            $etablissement->setResponsable($request->get('_Resp')[$i]);
            $etablissement->setTelResponsable($request->get('_TelResp')[$i]);
            $etablissement->setMailResponsable($request->get('_MailResp')[$i]);
            $etablissement->setSite($request->get('_Site')[$i]);
            $etablissement->setTier($tier);

            $em->persist($etablissement);
            $em->flush();

            $admins = $this->getDoctrine()->getRepository('GenericBundle:User')->findByRoles(array('ROLE_SUPER_ADMIN','ROLE_ADMINECOLE','ROLE_ADMINSOC'));

            foreach($admins as $admin){
                $notif = new Notification();
                $notif->setEntite($etablissement->getId());
                if($etablissement->getTier()->getEcole() && $admin.$this->isGranted('ROLE_ADMINSOC'))
                {
                    $notif->setType('Ecole');
                    $notif->setUser($admin);
                    $em->persist($notif);
                    $em->flush();
                }
                if(!$etablissement->getTier()->getEcole() && $admin.$this->isGranted('ROLE_ADMINECOLE')){
                    $notif->setType('Societe');
                    $notif->setUser($admin);
                    $em->persist($notif);
                    $em->flush();
                }

            }


        }

        $em->flush();

        if($this->get('security.token_storage')->getToken()->getUser()->hasRole('ROLE_SUPER_ADMIN'))
        {
            return $this->redirect($this->generateUrl('metier_user_admin'));
        }
        elseif($this->get('security.token_storage')->getToken()->getUser()->hasRole('ROLE_ADMINECOLE'))
        {
            return $this->redirect($this->generateUrl('ecole_admin',array('ecole'=>$this->get('security.token_storage')->getToken()->getUser()->getTier()->getRaisonsoc())));
        }


    }

    public function affichageAction($id)
    {
        // Utilisateur actuellement connecté.
        $user = $this->get('security.token_storage')->getToken()->getUser();
        // LicenceDef pour peupler le select de l'instanciation de la licence
        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        // Recuperation des differents QCM
        $qcmstest = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->findBy(array('affinite'=>false));
        $qcmsaffinite = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->findBy(array('affinite'=>true));
        // Etablissement à afficher
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($id);

        // séparer les QCMs affinité deja associer à l'etablissement des autres
        $QcmNotEtab = array();
        foreach($qcmsaffinite as $item)
        {
            if(!in_array ($item,$etablissement->getQcmdef()->toArray()))
            {
                array_push($QcmNotEtab,$item);
            }
        }

        // Tout les tuteurs existant
        $userMiss = $this->getDoctrine()->getRepository('GenericBundle:User')->findByRole('ROLE_TUTEUR');
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

        // chargement des images
        if($etablissement->getTier()->getLogo())
        {
            $etablissement->getTier()->setLogo(base64_encode(stream_get_contents($etablissement->getTier()->getLogo())));
        }
        if($etablissement->getTier()->getFondecran())
        {
            $etablissement->getTier()->setFondecran(base64_encode(stream_get_contents($etablissement->getTier()->getFondecran())));
        }

        // Recup des licences associer au tier de l'etablissement.
        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$etablissement->getTier() ));


        // les formation de l'etablissement
        $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findBy(array('etablissement'=>$etablissement ));

        // Les utilisateurs de l'etablissement et du tier auquel il est lié
        $users = array();
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('tier'=>$etablissement->getTier() )));
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$etablissement )));

        // les tiers pour peuplé l'association d'ecole
        $tiers = $this->getDoctrine()->getRepository('GenericBundle:Tier')->findAll();

        //$licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$etablissement->getTier(),'suspendu'=>false ));

        // missions non suspendu
        $missions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('suspendu'=>false),array('date' => 'DESC'));

        return $this->render('TierBundle::iFrameContent.html.twig',array('licencedef'=>$licencedef,'etablissement'=>$etablissement,'tiers'=>$tiers,'users'=>$users,'formations'=>$formation,
            'libs'=>$licences, 'missions'=>$missions ,'usermis'=>$userMiss,'QCMS'=>$qcmstest,'QCMSNOTETAB'=>$QcmNotEtab));
    }

    public function supprimeretabAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $etab = $em->getRepository('GenericBundle:Etablissement')->find($id);

        if(!$etab)
        {
            throw new Exception('Aucune école ne posséde l\'id ' . $id);
        }

        $etab->setSuspendu(true);
        $em->flush();
        return $this->render('AdminBundle:Admin:iFrameContent.html.twig');
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

        return $this->forward('TierBundle:Default:affichage',array('id'=>$id));
    }

    public function modifierAction($id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $modeles = $this->getDoctrine()->getRepository('GenericBundle:Modele')->findBy(array('user'=>$user));
        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($id);

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

        if($etablissement->getTier()->getLogo())
        {
            $etablissement->getTier()->setLogo(base64_encode(stream_get_contents($etablissement->getTier()->getLogo())));
        }
        if($etablissement->getTier()->getFondecran())
        {
            $etablissement->getTier()->setFondecran(base64_encode(stream_get_contents($etablissement->getTier()->getFondecran())));
        }
        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$etablissement->getTier() ));




        $formation = array();

        $formation = array_merge($formation,$this->getDoctrine()->getRepository('GenericBundle:Formation')->findBy(array('etablissement'=>$etablissement )));


        $users = array();
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('tier'=>$etablissement->getTier() )));
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$etablissement )));
        $tiers = $this->getDoctrine()->getRepository('GenericBundle:Tier')->findAll();
        return $this->render('TierBundle::modifierEtablissement.html.twig',array('licencedef'=>$licencedef,'etablissement'=>$etablissement,
            'libs'=>$licences,'tiers'=>$tiers,'users'=>$users,'modeles'=>$modeles,'formations'=>$formation));
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
        $etablissement->setResponsable($request->get('_Resp'));
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
        $serializer = SerializerBuilder::create()->build();
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
        $tier = $em->getRepository('GenericBundle:Tier')->findOneBy(array('siren'=>$request->get('_SIREN')));

        $etablissements = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findBy(array('tier'=>$tier));
        return $this->render('TierBundle::SocietExist.html.twig',array('etablissements'=>$etablissements,
            'sirets'=>$request->get('_SIRET'),
            'adresses'=>$request->get('_Adresse'),
            'codeps'=>$request->get('_CodeP'),
            'tels'=>$request->get('_Tel'),
            'faxs'=>$request->get('_Fax'),
            'villes'=>$request->get('_Ville'),
            'resps'=>$request->get('_Resp'),
            'telresps'=>$request->get('_TelResp'),
            'mailresps'=>$request->get('_MailResp'),
            'sites'=>$request->get('_Site')));
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


}
