<?php

namespace MissionBundle\Controller;

use GenericBundle\Entity\Diffusion;
use GenericBundle\Entity\Message;
use GenericBundle\Entity\Mission;
use GenericBundle\Entity\MissionPublic;
use GenericBundle\Entity\RecupSociete;
use GenericBundle\Entity\Notification;
use GenericBundle\Entity\ContactSociete;
use GenericBundle\Entity\Postulation;
use GenericBundle\Entity\AjoutManuelle;
use GenericBundle\Entity\Tier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function addMissionAction(Request $request)
    {

       // var_dump($request->get('_Recup'));die;
        $em = $this->getDoctrine()->getEntityManager();

        $usercon = $this->get('security.token_storage')->getToken()->getUser();

        $mission = new Mission();
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_idetab'));

        $mission->setEtablissement($etablissement);

        $mission->setDescriptif($request->get('_Descriptif'));
        $mission->setProfil($request->get('_ProfilRech'));

        //$mission->setTypecontrat($request->get('_TypeContrat'));

        $mission->setTypecontrat($request->get('_Stage').' , '.$request->get('_StageAlterne').' , '.$request->get('_ContratProfessionalisation').' , '.$request->get('_ContratApprentissage'));


        $mission->setDomaine($request->get('_Domaine'));


       if($usercon->getEtablissement()){
            $mission->setTier($usercon->getEtablissement()->getTier());
        }
        elseif($usercon->getTier()){
            $mission->setTier($usercon->getTier());
        }


        if($usercon->hasRole('ROLE_SUPER_ADMIN') or $usercon->hasRole('ROLE_ADMINSOC')){
            $mission->setStatut(3);
        }
        elseif($usercon->hasRole('ROLE_ADMINECOLE') or $usercon->hasRole('ROLE_RECRUTEUR')){
            $mission->setStatut(1);
        }




        $date = new \DateTime();
        $mission->setDatecreation($date);
        if($request->get('_Remuneration') and !$request->get('_Remuneration') == '' ){
            $mission->setRemuneration($request->get('_Remuneration'));
        }
        $mission->setHoraire($request->get('_Horaire'));

        $mission->setDatedebut(date_create_from_format('d/m/Y',$request->get('_Datedebut')) );
        if($request->get('_Datefin') and !$request->get('_Datefin')==''){
            $mission->setDatefin(date_create_from_format('d/m/Y',$request->get('_Datefin')) );
        }


        $mission->setNomcontact($request->get('_NomContact'));
        $mission->setPrenomContact($request->get('_PrenomContact'));
        $mission->setFonctionContact($request->get('_FonctionContact'));
        $mission->setTelContact($request->get('_TelContact'));
        $mission->setEmailContact($request->get('_EmailContact'));
        $mission->setCommentaire($request->get('_Commentaire'));
        $mission->setIntitule($request->get('_Intitule'));
        $mission->setNbreposte($request->get('_NbrePoste'));
        if($request->get('_Embauche') == '1'){
            $mission->setEmploi(true);
        }
        else{
            $mission->setEmploi(false);
        }

        if($request->get('_Metier1') and !$request->get('_Metier1') == '' ){
            $mission->setMetier1($request->get('_Metier1'));
        }
        if($request->get('_Metier2') and !$request->get('_Metier2') == '' ){
            $mission->setMetier2($request->get('_Metier2'));
        }
        if($request->get('_Metier3') and !$request->get('_Metier3') == '' ){
            $mission->setMetier3($request->get('_Metier3'));
        }

        $em->persist($mission);
        $em->flush();
        $mission->genererCode();
        $em->flush();
        if($request->get('_contact') ){
           /* $tuteur = $em->getRepository('GenericBundle:User')->find($request->get('_TUTEURMISSION'));
            $mission->setTuteur($tuteur);
            $em->flush();*/
            if ($request->get('_contact')=='-99'){

                $newcontact = new ContactSociete();

                $newcontact->setEtablissement($etablissement);
                $newcontact->setNom($request->get('_NomContact'));
                $newcontact->setPrenom($request->get('_PrenomContact'));
                $newcontact->setMail($request->get('_EmailContact'));
                $newcontact->setTelephone($request->get('_TelContact'));
                $newcontact->setFonction($request->get('_FonctionContact'));
                $em = $this->getDoctrine()->getManager();
                $em->persist($newcontact);
                $em->flush();

                $contact = $newcontact;


            }
            else
            {

                $contact = $em->getRepository('GenericBundle:ContactSociete')->find($request->get('_contact'));
            }




            if ($contact->getUser() ){
                $mission->setTuteur($contact->getUser());
                $em->flush();



                $MessageTexte='Vous avez été désigné contact de référence pour la mission '.$mission->getIntitule().', vous pouvez vous connecter à la plateforme à l\'adresse hub3e.atpmg.com ';


                $modele = 'GenericBundle:Mail:EmailStandard.html.twig';
                $message = \Swift_Message::newInstance()
                    ->setSubject('Email')
                    ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                    ->setTo($contact->getMail())
                    ->setBody($this->renderView($modele,array('Message'=>$MessageTexte))
                        ,'text/html'
                    );
                $this->get('mailer')->send($message);





            }else{

                // creation de user role_contact_mission
                $Nom=$contact->getNom();
                $Prenom=$contact->getPrenom();


                $userManager = $this->get('fos_user.user_manager');
                $newuser = $userManager->createUser();

                $usernameexist = $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('nom'=>$Nom,'prenom'=>$Prenom));
                    if($usernameexist){
                        $newuser->setUsername($Nom.'.'.$Prenom.count($usernameexist));
                    }
                    else{
                        $newuser->setUsername($Nom.'.'.$Prenom);
                    }
                    $newuser->setEmail($contact->getMail());
                    $newuser->addRole('ROLE_CONTACT_MISSION');
                    //$newuser->setCivilite($request->get('civilite'));
                    $newuser->setTelephone($contact->getTelephone());
                    $newuser->setPrenom($contact->getPrenom());
                    $newuser->setNom($contact->getNom());



                //generate a password
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars
                $hash =  $this->get('security.password_encoder')->encodePassword($newuser, $password);
                $newuser->setPassword($hash);

                $etab = $etablissement;
                $newuser->setEtablissement($etab);



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



                $MessageTexte='Vous avez été désigné contact de référence pour la mission '.$mission->getIntitule().', vos identifiants de connexion sont : '.$newuser->getUsername().' / '.$password.' , vous pouvez vous connecter à la plateforme à l\'adresse hub3e.atpmg.com';

                $modele = 'GenericBundle:Mail:EmailStandard.html.twig';

                $message = \Swift_Message::newInstance()
                    ->setSubject('Email')
                    ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                    ->setTo($contact->getMail())
                    ->setBody($this->renderView($modele,array('Message'=>$MessageTexte))
                        ,'text/html'
                    );
                $this->get('mailer')->send($message);

                $mission->setTuteur($newuser);
                $em->flush();


                $contact->setUser($newuser);
                $em->flush();







            }



        }

        if($request->get('formation')){
            foreach($request->get('formation') as $idFormation)
            {
                $diffuser = new Diffusion();
                $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->find($idFormation);
                $diffuser->setFormation($formation);
                $diffuser->setMission($mission);
                if($this->get('security.authorization_checker')->isGranted('ROLE_RECRUTEUR')){
                    $diffuser->setStatut(5);
                }
                else{
                    $diffuser->setStatut(1);
                }

                $em->persist($diffuser);
                $em->flush();
            }
        }

        if($request->get('reponse')){
            foreach($request->get('reponse') as $rep){
                $reponse = $em->getRepository('GenericBundle:Reponsedef')->find($rep);
                $reponse->addMission($mission);
                $em->flush();

            }
        }


        if ($request->get('_Recup')){
            if ($request->get('_Recup')=='Recup'){

                $recup = new RecupSociete();

                $recup->setEcole($usercon->getEtablissement());
                $recup->setSociete($this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_Societe')));

                $em = $this->getDoctrine()->getManager();
                $em->persist($recup);
                $em->flush();

            }
        }
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }


    public function addMissionUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $usercon = $this->get('security.token_storage')->getToken()->getUser();

        $mission = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($request->get('_idMission'));
        $etablissement = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_idetab'));

        $mission->setEtablissement($etablissement);

        $mission->setDescriptif($request->get('_Descriptif'));
        $mission->setProfil($request->get('_ProfilRech'));

        //$mission->setTypecontrat($request->get('_TypeContrat'));

        $mission->setTypecontrat($request->get('_Stage').' , '.$request->get('_StageAlterne').' , '.$request->get('_ContratProfessionalisation').' , '.$request->get('_ContratApprentissage'));


        $mission->setDomaine($request->get('_Domaine'));


        if($usercon->getEtablissement()){
            $mission->setTier($usercon->getEtablissement()->getTier());
        }
        elseif($usercon->getTier()){
            $mission->setTier($usercon->getTier());
        }


        if($usercon->hasRole('ROLE_SUPER_ADMIN') or $usercon->hasRole('ROLE_ADMINSOC')){
            $mission->setStatut(3);
        }
        elseif($usercon->hasRole('ROLE_ADMINECOLE') or $usercon->hasRole('ROLE_RECRUTEUR')){
            $mission->setStatut(1);
        }






        if($request->get('_Remuneration') and !$request->get('_Remuneration') == '' ){
            $mission->setRemuneration($request->get('_Remuneration'));

        }
        $mission->setHoraire($request->get('_Horaire'));

        $mission->setDatedebut(date_create_from_format('d/m/Y',$request->get('_Datedebut')) );
        if($request->get('_Datefin') and !$request->get('_Datefin')==''){
            $mission->setDatefin(date_create_from_format('d/m/Y',$request->get('_Datefin')) );
        }


        $mission->setNomcontact($request->get('_NomContact'));
        $mission->setPrenomContact($request->get('_PrenomContact'));
        $mission->setFonctionContact($request->get('_FonctionContact'));
        $mission->setTelContact($request->get('_TelContact'));
        $mission->setEmailContact($request->get('_EmailContact'));
        $mission->setCommentaire($request->get('_Commentaire'));
        $mission->setIntitule($request->get('_Intitule'));
        $mission->setNbreposte($request->get('_NbrePoste'));
        if($request->get('_Embauche') == '1'){
            $mission->setEmploi(true);
        }
        else{
            $mission->setEmploi(false);
        }

        if($request->get('_Metier1') and !$request->get('_Metier1') == '' ){
            $mission->setMetier1($request->get('_Metier1'));
        }else{
            $mission->setMetier1(null);
        }
        if($request->get('_Metier2') and !$request->get('_Metier2') == '' ){
            $mission->setMetier2($request->get('_Metier2'));
        }
        else{
            $mission->setMetier2(null);
        }
        if($request->get('_Metier3') and !$request->get('_Metier3') == '' ){
            $mission->setMetier3($request->get('_Metier3'));
        }
        else{
            $mission->setMetier3(null);
        }


        $em->flush();
        $mission->genererCode();
        $em->flush();




        if($request->get('_contact')  ){

            if ($mission->getTuteur()){
                $anciennecontact=$this->getDoctrine()->getRepository('GenericBundle:ContactSociete')->find($mission->getTuteur());

            }else{
                $anciennecontact=null;
            }

            if ($anciennecontact){
                $idAncienContact=$anciennecontact->getId();
            }else{
                $idAncienContact=0;
            }

            if($request->get('_contact')!=strval($idAncienContact))
            {


            if ($request->get('_contact')=='-99'){

                $newcontact = new ContactSociete();

                $newcontact->setEtablissement($etablissement);
                $newcontact->setNom($request->get('_NomContact'));
                $newcontact->setPrenom($request->get('_PrenomContact'));
                $newcontact->setMail($request->get('_EmailContact'));
                $newcontact->setTelephone($request->get('_TelContact'));
                $newcontact->setFonction($request->get('_FonctionContact'));
                $em = $this->getDoctrine()->getManager();
                $em->persist($newcontact);
                $em->flush();

                $contact = $newcontact;


            }
            else
            {

                $contact = $this->getDoctrine()->getRepository('GenericBundle:ContactSociete')->find($request->get('_contact'));

            }





            if ($contact->getUser() ){
           // var_dump($contact->getUser()->getId(),$mission->getTuteur()->getId());die;

                if ($contact->getUser()->getId()!=$mission->getTuteur()->getId()){



                    $MessageTexte='Vous avez été désigné contact de référence pour la mission '.$mission->getIntitule().', vous pouvez vous connecter à la plateforme à l\'adresse hub3e.atpmg.com ';


                    $modele = 'GenericBundle:Mail:EmailStandard.html.twig';
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Email')
                        ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                        ->setTo($contact->getMail())
                        ->setBody($this->renderView($modele,array('Message'=>$MessageTexte))
                            ,'text/html'
                        );
                    $this->get('mailer')->send($message);
                }

                $mission->setTuteur($contact->getUser());
                $em->flush();


            }else{

                // creation de user role_contact_mission
                $Nom=$contact->getNom();
                $Prenom=$contact->getPrenom();


                $userManager = $this->get('fos_user.user_manager');
                $newuser = $userManager->createUser();

                $usernameexist = $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('nom'=>$Nom,'prenom'=>$Prenom));
                if($usernameexist){
                    $newuser->setUsername($Nom.'.'.$Prenom.count($usernameexist));
                }
                else{
                    $newuser->setUsername($Nom.'.'.$Prenom);
                }
                $newuser->setEmail($contact->getMail());
                $newuser->addRole('ROLE_CONTACT_MISSION');
                //$newuser->setCivilite($request->get('civilite'));
                $newuser->setTelephone($contact->getTelephone());
                $newuser->setPrenom($contact->getPrenom());
                $newuser->setNom($contact->getNom());



                //generate a password
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars
                $hash =  $this->get('security.password_encoder')->encodePassword($newuser, $password);
                $newuser->setPassword($hash);

                $etab = $etablissement;
                $newuser->setEtablissement($etab);



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



                $MessageTexte='Vous avez été désigné contact de référence pour la mission '.$mission->getIntitule().', vos identifiants de connexion sont : '.$newuser->getUsername().' / '.$password.' , vous pouvez vous connecter à la plateforme à l\'adresse hub3e.atpmg.com';

                $modele = 'GenericBundle:Mail:EmailStandard.html.twig';

                $message = \Swift_Message::newInstance()
                    ->setSubject('Email')
                    ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                    ->setTo($contact->getMail())
                    ->setBody($this->renderView($modele,array('Message'=>$MessageTexte))
                        ,'text/html'
                    );
                $this->get('mailer')->send($message);

                $mission->setTuteur($newuser);
                $em->flush();


                $contact->setUser($newuser);
                $em->flush();






            }


            }

        }

        if($request->get('formation')){

            foreach($em->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission)) as $Diff)
            {

                    $em->remove($Diff);
                    $em->flush();


            }


            foreach($request->get('formation') as $idFormation)
            {
                $diffuser = new Diffusion();
                $formation = $this->getDoctrine()->getRepository('GenericBundle:Formation')->find($idFormation);
                $diffuser->setFormation($formation);
                $diffuser->setMission($mission);
                if($this->get('security.authorization_checker')->isGranted('ROLE_RECRUTEUR')){
                    $diffuser->setStatut(5);
                }
                else{
                    $diffuser->setStatut(1);
                }

                $em->persist($diffuser);
                $em->flush();
            }
        }

        if($request->get('reponse')){


            $delete = $this->getDoctrine()->getConnection()->prepare("DELETE   FROM reponsedef_mission  WHERE  mission_id=:mission_id ");
            $delete->bindValue('mission_id', $request->get('_idMission'));
            $delete->execute();

            foreach($request->get('reponse') as $rep){
                $reponse = $em->getRepository('GenericBundle:Reponsedef')->find($rep);
                $reponse->addMission($mission);
                $em->flush();

            }
        }
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function affichageMissionFormAction($id,$idFor){

        $em = $this->getDoctrine()->getManager();
        $mission = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($id);

        $formation =$this->getDoctrine()->getRepository('GenericBundle:Formation')->find($idFor);

        $users = array();
        $tuteurs = array();

        foreach($em->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$mission->getEtablissement())) as $users_etablissement)
        {
            if($users_etablissement->hasRole('ROLE_CONTACT_MISSION'))
            {
                array_push($tuteurs,$users_etablissement);
            }
        }

        if($formation){
            $diffusions = $em->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission,'formation'=>$formation));
        }
        else{
            $diffusions = $em->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission));
        }


        foreach($diffusions as $diffusion)
        {
            $Userconnecte = $this->get('security.token_storage')->getToken()->getUser();
            if($Userconnecte->hasRole('ROLE_SUPER_ADMIN') and ($diffusion->getStatut() == 2 or $diffusion->getStatut() == 5)) {
                foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
                {
                    if($candidature->getUser() and $candidature->getUser()->getInfo()->getProfilcomplet() == 3  ){
                        array_push($users,$candidature->getUser());
                    }

                }
            }
            elseif($Userconnecte->hasRole('ROLE_ADMINSOC') and $diffusion->getStatut() == 2)
            {
                foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
                {
                    if($candidature->getUser() and $candidature->getUser()->getInfo()->getProfilcomplet() == 3  ){
                        array_push($users,$candidature->getUser());
                    }

                }
            }
            elseif(($Userconnecte->hasRole('ROLE_ADMINECOLE') or $Userconnecte->hasRole('ROLE_CONTACT_MISSION')) and $Userconnecte->getTier() == $diffusion->getFormation()->getEtablissement()->getTier()){
                foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
                {
                    if($candidature->getUser() and $candidature->getUser()->getInfo()->getProfilcomplet() == 3 ){
                        array_push($users,$candidature->getUser());
                    }
                }
            }

            elseif($Userconnecte->hasRole('ROLE_RECRUTEUR') and $Userconnecte->getEtablissement() == $diffusion->getFormation()->getEtablissement())
            {
                foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
                {
                    if($candidature->getUser() and $candidature->getUser()->getInfo()->getProfilcomplet() == 3 ){
                        array_push($users,$candidature->getUser());
                    }
                }
            }
        }

        $ajoutmanuelle = $this->getDoctrine()->getRepository('GenericBundle:AjoutManuelle')->findBy(array('mission'=>$id));

        foreach($ajoutmanuelle as $ajoutMan)
        {
            array_push($users,$ajoutMan->getApprenant());

        }


        usort($users, array($this, "cmpN"));



        //calcul Score
        $scores = array();
        foreach($users as $apprenant)
        {
            if($apprenant->getPhotos() and !is_string($apprenant->getPhotos()))
            {
                $apprenant->setPhotos(base64_encode(stream_get_contents($apprenant->getPhotos())));
            }
            $scoreapprenant = 0;


            foreach($mission->getReponsedef() as $rep){
                if(in_array($rep,$apprenant->getReponsedef()->toArray())){

                    $scoreapprenant = $scoreapprenant + $rep->getScore();
                }
                else{$scoreapprenant++;}
            }
            array_push($scores,$scoreapprenant);
        }

        if($mission->getEtablissement()->getTier()->getLogo())
        {
            $mission->getEtablissement()->getTier()->setLogo(base64_encode(stream_get_contents($mission->getEtablissement()->getTier()->getLogo())));
        }
        if($mission->getEtablissement()->getTier()->getFondecran())
        {
            $mission->getEtablissement()->getTier()->setFondecran(base64_encode(stream_get_contents($mission->getEtablissement()->getTier()->getFondecran())));
        }



        $formations_prop = null;
        $formations_prop = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findAll();


        $informations_maps = array();
        foreach($users as $user)
        {
            array_push($informations_maps,[$user->getNom() .' '. $user->getPrenom(),$user->getInfo()->getAdresse() .' '. $user->getInfo()->getCp()]);
        }



        $Diffusion = $this->getDoctrine()->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission));

        $miseEnrelation = $this->getDoctrine()->getRepository('GenericBundle:Message')->findBy(array('mission'=>$mission));





        $sql="SELECT Max(M.id) FROM GenericBundle:Message M  GROUP BY M.destinataire,M.mission order by M.id  " ;
        $query = $em->createQuery($sql);
        $max= $query->getResult();



        if(substr($this->array2string($max), 0, -1)==''){

            $Messages = $this->getDoctrine()->getRepository('GenericBundle:Message')->findAll();
        }else{

            $sql="SELECT M FROM GenericBundle:Message M  WHERE M.id in (".substr($this->array2string($max), 0, -1).")  " ;
            $query = $em->createQuery($sql);
            $Messages = $query->getResult();
        }


        $listeApprenanats = array();

        if( !$Userconnecte->hasRole('ROLE_SUPER_ADMIN')){
            $Tier = new Tier();
            if ($this->get('security.token_storage')->getToken()->getUser()->getTier()){

                $Tier=$this->get('security.token_storage')->getToken()->getUser()->getTier();
            }else{

                $Tier=$this->get('security.token_storage')->getToken()->getUser()->getEtablissement()->getTier()->getId();
            }
            foreach($this->getDoctrine()->getRepository('GenericBundle:User')->getUserofTier($Tier) as $apprenanats_etablissement)
            {
                if($apprenanats_etablissement->hasRole('ROLE_APPRENANT'))
                {
                    if (!in_array($apprenanats_etablissement, $users)) {

                        array_push($listeApprenanats,$apprenanats_etablissement);
                    }

                }
            }

            usort($listeApprenanats, array($this, "cmpN"));

            foreach($listeApprenanats as $apprenant)
            {
                if($apprenant->getPhotos() and !is_string($apprenant->getPhotos()))
                {
                    $apprenant->setPhotos(base64_encode(stream_get_contents($apprenant->getPhotos())));
                }

            }
        }








        return $this->render('MissionBundle::afficheMission.html.twig',array('mission'=>$mission,'users'=>$users,'formations_prop'=>$formations_prop,'informations_maps'=>$informations_maps,
            'tuteur_etablissement'=>$tuteurs,'scores'=>$scores,'Diffusions'=>$Diffusion,'miseEnrelation'=>$miseEnrelation,'Messages'=>$Messages,'listeApprenants'=>$listeApprenanats));


    }

    public function affichageMissionPublicFormAction($id,$idFor){

        $em = $this->getDoctrine()->getManager();
        $mission = $this->getDoctrine()->getRepository('GenericBundle:MissionPublic')->find($id);

        $formation =$this->getDoctrine()->getRepository('GenericBundle:Formation')->find($idFor);

        $users = array();
        $tuteurs = array();

        foreach($em->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$mission->getEtablissement())) as $users_etablissement)
        {
            if($users_etablissement->hasRole('ROLE_CONTACT_MISSION'))
            {
                array_push($tuteurs,$users_etablissement);
            }
        }

        if($formation){
            $diffusions = $em->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission,'formation'=>$formation));
        }
        else{
            $diffusions = $em->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission));
        }


        foreach($diffusions as $diffusion)
        {
            $Userconnecte = $this->get('security.token_storage')->getToken()->getUser();
            if($Userconnecte->hasRole('ROLE_SUPER_ADMIN') and ($diffusion->getStatut() == 2 or $diffusion->getStatut() == 5)) {
                foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
                {
                    if($candidature->getUser() and $candidature->getUser()->getInfo()->getProfilcomplet() == 3  ){
                        array_push($users,$candidature->getUser());
                    }

                }
            }
            elseif($Userconnecte->hasRole('ROLE_ADMINSOC') and $diffusion->getStatut() == 2)
            {
                foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
                {
                    if($candidature->getUser() and $candidature->getUser()->getInfo()->getProfilcomplet() == 3  ){
                        array_push($users,$candidature->getUser());
                    }

                }
            }
            elseif(($Userconnecte->hasRole('ROLE_ADMINECOLE') or $Userconnecte->hasRole('ROLE_CONTACT_MISSION')) and $Userconnecte->getTier() == $diffusion->getFormation()->getEtablissement()->getTier()){
                foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
                {
                    if($candidature->getUser() and $candidature->getUser()->getInfo()->getProfilcomplet() == 3 ){
                        array_push($users,$candidature->getUser());
                    }
                }
            }

            elseif($Userconnecte->hasRole('ROLE_RECRUTEUR') and $Userconnecte->getEtablissement() == $diffusion->getFormation()->getEtablissement())
            {
                foreach($em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
                {
                    if($candidature->getUser() and $candidature->getUser()->getInfo()->getProfilcomplet() == 3 ){
                        array_push($users,$candidature->getUser());
                    }
                }
            }
        }

        $ajoutmanuelle = $this->getDoctrine()->getRepository('GenericBundle:AjoutManuelle')->findBy(array('mission'=>$id));

        foreach($ajoutmanuelle as $ajoutMan)
        {
            array_push($users,$ajoutMan->getApprenant());

        }


        usort($users, array($this, "cmpN"));



        //calcul Score
        $scores = array();
        foreach($users as $apprenant)
        {
            if($apprenant->getPhotos() and !is_string($apprenant->getPhotos()))
            {
                $apprenant->setPhotos(base64_encode(stream_get_contents($apprenant->getPhotos())));
            }
            $scoreapprenant = 0;


            foreach($mission->getReponsedef() as $rep){
                if(in_array($rep,$apprenant->getReponsedef()->toArray())){

                    $scoreapprenant = $scoreapprenant + $rep->getScore();
                }
                else{$scoreapprenant++;}
            }
            array_push($scores,$scoreapprenant);
        }

        if($mission->getEtablissement()->getTier()->getLogo())
        {
            $mission->getEtablissement()->getTier()->setLogo(base64_encode(stream_get_contents($mission->getEtablissement()->getTier()->getLogo())));
        }
        if($mission->getEtablissement()->getTier()->getFondecran())
        {
            $mission->getEtablissement()->getTier()->setFondecran(base64_encode(stream_get_contents($mission->getEtablissement()->getTier()->getFondecran())));
        }



        $formations_prop = null;
        $formations_prop = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findAll();


        $informations_maps = array();
        foreach($users as $user)
        {
            array_push($informations_maps,[$user->getNom() .' '. $user->getPrenom(),$user->getInfo()->getAdresse() .' '. $user->getInfo()->getCp()]);
        }



        $Diffusion = $this->getDoctrine()->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission));

        $miseEnrelation = $this->getDoctrine()->getRepository('GenericBundle:Message')->findBy(array('mission'=>$mission));





        $sql="SELECT Max(M.id) FROM GenericBundle:Message M  GROUP BY M.destinataire,M.mission order by M.id  " ;
        $query = $em->createQuery($sql);
        $max= $query->getResult();



        if(substr($this->array2string($max), 0, -1)==''){

            $Messages = $this->getDoctrine()->getRepository('GenericBundle:Message')->findAll();
        }else{

            $sql="SELECT M FROM GenericBundle:Message M  WHERE M.id in (".substr($this->array2string($max), 0, -1).")  " ;
            $query = $em->createQuery($sql);
            $Messages = $query->getResult();
        }


        $listeApprenanats = array();

        if( !$this->get('security.token_storage')->getToken()->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            $Tier = new Tier();
            if ($this->get('security.token_storage')->getToken()->getUser()->getTier()){

                $Tier=$this->get('security.token_storage')->getToken()->getUser()->getTier();
            }else{

                $Tier=$this->get('security.token_storage')->getToken()->getUser()->getEtablissement()->getTier()->getId();
            }
            foreach($this->getDoctrine()->getRepository('GenericBundle:User')->getUserofTier($Tier) as $apprenanats_etablissement)
            {
                if($apprenanats_etablissement->hasRole('ROLE_APPRENANT'))
                {
                    if (!in_array($apprenanats_etablissement, $users)) {

                        array_push($listeApprenanats,$apprenanats_etablissement);
                    }

                }
            }

            usort($listeApprenanats, array($this, "cmpN"));

            foreach($listeApprenanats as $apprenant)
            {
                if($apprenant->getPhotos() and !is_string($apprenant->getPhotos()))
                {
                    $apprenant->setPhotos(base64_encode(stream_get_contents($apprenant->getPhotos())));
                }

            }
        }








        return $this->render('MissionBundle::afficheMission.html.twig',array('mission'=>$mission,'users'=>$users,'formations_prop'=>$formations_prop,'informations_maps'=>$informations_maps,
            'tuteur_etablissement'=>$tuteurs,'scores'=>$scores,'Diffusions'=>$Diffusion,'miseEnrelation'=>$miseEnrelation,'Messages'=>$Messages,'listeApprenants'=>$listeApprenanats));


    }



    public function array2string($data){
        $log_a = "";
        foreach ($data as $key => $value) {
            if(is_array($value))    $log_a .=  $this->array2string($value);
            else                    $log_a .= "'".$value."',";
        }
        return $log_a;
    }

    public function ModifStatutMissionAction($id,$statut)
    {

        $em = $this->getDoctrine()->getManager();
        $mission = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($id);

        $mission->setStatut($statut);
        $em->flush();

       $reponse = new JsonResponse();
        return $reponse->setData(1);

    }

    public function suppMissionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $mis = $em->getRepository('GenericBundle:Mission')->find($id);
        $mis->setSuspendu(true);
        $em->flush();
        $reponse = new JsonResponse();
        return $reponse->setData(array('Status'=>'Mission correctement supprimer'));
    }

    public function missionModifAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $mission = $em->getRepository('GenericBundle:Mission')->find($request->get('_ID'));
        if($request->get('_Descriptif') and !$request->get('_Descriptif')==''){
            $mission->setDescriptif($request->get('_Descriptif'));
        }
        if($request->get('_ProfilRech') and !$request->get('_ProfilRech')==''){
            $mission->setProfil($request->get('_ProfilRech'));
        }
        if($request->get('_Commentaire') and !$request->get('_Commentaire')==''){
            $mission->setCommentaire($request->get('_Commentaire'));
        }
        if($request->get('_TypeContrat') and !$request->get('_TypeContrat')==''){
            $mission->setTypecontrat($request->get('_TypeContrat'));
        }
        if($request->get('_NomContact') and !$request->get('_NomContact')==''){
            $mission->setNomcontact($request->get('_NomContact'));
        }
        if($request->get('_Fonctioncontact') and !$request->get('_Fonctioncontact')==''){
            $mission->setFonctioncontact($request->get('_Fonctioncontact'));
        }
        if($request->get('_Codemission') and !$request->get('_Codemission')==''){
            $mission->setCodemission($request->get('_Codemission'));
        }
        if($request->get('_Emailcontact') and !$request->get('_Emailcontact')==''){
            $mission->setEmailcontact($request->get('_Emailcontact'));
        }
        if($request->get('_Domaine') and !$request->get('_Domaine')==''){
            $mission->setDomaine($request->get('_Domaine'));
        }
        if($request->get('_Datedebut') and !$request->get('_Datedebut')==''){
            $mission->setDatedebut(date_create_from_format('d/m/Y',$request->get('_Datedebut')) );
        }
        if($request->get('_Datefin') and !$request->get('_Datefin')==''){
            $mission->setDatefin(date_create_from_format('d/m/Y',$request->get('_Datefin')) );
        }
        if($request->get('_Remuneration') and !$request->get('_Remuneration')==''){
            $mission->setRemuneration($request->get('_Remuneration'));
        }
        if($request->get('_Nbreposte') and !$request->get('_Nbreposte')==''){
            $mission->setNbreposte($request->get('_Nbreposte'));
        }
        $mission->setDatemodification(date_create());

        $em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }


    public function DiffuserMissionAction($id ,Request $request){
        $em = $this->getDoctrine()->getManager();
        $mission = $em->getRepository('GenericBundle:Mission')->find($id);
        foreach($request->get('formation') as $idformation)
        {
            if($idformation == 'Recherche')
            {
                continue;
            }

            $formation = $em->getRepository('GenericBundle:Formation')->find($idformation);
            $duplicata = $em->getRepository('GenericBundle:Diffusion')->findOneBy(array('formation'=>$formation,'mission'=>$mission));
            if(!$duplicata)
            {
                $diffuser = new Diffusion();
                $diffuser->setFormation($formation);
                $diffuser->setMission($mission);
                if($this->get('security.authorization_checker')->isGranted('ROLE_RECRUTEUR')){
                    $diffuser->setStatut(5);
                }
                else{
                    $diffuser->setStatut(1);
                }
                $em->persist($diffuser);
                $em->flush();
            }
        }
        $response = new JsonResponse();
        return $response->setData(array('status'=>1));
    }

    public function AfficherMissionProposeAction($id)
    {
        $mission = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($id);

        if($mission->getEtablissement()->getTier()->getLogo())
        {
            $mission->getEtablissement()->getTier()->setLogo(base64_encode(stream_get_contents($mission->getEtablissement()->getTier()->getLogo())));
        }
        if($mission->getEtablissement()->getTier()->getFondecran())
        {
            $mission->getEtablissement()->getTier()->setFondecran(base64_encode(stream_get_contents($mission->getEtablissement()->getTier()->getFondecran())));
        }
        $diffusions = $this->getDoctrine()->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission));

        // var_dump($mission);die;
        return $this->render('MissionBundle::MissionProposee.html.twig',array('mission'=>$mission,'diffusions'=>$diffusions));
    }

    public function ValiderDiffusionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $diffusion = $em->getRepository('GenericBundle:Diffusion')->find($id);
        if($diffusion)
        {
            $diffusion->setStatut(2);
            $em->flush();
        }

        $response = new JsonResponse();
        return $response->setData(array('status'=>1));
    }

    public function PostulerAction($id){
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $mission = $em->getRepository('GenericBundle:Mission')->find($id);
        $reponse = new JsonResponse();

        //$postdup = $em->getRepository('GenericBundle:Message')->findOneBy(array('expediteur'=>$user,'mission'=>$mission,'statut'=>7));
        $postdup = $em->getRepository('GenericBundle:Postulation')->findOneBy(array('user'=>$user,'mission'=>$mission));
        if(!$postdup){
                    $postulation = new Postulation();
            $postulation->setUser($this->get('security.token_storage')->getToken()->getUser());
            $postulation->setMission($mission);
            $postulation->setStatut(1);
            //$postulation->setAction('Postuler');
            $em->persist($postulation);
            $em->flush();

            if($user->getEtablissement())
            {
                $destinataires = $em->getRepository('GenericBundle:User')->findByRoles(array('ROLE_RECRUTEUR','ROLE_ADMINECOLE'));
                foreach($destinataires as $destinataire)
                {
                    if(($destinataire->hasRole('ROLE_ADMINECOLE') and $destinataire->getTier() == $user->getEtablissement()->getTier()) or ($destinataire->hasRole('ROLE_RECRUTEUR') and $destinataire->getEtablissement() == $user->getEtablissement()))
                    {
                        $message = new Message();
                        $message->setStatut(7);
                        $message->setExpediteur($user);
                        $message->setMessage('Cette mission m\'intéresse ');
                        $message->setMission($mission);
                        $message->setDestinataire($destinataire);
                        $message->setDate(date_create());
                        $em->persist($message);
                        $em->flush();

                        $mail = \Swift_Message::newInstance()
                            ->setSubject('Postulation')
                            ->setFrom(array('ne-pas-repondre-svp@atpmg.com'=>"HUB3E"))
                            ->setTo($destinataire->getEmail())
                            ->setBody($this->renderView('PostulationSpontanée.html.twig',array('apprenant'=>$user,'mission'=>$mission))
                                ,'text/html'
                            );
                        $this->get('mailer')->send($mail);
                    }
                }
                return $reponse->setData(1);

            }
        }
        return $reponse->setData(0);
    }

    public function AssignerTuteurAction($id,Request $request){
        $mission = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($id);
        if($request->get('tuteur'))
        {
            $tuteur = $this->getDoctrine()->getRepository('GenericBundle:User')->find($request->get('tuteur'));
            if($tuteur)
            {
                $mission->setTuteur($tuteur);
            }
        }

        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }


    function cmp($a, $b)
    {
        return strcmp(mb_strtoupper($a->getPrenom()), mb_strtoupper($b->getPrenom()));
    }
    function cmpN($c,$d){
        return strcmp(mb_strtoupper($c->getPrenom().' '. $c->getNom()), mb_strtoupper($d->getPrenom().' '.$d->getNom()));
    }

    public function AjoutManuelleAction($idApp,$idMission){
        $em = $this->getDoctrine()->getEntityManager();

       $user = $this->getDoctrine()->getRepository('GenericBundle:User')->find($idApp);

       $mission = $this->getDoctrine()->getRepository('GenericBundle:Mission')->find($idMission);

        $ajoutManuelle = new AjoutManuelle();

        $ajoutManuelle->setApprenant($user);
        $ajoutManuelle->setMission($mission);

        $ajoutManuelle->setDate(date_create());
        $em->persist($ajoutManuelle);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status'=>1,'redirect'=>$_SERVER['HTTP_REFERER']));
    }




}


