<?php

namespace UserBundle\Controller;

use Ddeboer\DataImport\Reader\ExcelReader;
use GenericBundle\Entity\Candidature;
use GenericBundle\Entity\Diplome;
use GenericBundle\Entity\Document;
use GenericBundle\Entity\Experience;
use GenericBundle\Entity\Formation;
use GenericBundle\Entity\Hobbies;
use GenericBundle\Entity\ImportCandidat;
use GenericBundle\Entity\Infocomplementaire;
use GenericBundle\Entity\Langue;
use GenericBundle\Entity\Mission;
use GenericBundle\Entity\Parents;
use GenericBundle\Entity\Recommandation;
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

        $userid = $this->getDoctrine()->getRepository('GenericBundle:User')->find($id);
        $info = $userid->getInfo();
        $Parents = $this->getDoctrine()->getRepository('GenericBundle:Parents')->findAll();

        $Langue = $this->getDoctrine()->getRepository('GenericBundle:Langue')->findAll();

        $Experience = $this->getDoctrine()->getRepository('GenericBundle:Experience')->findAll();
        $Recommandation = $this->getDoctrine()->getRepository('GenericBundle:Recommandation')->findAll();
        $Diplome = $this->getDoctrine()->getRepository('GenericBundle:Diplome')->findAll();
        $Document = $this->getDoctrine()->getRepository('GenericBundle:Document')->findAll();
        $type = 'Utilisateur';
        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findOneBy(array('user'=>$user,'entite'=>$userid->getId(),'type'=>$type));
        if($notifications)
        {
            $this->getDoctrine()->getEntityManager()->remove($notifications);
            $this->getDoctrine()->getEntityManager()->flush();
        }
        $candidatures = $this->getDoctrine()->getRepository('GenericBundle:Candidature')->findBy(array('user'=>$userid));

        if($userid->hasRole('ROLE_APPRENANT'))
        {
            $questions =array();
            $reponses = array();
            foreach($userid->getEtablissement()->getQcmdef() as $key => $qcm){

                $questions[$key] = $this->getDoctrine()->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef'=>$qcm));
                usort($questions[$key],array('\GenericBundle\Entity\Questiondef','sort_questions_by_order'));
                foreach ( $questions[$key] as $keyqst => $qst)
                {
                    $reps = $this->getDoctrine()->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef'=>$qst));
                    usort($reps,array('\GenericBundle\Entity\Reponsedef','sort_reponses_by_order'));
                    $reponses[$key][$keyqst] = $reps;
                }

            }
            return $this->render('UserBundle:Gestion:iFrameContentUser.html.twig',array('User'=>$userid,
                'Infocomplementaire'=>$info,'Parents'=>$Parents,'Experience'=>$Experience,'Recommandation'=>$Recommandation,'Diplome'=>$Diplome,'Document'=>$Document,'Langue'=>$Langue,
                'candidatures'=>$candidatures,'QCMs'=>$userid->getEtablissement()->getQcmdef() ,'Questions'=>$questions,'reponses'=>$reponses));
        }
        else{
            return $this->render('UserBundle:Gestion:iFrameContentUser.html.twig',array('User'=>$userid,
                'Infocomplementaire'=>$info,'Parents'=>$Parents,'Experience'=>$Experience,'Recommandation'=>$Recommandation,'Diplome'=>$Diplome,'Document'=>$Document,'Langue'=>$Langue,
                'candidatures'=>$candidatures));
        }


    }

    public function affichageSASAction($id){

    }

    public function affichageProfilAction()
    {
        $userid = $this->get('security.token_storage')->getToken()->getUser();

        $info = $userid->getInfo();
        $Parents = $this->getDoctrine()->getRepository('GenericBundle:Parents')->findAll();

        $Langue = $this->getDoctrine()->getRepository('GenericBundle:Langue')->findAll();

        $Experience = $this->getDoctrine()->getRepository('GenericBundle:Experience')->findAll();
        $Recommandation = $this->getDoctrine()->getRepository('GenericBundle:Recommandation')->findAll();
        $Diplome = $this->getDoctrine()->getRepository('GenericBundle:Diplome')->findAll();
        $Document = $this->getDoctrine()->getRepository('GenericBundle:Document')->findAll();

        $candidatures = $this->getDoctrine()->getRepository('GenericBundle:Candidature')->findBy(array('user'=>$userid));

        $questions =array();
        $reponses = array();
        foreach($userid->getEtablissement()->getQcmdef() as $key => $qcm){

            $questions[$key] = $this->getDoctrine()->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef'=>$qcm));
            usort($questions[$key],array('\GenericBundle\Entity\Questiondef','sort_questions_by_order'));
            foreach ( $questions[$key] as $keyqst => $qst)
            {
                $reps = $this->getDoctrine()->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef'=>$qst));
                usort($reps,array('\GenericBundle\Entity\Reponsedef','sort_reponses_by_order'));
                $reponses[$key][$keyqst] = $reps;
            }

        }
        return $this->render('UserBundle:Gestion:iFrameContentUser.html.twig',array('User'=>$userid,
            'Infocomplementaire'=>$info,'Parents'=>$Parents,'Experience'=>$Experience,'Recommandation'=>$Recommandation,'Diplome'=>$Diplome,'Document'=>$Document,'Langue'=>$Langue,
            'candidatures'=>$candidatures,'QCMs'=>$userid->getEtablissement()->getQcmdef() ,'Questions'=>$questions,'reponses'=>$reponses));
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
            return $this->redirect($this->generateUrl('affiche_etab',array('id'=>$request->get('_id'))));
        }
        elseif($usercon->hasRole('ROLE_SUPER_ADMIN'))
        {
            return $this->redirect($this->generateUrl('metier_user_admin'));
        }
        elseif($request->get('_id') and $usercon->hasRole('ROLE_ADMINECOLE'))
        {
            return $this->redirect($this->generateUrl('affiche_etab',array('id'=>$request->get('_id'))));
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
            return $this->redirect($this->generateUrl('ecole_admin',array('ecole'=>$this->get('security.token_storage')->getToken()->getUser()->getTier()->getRaisonsoc())));
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
                if(!$erreur)
                {
                    $duplicaimport = $em->getRepository('GenericBundle:ImportCandidat')->findOneBy(array('civilite' =>mb_convert_encoding($row[1],'UTF-8','auto')  , 'nom' => mb_convert_encoding($row[2],'UTF-8','auto')
                    , 'prenom' => mb_convert_encoding($row[3],'UTF-8','auto'),'user'=>$this->get('security.token_storage')->getToken()->getUser()));
                    if ($duplicaimport) {
                        $erreur = 'Import existant';
                    }
                }
                $candidat = new ImportCandidat();
                $candidat->setCivilite(mb_convert_encoding($row[1],'UTF-8','auto'));
                $candidat->setNom(mb_convert_encoding($row[2],'UTF-8','auto'));
                $candidat->setPrenom(mb_convert_encoding($row[3],'UTF-8','auto'));
                $candidat->setTelephone(mb_convert_encoding($row[6],'UTF-8','auto'));
                $candidat->setEmail(mb_convert_encoding($row[7],'UTF-8','auto'));
                $etablissement = $em->getRepository('GenericBundle:Etablissement')->find($request->get('Etablissement'));
                $candidat->setEtablissement($etablissement);
                $candidat->setUser($this->get('security.token_storage')->getToken()->getUser());
                $candidat->setErreur($erreur);


                // infocomplementaire
                $infocomp = new Infocomplementaire();
                $datephp = ($row[4] - 25569) * 86400;
                $infocomp->setDatenaissance(date_create(gmdate("d-m-Y H:i:s", $datephp)));
                $infocomp->setCPNaissance(mb_convert_encoding($row[5],'UTF-8','auto'));
                $infocomp->setAdresse(mb_convert_encoding($row[8],'UTF-8','auto'));
                $infocomp->setCp(mb_convert_encoding($row[9],'UTF-8','auto'));

                if (mb_convert_encoding($row[13],'UTF-8','auto') == 'oui') {
                    $infocomp->setPermis(true);
                } elseif (mb_convert_encoding($row[13],'UTF-8','auto') == 'non') {
                    $infocomp->setPermis(false);
                }
                if (mb_convert_encoding($row[14],'UTF-8','auto') == 'oui') {
                    $infocomp->setVehicule(true);
                } elseif (mb_convert_encoding($row[14],'UTF-8','auto') == 'non') {
                    $infocomp->setVehicule(false);
                }

                $em->persist($infocomp);
                $em->flush();
                $candidat->setInfo($infocomp);
                $em->persist($candidat);
                $em->flush();
                //candidature 1
                $formation1 = $em->getRepository('GenericBundle:Formation')->findBy(array('nom'=>mb_convert_encoding($row[17],'UTF-8','auto'),'etablissement'=>$etablissement));
                if($formation1)
                {
                    $candidature = new Candidature();
                    $candidature->setFormation($formation1);
                    $candidature->setImportcandidat($candidat);
                    $em->persist($candidature);
                    $em->flush();
                }
                //candidature 2
                $formation2 = $em->getRepository('GenericBundle:Formation')->findBy(array('nom'=>mb_convert_encoding($row[19],'UTF-8','auto'),'etablissement'=>$etablissement));
                if($formation2)
                {
                    $candidature = new Candidature();
                    $candidature->setFormation($formation2);
                    $candidature->setImportcandidat($candidat);
                    $em->persist($candidature);
                    $em->flush();
                }
                //candidature
                $formation3 = $em->getRepository('GenericBundle:Formation')->findBy(array('nom'=>mb_convert_encoding($row[21],'UTF-8','auto'),'etablissement'=>$etablissement));
                if($formation3)
                {
                    $candidature = new Candidature();
                    $candidature->setFormation($formation3);
                    $candidature->setImportcandidat($candidat);
                    $em->persist($candidature);
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
        $response = new JsonResponse();
        if($import)
        {
            $info = $import->getInfo();
            foreach($em->getRepository('GenericBundle:Experience')->findBy(array('importCandidat'=>$import)) as $experience)
            {
                $em->remove($experience);
                $em->flush();
            }
            foreach($em->getRepository('GenericBundle:Diplome')->findBy(array('importCandidat'=>$import)) as $diplome)
            {
                $em->remove($diplome);
                $em->flush();
            }
            foreach($em->getRepository('GenericBundle:Document')->findBy(array('importCandidat'=>$import)) as $document)
            {
                $em->remove($document);
                $em->flush();
            }
            foreach($em->getRepository('GenericBundle:Parents')->findBy(array('importCandidat'=>$import)) as $parents)
            {
                $em->remove($parents);
                $em->flush();
            }
            foreach($em->getRepository('GenericBundle:Recommandation')->findBy(array('importCandidat'=>$import)) as $recommandation)
            {
                $em->remove($recommandation);
                $em->flush();
            }
            $em->remove($import);
            $em->flush();
            $em->remove($info);
            $em->flush();

            return $response->setData(array('Delete'=>'1'));
        }
        else{
            return $response->setData(array('Delete'=>'0'));
        }
    }

    public function afficherDuplicaAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('GenericBundle:User')->findApprenantDuplicata($id);
        return $this->render('UserBundle:Gestion:IframeDuplicata.html.twig', array('duplicas'=>$users));
    }

    public function ImportCandidatAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $import = $em->getRepository('GenericBundle:ImportCandidat')->find($id);
        $user = $em->getRepository('GenericBundle:User')->findApprenantDuplicata($id);
        $response = new JsonResponse();

        if($user)
        {
            return $response->setData(array('Ajout'=>'0'));
        }
        else{
            //$userManager = $this->get('fos_user.user_manager');
            //$newuser = $userManager->createUser();
            $newuser = new User();
            $newuser->setCivilite($import->getCivilite());
            $newuser->setNom($import->getNom());
            $newuser->setEmail($import->getEmail());
            $newuser->setPrenom($import->getPrenom());
            $newuser->setTelephone($import->getTelephone());
            $newuser->setEtablissement($import->getEtablissement());
            $newuser->addRole('ROLE_APPRENANT');
            $newuser->setUsername($import->getPrenom().'.'.$import->getNom());

            $newuser->setInfo($import->getInfo());

            //generate a password
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars
            $hash =  $this->get('security.password_encoder')->encodePassword($newuser, $password);
            $newuser->setPassword($hash);

            $em = $this->getDoctrine()->getManager();
            $em->persist($newuser);
            $em->flush();

            $date = new \DateTime();
            $newuser->getInfo()->setDaterecup($date);
            foreach($import->getHobbies() as $hobby)
            {
                $newuser->addHobby($hobby);
            }
            foreach($import->getLangue() as $langue)
            {
                $newuser->addCulturel($langue);
            }

            foreach($em->getRepository('GenericBundle:Experience')->findBy(array('importCandidat'=>$import)) as $experience)
            {
                $experience->setUser($newuser);
                $experience->setImportCandidat(null);
            }
            foreach($em->getRepository('GenericBundle:Diplome')->findBy(array('importCandidat'=>$import)) as $diplome)
            {
                $diplome->setUser($newuser);
                $diplome->setImportCandidat(null);
            }
            foreach($em->getRepository('GenericBundle:Document')->findBy(array('importCandidat'=>$import)) as $document)
            {
                $document->setUser($newuser);
                $document->setImportCandidat(null);
            }
            foreach($em->getRepository('GenericBundle:Parents')->findBy(array('importCandidat'=>$import)) as $parents)
            {
                $parents->setUser($newuser);
                $parents->setImportCandidat(null);
            }
            foreach($em->getRepository('GenericBundle:Recommandation')->findBy(array('importCandidat'=>$import)) as $recommandation)
            {
                $recommandation->setUser($newuser);
                $recommandation->setImportCandidat(null);
            }

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
                ->setTo($import->getEmail())
                ->setBody($this->renderView($modele,array('username'=>$import->getPrenom().'.'.$import->getNom(), 'password'=>$password))
                    ,'text/html'
                );
            $this->get('mailer')->send($message);
            $em->remove($import);
            $em->flush();
            return $response->setData(array('Ajout'=>'1'));

        }




    }

    public function FusionnerAction($sas,$user){
        $em = $this->getDoctrine()->getEntityManager();
        $import = $em->getRepository('GenericBundle:ImportCandidat')->find($sas);
        $userfus = $em->getRepository('GenericBundle:User')->find($user);
        if(!$userfus->getCivilite()){
            $userfus->setCivilite($import->getCivilite());
        }
        if(!$userfus->getNom()){
            $userfus->setNom($import->getNom());
        }
        if(!$userfus->getPrenom()){
            $userfus->setPrenom($import->getPrenom());
        }
        if(!$userfus->getTelephone()){
            $userfus->setTelephone($import->getTelephone());
        }
        if(!$userfus->getInfo()->getAdresse()){
            $userfus->getInfo()->setAdresse($import->getInfo()->getAdresse());
        }
        if(!$userfus->getInfo()->getCp()){
            $userfus->getInfo()->setCp($import->getInfo()->getCp());
        }
        if(!$userfus->getInfo()->getFacebook()){
            $userfus->getInfo()->setFacebook($import->getInfo()->getFacebook());
        }
        if(!$userfus->getInfo()->getLinkedin()){
            $userfus->getInfo()->setLinkedin($import->getInfo()->getLinkedin());
        }
        if(!$userfus->getInfo()->getViadeo()){
            $userfus->getInfo()->setViadeo($import->getInfo()->getViadeo());
        }
        if(!$userfus->getInfo()->getMobilite()){
            $userfus->getInfo()->setMobilite($import->getInfo()->getMobilite());
        }
        if(!$userfus->getInfo()->getFratrie()){
            $userfus->getInfo()->setFratrie($import->getInfo()->getFratrie());
        }
        if(!$userfus->getInfo()->getPermis()){
            $userfus->getInfo()->setPermis($import->getInfo()->getPermis());
        }
        if(!$userfus->getInfo()->getVehicule()){
            $userfus->getInfo()->setVehicule($import->getInfo()->getVehicule());
        }
        $em->flush();
        foreach($import->getLangue() as $langue)
        {
            if(!in_array($langue,$userfus->getLangue()->toArray()))
            {
                $userfus->addLangue($langue);
                $em->flush();
            }
        }
        foreach($import->getHobbies() as $hobbie)
        {
            if(!in_array($hobbie,$userfus->getHobbies()->toArray()))
            {
                $userfus->addHobby($hobbie);
                $em->flush();
            }
        }
        foreach($import->getInfo()->getVillesFranceFreeVille() as $ville)
        {
            if(!in_array($ville,$userfus->getInfo()->getVillesFranceFreeVille()->toArray()))
            {
                $userfus->getInfo()->addVillesFranceFreeVille($ville);
                $em->flush();
            }
        }

        foreach($em->getRepository('GenericBundle:Experience')->findBy(array('importCandidat'=>$import)) as $experience)
        {
            foreach($em->getRepository('GenericBundle:Experience')->findBy(array('user'=>$userfus)) as $experienceuser)
            {
                if($experience->isEqual($experienceuser))
                {
                    $em->remove($experience);
                    $em->flush();
                }
                else{
                    $experience->setUser($userfus);
                    $experience->setImportCandidat(null);
                }
            }
        }
        foreach($em->getRepository('GenericBundle:Diplome')->findBy(array('importCandidat'=>$import)) as $diplome)
        {
            foreach($em->getRepository('GenericBundle:Diplome')->findBy(array('user'=>$userfus)) as $diplomeuser)
            {
                if($diplome->isEqual($diplomeuser))
                {
                    $em->remove($diplome);
                    $em->flush();
                }
                else{
                    $diplome->setUser($userfus);
                    $diplome->setImportCandidat(null);
                }
            }
        }
        foreach($em->getRepository('GenericBundle:Document')->findBy(array('importCandidat'=>$import)) as $document)
        {
            foreach($em->getRepository('GenericBundle:Document')->findBy(array('user'=>$userfus)) as $documentuser)
            {
                if($document->isEqual($documentuser))
                {
                    $em->remove($document);
                    $em->flush();
                }
                else{
                    $document->setUser($userfus);
                    $document->setImportCandidat(null);
                }
            }
        }
        foreach($em->getRepository('GenericBundle:Parents')->findBy(array('importCandidat'=>$import)) as $parents)
        {
            foreach($em->getRepository('GenericBundle:Parents')->findBy(array('user'=>$userfus)) as $parentsuser)
            {
                if($parents->isEqual($parentsuser))
                {
                    $em->remove($parents);
                    $em->flush();
                }
                else{
                    $parents->setUser($userfus);
                    $parents->setImportCandidat(null);
                }
            }
        }
        foreach($em->getRepository('GenericBundle:Recommandation')->findBy(array('importCandidat'=>$import)) as $recommandation)
        {
            foreach($em->getRepository('GenericBundle:Recommandation')->findBy(array('user'=>$userfus)) as $recommandationuser)
            {
                if($recommandation->isEqual($recommandationuser))
                {
                    $em->remove($recommandation);
                    $em->flush();
                }
                else{
                    $recommandation->setUser($userfus);
                    $recommandation->setImportCandidat(null);
                }
            }
        }

        $info = $import->getInfo();
        $em->remove($import);
        $em->remove($info);
        $em->flush();

        return $this->render('AdminBundle:Admin:iFrameContent.html.twig');
    }



    public function ajouterApprenantAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $InfoComp = new Infocomplementaire();
        $InfoComp->setDatenaissance(date_create($request->get('_Datenaissance')) );
        $InfoComp->setAdresse($request->get('_Adresse').' '.$request->get('_Ville'));
        $InfoComp->setCp($request->get('_Codepostal'));
        $em->persist($InfoComp);
        $em->flush();

        $apprenant = new ImportCandidat();
        $apprenant->setEtablissement($this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_idEtab')));
        $apprenant->setUser($this->get('security.token_storage')->getToken()->getUser());

        // $apprenant->setPhotos($request->get('_Photos'));

        $apprenant->setNom($request->get('_Nom'));
        $apprenant->setPrenom($request->get('_Prenom'));
        $apprenant->setCivilite($request->get('_Civilite'));
        $apprenant->setEmail($request->get('_Email'));
        $apprenant->setTelephone($request->get('_Telephone'));
        $apprenant->setInfo($InfoComp);
        $em->persist($apprenant);

        $em->flush();


        for($i = 0; $i< count($request->get('_Nomresp'));$i++) {
        $responsable = new Parents();
        $responsable->setNom($request->get('_Nomresp')[$i].' '.$request->get('_Civiliteresp')[$i]);
        $responsable->setPrenom($request->get('_Prenomresp')[$i]);
        $responsable->setAdresse($request->get('_Adresseresp')[$i].' '.$request->get('_CodePostaleresp')[$i].' '.$request->get('_Villeresp')[$i]);
        $responsable->setMetier($request->get('_Metierresp')[$i]);
        $responsable->setEmail($request->get('_Emailresp')[$i]);
        $responsable->setProfession($request->get('_Profession')[$i]);
        $responsable->setTelephone($request->get('_Telephoneresp')[$i]);

            $em->persist($responsable);
            $em->flush();
        }
       // var_dump($responsable);die;


        for($i = 0; $i< count($request->get('_Libelle'));$i++) {
        $diplome = new Diplome();
        $diplome->setLibelle($request->get('_Libelle')[$i]);
        $diplome->setObtention($request->get('_Obtention')[$i]);
        $diplome->setEcole($request->get('_Ecole')[$i]);
          $em->persist($diplome);
          $em->flush();
        }


        for($i = 0; $i< count($request->get('_Nomsociete'));$i++) {
        $experience = new Experience();
        $experience->setNomsociete($request->get('_Nomsociete')[$i]);
        $experience->setActivite($request->get('_Activite')[$i]);
        $experience->setLieu($request->get('_Lieu')[$i]);
        $experience->setPoste($request->get('_Poste')[$i]);
        $experience->setNbreannee($request->get('_Nbreannee')[$i]);
        $experience->setDescription($request->get('_Descriptionexp')[$i]);
         $em->persist($experience);
          $em->flush();
        }


        for($i = 0; $i< count($request->get('_Nomrec'));$i++) {
        $recommandation = new Recommandation();
        $recommandation->setNom($request->get('_Nomrec')[$i].' '.$request->get('_Prenomrec')[$i]);
        $recommandation->setFonction($request->get('_Fonctionrec')[$i]);
        $recommandation->setTelephone($request->get('_Telephonerec')[$i]);
        $recommandation->setEmail($request->get('_Emailrec')[$i]);
        $recommandation->setText($request->get('_Text')[$i]);
            $em->persist($recommandation);
            $em->flush();
        }

        for($i = 0; $i< count($request->get('_Langue'));$i++) {
        $langue = new Langue();
            $langue->setLangue($request->get('_Langue')[$i]);
            $langue->setNiveau($request->get('_Niveau')[$i]);
            $em->persist($langue);
            $em->flush();
        }

        if($request->get('formations'))
        {
            foreach($request->get('formations') as $idFormation){
                $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->find($idFormation);
                $candidature = new Candidature();
                $candidature->setFormation($formation);
                $candidature->setImportcandidat($apprenant);
                $em->persist($candidature);
                $em->flush();
            }
        }

        if($request->get('hobbies')) {
            foreach ($request->get('hobbies') as $idHobbies) {
                $hobby = $this->getDoctrine()->getRepository('GenericBundle:Hobbies')->find($idHobbies);
                $hobby->addImportCandidat($apprenant);
                $em->flush();
            }
        }
        for($i = 0; $i < count($request->get('_Type')); $i++)
        {
            $document = new Document();
            $document->setType($request->get('_Type')[$i]);
            $document->setExtension($_FILES['_Document']['type'][$i]);
            $document->setName($_FILES['_Document']['name'][$i]);
            $document->setTaille($_FILES['_Document']['size'][$i]);
            $document->setDocument(file_get_contents($_FILES['_Document']['tmp_name'][$i]));
            $document->setImportCandidat($apprenant);
            $em->persist($document);
            $em->flush();
        }




        // var_dump($apprenant);die;



        return $this->redirect($this->generateUrl('affiche_etab',array('id'=>$request->get('_idEtab'))));


    }
}
