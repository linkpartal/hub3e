<?php
namespace GenericBundle\Twig;

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

class globalExtension extends \Twig_Extension{

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return array(
            'CountMission' => new \Twig_Function_Method($this, 'CountMission'),
            'unique_multidim_array' => new \Twig_Function_Method($this, 'unique_multidim_array'),
            'GetMissionSansformation' => new \Twig_Function_Method($this, 'GetMissionSansformation'),
            'GetMyPhoto' => new \Twig_Function_Method($this, 'GetMyPhoto'),
            'GetDateRDV' => new \Twig_Function_Method($this, 'GetDateRDV'),
            'GetStatutRDV' => new \Twig_Function_Method($this, 'GetStatutRDV'),
            'affichageMission' => new \Twig_Function_Method($this, 'affichageMission'),
            'affichageProfil' => new \Twig_Function_Method($this, 'affichageProfil'),
            'GetStatutTuteurRDV' => new \Twig_Function_Method($this, 'GetStatutTuteurRDV'),
            'GetFormationCommun' => new \Twig_Function_Method($this, 'GetFormationCommun'),
            'GetMissionSansformation' => new \Twig_Function_Method($this, 'GetMissionSansformation'),
            'GetRDV' => new \Twig_Function_Method($this, 'GetRDV'),
            'GetIdTopOfMessges' => new \Twig_Function_Method($this, 'GetIdTopOfMessges'),

        );
    }

    function GetMissionSansformation($Tier)
    {

        $Allmissions = $this->em->getRepository('GenericBundle:Mission')->findBy(array('tier'=>$Tier));
        $Mymissionssansformations=array();
            
        foreach($Allmissions as $missionsf)
        { 
            $sql="SELECT C.statut FROM GenericBundle:Diffusion C WHERE C.mission=:mission ";
            $query = $this->em->createQuery($sql);
            $query->setParameter('mission', $missionsf->getId());
            $Statut = $query->getResult(); // array of ForumUser object

                if(!($Allmissions))
                {
                }else{    
                    if (!$Statut)
                    {
                          
                        // var_dump(count($missionsf));die;  
                        
                            array_push($Mymissionssansformations,$missionsf);
                        
                    }
                }
                    
        }
            return array('mission'=>$Mymissionssansformations);
    }

    function GetFormationAValider($tier)
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
            return array('apprenant'=>$MyFormationAvalider);

    }


    function GetFormationCommun($idapp,$idMission){

        $Diffusion = $this->em->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$idMission));
        $Candidature = $this->em->getRepository('GenericBundle:Candidature')->findBy(array('user'=>$idapp));


        $Formation=null;
        $FormationDiff=array();

        foreach( $Diffusion as $forma){

            array_push($FormationDiff,$forma->getFormation()->getId());

        }
        $FormationCand=array();
        foreach( $Candidature as $forma){

            array_push($FormationCand,$forma->getFormation()->getId());

        }
        $Formations = array_intersect( $FormationDiff,$FormationCand);

        // normalement ca peut êtres plusieurs formation en commun mais pour l'instant on affihce juste la premiere formation $Formations[0]

       /* foreach( $Formations as $forma){
            $Formation=$Formation.';;'.$forma.';;';
        }*/

        $Myformation = $this->em->getRepository('GenericBundle:Formation')->find(array('id'=>$Formations[0]));

        $Formation=$Myformation->getNom();




        return $Formation;
    }


    function GetMyPhoto($id){

        $user = $this->em->getRepository('GenericBundle:User')->findOneBy(array('id'=>$id));
        //var_dump($apprenants->getPhotos());die;

            if($user->getPhotos() and !is_string($user->getPhotos()))
            {
                $user->setPhotos(base64_encode(stream_get_contents($user->getPhotos())));
            }

        return $user->getPhotos();

    }

    function GetDateRDV($idapp,$idMission){

        $rdv = $this->em->getRepository('GenericBundle:RDV')->findOneBy(array('apprenant'=>$idapp,'mission'=>$idMission));
        $Dates=null;
        if ($rdv->getDate1()){
              $Dates=$Dates.$rdv->getDate1()->format('Y-m-d à H:i');
        }
        if ($rdv->getDate2()){
            $Dates=$Dates.' ou '.$rdv->getDate2()->format('Y-m-d à H:i');
        }
        if ($rdv->getDate3()){
            $Dates=$Dates.' ou '.$rdv->getDate3()->format('Y-m-d à H:i');
        }
        return $Dates;
    }


    function GetRDV($idapp,$idMission){

        $rdv = $this->em->getRepository('GenericBundle:RDV')->findOneBy(array('apprenant'=>$idapp,'mission'=>$idMission));

        return $rdv;
        return array('RDV'=>$rdv);
    }


    function CountMission($destinataire){


        $sql="SELECT M FROM GenericBundle:Message M  WHERE M.destinataire=:destinataire GROUP BY M.destinataire,M.mission " ;
        $query = $this->em->createQuery($sql);
        $query->setParameter('destinataire', $destinataire);
        $Messages = $query->getResult(); // array of ForumUser objects
        return count($Messages);
    }

    function GetStatutRDV($idApp,$idMission){

        $rdv = $this->em->getRepository('GenericBundle:RDV')->findOneBy(array('apprenant'=>$idApp,'mission'=>$idMission));

        if (count($rdv)==0){
            return 'ouui';
        }else{
            return $rdv->getStatut();
        }

    }
    function GetStatutTuteurRDV($idtuteur,$idMission){

        $rdv = $this->em->getRepository('GenericBundle:RDV')->findOneBy(array('tuteur'=>$idtuteur,'mission'=>$idMission));

        if (count($rdv)==0){
            return 'ouui';
        }else{
            return $rdv->getStatut();
        }

    }

    function affichageMission($id,$idFor,$utilisateurId){



       $mission = $this->em->getRepository('GenericBundle:Mission')->find($id);

       $formation =$this->em->getRepository('GenericBundle:Formation')->find($idFor);

       $users = array();
       $tuteurs = array();
       foreach($this->em->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$mission->getEtablissement())) as $users_etablissement)
       {
           if($users_etablissement->hasRole('ROLE_TUTEUR'))
           {
               array_push($tuteurs,$users_etablissement);
           }
       }

       if($formation){
           $diffusions = $this->em->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission,'formation'=>$formation));
       }
       else{
           $diffusions = $this->em->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission));
       }



      foreach($diffusions as $diffusion)
       {
           $Userconnecte=$this->em->getRepository('GenericBundle:User')->find($utilisateurId);
           if($Userconnecte->hasRole('ROLE_SUPER_ADMIN') and ($diffusion->getStatut() == 2 or $diffusion->getStatut() == 5)) {
               foreach($this->em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
               {
                   if($candidature->getUser() and $candidature->getUser()->getInfo()->getProfilcomplet() == 3  ){
                       array_push($users,$candidature->getUser());
                   }

               }
           }
           elseif($Userconnecte->hasRole('ROLE_ADMINSOC') and $diffusion->getStatut() == 2)
           {
               foreach($this->em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
               {
                   if($candidature->getUser() and $candidature->getUser()->getInfo()->getProfilcomplet() == 3  ){
                       array_push($users,$candidature->getUser());
                   }

               }
           }
           elseif($Userconnecte->hasRole('ROLE_ADMINECOLE') and $Userconnecte->getTier() == $diffusion->getFormation()->getEtablissement()->getTier()){
               foreach($this->em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
               {
                   if($candidature->getUser() and $candidature->getUser()->getInfo()->getProfilcomplet() == 3 ){
                       array_push($users,$candidature->getUser());
                   }
               }
           }

           elseif($Userconnecte->hasRole('ROLE_RECRUTEUR') and $Userconnecte->getEtablissement() == $diffusion->getFormation()->getEtablissement())
           {
               foreach($this->em->getRepository('GenericBundle:Candidature')->findBy(array('formation'=>$diffusion->getFormation(),'statut'=>3)) as $candidature)
               {
                   if($candidature->getUser() and $candidature->getUser()->getInfo()->getProfilcomplet() == 3 ){
                       array_push($users,$candidature->getUser());
                   }
               }
           }
       }


             // usort($users, array($this, "cmpN"));


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
              $formations_prop = $this->em->getRepository('GenericBundle:Formation')->findAll();


              $informations_maps = array();
              foreach($users as $user)
              {
                  array_push($informations_maps,[$user->getNom() .' '. $user->getPrenom(),$user->getInfo()->getAdresse() .' '. $user->getInfo()->getCp()]);
              }



                      $Diffusion = $this->em->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$mission));

                      $miseEnrelation = $this->em->getRepository('GenericBundle:Message')->findBy(array('mission'=>$mission));





                              $sql="SELECT Max(M.id) FROM GenericBundle:Message M  GROUP BY M.destinataire,M.mission order by M.id  " ;
                              $query = $this->em->createQuery($sql);
                              $max= $query->getResult();








                                     if(substr($this->array2string($max), 0, -1)==''){

                                         $Messages = $this->em->getRepository('GenericBundle:Message')->findAll();
                                     }else{

                                         $sql="SELECT M FROM GenericBundle:Message M  WHERE M.id in (".substr($this->array2string($max), 0, -1).")  " ;
                                         $query = $this->em->createQuery($sql);
                                         $Messages = $query->getResult();
                                     }


                                     $CountMessions = array();
                                     foreach($users as $apprenant)
                                     {

                                         array_push($CountMessions,$apprenant);
                                     }



                              //var_dump($Messages);die;

        return array('mission'=>$mission,'users'=>$users,'formations_prop'=>$formations_prop,'informations_maps'=>$informations_maps,
            'tuteur_etablissement'=>$tuteurs,'scores'=>$scores,'Diffusions'=>$Diffusion,'miseEnrelation'=>$miseEnrelation,'Messages'=>$Messages);


    }

    public function array2string($data){
        $log_a = "";
        foreach ($data as $key => $value) {
            if(is_array($value))    $log_a .=  $this->array2string($value);
            else                    $log_a .= "'".$value."',";
        }
        return $log_a;
    }

    public function affichageProfil($idUser)
    {


        $userid = $this->em->getRepository('GenericBundle:User')->find($idUser);

        // chargement des images
         if($userid->getPhotos() and !is_string($userid->getPhotos()))
          {
              $userid->setPhotos(base64_encode(stream_get_contents($userid->getPhotos())));
          }


          if($userid->hasRole('ROLE_APPRENANT')){
              $info = $userid->getInfo();
              $Parents = $this->em->getRepository('GenericBundle:Parents')->findBy(array('user'=>$userid));

              $formation = $this->em->getRepository('GenericBundle:Formation')->findAll();

              $Experience = $this->em->getRepository('GenericBundle:Experience')->findBy(array('user'=>$userid));
              $Recommandation = $this->em->getRepository('GenericBundle:Recommandation')->findBy(array('user'=>$userid));
              $Diplome = $this->em->getRepository('GenericBundle:Diplome')->findBy(array('user'=>$userid));
              $Document = $this->em->getRepository('GenericBundle:Document')->findBy(array('user'=>$userid));
              $candidatures = $this->em->getRepository('GenericBundle:Candidature')->findBy(array('user'=>$userid));

              if ($userid->getEtablissement()) {
                  $questions = array();
                  $reponses = array();
                  foreach ($userid->getEtablissement()->getQcmdef() as $key => $qcm) {

                      $questions[$key] = $this->em->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef' => $qcm));
                      usort($questions[$key], array('\GenericBundle\Entity\Questiondef', 'sort_questions_by_order'));
                      foreach ($questions[$key] as $keyqst => $qst) {
                          $reps = $this->em->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef' => $qst));
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
                      $questionsTest[$index] = $this->em->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef' => $qcm));
                      usort($questionsTest[$index], array('\GenericBundle\Entity\Questiondef', 'sort_questions_by_order'));
                      foreach ($questionsTest[$index] as $keyqst => $qst) {
                          $reps = $this->em->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef' => $qst));
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

                  return  array('User' => $userid,
                      'Infocomplementaire' => $info, 'Parents' => $Parents, 'Experience' => $Experience, 'Recommandation' => $Recommandation, 'Diplome' => $Diplome, 'Document' => $Document,
                      'candidatures' => $candidatures, 'QCMs' => $userid->getEtablissement()->getQcmdef(), 'Questions' => $questions,
                      'reponses' => $reponses, 'QCMtest' => $QCMtest, 'QuestionsTest' => $questionsTest, 'reponsesTest' => $reponsesTest,'formations'=>$formation);
              }
          }

        return array('User'=>$userid);

    }

   public function GetIdTopOfMessges($idDestinataire,$idMission){


        $Messages = $this->em->getRepository('GenericBundle:Message')->findBy(array('mission'=>$idMission,'destinataire'=>$idDestinataire));


        return $Messages[count($Messages)-1]->getId();
    }

    public function getName()
    {
        return 'config_global_extension';
    }


}

