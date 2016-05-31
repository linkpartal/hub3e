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
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function affichageUserAction($id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $userid = $this->getDoctrine()->getRepository('GenericBundle:User')->find($id);
        $type = 'Utilisateur';
        $notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findOneBy(array('user'=>$user,'entite'=>$userid->getId(),'type'=>$type));
        if($notifications)
        {
            $this->getDoctrine()->getEntityManager()->remove($notifications);
            $this->getDoctrine()->getEntityManager()->flush();
        }

        // chargement des images
        if($userid->getPhotos() and !is_string($userid->getPhotos()))
        {
            $userid->setPhotos(base64_encode(stream_get_contents($userid->getPhotos())));
        }
        if($userid->hasRole('ROLE_APPRENANT')){
            $info = $userid->getInfo();
            $Parents = $this->getDoctrine()->getRepository('GenericBundle:Parents')->findBy(array('user'=>$userid));

            $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findAll();

            $Experience = $this->getDoctrine()->getRepository('GenericBundle:Experience')->findBy(array('user'=>$userid));
            $Recommandation = $this->getDoctrine()->getRepository('GenericBundle:Recommandation')->findBy(array('user'=>$userid));
            $Diplome = $this->getDoctrine()->getRepository('GenericBundle:Diplome')->findBy(array('user'=>$userid));
            $Document = $this->getDoctrine()->getRepository('GenericBundle:Document')->findBy(array('user'=>$userid));
            $candidatures = $this->getDoctrine()->getRepository('GenericBundle:Candidature')->findBy(array('user'=>$userid));

            if ($userid->getEtablissement()) {
                $questions = array();
                $reponses = array();
                foreach ($userid->getEtablissement()->getQcmdef() as $key => $qcm) {

                    $questions[$key] = $this->getDoctrine()->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef' => $qcm));
                    usort($questions[$key], array('\GenericBundle\Entity\Questiondef', 'sort_questions_by_order'));
                    foreach ($questions[$key] as $keyqst => $qst) {
                        $reps = $this->getDoctrine()->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef' => $qst));
                        usort($reps, array('\GenericBundle\Entity\Reponsedef', 'sort_reponses_by_order'));
                        $reponses[$key][$keyqst] = $reps;
                    }

                }


                $questionsTest = array();
                $reponsesTest = array();
                $QCMtest = array();
                foreach ($candidatures as $cand) {
                    $QCMtest = array_merge($QCMtest, $cand->getFormation()->getQcmdef()->toArray());
                }

                $QCMtest = array_unique($QCMtest);


                $index = 0;
                foreach ($QCMtest as $qcm) {
                    $questionsTest[$index] = $this->getDoctrine()->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef' => $qcm));
                    usort($questionsTest[$index], array('\GenericBundle\Entity\Questiondef', 'sort_questions_by_order'));
                    foreach ($questionsTest[$index] as $keyqst => $qst) {
                        $reps = $this->getDoctrine()->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef' => $qst));
                        usort($reps, array('\GenericBundle\Entity\Reponsedef', 'sort_reponses_by_order'));
                        $reponsesTest[$index][$keyqst] = $reps;
                    }
                    $index++;
                }
                if($userid->getInfo()){
                    if( $userid->getCivilite() and $userid->getNom() and $userid->getPrenom() and $userid->getPhotos() and $userid->getTelephone() and $userid->getUsername() and $userid->getEmail()
                        and $userid->getInfo()->getDatenaissance() and $userid->getInfo()->getCpnaissance() and $userid->getInfo()->getLieunaissance() and $userid->getInfo()->getAdresse()

                        and !count($candidatures) == 0 and !count($Parents) == 0 and !count($Experience) == 0 and !count($Recommandation) == 0 and !count($Diplome) == 0 and !count($Document) == 0
                        and !in_array($userid->getInfo()->getProfilcomplet(),[3,2])){

                        $userid->getInfo()->setProfilcomplet(1);
                        $this->getDoctrine()->getManager()->flush();
                    }
                }


                return $this->render('UserBundle:Gestion:iFrameContentUser.html.twig', array('User' => $userid,
                    'Infocomplementaire' => $info, 'Parents' => $Parents, 'Experience' => $Experience, 'Recommandation' => $Recommandation, 'Diplome' => $Diplome, 'Document' => $Document,
                    'candidatures' => $candidatures, 'QCMs' => $userid->getEtablissement()->getQcmdef(), 'Questions' => $questions,
                    'reponses' => $reponses, 'QCMtest' => $QCMtest, 'QuestionsTest' => $questionsTest, 'reponsesTest' => $reponsesTest,'formations'=>$formation));
            }
        }

        return $this->render('UserBundle:Gestion:iFrameContentUser.html.twig',array('User'=>$userid));
    }

    public function affichageProfilAction()
    {
        $userid = $this->get('security.token_storage')->getToken()->getUser();

        // chargement des images
        if($userid->getPhotos() and !is_string($userid->getPhotos()))
        {
            $userid->setPhotos(base64_encode(stream_get_contents($userid->getPhotos())));
        }
        if($userid->hasRole('ROLE_APPRENANT')){
            $info = $userid->getInfo();
            $Parents = $this->getDoctrine()->getRepository('GenericBundle:Parents')->findBy(array('user'=>$userid));

            $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findAll();

            $Experience = $this->getDoctrine()->getRepository('GenericBundle:Experience')->findBy(array('user'=>$userid));
            $Recommandation = $this->getDoctrine()->getRepository('GenericBundle:Recommandation')->findBy(array('user'=>$userid));
            $Diplome = $this->getDoctrine()->getRepository('GenericBundle:Diplome')->findBy(array('user'=>$userid));
            $Document = $this->getDoctrine()->getRepository('GenericBundle:Document')->findBy(array('user'=>$userid));
            $candidatures = $this->getDoctrine()->getRepository('GenericBundle:Candidature')->findBy(array('user'=>$userid));

            if ($userid->getEtablissement()) {
                $questions = array();
                $reponses = array();
                foreach ($userid->getEtablissement()->getQcmdef() as $key => $qcm) {

                    $questions[$key] = $this->getDoctrine()->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef' => $qcm));
                    usort($questions[$key], array('\GenericBundle\Entity\Questiondef', 'sort_questions_by_order'));
                    foreach ($questions[$key] as $keyqst => $qst) {
                        $reps = $this->getDoctrine()->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef' => $qst));
                        usort($reps, array('\GenericBundle\Entity\Reponsedef', 'sort_reponses_by_order'));
                        $reponses[$key][$keyqst] = $reps;
                    }

                }


                $questionsTest = array();
                $reponsesTest = array();
                $QCMtest = array();
                foreach ($candidatures as $cand) {
                    $QCMtest = array_merge($QCMtest, $cand->getFormation()->getQcmdef()->toArray());
                }

                $QCMtest = array_unique($QCMtest);


                $index = 0;
                foreach ($QCMtest as $qcm) {
                    $questionsTest[$index] = $this->getDoctrine()->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef' => $qcm));
                    usort($questionsTest[$index], array('\GenericBundle\Entity\Questiondef', 'sort_questions_by_order'));
                    foreach ($questionsTest[$index] as $keyqst => $qst) {
                        $reps = $this->getDoctrine()->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef' => $qst));
                        usort($reps, array('\GenericBundle\Entity\Reponsedef', 'sort_reponses_by_order'));
                        $reponsesTest[$index][$keyqst] = $reps;
                    }
                    $index++;
                }


                if($userid->getInfo()){
                    if( !$userid->getCivilite() and !$userid->getNom() and !$userid->getPrenom() and !$userid->getPhotos() and !$userid->getTelephone() and !$userid->getUsername() and !$userid->getEmail()
                        and !$userid->getInfo()->getDatenaissance() and !$userid->getInfo()->getCpnaissance() and !$userid->getInfo()->getLieunaissance() and !$userid->getInfo()->getAdresse()

                        and !count($candidatures) == 0 and !count($Parents) == 0 and !count($Experience) == 0 and !count($Recommandation) == 0 and !count($Diplome) == 0 and !count($Document) == 0
                        and !in_array($userid->getInfo()->getProfilcomplet(),[3,2])){

                        $userid->getInfo()->setProfilcomplet(1);
                        $this->getDoctrine()->getManager()->flush();
                    }
                }

                return $this->render('UserBundle:Gestion:iFrameContentUser.html.twig', array('User' => $userid,
                    'Infocomplementaire' => $info, 'Parents' => $Parents, 'Experience' => $Experience, 'Recommandation' => $Recommandation, 'Diplome' => $Diplome, 'Document' => $Document,
                    'candidatures' => $candidatures, 'QCMs' => $userid->getEtablissement()->getQcmdef(), 'Questions' => $questions,
                    'reponses' => $reponses, 'QCMtest' => $QCMtest, 'QuestionsTest' => $questionsTest, 'reponsesTest' => $reponsesTest,'formations'=>$formation));
            }
        }

        return $this->render('UserBundle:Gestion:iFrameContentUser.html.twig',array('User'=>$userid));

    }

    public function afficher_messagerieAction(){

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $message = $this->getDoctrine()->getRepository('GenericBundle:Message')->findBy(array('destinataire'=>$user ),array('date'=>'desc'));

        //calcul Score
        $scores = array();
        foreach($message as $msg)
        {
            if($msg->getExpediteur()->getPhotos() and !is_string($msg->getExpediteur()->getPhotos()))
            {
                $msg->getExpediteur()->setPhotos(base64_encode(stream_get_contents($msg->getExpediteur()->getPhotos())));
            }
            if($msg->getMission()->getEtablissement()->getTier()->getLogo() and !is_string($msg->getMission()->getEtablissement()->getTier()->getLogo())){
                $msg->getMission()->getEtablissement()->getTier()->setLogo(base64_encode(stream_get_contents($msg->getMission()->getEtablissement()->getTier()->getLogo())));
            }

            $apprenant = null;
            if($msg->getExpediteur()->hasRole('ROLE_APPRENANT')){
                $apprenant = $msg->getExpediteur();
            }
            elseif($msg->getDestinataire()->hasRole('ROLE_APPRENANT')){
                $apprenant = $msg->getDestinataire();
            }
            else{
                continue;
            }
            $scoreapprenant = 0;

            foreach($msg->getMission()->getReponsedef() as $rep){
                if(in_array($rep,$apprenant->getReponsedef()->toArray())){

                    $scoreapprenant = $scoreapprenant + $rep->getScore();
                }
                else{$scoreapprenant++;}
            }
            array_push($scores,$scoreapprenant);
        }
        return  $this->render('UserBundle:messagerie:messagerie.html.twig',array('messageies'=>$message,'scores'=>$scores));
    }

    public function afficher_rendezvousAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $rendezvous= null;
        if($user->hasRole('ROLE_APPRENANT')){
            $rendezvous = $this->getDoctrine()->getRepository('GenericBundle:RDV')->findBy(array('apprenant'=>$user ));
        }
        elseif ($user->hasRole('ROLE_TUTEUR')){
            $rendezvous = $this->getDoctrine()->getRepository('GenericBundle:RDV')->findBy(array('tuteur'=>$user ));
        }
        elseif($user->hasRole('ROLE_SUPER_ADMIN')){
            $rendezvous = $this->getDoctrine()->getRepository('GenericBundle:RDV')->findAll();
        }
        elseif($user->hasRole('ROLE_RECRUTEUR')){
            $apprenants = $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$user->getEtablissement()));

            foreach($apprenants as $key => $value)
            {
                if(!$value->hasRole('ROLE_APPRENANT'))
                {
                    unset($apprenants[$key]);
                }
            }
            $rendezvous = array();
            foreach($apprenants as $apprenant){
                $rendezvous = array_merge($rendezvous, $this->getDoctrine()->getRepository('GenericBundle:RDV')->findBy(array('apprenant'=>$apprenant )));
            }
        }
        elseif($user->hasRole('ROLE_ADMINECOLE')){
            $apprenants =$this->getDoctrine()->getRepository('GenericBundle:User')->getUserofTier($user->getTier());
            foreach($apprenants as $key => $value)
            {
                if(!$value->hasRole('ROLE_APPRENANT'))
                {
                    unset($apprenants[$key]);
                }
            }
            $rendezvous = array();
            foreach($apprenants as $apprenant){
                $rendezvous = array_merge($rendezvous, $this->getDoctrine()->getRepository('GenericBundle:RDV')->findBy(array('apprenant'=>$apprenant )));
            }
        }

        foreach($rendezvous as $rdv)
        {
            if($rdv->getApprenant()->getPhotos() and !is_string($rdv->getApprenant()->getPhotos()))
            {
                $rdv->getApprenant()->setPhotos(base64_encode(stream_get_contents($rdv->getApprenant()->getPhotos())));
            }
            if($rdv->getMission()->getEtablissement()->getTier()->getLogo() and !is_string($rdv->getMission()->getEtablissement()->getTier()->getLogo())){
                $rdv->getMission()->getEtablissement()->getTier()->setLogo(base64_encode(stream_get_contents($rdv->getMission()->getEtablissement()->getTier()->getLogo())));
            }
        }
        return  $this->render('UserBundle:messagerie:Rendezvous.html.twig',array('rendezvous'=>$rendezvous));
    }

    public function UserAddedAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $newuser = $userManager->createUser();
        if($request->get('_Username')){
            $usernameexist = $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('username'=>$request->get('_Username')));
            if($usernameexist){
                $newuser->setUsername($request->get('_Username').''.count($usernameexist));
            }
            else{
                $newuser->setUsername($request->get('_Username'));
            }
            $newuser->setEmail($request->get('_mail'));
            $newuser->addRole($request->get('_role'));
            $newuser->setCivilite($request->get('civilite'));
            $newuser->setTelephone($request->get('_Tel'));
            $newuser->setPrenom($request->get('_Prenom'));
            $newuser->setNom($request->get('_Nom'));
        }


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
        if($request->get('_role')=='ROLE_RECRUTEUR' || $request->get('_role')=='ROLE_TUTEUR')
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
            if(!$this->getDoctrine()->getRepository('GenericBundle:Notification')->findOneBy(array('entite'=>$newuser->getId(),'type'=>'Utilisateur','user'=>$admin))){
                $notif = new Notification();
                $notif->setEntite($newuser->getId());
                $notif->setType('Utilisateur');
                $notif->setUser($admin);
                $em->persist($notif);
                $em->flush();
            }
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
            ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
            ->setTo($request->get('_mail'))
            ->setBody($this->renderView($modele,array('username'=>$newuser->getUsername(), 'password'=>$password))
                ,'text/html'
            );
        $this->get('mailer')->send($message);

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function ajouterApprenantAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $InfoComp = new Infocomplementaire();
        $InfoComp->setDatenaissance(date_create_from_format('d/m/Y',$request->get('_Datenaissance')) );
        $InfoComp->setAdresse($request->get('_Adresse').' '.$request->get('_Ville'));
        $InfoComp->setCp($request->get('_Codepostal'));
        $InfoComp->setCpnaissance($request->get('_Cpnaissance'));
        $InfoComp->setLieunaissance($request->get('_Lieunaissance'));
        $InfoComp->setPortable($request->get('_Portable'));
        $InfoComp->setPermis($request->get('_Permis'));
        $InfoComp->setVehicule($request->get('_Vehicule'));
        $InfoComp->setFormationactuelle($request->get('_FormationCours'));
        $InfoComp->setDernierDiplome($request->get('_Diplome'));
        if($request->get('_handicap')){
            $InfoComp->setHandicape(true);
        }
        else{
            $InfoComp->setHandicape(false);
        }
        if($request->get('_entrepreneur')){
            $InfoComp->setEntrepreneur(true);
        }
        else{
            $InfoComp->setEntrepreneur(false);
        }


        $InfoComp->setDatecreation(date_create());
        $em->persist($InfoComp);
        $em->flush();




        $apprenant = new ImportCandidat();
        $apprenant->setEtablissement($this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_idEtab')));
        $apprenant->setUser($this->get('security.token_storage')->getToken()->getUser());
        $apprenant->setCivilite(($request->get('_Civilite')));
        $apprenant->setNom($request->get('_Nom'));
        $apprenant->setPrenom($request->get('_Prenom'));
        $apprenant->setEmail($request->get('_Email'));
        $apprenant->setTelephone($request->get('_Telephone'));
        $apprenant->setInfo($InfoComp);

        $em->persist($apprenant);
        $em->flush();

        if($request->get('formations'))
        {
            foreach($request->get('formations') as $idFormation){
                $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->find($idFormation);
                $candidature = new Candidature();
                $candidature->setFormation($formation);
                $candidature->setImportcandidat($apprenant);
                $candidature->setDatecandidature(date_create());
                $candidature->setStatut(2);
                $em->persist($candidature);
                $em->flush();
            }
        }





        if($_FILES['_Document'] )
        {
            $fileNotInsert = "";
            for($i = 0; $i < count($_FILES['_Document']['type']); $i++)
            {
                if($_FILES['_Document']['size'][$i] > 1000000){
                    $fileNotInsert+=$_FILES['_Document']['name'][$i].' ';
                }
                else if($_FILES['_Document']['size'][$i] > 0){
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

            }
        }

        if($request->get('_Diplome')){
            $diplome = new Diplome();
            $diplome->setImportCandidat($apprenant);
            $diplome->setLibelle($request->get('_Diplome'));
            $em->persist($diplome);
            $em->flush();
        }

        if($fileNotInsert==""){
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        else{
            return new Response('<script language="JavaScript">window.onload = function(){alert("les fichiers :'.$fileNotInsert.'; n\'ont pas été ajoutés car leurs tailles est trop importante.");window.location.href = "'.$_SERVER['HTTP_REFERER'].'"}</script>');
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

    public function modifierStatutCandidatureAction($id,$statut){
        $em = $this->getDoctrine()->getManager();
        $candi= $em->getRepository('GenericBundle:Candidature')->find($id);

        $candi->setStatut($statut);
        $em->flush();
        $mail =null;
        $Statutmessage = null;
        if($statut==3)
        {
            $Statutmessage ='validé';
            if($candi->getUser()->getInfo()){
                $candi->getUser()->getInfo()->setProfilcomplet(3);
                $em->flush();
            }

        }
        elseif($statut==99)
        {
            $Statutmessage ='refusé';
            $StatutApprenant = 2;
            foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('user'=>$candi->getUser())) as $candidature){
                if($candidature->getStatut() == 3 ){
                    $StatutApprenant = 3;
                    break;
                }
            }
            if($candi->getUser()->getInfo()){
                $candi->getUser()->getInfo()->setProfilcomplet($StatutApprenant);
            }
            $em->flush();

        }

        if($candi->getUser())
        {
            $mail = $candi->getUser()->getEmail();
        }
        elseif($candi->getImportcandidat())
        {
            $mail = $candi->getImportcandidat()->getEmail();
        }
        $modele = 'GenericBundle:Mail:ConfCandidature.html.twig';

        if($mail and $Statutmessage){
            $message = \Swift_Message::newInstance()
                ->setSubject('confirmation de candidature')
                ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                ->setTo($mail)
                ->setBody($this->renderView($modele,array('statut'=>$Statutmessage,'formation'=>$candi->getFormation()->getNom()))
                    ,'text/html'
                );
            $this->get('mailer')->send($message);
        }

        $reponse = new JsonResponse();
        return $reponse->setData(array('success'=>1));

    }

    public function CompleterProfilAction($IdUser){
        $em = $this->getDoctrine()->getManager();
        $user= $em->getRepository('GenericBundle:User')->find($IdUser);
        $mail = $user->getEmail();


        if($mail){
            $message = \Swift_Message::newInstance()
                ->setSubject('completez votre profil')
                ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                ->setTo($mail)
                ->setBody($this->renderView('GenericBundle:Mail:EmailCompleterProfil.html.twig',array('apprenant'=>$user))
                    ,'text/html'
                );
            $this->get('mailer')->send($message);
        }

        $reponse = new JsonResponse();
        return $reponse->setData(array('success'=>$mail));

    }

    public function ModifAppSasAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $apprenant = $em->getRepository('GenericBundle:ImportCandidat')->find($request->get('_ID'));
        if($request->get('_Civilite') and !$request->get('_Civilite')==''){
            $apprenant->setCivilite($request->get('_Civilite'));
        }
        if($request->get('_NomAppr') and !$request->get('_NomAppr')==''){
            $apprenant->setNom($request->get('_NomAppr'));
        }
        if($request->get('_PrenomAppr') and !$request->get('_PrenomAppr')==''){
            $apprenant->setPrenom($request->get('_PrenomAppr'));
        }
        if($request->get('_EmailAppr') and !$request->get('_EmailAppr')==''){
            $apprenant->setEmail($request->get('_EmailAppr'));
        }
        if($request->get('_telAppr') and !$request->get('_telAppr')==''){
            $apprenant->setTelephone($request->get('_telAppr'));
        }
        if($request->get('_DateNaissance') and !$request->get('_DateNaissance')==''){
            $apprenant->getInfo()->setDatenaissance(date_create_from_format('d/m/Y',$request->get('_DateNaissance')));
        }
        if($request->get('_CpNaissance') and !$request->get('_CpNaissance')==''){
            $apprenant->getInfo()->setCpnaissance($request->get('_CpNaissance'));
        }
        if($request->get('_LieuNaissance') and !$request->get('_LieuNaissance')==''){
            $apprenant->getInfo()->setLieunaissance($request->get('_LieuNaissance'));
        }
        if($request->get('_Adresse') and !$request->get('_Adresse')==''){
            $apprenant->getInfo()->setAdresse($request->get('_Adresse'));
        }
        if($request->get('_Diplome') and !$request->get('_Diplome')==''){
            $apprenant->getInfo()->setDernierDiplome($request->get('_Diplome'));
        }
        if($request->get('_Formation') and !$request->get('_Formation')==''){
            $apprenant->getInfo()->setFormationactuelle($request->get('_Formation'));
        }

        $em->flush();
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function userModifAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('GenericBundle:User')->findOneBy(array('id'=>$request->get('_ID')));

        $user->setCivilite($request->get('_Civilite'));
        $user->setNom($request->get('_Nom'));
        $user->setPrenom($request->get('_Prenom'));

        if($_FILES && $_FILES['_Photos']['size'] >0)
        {
            $user->setPhotos(file_get_contents($_FILES['_Photos']['tmp_name']));
            $em->flush();
        }

        $user->setTelephone($request->get('_Tel'));
        $user->setUsername($request->get('_Username'));
        $user->setEmail($request->get('_Mail'));
        $em->flush();

        if($user->hasRole('ROLE_APPRENANT')){
            $info = $em->getRepository('GenericBundle:infocomplementaire')->find($request->get('_IdInfo'));

            if($info)
            {
                $info->setDatenaissance(date_create_from_format('d/m/Y',$request->get('_Datenaissance')) );
                $info->setCpnaissance($request->get('_Cpnaissance'));
                $info->setLieunaissance($request->get('_Lieunaissance'));
                $info->setPortable($request->get('_Portable'));
                $info->setCp($request->get('_Codepostal'));
                $info->setPermis($request->get('_Permis'));
                $info->setVehicule($request->get('_Vehicule'));
                $info->setFormationactuelle($request->get('_FormationActuel'));
                $info->setDernierDiplome($request->get('_DernierDiplome'));
                if($request->get('_handicap')){
                    $info->setHandicape(true);
                }
                else{
                    $info->setHandicape(false);
                }
                if($request->get('_entrepreneur')){
                    $info->setEntrepreneur(true);
                }
                else{
                    $info->setEntrepreneur(false);
                }
                $info->setAdresse($request->get('_Adresse'));
                //$info->setFacebook($request->get('_Facebook'));
                //$info->setLinkedin($request->get('_Linkedin'));
                $info->setMobilite($request->get('_Mobilite'));

                $info->setLienexterne1(null);
                $info->setLienexterne2(null);
                $info->setLienexterne3(null);

                for($i = 0; $i < count($request->get('_lien')); $i++){
                    if($i == 0 and !$request->get('_lien')[0]==''){
                        $info->setLienexterne1($request->get('_lien')[0]);
                    }
                    if($i == 1 and !$request->get('_lien')[1]==''){
                        $info->setLienexterne2($request->get('_lien')[1]);
                    }
                    if($i == 2 and !$request->get('_lien')[2]==''){
                        $info->setLienexterne3($request->get('_lien')[2]);
                    }

                }

                if($request->get('_Fratrie')!=null and intval($request->get('_Fratrie')) >= 0){
                    $info->setFratrie(intval($request->get('_Fratrie')));
                }


                $info->setHobbie1(null);
                $info->setHobbie2(null);
                $info->setHobbie3(null);
                $info->setHobbie4(null);
                $info->setHobbie5(null);
                for($i = 0; $i < count($request->get('_hobbie')); $i++){
                    if($i == 0 and !$request->get('_hobbie')[0]==''){
                        $info->setHobbie1($request->get('_hobbie')[0]);
                    }
                    if($i == 1 and !$request->get('_hobbie')[1]==''){
                        $info->setHobbie2($request->get('_hobbie')[1]);
                    }
                    if($i == 2 and !$request->get('_hobbie')[2]==''){
                        $info->setHobbie3($request->get('_hobbie')[2]);
                    }
                    if($i == 3 and !$request->get('_hobbie')[3]==''){
                        $info->setHobbie4($request->get('_hobbie')[3]);
                    }
                    if($i == 4 and !$request->get('_hobbie')[4]==''){
                        $info->setHobbie5($request->get('_hobbie')[4]);
                    }

                }

                $info->setLangue1(null);
                $info->setLangue2(null);
                $info->setLangue3(null);
                $info->setLangue4(null);
                $info->setLangue5(null);
                for($i = 0; $i < count($request->get('_Langue')); $i++){
                    if($i == 0 and !$request->get('_Langue')[0]==''){
                        $info->setLangue1($request->get('_Langue')[0].','.$request->get('_Niveau')[0]);
                    }
                    if($i == 1 and !$request->get('_Langue')[1]==''){
                        $info->setLangue2($request->get('_Langue')[1].','. $request->get('_Niveau')[1]);
                    }
                    if($i == 2 and !$request->get('_Langue')[2]==''){
                        $info->setLangue3($request->get('_Langue')[2].','. $request->get('_Niveau')[2]);
                    }
                    if($i == 3 and !$request->get('_Langue')[3]==''){
                        $info->setLangue4($request->get('_Langue')[3].','. $request->get('_Niveau')[3]);
                    }
                    if($i == 4 and !$request->get('_Langue')[4]==''){
                        $info->setLangue5($request->get('_Langue')[4].','. $request->get('_Niveau')[4]);
                    }

                }

                $info->setDatemodification(date_create());



                $em->flush();
            }
            else{
                $info = new Infocomplementaire();
                $info->setDatenaissance(date_create_from_format('d/m/Y',$request->get('_Datenaissance')) );
                $info->setCpnaissance($request->get('_Cpnaissance'));
                $info->setLieunaissance($request->get('_Lieunaissance'));
                $info->setPortable($request->get('_Portable'));
                $info->setCp($request->get('_Codepostal'));
                $info->setPermis($request->get('_Permis'));
                $info->setVehicule($request->get('_Vehicule'));
                $info->setFormationactuelle($request->get('_FormationActuel'));
                $info->setDernierDiplome($request->get('_DernierDiplome'));
                if($request->get('_handicap')=="0"){
                    $info->setHandicape(true);
                }
                else{
                    $info->setHandicape(false);
                }
                if($request->get('_entrepreneur')=="0"){
                    $info->setEntrepreneur(true);
                }
                else{
                    $info->setEntrepreneur(false);
                }
                $info->setDatecreation(date_create());
                $info->setAdresse($request->get('_Adresse'));
                //$info->setFacebook($request->get('_Facebook'));
                //$info->setLinkedin($request->get('_Linkedin'));
                $info->setMobilite($request->get('_Mobilite'));

                if($request->get('_Fratrie')!=null and intval($request->get('_Fratrie')) >= 0){
                    $info->setFratrie(intval($request->get('_Fratrie')));
                }

                $info->setLienexterne1(null);
                $info->setLienexterne2(null);
                $info->setLienexterne3(null);
                for($i = 0;$i < count($request->get('_lien')); $i++){
                    if($i == 0 and !$request->get('_lien')[0]==''){
                        $info->setLienexterne1($request->get('_lien')[0]);
                    }
                    if($i == 1 and !$request->get('_lien')[1]==''){
                        $info->setLienexterne2($request->get('_lien')[1]);
                    }
                    if($i == 2 and !$request->get('_lien')[2]==''){
                        $info->setLienexterne3($request->get('_lien')[2]);
                    }
                }



                $info->setHobbie1(null);
                $info->setHobbie2(null);
                $info->setHobbie3(null);
                $info->setHobbie4(null);
                $info->setHobbie5(null);
                for($i = 0; $i < count($request->get('_hobbie')); $i++){
                    if($i == 0 and !$request->get('_hobbie')[0]==''){
                        $info->setHobbie1($request->get('_hobbie')[0]);
                    }
                    if($i == 1 and !$request->get('_hobbie')[1]==''){
                        $info->setHobbie2($request->get('_hobbie')[1]);
                    }
                    if($i == 2 and !$request->get('_hobbie')[2]==''){
                        $info->setHobbie3($request->get('_hobbie')[2]);
                    }
                    if($i == 3 and !$request->get('_hobbie')[3]==''){
                        $info->setHobbie4($request->get('_hobbie')[3]);
                    }
                    if($i == 4 and !$request->get('_hobbie')[4]==''){
                        $info->setHobbie5($request->get('_hobbie')[4]);
                    }

                }

                $info->setLangue1(null);
                $info->setLangue2(null);
                $info->setLangue3(null);
                $info->setLangue4(null);
                $info->setLangue5(null);
                for($i = 0; $i < count($request->get('_Langue')); $i++){
                    if($i == 0 and !$request->get('_Langue')[0]==''){
                        $info->setLangue1($request->get('_Langue')[0].','.$request->get('_Niveau')[0]);
                    }
                    if($i == 1 and !$request->get('_Langue')[1]==''){
                        $info->setLangue2($request->get('_Langue')[1].','.$request->get('_Niveau')[1]);
                    }
                    if($i == 2 and !$request->get('_Langue')[2]==''){
                        $info->setLangue3($request->get('_Langue')[2].','.$request->get('_Niveau')[2]);
                    }
                    if($i == 3 and !$request->get('_Langue')[3]==''){
                        $info->setLangue4($request->get('_Langue')[3]. ','.$request->get('_Niveau')[3]);
                    }
                    if($i == 4 and !$request->get('_Langue')[4]==''){
                        $info->setLangue5($request->get('_Langue')[4]. ','.$request->get('_Niveau')[4]);
                    }

                }
                $info->setDatemodification(date_create());
                $em->persist($info);
                $em->flush();
            }

        }




        return $this->redirect($_SERVER['HTTP_REFERER']);
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
        return $this->render('GenericBundle::ReloadParent.html.twig',array('clear'=>true));

    }

    public function suppParentAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $par = $em->getRepository('GenericBundle:Parents')->find($id);
        $em->remove($par);
        $em->flush();
        $reponse = new JsonResponse();
        return $reponse->setData(array('Status'=>'Parent correctement supprimer'));
    }

    public function suppExperienceAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $exp = $em->getRepository('GenericBundle:Experience')->find($id);
        $em->remove($exp);
        $em->flush();
        $reponse = new JsonResponse();
        return $reponse->setData(array('Status'=>'Experience correctement supprimer'));

    }

    public function suppRecommandationAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $recom = $em->getRepository('GenericBundle:Recommandation')->find($id);
        $em->remove($recom);
        $em->flush();
        $reponse = new JsonResponse();
        return $reponse->setData(array('Status' => 'Recommandation correctement supprimer'));
    }

    public function suppDiplomeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $diplome = $em->getRepository('GenericBundle:Diplome')->find($id);
        $em->remove($diplome);
        $em->flush();
        $reponse = new JsonResponse();
        return $reponse->setData(array('Status' => 'diplome correctement supprimer'));
    }

    public function suppCandidatureAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $candi = $em->getRepository('GenericBundle:Candidature')->find($id);
        $user = $candi->getUser();

        $em->remove($candi);
        $em->flush();
        $StatutApprenant = 2;
        foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('user'=>$user)) as $candidature){
            if($candidature->getStatut() == 3 ){
                $StatutApprenant = 3;
                break;
            }
        }
        if($candi->getUser()->getInfo()){
            $candi->getUser()->getInfo()->setProfilcomplet($StatutApprenant);
            $em->flush();
        }
        $reponse = new JsonResponse();
        return $reponse->setData($StatutApprenant);
    }

    public function suppDocumentAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $document = $em->getRepository('GenericBundle:Document')->find($id);
        $em->remove($document);
        $em->flush();
        $reponse = new JsonResponse();
        return $reponse->setData(array('Status' => 'document correctement supprimer'));

    }

    public function importAction(Request $request)
    {
        if($request->get('Import')==0)
        {
            $this->ImportApprenant($request,$_FILES['_CSV']['tmp_name']);
        }
        elseif($request->get('Import')==1)
        {
            $this->ImportMissions($_FILES['_CSV']['tmp_name']);
        }
        elseif($request->get('Import')==2)
        {
            $this->ImportMissions($_FILES['_CSV']['tmp_name']);
        }
        return $this->redirect($_SERVER['HTTP_REFERER']);
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
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $file = new \SplFileObject($uploadedfile);
        $reader = new ExcelReader($file);
        $jump = 0;
        $em = $this->getDoctrine()->getEntityManager();
        foreach ($reader as $row) {
            if($jump++ < 1 || ('' == $row[1] and '' ==$row[2] and '' == $row[3] and '' == $row[4])){
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
                    $newsiege->setSecteur(mb_convert_encoding($row[4],'UTF-8','auto'));
                    $newsiege->setTier($tier);
                    $em->persist($newsiege);
                    $em->flush();
                }
                $etab_mission = $em->getRepository('GenericBundle:Etablissement')->findOneBy(array('siret'=>mb_convert_encoding($row[2],'UTF-8','auto')));
                if(!$etab_mission)
                {
                    $newetab = new Etablissement();
                    $newetab->setSiret(mb_convert_encoding($row[2],'UTF-8','auto'));
                    $newetab->setActive(mb_convert_encoding($row[4],'UTF-8','auto'));
                    $newetab->setAdresse(mb_convert_encoding($row[8],'UTF-8','auto'));
                    $newetab->setCodepostal(mb_convert_encoding($row[9],'UTF-8','auto'));
                    $newetab->setVille(mb_convert_encoding($row[10],'UTF-8','auto'));
                    $newetab->setTier($tier);
                    $em->persist($newetab);
                    $em->flush();
                    $etab_mission = $newetab;
                }

                $mission = new Mission();
                $mission->setTypecontrat(mb_convert_encoding($row[16],'UTF-8','auto'));
                $mission->setIntitule(mb_convert_encoding($row[18],'UTF-8','auto'));
                if( strlen( mb_convert_encoding($row[19],'UTF-8','auto')) < 255)
                {
                    $mission->setDescriptif(mb_convert_encoding($row[19],'UTF-8','auto'));
                }
                $mission->setDomaine(mb_convert_encoding($row[20],'UTF-8','auto'));
                $mission->setNomcontact(mb_convert_encoding($row[11],'UTF-8','auto'));
                $mission->setPrenomcontact(mb_convert_encoding($row[12],'UTF-8','auto'));
                $mission->setFonctioncontact(mb_convert_encoding($row[13],'UTF-8','auto'));
                $mission->setTelcontact(mb_convert_encoding($row[14],'UTF-8','auto'));
                $mission->setEmailcontact(mb_convert_encoding($row[15],'UTF-8','auto'));
                $mission->setEtablissement($etab_mission);

                if($user->hasRole('ROLE_SUPER_ADMIN') or $user->hasRole('ROLE_ADMINSOC')){
                    $mission->setStatut(3);
                }
                elseif($user->hasRole('ROLE_ADMINECOLE') or $user->hasRole('ROLE_RECRUTEUR')){
                    $mission->setStatut(1);
                }
                if($user->getEtablissement()){
                    $mission->setTier($user->getEtablissement()->getTier());
                }
                elseif($user->getTier()){
                    $mission->setTier($user->getTier());
                }

                if(!$row[0]=='' and !mb_convert_encoding($row[0],'UTF-8','auto')=='jj/mm/aaaa')
                {
                    $date=date_create_from_format('dd/mm/YYYY',mb_convert_encoding($row[0],'UTF-8','auto'));
                    $mission->setDatecreation($date);
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
                    if(!$this->getDoctrine()->getRepository('GenericBundle:Notification')->findOneBy(array('entite'=>$mission->getId(),'type'=>'Mission','user'=>$admin))){
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
    }
    private function ImportSoc($uploadfile){}

    public function afficherImportsAction()
    {
        $imports =$this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->findBy(array('user'=>$this->get('security.token_storage')->getToken()->getUser()));

        $array_candidature = array();
        foreach($imports as $import){
            if($import->getPhotos() and !is_string($import->getPhotos()))
            {
                $import->setPhotos(base64_encode(stream_get_contents($import->getPhotos())));
            }
            $jsonstring = '[';
            $first =true;
            foreach($this->getDoctrine()->getRepository('GenericBundle:Candidature')->findBy(array('importcandidat'=>$import)) as $candidature){

                if($first){
                    $first = false;
                    $jsonstring = $jsonstring . '{"NomeFormation":"'.$candidature->getFormation()->getNom().'","Adresse":"'.$candidature->getFormation()->getEtablissement()->getAdresse().'","NomEcole":"'.$candidature->getFormation()->getEtablissement()->getTier()->getRaisonsoc().'","statut":'.$candidature->getStatut().'}';
                }else{
                    $jsonstring = $jsonstring . ',{"NomeFormation":"'.$candidature->getFormation()->getNom().'","Adresse":"'.$candidature->getFormation()->getEtablissement()->getAdresse().'","NomEcole":"'.$candidature->getFormation()->getEtablissement()->getTier()->getRaisonsoc().'","statut":'.$candidature->getStatut().'}';
                }

            }

            $jsonstring = $jsonstring . ']';

            array_push($array_candidature,$jsonstring);


        }
       // var_dump($array_candidature,$jsonstring);die();

        return $this->render('UserBundle:Gestion:Import.html.twig',array('imports'=>$imports,'candidatures'=>$array_candidature));
    }

    public function supprimerImportsAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $import = $em->getRepository('GenericBundle:ImportCandidat')->find($id);
        $response = new JsonResponse();

        if($import)
        {
            $message = \Swift_Message::newInstance()
                ->setSubject('')
                ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                ->setTo($import->getEmail())
                ->setBody($this->renderView('GenericBundle:Mail:EmailSupp.html.twig',array('ImportCandidat'=>$import,'Candidatures'=>$em->getRepository('GenericBundle:Candidature')->findBy(array('importcandidat'=>$import)))),'text/html');
            $this->get('mailer')->send($message);

            $em->remove($import);
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
        $rep = new JsonResponse();
        $users = $em->getRepository('GenericBundle:User')->findApprenantDuplicata($id);
        $results = array();
        foreach($users as $user){
            if($user->getPhotos() and !is_string($user->getPhotos()))
            {
                $user->setPhotos(base64_encode(stream_get_contents($user->getPhotos())));
            }
            elseif(!$user->getPhotos()){
                if($user->getCivilite()){

                }
            }
            if($user->getInfo())
            {
                array_push($results,array($user->getId(),$user->getPhotos(),$user->getNom(),$user->getPrenom(),$user->getTelephone(),
                    $user->getEmail(),$user->getInfo()->getLieunaissance(),date_format($user->getInfo()->getDatenaissance(),'d/m/Y') ));
            }
            else{
                array_push($results,array($user->getId(),$user->getPhotos(),$user->getNom(),$user->getPrenom(),$user->getTelephone(),$user->getEmail()));
            }

        }
        return $rep->setData($results);
    }

    public function SAStoUserAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $import = $em->getRepository('GenericBundle:ImportCandidat')->find($id);
        $user = $em->getRepository('GenericBundle:User')->findApprenantDuplicata($id);
        $response = new JsonResponse();

        if($user)
        {
            return $response->setData(array('Ajout'=>'0','Id'=>$id,'LieuNaissance'=>$import->getInfo()->getLieunaissance(),'DateNaissance'=>date_format($import->getInfo()->getDatenaissance(),'d/m/Y')));
        }
        else{
            $userManager = $this->get('fos_user.user_manager');
            $newuser = $userManager->createUser();
            //$newuser = new User();
            $newuser->setCivilite($import->getCivilite());
            $newuser->setNom($import->getNom());
            $newuser->setEmail($import->getEmail());
            $newuser->setPrenom($import->getPrenom());
            $newuser->setTelephone($import->getTelephone());
            $newuser->setEtablissement($import->getEtablissement());
            $newuser->setPhotos($import->getPhotos());
            $newuser->addRole('ROLE_APPRENANT');
            $usernameexist = $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('prenom'=>$import->getPrenom(),'nom'=>$import->getNom()));
            if($usernameexist){
                $newuser->setUsername($import->getPrenom().'.'.$import->getNom().''.count($usernameexist));
            }
            else{
                $newuser->setUsername($import->getPrenom().'.'.$import->getNom());
            }
            //generate a password
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars
            $hash =  $this->get('security.password_encoder')->encodePassword($newuser, $password);
            $newuser->setPassword($hash);

            $em = $this->getDoctrine()->getManager();
            $em->persist($newuser);
            $em->flush();

            if($import->getInfo()) {
                $import->getInfo()->setDaterecup(date_create());
                $import->getInfo()->setProfilcomplet(0);
                $em->flush();
                $newuser->setInfo($import->getInfo());
                $import->setInfo(null);
                $em->flush();
            }

            foreach($em->getRepository('GenericBundle:Document')->findBy(array('importCandidat'=>$import)) as $document)
            {
                $document->setUser($newuser);
                $document->setImportCandidat(null);
                $em->flush();
            }
            foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('importcandidat'=>$import)) as $candidature)
            {
                $candidature->setUser($newuser);
                $candidature->setImportCandidat(null);
                $em->flush();
            }
            foreach($em->getRepository('GenericBundle:Diplome')->findBy(array('importCandidat'=>$import)) as $diplome)
            {
                $diplome->setUser($newuser);
                $diplome->setImportCandidat(null);
                $em->flush();
            }

            /*
            foreach($em->getRepository('GenericBundle:Experience')->findBy(array('importCandidat'=>$import)) as $experience)
            {
                $experience->setUser($newuser);
                $experience->setImportCandidat(null);
                $em->flush();
            }

            foreach($import->getHobbies() as $hobby)
            {
                $newuser->addHobby($hobby);
                $em->flush();
            }
            foreach($import->getLangue() as $langue)
            {
                $newuser->addLangue($langue);
                $em->flush();
            }
            foreach($em->getRepository('GenericBundle:Parents')->findBy(array('importCandidat'=>$import)) as $parents)
            {
                $parents->setUser($newuser);
                $parents->setImportCandidat(null);
                $em->flush();
            }
            foreach($em->getRepository('GenericBundle:Recommandation')->findBy(array('importCandidat'=>$import)) as $recommandation)
            {
                $recommandation->setUser($newuser);
                $recommandation->setImportCandidat(null);
                $em->flush();
            }*/

            $usercon = $this->get('security.token_storage')->getToken()->getUser();

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
                ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                ->setTo($import->getEmail())
                ->setBody($this->renderView($modele,array('username'=>$newuser->getUsername(), 'password'=>$password))
                    ,'text/html'
                );
            $this->get('mailer')->send($message);

            $em->remove($import);
            $em->flush();
            return $response->setData(array('Ajout'=>'1'));
        }
    }

    public function FusionnerAction($sas,Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $reponse = new JsonResponse();

        if(!$request->get('DuplicaPopUpRadioUSer')){
            return $reponse->setData(0);
        }
        $import = $em->getRepository('GenericBundle:ImportCandidat')->find($sas);
        $userfus = $em->getRepository('GenericBundle:User')->find($request->get('DuplicaPopUpRadioUSer'));

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

        if(!$userfus->getPhotos() and !is_string(!$userfus->getPhotos())){
            $userfus->setPhotos($import->getPhotos());
        }
        if(!$userfus->getInfo()->getAdresse()){
            $userfus->getInfo()->setAdresse($import->getInfo()->getAdresse());
        }
        if(!$userfus->getInfo()->getCp()){
            $userfus->getInfo()->setCp($import->getInfo()->getCp());
        }
        if(!$userfus->getInfo()->getLienexterne1()){
            $userfus->getInfo()->setLienexterne1($import->getInfo()->getLienexterne1());
        }
        if(!$userfus->getInfo()->getLienexterne2()){
            $userfus->getInfo()->setLienexterne2($import->getInfo()->getLienexterne2());
        }
        if(!$userfus->getInfo()->getLienexterne3()){
            $userfus->getInfo()->setLienexterne3($import->getInfo()->getLienexterne3());
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
        if(!$userfus->getInfo()->getInsee()){
            $userfus->getInfo()->setInsee($import->getInfo()->getInsee());
        }
        if(!$userfus->getInfo()->getHandicape()){
            $userfus->getInfo()->setHandicape($import->getInfo()->getHandicape());
        }
        if(!$userfus->getInfo()->getEntrepreneur()){
            $userfus->getInfo()->setEntrepreneur($import->getInfo()->getEntrepreneur());
        }
        if(!$userfus->getInfo()->getPortable()){
            $userfus->getInfo()->setPortable($import->getInfo()->getPortable());
        }
        $em->flush();

       /* foreach($import->getLangue() as $langue)
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
        }*/
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
            $delete = false;
            foreach($em->getRepository('GenericBundle:Experience')->findBy(array('user'=>$userfus)) as $experienceuser)
            {
                if($experience->isEqual($experienceuser))
                {
                    $em->remove($experience);
                    $em->flush();
                    $delete = true;
                }
            }
            if(!$delete){
                $experience->setUser($userfus);
                $experience->setImportCandidat(null);
                $em->flush();
            }
        }
        foreach($em->getRepository('GenericBundle:Diplome')->findBy(array('importCandidat'=>$import)) as $diplome)
        {
            $delete = false;
            foreach($em->getRepository('GenericBundle:Diplome')->findBy(array('user'=>$userfus)) as $diplomeuser)
            {
                if($diplome->isEqual($diplomeuser))
                {
                    $em->remove($diplome);
                    $em->flush();
                    $delete = true;
                }
                else{

                }
            }
            if(!$delete){
                $diplome->setUser($userfus);
                $diplome->setImportCandidat(null);
                $em->flush();
            }
        }
        foreach($em->getRepository('GenericBundle:Document')->findBy(array('importCandidat'=>$import)) as $document)
        {
            $delete = false;
            foreach($em->getRepository('GenericBundle:Document')->findBy(array('user'=>$userfus)) as $documentuser)
            {
                if($document->isEqual($documentuser))
                {
                    $em->remove($document);
                    $em->flush();
                    $delete = true;
                }
            }
            if(!$delete){
                $document->setUser($userfus);
                $document->setImportCandidat(null);
                $em->flush();
            }
        }
        foreach($em->getRepository('GenericBundle:Parents')->findBy(array('importCandidat'=>$import)) as $parents)
        {
            $delete = false;
            foreach($em->getRepository('GenericBundle:Parents')->findBy(array('user'=>$userfus)) as $parentsuser)
            {
                if($parents->isEqual($parentsuser))
                {
                    $em->remove($parents);
                    $em->flush();
                    $delete = true;
                }
            }
            if(!$delete){
                $parents->setUser($userfus);
                $parents->setImportCandidat(null);
                $em->flush();
            }
        }
        foreach($em->getRepository('GenericBundle:Recommandation')->findBy(array('importCandidat'=>$import)) as $recommandation)
        {
            $delete = false;
            foreach($em->getRepository('GenericBundle:Recommandation')->findBy(array('user'=>$userfus)) as $recommandationuser)
            {
                if($recommandation->isEqual($recommandationuser))
                {
                    $em->remove($recommandation);
                    $em->flush();
                    $delete = true;
                }
            }
            if(!$delete){
                $recommandation->setUser($userfus);
                $recommandation->setImportCandidat(null);
                $em->flush();
            }
        }

        $em->remove($import);
        $em->flush();
        return $reponse->setData(1);
    }

    public function AjouterParentAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();

        if($request->get('_IDPARENT')){
            $parent = $em->getRepository('GenericBundle:Parents')->find($request->get('_IDPARENT'));
            $parent->setCivilite($request->get('_Civiliteparent'));
            $parent->setNom($request->get('_Nomresp'));
            $parent->setPrenom($request->get('_Prenomresp'));
            $parent->setTelephone($request->get('_Telephoneresp'));
            $parent->setAdresse($request->get('_Adresseresp'));
            $parent->setStatut($request->get('_Statutparent'));
            $parent->setEmail($request->get('_Emailresp'));
            $parent->setNomjeunefille($request->get('_Nomjeunefille'));
            $em->persist($parent);
            $em->flush();
        }
        else{
            $parent = new Parents();
            $user = $this->getDoctrine()->getRepository('GenericBundle:User')->find($request->get('_idUser'));
            $parent->setUser($user);
            $parent->setCivilite($request->get('_Civiliteparent'));
            $parent->setNom($request->get('_Nomparent'));
            $parent->setPrenom($request->get('_Prenomparent'));
            $parent->setStatut($request->get('_Statutparent'));
            $parent->setProfession($request->get('_Professionparent'));
            $parent->setTelephone($request->get('_Telephoneparent'));
            $parent->setAdresse($request->get('_Adresseparent').' '.$request->get('_Villeparent').' '.$request->get('_CodePostaleparent'));
            $parent->setEmail($request->get('_Emailparent'));

            $em->persist($parent);
            $em->flush();
        }

        return $this->redirect($_SERVER['HTTP_REFERER']);


    }

    public function AjouterExperienceAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        if($request->get('_IDExp')){
            $experience = $em->getRepository('GenericBundle:Experience')->find($request->get('_IDExp'));
            $experience->setNomsociete($request->get('_Nomsociete'));
            $experience->setActivite($request->get('_Activite'));
            $experience->setLieu($request->get('_Lieu'));
            $experience->setPoste($request->get('_Poste'));
            $experience->setDebut(date_create_from_format('m/Y',$request->get('_Datedebut')));
            $experience->setFin(date_create_from_format('m/Y',$request->get('_Datefin')));
            $experience->setDescription($request->get('_Descriptionexp'));
            $em->persist($experience);
            $em->flush();
        }
        else{
            $experience = new Experience();
            $user = $this->getDoctrine()->getRepository('GenericBundle:User')->find($request->get('_idUser'));
            $experience->setUser($user);

            $experience->setNomsociete($request->get('_Nomsociete'));
            $experience->setActivite($request->get('_Activite'));
            $experience->setLieu($request->get('_Lieu'));
            $experience->setPoste($request->get('_Poste'));
            $experience->setDebut(date_create_from_format('m/Y',$request->get('_Datedebut')));
            $experience->setFin(date_create_from_format('m/Y',$request->get('_Datefin')));
            $experience->setDescription($request->get('_Descriptionexp'));

            $em->persist($experience);
            $em->flush();
        }



        return $this->redirect($_SERVER['HTTP_REFERER']);

    }

    public function AjouterRecommandationAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        if($request->get('_IDRecom')){
            $recommandation = $em->getRepository('GenericBundle:Recommandation')->find($request->get('_IDRecom'));
            $recommandation->setNom($request->get('_Nomrec'));
            $recommandation->setFonction($request->get('_Fonctionrec'));
            $recommandation->setTelephone($request->get('_Telephonerec'));
            $recommandation->setEmail($request->get('_Emailrec'));
            $recommandation->setText($request->get('_Text'));
            $em->persist($recommandation);
            $em->flush();
        }
        else{
            $recommandation = new Recommandation();
            $user = $this->getDoctrine()->getRepository('GenericBundle:User')->find($request->get('_idUser'));
            $recommandation->setUser($user);
            $recommandation->setNom($request->get('_Nomrec').' '.$request->get('_Prenomrec'));
            $recommandation->setFonction($request->get('_Fonctionrec'));
            $recommandation->setTelephone($request->get('_Telephonerec'));
            $recommandation->setEmail($request->get('_Emailrec'));
            $recommandation->setText($request->get('_Text'));
            $em->persist($recommandation);
            $em->flush();
        }


        return $this->redirect($_SERVER['HTTP_REFERER']);


    }

    public function AjouterDiplomeAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        if($request->get('_IDDiplome')){
            $diplome = $em->getRepository('GenericBundle:Diplome')->find($request->get('_IDDiplome'));
            $diplome->setLibelle($request->get('_Libelle'));
            $diplome->setNiveau($request->get('_Niveau'));
            $diplome->setObtention($request->get('_Obtention'));
            $diplome->setEcole($request->get('_Ecole'));
            $em->persist($diplome);
            $em->flush();

        }
        else{
            $diplome = new Diplome();
            $user = $this->getDoctrine()->getRepository('GenericBundle:User')->find($request->get('_idUser'));
            $diplome->setUser($user);
            $diplome->setLibelle($request->get('_Libelle'));
            $diplome->setNiveau($request->get('_Niveau'));
            $diplome->setObtention($request->get('_Obtention'));
            $diplome->setEcole($request->get('_Ecole'));

            $em->persist($diplome);
            $em->flush();
        }


        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function AjouterDocumentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if($_FILES['_Document'] and $_FILES['_Document']['size'] > 1000000){
            return new Response('<script language="JavaScript">window.onload = function(){alert("la taille du fichier est trop grande!");window.location.href = "'.$_SERVER['HTTP_REFERER'].'"}</script>');
        }
        if($_FILES['_Document'] and $_FILES['_Document']['size'] > 0){
            $document = new Document();
            $user = $this->getDoctrine()->getRepository('GenericBundle:User')->find($request->get('_idUser'));
            $document->setUser($user);
            $document->setType($request->get('_Type'));
            $document->setExtension($_FILES['_Document']['type']);
            $document->setName($_FILES['_Document']['name']);
            $document->setTaille($_FILES['_Document']['size']);
            $document->setDocument(file_get_contents($_FILES['_Document']['tmp_name']));
            $em->persist($document);
            $em->flush();

        }


        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function AjouterCandidatureAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user= $this->getDoctrine()->getRepository('GenericBundle:User')->find($request->get('_idUser'));

        if($request->get('formations'))
        {
            foreach($request->get('formations') as $idFormation){
                $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->find($idFormation);
                $cand = $this->getDoctrine()->getRepository('GenericBundle:Candidature')->findOneBy(array('user'=>$user,'formation'=>$formation));
                if(!$cand)
                {
                    $candidature = new Candidature();
                    $candidature->setUser($user);
                    $candidature->setFormation($formation);
                    $candidature->setStatut(2);
                    $date = new \DateTime();
                    $candidature->setDatecandidature($date);
                    $em->persist($candidature);
                    $em->flush();
                }

            }
        }


        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function ReponseQCMAction($iduser,$idreponse){
        $em = $this->getDoctrine()->getManager();
        $apprenant = $em->getRepository('GenericBundle:User')->find($iduser);
        $reponse = $em->getRepository('GenericBundle:Reponsedef')->find($idreponse);

        foreach($em->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef'=>$reponse->getQuestiondef())) as $rep)
        {
            if(in_array($rep,$apprenant->getReponsedef()->toArray()))
            {
                $rep->removeUser($apprenant);
            }
        }
        $reponse->addUser($apprenant);
        $em->flush();

        $reponsejson = new JsonResponse();
        return $reponsejson->setData(array('success'=>1));
    }

    public function DownloadDocAction($id){
        $em = $this->getDoctrine()->getManager();
        $document = $em->getRepository('GenericBundle:Document')->find($id);

        // Generate response
        $response = new Response();

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', $document->getType());
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $document->getName() . '";');
        $response->headers->set('Content-length', $document->getTaille());

        // Send headers before outputting anything
        //$response->sendHeaders();
        if(!is_string($document->getDocument())){
            $response->setContent(stream_get_contents( $document->getDocument()));
        }
        return $response;
    }

    public function ChangerStatutApprenantAction($idUser,$statut){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('GenericBundle:User')->find($idUser);
        $reponse = new JsonResponse();
        if($user->getInfo()){
            $user->getInfo()->setProfilcomplet($statut);
            $em->flush();
            return $reponse->setData(1);
        }
        else{
            return $reponse->setData(0);
        }

    }

    public function ChangementRoleAction($idUser){
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $user = $em->getRepository('GenericBundle:User')->find($idUser);
        if($user->hasRole('ROLE_TUTEUR') or $user->hasRole('ROLE_RECRUTEUR')){
            $user->setTier($user->getEtablissement()->getTier());
            $user->setEtablissement(null);
            if($user->hasRole('ROLE_TUTEUR')){
                $user->removeRole('ROLE_TUTEUR');
                $user->addRole('ROLE_ADMINSOC');
            }
            else{
                $user->removeRole('ROLE_RECRUTEUR');
                $user->addRole('ROLE_ADMINECOLE');
            }
            $em->flush();
            return $response->setData(1);
        }
        elseif($user->hasRole('ROLE_ADMINECOLE') or $user->hasRole('ROLE_ADMINSOC')){
            $etablissement = $em->getRepository('GenericBundle:Etablissement')->findBy(array('tier'=>$user->getTier()));

            if(count($etablissement) > 1){
                return $response->setData(0);
            }
            
            $user->setEtablissement($etablissement[0]);
            $user->setTier(null);
            if($user->hasRole('ROLE_ADMINECOLE')){
                $user->removeRole('ROLE_ADMINECOLE');
                $user->addRole('ROLE_RECRUTEUR');
            }
            else{
                $user->removeRole('ROLE_ADMINSOC');
                $user->addRole('ROLE_TUTEUR');
            }
            $em->flush();
            return $response->setData(1);
        }

        return $response->setData("Error");

    }
}