<?php

namespace EcoleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use GenericBundle\Entity\Diffusion;


class DefaultController extends Controller
{
    public function loadAdminAction()
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($user->getPhotos() and !is_string($user->getPhotos()))
        {
            $user->setPhotos(base64_encode(stream_get_contents($user->getPhotos())));
        }

        $messages = $this->getDoctrine()->getRepository('GenericBundle:Message')->findBy(array('destinataire'=>$user));
        $messageNonLu = 0;
        foreach($messages as $msg){
            if(!$msg->getStatut()==1 and !$msg->getStatut()==-1){
                $messageNonLu++;
            }
        }
        /*$notifications = $this->getDoctrine()->getRepository('GenericBundle:Notification')->findBy(array('user'=>$user));
        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($notifications, 'json');*/

        $ecoles = array();
        $ecoles = array_merge($ecoles,$this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findAdressesOfEcole($user->getTier()->getId()));

        foreach($user->getTier()->getTier1() as $partenaire) {
            $ecoles = array_merge($ecoles, $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findAdressesOfEcole($partenaire->getId()));
        }

        foreach($ecoles as $key => $ecole)
        {
            if($ecole->getSuspendu())
            {
                unset($ecoles[$key]);
            }
        }

        $missions_propose = array();
        $mes_missions = array();
        foreach($ecoles as $ecole)
        {
            $formations = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findBy(array('etablissement'=>$ecole));
            foreach($formations as $formation)
            {
                $diffusions = $this->getDoctrine()->getRepository('GenericBundle:Diffusion')->findBy(array('formation'=>$formation));
                foreach($diffusions as $diffusion)
                {
                    if((($diffusion->getMission()->getStatut() == 1 or $diffusion->getMission()->getStatut() == 2) and $diffusion->getMission()->getTier() == $user->getTier())
                        or ($diffusion->getMission()->getStatut() == 2 and in_array($user->getTier(),$diffusion->getMission()->getTier()->getTier1()->toArray()) ) or $diffusion->getMission()->getStatut() == 3){
                        if($diffusion->getStatut()==5 and !in_array($diffusion->getMission(), $mes_missions))
                        {
                            array_push($mes_missions,$diffusion->getMission());
                        }
                        elseif($diffusion->getStatut()==1 and !in_array($diffusion->getMission(), $mes_missions)){
                            array_push($missions_propose,$diffusion->getMission());
                        }
                    }

                }
            }
        }

        $users = $this->getDoctrine()->getRepository('GenericBundle:User')->getUserofTier($user->getTier());
        $apprenants =array();
        $notapprenant = array();
        foreach($users as $userd)
        {
            if($userd->hasRole('ROLE_APPRENANT'))
            {
                array_push($apprenants,$userd);
            }
            else{
                array_push($notapprenant,$userd);
            }
        }

        $licences = $this->getDoctrine()->getRepository('GenericBundle:Licence')->findBy(array('tier'=>$user->getTier(),'suspendu'=>false));
        //$missions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('suspendu'=>false),array('date'=>'DESC'));



        return $this->render('EcoleBundle:Adminecole:index.html.twig', array('ecoles'=>$ecoles,/*'notifications'=>$jsonContent ,*/'users'=>$notapprenant,'AllLicences'=>$licences,
            'societes'=>$user->getReferenciel(),'missions'=>$mes_missions,'missions_propose'=>$missions_propose,'apprenants'=>$apprenants,'image'=>$user->getPhotos(),'messages'=>$messageNonLu));
    }

    public function affichageLicenceAction($id)
    {
        $licence = $this->getDoctrine()->getRepository('GenericBundle:Licence')->find($id);

        return $this->render('EcoleBundle:Adminecole:afficheLicence.html.twig',array('licence'=>$licence));
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

    public function loadQCMAction($id)
    {

            $qcm = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->find($id);
            $questions = $this->getDoctrine()->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef'=>$qcm));
            usort($questions,array('\GenericBundle\Entity\Questiondef','sort_questions_by_order'));
            $reponses = array(array());
            for($i = 0; $i < count($questions); $i++)
            {
                $reps = $this->getDoctrine()->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef'=>$questions[$i]));
                usort($reps,array('\GenericBundle\Entity\Reponsedef','sort_reponses_by_order'));
                $reponses[] = $reps;
            }
            return $this->render('EcoleBundle:Adminecole:LoadQCM.html.twig', array('QCM'=>$qcm ,'Questions'=>$questions,'reponses'=>$reponses));
        }

    public function affichageTableauBordAction()
    {
        $userid = $this->get('security.token_storage')->getToken()->getUser();
        $etablissement=$userid->getEtablissement();
        $tiercreation=$userid->getetablissement()->gettier()->getId();
        $apprenants = $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$etablissement));
        $formations = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findBy(array('etablissement'=>$etablissement));
        $Allapprenants =array();
        $Importcandidat = $this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->findAll();
        // var_dump($etablissement->getId());die;

        foreach($apprenants as $userd)
        {              
            if($userd->hasRole('ROLE_APPRENANT'))
            {
                array_push($Allapprenants,$userd);
            }              
        }

        $TousLesApprenants=$Allapprenants;
        $Myapprenantplacer=$Allapprenants;
        foreach($Importcandidat as $userd)
        {
            array_push($Allapprenants,$userd);
        }

        // Postulation en cours

        $Postulation = $this->getDoctrine()->getRepository('GenericBundle:Postulation')->findAll();
        $MyPostulation = array();

        $PostulationEnCours=round((count($Myapprenantplacer)*100)/count($Allapprenants), 2);

        // formation à valider

        $em = $this->getDoctrine()->getEntityManager();

        $sql="SELECT C FROM GenericBundle:Candidature C WHERE C.statut=:statut and C.user is not null GROUP BY C.user,C.statut " ;
        $query = $em->createQuery($sql);
        $query->setParameter('statut', '2');
        $FormationCandidature = $query->getResult(); // array of ForumUser objects

        $MyFormationAvalider =array();
        foreach($FormationCandidature as $formation)
        {
            if(!($Allapprenants))
            {
            }else{
                if( in_array($formation->getuser(),$Allapprenants)  )
                {
                    array_push($MyFormationAvalider,$formation);
                }
            }    

        }

        $FormationAvalider=round((count($MyFormationAvalider)*100)/count($Allapprenants), 2);

       // apprenants à placer

        $MyapprenantAplacer=$Importcandidat;
        $ApprenantAplacer=round((count($Importcandidat)*100)/count($Allapprenants), 2);


       // Apprenants mis en relation

        $sql="SELECT M FROM GenericBundle:Message M   GROUP BY M.destinataire " ;
        $query =$em->createQuery($sql);

        $ApprenantMiseRelation = $query->getResult();

        $MyApprenantRelation=array();
        foreach($ApprenantMiseRelation as $appMise)
        {
            if(!($Allapprenants))
            {
            }else{
                if( in_array($appMise->getDestinataire(),$Allapprenants)  )
                {
                    array_push($MyApprenantRelation,$appMise);
                }
            }    

        }


        $ApprenantRelation=round((count($MyApprenantRelation)*100)/count($Allapprenants), 2);



       //Apprenants placés refaire le calcul une fois le process terminé sur la plateforme 

        $Myapprenantplacer=$Myapprenantplacer;
        $Apprenantplacer='0';
        // $Apprenantplacer=round((count($Myapprenantplacer)*100)/count($Allapprenants), 2);


       // Missions à pourvoir ( RESTE A REGLER CETTE PUTIN DE VARIABLE $ETABLISSEMENT QUI REND FOU)

        $Allmissions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('tier'=>$tiercreation));

        $Mymissionapourvoir=array();

        foreach($Allmissions as $missionap)
        {
            if(!($Allmissions))
            {
            }else{
                if($missionap.'statut' != 2) 
                {
                    array_push($Mymissionapourvoir,$missionap);
                }
            }    
        }

        // var_dump(count($Allmissions));die;
      
        $Missionapourvoir=round((count($Mymissionapourvoir)*100)/count($Allmissions), 2);

        // Missions pourvues

        $Mymissionspourvues=array();

        foreach($Allmissions as $missionp)
        {
            if(!($Allmissions))
            {
            }else{
                if($missionp.'statut' == 2) 
                {
                    array_push($Mymissionspourvues,$missionp);
                }
            }    
        }

        // var_dump(count($Mymissionspourvues));die;
      
        $MissionsPourvues=round((count($Mymissionspourvues)*100)/count($Allmissions), 2); 

        // Missions sans formations Pourquoi mon $mymissionssansformations me renvoi 4 ???

        $Mymissionssansformations=array();

        foreach($Allmissions as $missionsf)
        {  

        // Code Abdellah à mettre dans le foreach pour tenter de résoude le problème lié au getStatut()   
        $em = $this->getDoctrine()->getEntityManager();
        $sql="SELECT C.statut FROM GenericBundle:Diffusion C WHERE C.mission=:mission ";
        $query = $em->createQuery($sql);
        $query->setParameter('mission', $missionsf->getId());
        $Statut = $query->getResult(); // array of ForumUser object

            if(!($Allmissions))
            {
            }else{    
                if ($Statut)
                {
                    if( $Statut == 5)
                    {   
                    // var_dump(count($missionsf));die;  
                    }else{
                        array_push($Mymissionssansformations,$missionsf);
                    } 
                }
            }
            
        }
  
        // var_dump(count($Allmissions),count($Mymissionssansformations));die;
        // var_dump(count($Mymissionssansformations));die;

        $MissionsSansFormations=round((count($Mymissionssansformations)*100)/count($Allmissions), 2);

        // Missions sans tuteur ( toujours le même problème rien ne se push dans mon array )

        $Mymissionssanstuteur=array();

        foreach($Allmissions as $missionst)
        {
            if(!($Allmissions))
            {
            }else{
                if($missionst.'tuteur_id' == null )
                {
                    array_push($Mymissionssanstuteur,$missionst);
                } 
            }       
        }

        // var_dump(count($Mymissionssanstuteur));die;

        $MissionsSansTuteur=round((count($missionst.'tuteur_id' == null)*100)/count($Allmissions),2);

        // Missions Sans QCM REMPLACER LE $Mymissionssansformations une fois la relation Mission -> Formation -> Qcm gérée

        // $MymissionsssansQCM=array();

        // foreach($Allmissions as missionsq)
        // {
        //     if($missionsq.)
        // }

        $MissionsSansQcm=round((count($Mymissionssansformations)*100)/count($Allmissions),2); 

        $CountLesMissions=count($Allmissions);   
        $CountLesApprenants=count($TousLesApprenants)+count($Importcandidat);

        return $this->render('EcoleBundle:Recruteur:TableauBord.html.twig', array(
            'etablissement'=>$etablissement,
            'formations'=>$formations,
            'PostulationEnCours'=>$PostulationEnCours,'MyPostulation'=>$MyPostulation,
            'FormationAvalider'=>$FormationAvalider,'MyFormationAvalider'=>$MyFormationAvalider,
            'ApprenantAplacer'=>$ApprenantAplacer,'MyapprenantAplacer'=>$MyapprenantAplacer,
            'ApprenantRelation'=>$ApprenantRelation,'MyApprenantRelation'=>$MyApprenantRelation,
            'Apprenantplacer'=>$Apprenantplacer,
            'Myapprenantplacer'=>$Myapprenantplacer,
            'MissionAPourvoir'=>$Missionapourvoir,'Mymissionapourvoir'=>$Mymissionapourvoir,
            'MissionsPourvues'=>$MissionsPourvues, 'Mymissionspourvues'=>$Mymissionspourvues,
            'MissionsSansFormations'=>$MissionsSansFormations, 'Mymissionssansformations'=>$Mymissionssansformations,
            'MissionsSansTuteur'=>$MissionsSansTuteur, 'Mymissionssanstuteur'=>$Mymissionssanstuteur,
            'MissionsSansQcm'=>$MissionsSansQcm, //'Mymissionssansqcm'=>$Mymissionssansqcm,
            'CountLesMissions'=>$CountLesMissions,
            'CountLesApprenants'=>$CountLesApprenants,

        ));

    }


}
