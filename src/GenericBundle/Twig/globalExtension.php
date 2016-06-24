<?php
namespace GenericBundle\Twig;

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

// function GetFormationAValider($tier)
// {
//         $userid = $this->get('security.token_storage')->getToken()->getUser();
//         $etablissement=$userid->getEtablissement();
//         $tiercreation=$userid->getetablissement()->gettier()->getId();
//         $apprenants = $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$etablissement));
//         $formations = $this->getDoctrine()->getRepository('GenericBundle:Formation')->findBy(array('etablissement'=>$etablissement));
//         $Allapprenants =array();
//         $Importcandidat = $this->getDoctrine()->getRepository('GenericBundle:ImportCandidat')->findAll();
//         // var_dump($etablissement->getId());die;

//         foreach($apprenants as $userd)
//         {              
//             if($userd->hasRole('ROLE_APPRENANT'))
//             {
//                 array_push($Allapprenants,$userd);
//             }              
//         }

//         $TousLesApprenants=$Allapprenants;
//         $Myapprenantplacer=$Allapprenants;
//         foreach($Importcandidat as $userd)
//         {
//             array_push($Allapprenants,$userd);
//         }

//         $em = $this->getDoctrine()->getEntityManager();

//         $sql="SELECT C FROM GenericBundle:Candidature C WHERE C.statut=:statut and C.user is not null GROUP BY C.user,C.statut " ;
//         $query = $em->createQuery($sql);
//         $query->setParameter('statut', '2');
//         $FormationCandidature = $query->getResult(); // array of ForumUser objects

//         $MyFormationAvalider =array();
//         foreach($FormationCandidature as $formation)
//         {
//             if(!($Allapprenants))
//             {
//             }else{
//                 if( in_array($formation->getuser(),$Allapprenants)  )
//                 {
//                     array_push($MyFormationAvalider,$formation);
//                 }
//             }    

//         }
//         return array('apprenant'=>$MyFormationAvalider);

// }

    function CountMission($destinataire){


        $sql="SELECT M FROM GenericBundle:Message M  WHERE M.destinataire=:destinataire GROUP BY M.destinataire,M.mission " ;
        $query = $this->em->createQuery($sql);
        $query->setParameter('destinataire', $destinataire);
        $Messages = $query->getResult(); // array of ForumUser objects
        return count($Messages);
    }

    /*function GetFormationMission($Mission){

        $Diffusion = $this->em->getRepository('GenericBundle:Diffusion')->findBy(array('mission'=>$Mission));
        return $Diffusion;
    }*/
    public function getName()
    {
        return 'config_global_extension';
    }
}

