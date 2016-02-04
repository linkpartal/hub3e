<?php

namespace AdminBundle\Controller;

use GenericBundle\Entity\Etablissement;
use GenericBundle\Entity\Notification;
use GenericBundle\Entity\Tier;
use GenericBundle\Entity\Mission;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;



class EcoleController extends Controller
{

    public function checkExistAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $reponse = new JsonResponse();
        $tier = $em->getRepository('GenericBundle:Tier')->findOneBy(array('siren'=>$request->get('_SIREN')));

        if ($tier) {
            return $reponse->setData(array('status'=>$request->get('_SIREN') . ' appartient déjà à un tier existant.'));
        }


        for($i = 0; $i< count($request->get('_SIRET'));$i++)
        {

            $etablissement = $em->getRepository('GenericBundle:Etablissement')->findOneBy(array('siret'=>$request->get('_SIRET')[$i]));

            if($etablissement)
            {
                return $reponse->setData(array('status'=>$request->get('_SIRET')[$i] . ' appartient déjà à un établissement existant.'));
            }

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

        return $this->redirect($this->generateUrl('metier_user_admin'));

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

    public function suppMissionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $lic = $em->getRepository('GenericBundle:Mission')->find($id);
        $lic->setSuspendu(true);
        $em->flush();
        $reponse = new JsonResponse();
        return $reponse->setData(array('Status'=>'Mission correctement supprimer'));
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

        return $this->forward('AdminBundle:Default:affichage',array('id'=>$id));
    }

    public function modifierAction($id)
    {
        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($id);
        if($etablissement->getTier()->getLogo())
        {
            $etablissement->getTier()->setLogo(base64_encode(stream_get_contents($etablissement->getTier()->getLogo())));
        }
        if($etablissement->getTier()->getFondecran())
        {
            $etablissement->getTier()->setFondecran(base64_encode(stream_get_contents($etablissement->getTier()->getFondecran())));
        }
        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$etablissement->getTier() ));
        $users = array();
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('tier'=>$etablissement->getTier() )));
        $users = array_merge($users,$this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$etablissement )));
        $tiers = $this->getDoctrine()->getRepository('GenericBundle:Tier')->findAll();
        return $this->render('AdminBundle:Admin:modifierEtablissement.html.twig',array('licencedef'=>$licencedef,'etablissement'=>$etablissement,
            'libs'=>$licences,'tiers'=>$tiers,'users'=>$users));
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

        return $this->render('AdminBundle:Admin:iFrameContent.html.twig');
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

    public function addMissionAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $mission = new Mission();
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_idetab'));
        $mission->setEtablissement($etablissement);

        $mission->setDescriptif($request->get('_Descriptif'));
        $mission->setProfil($request->get('_Profil'));
        $mission->setEtat($request->get('_Etat'));
        $mission->setTypecontrat($request->get('_TypeContrat'));
        $mission->setDomaine($request->get('_Domaine'));
        $date = new \DateTime();
        $mission->setDate($date);
        $mission->setRemuneration($request->get('_Remuneration'));
        $mission->setHoraire($request->get('_Horaire'));

        $mission->setDatedebut(date_create($request->get('_Datedebut')) );
        $mission->setDatefin(date_create($request->get('_Datefin')) );
        $mission->setNomcontact($request->get('_NomContact'));
        $mission->setPrenomContact($request->get('_PrenomContact'));
        $mission->setFonctionContact($request->get('_FonctionContact'));
        $mission->setTelContact($request->get('_TelContact'));
        $mission->setEmailContact($request->get('_EmailContact'));
        $mission->setIntitule($request->get('_Intitule'));
        $mission->setEmploi($request->get('_Emploi'));
        //$mission->setFormation($request->get('_idform'));

        $us = $this->getDoctrine()->getRepository('GenericBundle:User')->find($request->get('userselect'));
        $mission->setTuteur($us);

       // var_dump($mission);die;
        /*  $user = $this->getDoctrine()->getRepository('GenericBundle:User')->find($request->get('_iduse'));
          $mission->setTuteur($user);*/
       /* $qb=$em->createQueryBuilder();
        $qb->select('mission')
           ->from('EcoleBundle:Default:affichage','mission')
            ->orderBy('mission.date','ASC');
        $query = $qb->getQuery();
        $mission = $query->getResult();*/

     //$mission=$this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('date', 'DESC'));


        $em->persist($mission);
        $em->flush();

        $mission->genererCode();
        $em->flush();



        return $this->forward('EcoleBundle:Default:affichage',array('id'=>$etablissement->getId()));


    }

    public function affichageMissionAction($id)
    {
        $mission = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->find($id);
        return $this->render('EcoleBundle:Adminecole:iFrameContent.html.twig',array('mission'=>$mission));
    }
}
