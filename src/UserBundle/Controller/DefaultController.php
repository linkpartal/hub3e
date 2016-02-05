<?php

namespace UserBundle\Controller;

use Ddeboer\DataImport\Reader\ExcelReader;
use GenericBundle\Entity\ImportCandidat;
use GenericBundle\Entity\Mission;
use GenericBundle\Entity\User;
use GenericBundle\Entity\Etablissement;
use GenericBundle\Entity\Tier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use GenericBundle\Entity\Notification;
use Ddeboer\DataImport\Reader\CsvReader;

class DefaultController extends Controller
{
    public function affichageUserAction($id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        $userid = $this->getDoctrine()->getRepository('GenericBundle:User')->find($id);
        $type = 'Utilisateur';
        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findOneBy(array('user'=>$user,'entite'=>$userid->getId(),'type'=>$type));
        if($notifications)
        {
            $this->getDoctrine()->getEntityManager()->remove($notifications);
            $this->getDoctrine()->getEntityManager()->flush();
        }


        return $this->render('UserBundle:Gestion:iFrameContentUser.html.twig',array('licencedef'=>$licencedef,'User'=>$userid
        ));
    }

    public function UserAddedAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $newuser = $userManager->createUser();
        $newuser->setUsername($request->get('_Username'));
        $newuser->setEmail($request->get('_mail'));
        $newuser->addRole($request->get('_role'));
        $newuser->setCivilite($request->get('civilite'));
        $newuser->setTelephone($request->get('_Tel'));
        $newuser->setPrenom($request->get('_Prenom'));
        $newuser->setNom($request->get('_Nom'));

        //generate a password
        $tokenGenerator = $this->get('fos_user.util.token_generator');
        $password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars
        $hash =  $this->get('security.password_encoder')->encodePassword($newuser, $password);
        $newuser->setPassword($hash);


        if($request->get('_role')=='ROLE_ADMINSOC' || $request->get('_role')=='ROLE_ADMINECOLE')
        {
            $etab = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_id'));
            $newuser->setTier($etab->getTier());
        }
        if($request->get('_role')=='ROLE_RECRUTE' || $request->get('_role')=='ROLE_TUTEUR')
        {
            $etab = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_id'));
            $newuser->setEtablissement($etab);
        }


        $em = $this->getDoctrine()->getManager();
        $em->persist($newuser);
        $em->flush();

        $superadmins = $this->getDoctrine()->getRepository('GenericBundle:User')->findByRole('ROLE_SUPER_ADMIN');
        $usercon = $this->get('security.token_storage')->getToken()->getUser();
        $superadmins = array_merge($superadmins, $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('tier'=>$usercon->getTier())));

        foreach($superadmins as $admin){
            $notif = new Notification();
            $notif->setEntite($newuser->getId());
            $notif->setType('Utilisateur');
            $notif->setUser($admin);
            $em->persist($notif);
            $em->flush();
        }

        //send password
        if($usercon->getTier())
        {
            if($this->get('templating')->exists('GenericBundle:Mail/templates:'.$usercon->getTier()->getSiren().'_NewUser.html.twig'))
            {
                $modele = 'GenericBundle:Mail/templates:'.$usercon->getTier()->getSiren().'_NewUser.html.twig';
            }
            else{
                $modele = 'GenericBundle:Mail:NewUser.html.twig';
            }
        }
        elseif($usercon->getEtablissement())
        {
            if($this->get('templating')->exists('GenericBundle:Mail/templates:'.$usercon->getEtablissement()->getTier()->getSiren().'_NewUser.html.twig'))
            {
                $modele = 'GenericBundle:Mail/templates:'.$usercon->getEtablissement()->getTier()->getSiren().'_NewUser.html.twig';
            }
            else{
                $modele = 'GenericBundle:Mail:NewUser.html.twig';
            }
        }
        else{
            $modele = 'GenericBundle:Mail:NewUser.html.twig';
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Email')
            ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
            ->setTo($request->get('_mail'))
            ->setBody($this->renderView($modele,array('username'=>$request->get('_Username'), 'password'=>$password))
                ,'text/html'
            );
        $this->get('mailer')->send($message);

        if($request->get('_id') and $usercon->hasRole('ROLE_SUPER_ADMIN'))
        {
            return $this->redirect($this->generateUrl('metier_user_affiche',array('id'=>$request->get('_id'))));
        }
        elseif($usercon->hasRole('ROLE_SUPER_ADMIN'))
        {
            return $this->redirect($this->generateUrl('metier_user_admin'));
        }
        elseif($request->get('_id') and $usercon->hasRole('ROLE_ADMINECOLE'))
        {
            return $this->redirect($this->generateUrl('ecole_admin_affiche',array('id'=>$request->get('_id'))));
        }
        else{
            throw new Exception('ERROR');
        }
    }

    public function expiredAction($id){
        $utilis = $this->getDoctrine()->getRepository('GenericBundle:User')->find($id);
        $reponse = new JsonResponse();
        if($utilis->isExpired())
        {
            $utilis->setExpired(false);
            $reponse->setData(array('succes'=>'0'));
        }
        else{
            $utilis->setExpired(true);
            $reponse->setData(array('succes'=>'1'));
        }
        $this->getDoctrine()->getEntityManager()->flush();

        return $reponse;
    }

    public function modifierAction($id)
    {
        $userid = $this->getDoctrine()->getRepository('GenericBundle:User')->find($id);
        return $this->render('UserBundle:Gestion:modifierUtilisateur.html.twig',array('user'=>$userid));
    }

    public function userModifAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('GenericBundle:User')->findOneBy(array('id'=>$request->get('_ID')));

        $user->setCivilite($request->get('_Civilite'));
        $user->setNom($request->get('_Nom'));
        $user->setPrenom($request->get('_Prenom'));
        $user->setTelephone($request->get('_Tel'));
        $user->setUsername($request->get('_Username'));
        $user->setEmail($request->get('_Mail'));

        $em->flush();

        return $this->forward('UserBundle:Default:affichageUser',array('id'=>$request->get('_ID')));
    }

    public function supprimeruserAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('GenericBundle:User')->find($id);

        if(!$user)
        {
            throw new Exception('Aucun utilisateur ne posséde l\'id ' . $id);
        }


        $em->remove($user);
        $em->flush();
        $reponse = new JsonResponse();
        if($this->get('security.token_storage')->getToken()->getUser()->hasRole('ROLE_SUPER_ADMIN'))
        {
            return $reponse->setData(array('Succes'=>$this->generateUrl('metier_user_admin')));
        }
        elseif($this->get('security.token_storage')->getToken()->getUser()->hasRole('ROLE_ADMINECOLE'))
        {
            return $reponse->setData(array('Succes'=>$this->generateUrl('ecole_admin',array('ecole'=>$this->get('security.token_storage')->getToken()->getUser()->getTier()->getRaisonsoc()))));
        }

    }

    public function importAction(Request $request)
    {
        if($request->get('Import')==0)
        {
            $this->ImportApprenant($request,$_FILES['_CSV']['tmp_name']);
            return $this->redirect($this->generateUrl('afficher_import'));
        }
        elseif($request->get('Import')==1)
        {
            $this->ImportMissions($_FILES['_CSV']['tmp_name']);
            return $this->redirect($this->generateUrl('afficher_import'));
        }
        elseif($request->get('Import')==2)
        {
            $this->ImportMissions($_FILES['_CSV']['tmp_name']);
            return $this->redirect($this->generateUrl('ecole_admin',array('ecole'=>$this->get('security.token_storage')->getToken()->getUser()->getTier()->getRaisonsoc())));
        }

    }

    private function ImportApprenant(Request $request,$uploadedfile)
    {
        $file = new \SplFileObject($uploadedfile);
        $reader = new ExcelReader($file);
        $rowinserted = array();
        $jump = 0;
        $em = $this->getDoctrine()->getEntityManager();
        foreach ($reader as $row) {
            if ($jump++ < 2 || ('' == $row[1] and '' == $row[2] and '' == $row[3] and '' == $row[4])) {
                continue;
            }
            else {
                $erreur = null;
                if(in_array($row,$rowinserted))
                {
                    $erreur = 'Duplicata dans le fichier';
                }
                if (!$erreur) {
                    $databaseduplica = $em->getRepository('GenericBundle:User')->findOneBy(array('civilite' =>mb_convert_encoding($row[1],'UTF-8','auto')  , 'nom' => mb_convert_encoding($row[2],'UTF-8','auto'), 'prenom' => mb_convert_encoding($row[3],'UTF-8','auto')));
                    if ($databaseduplica) {
                        $erreur = 'Duplicata dans la base de données';
                    }
                }

                if (!$erreur) {
                    $apprenant = new User();
                    $apprenant->setCivilite(mb_convert_encoding($row[1],'UTF-8','auto'));
                    $apprenant->setNom(mb_convert_encoding($row[2],'UTF-8','auto'));
                    $apprenant->setPrenom(mb_convert_encoding($row[3],'UTF-8','auto'));
                    $apprenant->setTelephone(mb_convert_encoding($row[6],'UTF-8','auto'));
                    $apprenant->setEmail(mb_convert_encoding($row[7],'UTF-8','auto'));
                    $apprenant->setUsername(mb_convert_encoding($row[3][0],'UTF-8','auto') . '' . mb_convert_encoding($row[2],'UTF-8','auto'));
                    $apprenant->addRole('ROLE_APPRENANT');
                    $etablissement = $em->getRepository('GenericBundle:Etablissement')->find($request->get('Etablissement'));
                    $apprenant->setEtablissement($etablissement);
                    $apprenant->setPassword('import_passif');
                    $em->persist($apprenant);
                    $em->flush();
                    array_push($rowinserted,$row);
                    $superadmins = $this->getDoctrine()->getRepository('GenericBundle:User')->findByRole('ROLE_SUPER_ADMIN');
                    $usercon = $this->get('security.token_storage')->getToken()->getUser();
                    $superadmins = array_merge($superadmins, $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('tier' => $usercon->getTier())));

                    foreach ($superadmins as $admin) {
                        $notif = new Notification();
                        $notif->setEntite($apprenant->getId());
                        $notif->setType('Utilisateur');
                        $notif->setUser($admin);
                        $em->persist($notif);
                        $em->flush();
                    }

                    $em->flush();
                }
                else
                {
                    $candidat = new ImportCandidat();
                    $candidat->setCivilite(mb_convert_encoding($row[1],'UTF-8','auto'));
                    $candidat->setNom(mb_convert_encoding($row[2],'UTF-8','auto'));
                    $candidat->setPrenom(mb_convert_encoding($row[3],'UTF-8','auto'));
                    $candidat->setDateNaissance(mb_convert_encoding($row[4],'UTF-8','auto'));
                    $candidat->setCPNaissance(mb_convert_encoding($row[5],'UTF-8','auto'));
                    $candidat->setTelephone(mb_convert_encoding($row[6],'UTF-8','auto'));
                    $candidat->setEmail(mb_convert_encoding($row[7],'UTF-8','auto'));
                    $candidat->setAdresse(mb_convert_encoding($row[8],'UTF-8','auto'));
                    $candidat->setCp(mb_convert_encoding($row[9],'UTF-8','auto'));
                    $etablissement = $em->getRepository('GenericBundle:Etablissement')->find($request->get('Etablissement'));
                    $candidat->setEtablissement($etablissement);
                    $candidat->setUser($this->get('security.token_storage')->getToken()->getUser());
                    if (mb_convert_encoding($row[13],'UTF-8','auto') == 'oui') {
                        $candidat->setPermis(true);
                    } elseif (mb_convert_encoding($row[13],'UTF-8','auto') == 'non') {
                        $candidat->setPermis(false);
                    }
                    if (mb_convert_encoding($row[14],'UTF-8','auto') == 'oui') {
                        $candidat->setVehicule(true);
                    } elseif (mb_convert_encoding($row[14],'UTF-8','auto') == 'non') {
                        $candidat->setVehicule(false);
                    }
                    $candidat->setErreur($erreur);
                    $em->persist($candidat);
                    $em->flush();
                }
            }
        }
    }

    private function ImportMissions($uploadedfile)
    {
        $file = new \SplFileObject($uploadedfile);
        $reader = new CsvReader($file);
        $jump = 0;
        $em = $this->getDoctrine()->getEntityManager();
        foreach ($reader as $row) {
            if($jump++<1 || (''==$row[1] and ''==$row[2] and '' == $row[3] and '' == $row[4])){
                continue;
            }
            else{

                $siren = substr(mb_convert_encoding($row[1],'UTF-8','auto'),0,9);
                $tier = $em->getRepository('GenericBundle:Tier')->findOneBy(array('siren'=>$siren));
                if(!$tier)
                {
                    $newtier = new Tier();
                    $newtier->setSiren($siren);
                    $newtier->setRaisonsoc(mb_convert_encoding($row[3],'UTF-8','auto'));
                    $newtier->setActivite(mb_convert_encoding($row[4],'UTF-8','auto'));
                    $newtier->setEcole(false);
                    $em->persist($newtier);
                    $em->flush();
                    $tier = $newtier;
                }
                $siege = $em->getRepository('GenericBundle:Etablissement')->findOneBy(array('siret'=>mb_convert_encoding($row[1],'UTF-8','auto')));
                if(!$siege)
                {
                    $newsiege = new Etablissement();
                    $newsiege->setSiret(mb_convert_encoding($row[1],'UTF-8','auto'));
                    $newsiege->setAdresse(mb_convert_encoding($row[5],'UTF-8','auto'));
                    $newsiege->setCodepostal(mb_convert_encoding($row[6],'UTF-8','auto'));
                    $newsiege->setVille(mb_convert_encoding($row[7],'UTF-8','auto'));
                    $newsiege->setTier($tier);
                    $em->persist($newsiege);
                    $em->flush();
                    $siege = $newsiege;
                }
                $etab_mission = $em->getRepository('GenericBundle:Etablissement')->findOneBy(array('siret'=>mb_convert_encoding($row[2],'UTF-8','auto')));
                if(!$etab_mission)
                {
                    $newetab = new Etablissement();
                    $newetab->setSiret(mb_convert_encoding($row[2],'UTF-8','auto'));
                    $newetab->setAdresse(mb_convert_encoding($row[8],'UTF-8','auto'));
                    $newetab->setCodepostal(mb_convert_encoding($row[9],'UTF-8','auto'));
                    $newetab->setVille(mb_convert_encoding($row[10],'UTF-8','auto'));
                    $newetab->setTier($tier);
                    $em->persist($newetab);
                    $em->flush();
                    $etab_mission = $newetab;
                }

                $mission = new Mission();
                $mission->setEtat('À pourvoir');

                $mission->setTypecontrat(mb_convert_encoding($row[16],'UTF-8','auto'));

                $mission->setIntitule(mb_convert_encoding($row[18],'UTF-8','auto'));
                $mission->setDescriptif(mb_convert_encoding($row[19],'UTF-8','auto'));
                $mission->setDomaine(mb_convert_encoding($row[20],'UTF-8','auto'));
                $mission->setNomcontact(mb_convert_encoding($row[11],'UTF-8','auto'));
                $mission->setPrenomcontact(mb_convert_encoding($row[12],'UTF-8','auto'));
                $mission->setFonctioncontact(mb_convert_encoding($row[13],'UTF-8','auto'));
                $mission->setTelcontact(mb_convert_encoding($row[14],'UTF-8','auto'));
                $mission->setEmailcontact(mb_convert_encoding($row[15],'UTF-8','auto'));
                $mission->setEtablissement($etab_mission);
                if(!$row[0]=='' and !mb_convert_encoding($row[0],'UTF-8','auto')=='jj/mm/aaaa')
                {
                    $date=date_create_from_format('dd/mm/YYYY',mb_convert_encoding($row[0],'UTF-8','auto'));
                    $mission->setDate($date);
                }

                $em->persist($mission);
                $em->flush();
                if($row[17]=='')
                {
                    $mission->genererCode();
                }
                else{
                    $mission->setCodemission(mb_convert_encoding($row[17],'UTF-8','auto'));
                }

                $em->flush();

                $superadmins = $this->getDoctrine()->getRepository('GenericBundle:User')->findByRole('ROLE_SUPER_ADMIN');
                $usercon = $this->get('security.token_storage')->getToken()->getUser();
                $superadmins = array_merge($superadmins, $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('tier'=>$usercon->getTier())));

                foreach($superadmins as $admin){
                    $notif = new Notification();
                    $notif->setEntite($mission->getId());
                    $notif->setType('Mission');
                    $notif->setUser($admin);
                    $em->persist($notif);
                    $em->flush();
                }
            }
        }
    }

    public function afficherImportsAction()
    {
       return $this->render('UserBundle:Gestion:Import.html.twig',
           array('imports'=>$this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->findBy(array('user'=>$this->get('security.token_storage')->getToken()->getUser()))));
    }

    public function supprimerImportsAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $import = $em->getRepository('GenericBundle:ImportCandidat')->find($id);
        $em->remove($import);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('Succes'=>'1'));
    }

    public function afficherDuplicaAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $import = $em->getRepository('GenericBundle:ImportCandidat')->findBy($id);
        //$users = $em->getRepository('GenericBundle:User')->findBy()
            var_dump($import);die;
    }

}
