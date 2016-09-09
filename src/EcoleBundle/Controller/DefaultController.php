<?php

namespace EcoleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use GenericBundle\Entity\Diffusion;
use GenericBundle\Entity\MissionPublic;


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
        $societes =array();
        $societes = array_merge($societes,$user->getReferenciel()->toArray());
        $etablissement=$user->getEtablissement();
        $recupsocietes=$this->getDoctrine()->getRepository('GenericBundle:RecupSociete')->findBy(array('ecole'=>$etablissement));

        foreach($recupsocietes as $Recup)
        {
            array_push($societes, $Recup->getSociete());
        }

        $uniquesocietes = array_unique($societes);

       /// $licences = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        //$missions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('suspendu'=>false),array('date'=>'DESC'));

        return $this->render('EcoleBundle:Adminecole:index.html.twig', array('ecoles'=>$ecoles,/*'notifications'=>$jsonContent ,*/'users'=>$notapprenant,
            'societes'=>$uniquesocietes,'missions'=>$mes_missions,'missions_propose'=>$missions_propose,'apprenants'=>$apprenants,'image'=>$user->getPhotos(),'messages'=>$messageNonLu));


       // return $this->render('EcoleBundle:Adminecole:index.html.twig', array('ecoles'=>$ecoles,/*'notifications'=>$jsonContent ,*/'users'=>$notapprenant,'AllLicences'=>$licences,
       //     'societes'=>$user->getReferenciel(),'missions'=>$mes_missions,'missions_propose'=>$missions_propose,'apprenants'=>$apprenants,'image'=>$user->getPhotos(),'messages'=>$messageNonLu));
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

        $MissionPublic =$this->getDoctrine()->getRepository('GenericBundle:MissionPublic')->findBy([], ['id' => 'DESC']);
        $Allapprenants = array();
        $TotalApprenants = array();
        $Importcandidat = $this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->findBy(array('etablissement'=>$etablissement));
        // var_dump($etablissement->getId());die;

        foreach($apprenants as $useru)
        {
            if($useru->hasRole('ROLE_APPRENANT'))
            {
                array_push($TotalApprenants,$useru);
            }
        }

        foreach($apprenants as $userd)
        {              
            if($userd->hasRole('ROLE_APPRENANT') and $userd->getInfo()->getProfilcomplet()==3)
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

        // POSTULATION EN COURS

        $Postulation = $this->getDoctrine()->getRepository('GenericBundle:Postulation')->findAll();
        $MyPostulation = array();
        $PostulEnCours = array();

        foreach($apprenants as $userz)
        {              
            if($userz->hasRole('ROLE_APPRENANT') and $userz->getInfo()->getProfilcomplet() == 0)
            {
                array_push($PostulEnCours,$userd);
            }              
        }
//var_dump($TotalApprenants);die;
if ($TotalApprenants){
    $PostulationEnCours=round((count($PostulEnCours)*100)/count($TotalApprenants), 2);
}else{
    $PostulationEnCours=0;
}
        

        // FORMATION A VALIDER

        $MyFormationAvalider = array();

        foreach($apprenants as $userabc)
        {              
            if($userabc->hasRole('ROLE_APPRENANT') and $userabc->getInfo()->getProfilcomplet() != 3)
            {
                array_push($MyFormationAvalider,$userabc);
            }              
        }

        if ($TotalApprenants){
    
    $FormationAvalider=round((count($MyFormationAvalider)*100)/count($TotalApprenants), 2);
}else{
    $FormationAvalider=0;
}

        

        $em = $this->getDoctrine()->getEntityManager();

       // APPRENANTS A PLACER

        $MyapprenantAplacer= array();
        foreach($TousLesApprenants as $userd)
        {
            if($userd->getPlace()==null or $userd->getPlace()==0)
            {
                array_push($MyapprenantAplacer,$userd);
            }
        }

        // var_dump(count($MyapprenantAplacer));die;
        if(!($MyapprenantAplacer))
        {
            $ApprenantAplacer= '0';  
        }else{             
            $ApprenantAplacer=round((count($MyapprenantAplacer)*100)/count($TotalApprenants),2);
        }


       // APPRENANTS MIS EN RELATION

        $sql="SELECT M FROM GenericBundle:Message M   GROUP BY M.destinataire " ;
        $query =$em->createQuery($sql);

        $ApprenantMiseRelation = $query->getResult();

        $MyApprenantRelation=array();
        foreach($ApprenantMiseRelation as $appMise)
        {
            if(!($TousLesApprenants))
            {
            }else{
                if( in_array($appMise->getDestinataire(),$TousLesApprenants)  )
                {
                    array_push($MyApprenantRelation,$appMise);
                }
            }    

        }

        if ($TotalApprenants){
            $ApprenantRelation=round((count($MyApprenantRelation)*100)/count($TotalApprenants), 2);
        }else{
            $ApprenantRelation=0;
        }
        

       // APPRENANTS PLACES

        $MyApprenantPlacer= array();
        foreach($TotalApprenants as $userd)
        {
            if($userd->getPlace()==1)
            {
                array_push($MyApprenantPlacer,$userd);
            }
        }

        if(!($MyApprenantPlacer))
        {
            $Apprenantplacer= '0';  
        }else{             
            $Apprenantplacer=round((count($MyApprenantPlacer)*100)/count($TotalApprenants),2);
        }

       // MISSIONS A POURVOIR

        $Allmissions = $this->getDoctrine()->getRepository('GenericBundle:Mission')->findBy(array('tier'=>$tiercreation));

        $sql = " SELECT M FROM GenericBundle:Mission M WHERE M.pourvue is NUll and M.tier=:tier GROUP BY M.id ";
        $query = $em->createQuery($sql);
        $query->setParameter('tier', $tiercreation);
        $MyMissionsAP = $query->getResult();
        //var_dump(count($Allmissions));die;

        if(!($MyMissionsAP))
        {
            $MissionsAPourvoir= '0';  
        }else{             
            $MissionsAPourvoir=round((count($MyMissionsAP)*100)/count($Allmissions),2);
        }
      
        // MISSIONS POURVUES

        $sql = " SELECT M FROM GenericBundle:Mission M WHERE M.pourvue= 1 GROUP BY M.id ";
        $query = $em->createQuery($sql);
        $MyMissionsPourvues = $query->getResult();

        if(!($Allmissions))
        {
            $MissionsPourvues= '0';  
        }else{             
            $MissionsPourvues=round((count($MyMissionsPourvues)*100)/count($Allmissions),2);
        }

        // MISSIONS SANS FORMATIONS 

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
                if(!$Statut)
                {   
                    array_push($Mymissionssansformations,$missionsf);
                } 
            }  
        }

        if($Allmissions){
            $MissionsSansFormations=round((count($Mymissionssansformations)*100)/count($Allmissions), 2);
        }else{
            $MissionsSansFormations=0;
        }

       
        // MISSIONS SANS TUTEUR

        $Mymissionssanstuteur=array();

        foreach($Allmissions as $missionst)
        {
            if(!($Allmissions))
            {
            }else{
                if(!($missionst->getTuteur()))
                {
                    array_push($Mymissionssanstuteur,$missionst);
                } 
            }       
        }

        if($Allmissions){
            $MissionsSansTuteur=round((count($Mymissionssanstuteur)*100)/count($Allmissions),2);
        }else{
            $MissionsSansTuteur=0;
        }
        

        // MISSIONS SANS QCM ( comment récupérer le fait que le qcm ne soit pas remplis ?)

        // $MymissionsssansQCM=array();

        // foreach($Allmissions as missionsq)
        // {
        //     if($missionsq.)
        // }

if($Allmissions){
            $MissionsSansQcm=round((count($Mymissionssansformations)*100)/count($Allmissions),2); 

        }else{
            $MissionsSansQcm=0;
        }
        
        

        $CountLesMissions=count($Allmissions);   
        $CountLesApprenants=count($TousLesApprenants)+count($Importcandidat);


<<<<<<< HEAD


=======
//var_dump(count($MyApprenantRelation));die;
>>>>>>> origin/DjibrilLinkPart
        return $this->render('EcoleBundle:Recruteur:TableauBord.html.twig', array(
            'etablissement'=>$etablissement,
            'formations'=>$formations,
            'PostulationEnCours'=>$PostulationEnCours,'MyPostulation'=>$MyPostulation,
            'FormationAvalider'=>$FormationAvalider,'MyFormationAvalider'=>$MyFormationAvalider,
            'ApprenantAplacer'=>$ApprenantAplacer,'MyapprenantAplacer'=>$MyapprenantAplacer,
            'ApprenantRelation'=>$ApprenantRelation,'MyApprenantRelation'=>$MyApprenantRelation,
            'Apprenantplacer'=>$Apprenantplacer,
            'Myapprenantplacer'=>$Myapprenantplacer,
            'MissionsAPourvoir'=>$MissionsAPourvoir,//'Mymissionsapourvoir'=>$Mymissionsapourvoir,
            'MissionsPourvues'=>$MissionsPourvues, //'Mymissionspourvues'=>$Mymissionspourvues,
            'MissionsSansFormations'=>$MissionsSansFormations, 'Mymissionssansformations'=>$Mymissionssansformations,
            'MissionsSansTuteur'=>$MissionsSansTuteur, 'Mymissionssanstuteur'=>$Mymissionssanstuteur,
            'MissionsSansQcm'=>$MissionsSansQcm, //'Mymissionssansqcm'=>$Mymissionssansqcm,
            'CountLesMissions'=>$CountLesMissions,
            'CountLesApprenants'=>$CountLesApprenants,
            'missions'=>$Allmissions,
            'MissionsPublic'=>$MissionPublic


        ));

    }


}
