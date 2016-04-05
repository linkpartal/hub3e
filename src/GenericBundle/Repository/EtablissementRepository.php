<?php

namespace GenericBundle\Repository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
use \Doctrine\ORM\EntityRepository;

class EtablissementRepository extends EntityRepository
{
    public function findSocietes()
    {
        $qb = $this->getEntityManager()->createQuery("SELECT e FROM GenericBundle:Etablissement e JOIN e.tier t WHERE t.ecole = false");

        return $qb->getResult();
    }

    public function findAdressesOfSociete($id)
    {
        $qb = $this->getEntityManager()->createQuery("SELECT e FROM GenericBundle:Etablissement e JOIN e.tier t WHERE t.ecole = false AND t.id = ".$id);

        return $qb->getResult();
    }

    public function findEcoles()
    {
        $qb = $this->getEntityManager()->createQuery("SELECT e FROM GenericBundle:Etablissement e JOIN e.tier t WHERE t.ecole = true");

        return $qb->getResult();
    }

    public function findAdressesOfEcole($id)
    {
        $qb = $this->getEntityManager()->createQuery("SELECT e FROM GenericBundle:Etablissement e JOIN e.tier t WHERE t.ecole = true AND t.id = ".$id);

        return $qb->getResult();
    }
}