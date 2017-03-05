<?php

namespace Gedi\BaseBundle\Services;

use Doctrine\ORM\EntityManager;
use Exception;
use Gedi\BaseBundle\Entity\Groupe;
use Gedi\BaseBundle\Resources\Enum\BaseEnum;

/**
 * Service permettant de manipuler les groupes
 * Class GroupeService
 * @package Gedi\BaseBundle\Services
 */
class GroupeService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * GroupeService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Enregistre un groupe dans la BDD
     * @param $sel
     * @return Groupe
     */
    public function create($sel)
    {
        $objet = new Groupe();
        $objet->setNom($sel[1]['value']);
        $utilisateur = $this->em->find('GediBaseBundle:Utilisateur', $sel[2]['value']);
        $utilisateur->addIdGroupeUg($objet);
        $objet->addIdUtilisateurUg($utilisateur);
        $objet->setIdUtilisateurFkGroupe($utilisateur);
        $this->em->persist($objet);
        $this->em->flush();
        return $objet;
    }

    /**
     * Fonction d'ajout de membre à un groupe
     * @param $sel
     * @return Groupe
     */
    public function addMembre($sel)
    {
        /* @var $objet Groupe */
        $objet = $this->em->find('GediBaseBundle:Groupe', $sel['idGroupe']);
        $utilisateur = $this->em->find('GediBaseBundle:Utilisateur', $sel['idUtilisateur']);
        $utilisateur->addIdGroupeUg($objet);
        $objet->addIdUtilisateurUg($utilisateur);
        $this->em->persist($objet);
        $this->em->flush();
        return $objet;
    }

    /**
     * Retourne tous les groupes en BDD
     * @return array
     */
    public function read()
    {
        return $this->em->getRepository('GediBaseBundle:Groupe')->findAll();
    }

    /**
     * Retourne l'entite correspondant à l'id $sel
     * @param $sel
     * @return Groupe
     */
    public function findOne($sel)
    {
        /* @var $objet Groupe */
        $objet = $this->em->find('GediBaseBundle:Groupe', $sel);
        return $objet;
    }

    /**
     * Retourne les N derniers éléments
     * @param $max
     * @return array
     */
    public function readLast($max)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
            ->from('GediBaseBundle:Groupe', 'u')
            ->orderBy('u.dateCreation', 'DESC')
            ->setMaxResults($max);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Retourne le nombre d'entités en BDD
     * @return mixed
     */
    public function count()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('count(u.idGroupe)');
        $qb->from('GediBaseBundle:groupe', 'u');
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Met à jour un groupe
     * @param $sel
     * @return Groupe
     */
    public function update($sel)
    {
        /* @var $objet Groupe */
        $objet = $this->em->find('GediBaseBundle:Groupe', $sel[0]['value']);
        $objet->setNom($sel[1]['value']);
        $this->em->merge($objet);
        $this->em->flush();
        return $objet;
    }

    /**
     * Supprime un ou plusieurs groupes
     * @param $sel
     * @return string
     */
    public function delete($sel)
    {
        for ($i = 0; $i <= count($sel) - 1; $i++) {
            $toDel = $this->em->find('GediBaseBundle:Groupe', $sel[$i]['id']);
            $this->em->remove($toDel);
        }
        $this->em->flush();
        return "OK";
    }

    /**
     * Permet de récupérer les entités enfants d'un objet
     * @param $sel
     * @param $childType
     * @return \Doctrine\Common\Collections\Collection
     * @throws Exception
     */
    public function getChildren($sel, $childType)
    {
        /* @var $objet Groupe */
        $objet = $this->em->find('GediBaseBundle:Groupe', $sel);
        switch ($childType) {
            case BaseEnum::UTILISATEUR:
                return $objet->getIdUtilisateurUg();
                break;
            default:
                throw new Exception('ChildType n\'est pas défini');
        }
    }
}
