<?php

namespace GenericBundle\Repository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
use Doctrine\Common\Collections\ArrayCollection;
use \Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param string $role
     *
     * @return array
     */
    public function findByRole($role)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%');

        return $qb->getQuery()->getResult();
    }

    public function findByRoles(array $roles)
    {

        $users = array();
        foreach($roles as $role)
        {
            $users = array_merge($users,$this->findByRole($role));
        }
        return $users;

    }

    public function getUserofTier($tier)
    {
        $users = new ArrayCollection();
        $em = $this->getEntityManager();
        $users = new ArrayCollection(array_merge($users->toArray(),$em->getRepository('GenericBundle:User')->findBy(array('tier'=>$tier ))));

        $etablis = $em->getRepository('GenericBundle:Etablissement')->findBy(array('tier'=>$tier ));

        foreach( $etablis as $etab){
            $users = new ArrayCollection(array_merge($users->toArray(),$em->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$etab ))));
        }
        return $users;
    }

    public function findApprenantDuplicata($id)
    {
        $users = array();
        $em = $this->getEntityManager();
        $import = $em->getRepository('GenericBundle:ImportCandidat')->find($id);

        if($import)
        {
            $usersdup = $em->getRepository('GenericBundle:User')->findBy(array('civilite' =>$import->getCivilite()  , 'nom' => $import->getNom(), 'prenom' => $import->getPrenom()));

            foreach($usersdup as $userdup)
            {
                if($userdup->getInfo())
                {
                    if ($userdup->getInfo()->getDatenaissance() == $import->getInfo()->getDatenaissance() and $userdup->getInfo()->getLieunaissance()==$import->getInfo()->getLieunaissance()
                        and $userdup->hasRole('ROLE_APPRENANT'))
                    {
                        array_push($users,$userdup);
                    }
                }
            }
        }
        return $users;
    }
}
