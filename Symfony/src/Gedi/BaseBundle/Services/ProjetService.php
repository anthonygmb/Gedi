<?php

namespace Gedi\BaseBundle\Services;

use Doctrine\ORM\EntityManager;
use Exception;
use Gedi\BaseBundle\Entity\Projet;
use Gedi\BaseBundle\Resources\Enum\BaseEnum;

/**
 * Service permettant de manipuler les projets
 * Class ProjetService
 * @package Gedi\BaseBundle\Services
 */
class ProjetService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var FileService
     */
    private $fs;

    /**
     * ProjetService constructor.
     * @param EntityManager $entityManager
     * @param FileService $fileService
     */
    public function __construct(EntityManager $entityManager, FileService $fileService)
    {
        $this->em = $entityManager;
        $this->fs = $fileService;
    }

    /**
     * Enregistre un projet dans la BDD
     * @param $sel
     * @return Projet
     */
    public function create($sel)
    {
        $objet = new Projet();
        $objet->setNom($sel[1]['value']);
        $utilisateur = $this->em->find('GediBaseBundle:Utilisateur', $sel[2]['value']);
        $objet->setIdUtilisateurFkProjet($utilisateur);
        $path = $this->fs->getPath($objet->getIdUtilisateurFkProjet()->getIdUtilisateur(), false);
        // si le projet à un parent
        if ($sel[3]['value'] != null && $sel[3]['value'] != "") {
            /* @var $parent Projet */
            $parent = $this->em->find('GediBaseBundle:Projet', $sel[3]['value']);
            $parent->addChildren($objet);
            $objet->setParent($parent);
            mkdir($path . $this->fs->createPath($parent) . $objet->getNom(), 0777);
        } else {
            mkdir($path . $objet->getNom(), 0777);
        }
        $this->em->persist($objet);
        $this->em->flush();
        return $objet;
    }

    /**
     * Retourne tous les projets en BDD
     * @return array
     */
    public function read()
    {
        return $this->em->getRepository('GediBaseBundle:Projet')->findAll();
    }

    /**
     * Retourne l'entite correspondant à l'id $sel
     * @param $sel
     * @return Projet
     */
    public function findOne($sel)
    {
        /* @var $objet Projet */
        $objet = $this->em->find('GediBaseBundle:Projet', $sel);
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
            ->from('GediBaseBundle:Projet', 'u')
            ->orderBy('u.dateCreation', 'DESC')
            ->setMaxResults($max);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Retourne les N derniers éléments d'un utilisateur
     * @param $max
     * @param $id
     * @return array
     */
    public function readLastByUser($max, $id)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
            ->from('GediBaseBundle:Projet', 'u')
            ->where('u.idUtilisateurFkProjet =' . $id)
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
        $qb->select('count(u.idProjet)');
        $qb->from('GediBaseBundle:Projet', 'u');
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Met à jour un projet
     * @param $sel
     * @return Projet
     */
    public function update($sel)
    {
        /* @var $objet Projet */
        $objet = $this->em->find('GediBaseBundle:Projet', $sel[0]['value']);
        $oldPath = $this->fs->createPath($objet);
        // on enlève le dernier '/'
        $oldPath = substr($oldPath, 0, strlen($oldPath) - 1);
        $objet->setNom($sel[1]['value']);
        $path = $this->fs->getPath($objet->getIdUtilisateurFkProjet()->getIdUtilisateur(), false);
        // si le projet n'a pas de parent
        if ($objet->getParent() == null) {
            rename($path . $oldPath, $path . $objet->getNom());
        } else {
            rename($path . $oldPath, $path . substr($oldPath, 0, strrpos($oldPath, "/") + 1) . $objet->getNom());
        }
        $this->em->merge($objet);
        $this->em->flush();
        return $objet;
    }

    /**
     * Supprime un ou plusieurs projets
     * @param $sel
     * @return string
     */
    public function delete($sel)
    {
        for ($i = 0; $i <= count($sel) - 1; $i++) {
            /* @var $toDel Projet */
            $toDel = $this->em->find('GediBaseBundle:Projet', $sel[$i]['id']);
            $oldPath = $this->fs->createPath($toDel);
            // on enlève le dernier '/'
            $oldPath = substr($oldPath, 0, strlen($oldPath) - 1);
            $path = $this->fs->getPath($toDel->getIdUtilisateurFkProjet()->getIdUtilisateur(), false);
            $this->fs->rmdir_recursive($path . $oldPath);
            $this->em->remove($toDel);
        }
        $this->em->flush();
        return "OK";
    }

    /**
     * Permet de récupérer les entités enfants d'un objet
     * @param $sel
     * @param $childType
     * @return \Doctrine\Common\Collections\Collection|mixed
     * @throws Exception
     */
    public function getChildren($sel, $childType)
    {
        /* @var $objet Projet */
        $objet = $this->em->find('GediBaseBundle:Projet', $sel);
        switch ($childType) {
            case BaseEnum::DOCUMENT:
                return $objet->getIdProjetFkDocument();
                break;
            case BaseEnum::PROJET:
                return $objet->getChildren();
                break;
            default:
                throw new Exception('ChildType n\'est pas défini');
        }
    }
}
