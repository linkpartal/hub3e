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
        );
    }



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

