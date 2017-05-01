<?php

namespace Gedi\BaseBundle\Services;

use Doctrine\ORM\EntityManager;
use Exception;
use Gedi\BaseBundle\Entity\Utilisateur;
use Gedi\BaseBundle\Resources\Enum\BaseEnum;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

/**
 * Service permettant de manipuler les utilisateurs
 * Class UtilisateurService
 * @package Gedi\BaseBundle\Service
 */
class UtilisateurService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EncoderFactory
     */
    private $ef;

    /**
     * @var string
     */
    private $targetDir;

    /**
     * @var FileService
     */
    private $fs;

    /**
     * UtilisateurService constructor.
     * @param EntityManager $entityManager
     * @param EncoderFactory $encoderFactory
     * @param $targetDir
     * @param FileService $fileService
     */
    public function __construct(EntityManager $entityManager, EncoderFactory $encoderFactory, $targetDir, FileService $fileService)
    {
        $this->em = $entityManager;
        $this->ef = $encoderFactory;
        $this->targetDir = $targetDir;
        $this->fs = $fileService;

        // si le repertoire bankfile n'existe pas, on le crée
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777);
        }
    }

    /**
     * Enregistre les nouvelles demandes d'ajout utilisateur
     * @param Utilisateur $objet
     */
    public function register(Utilisateur $objet)
    {
        $objet->setSalt(substr(md5(time()), 0, 23));
        $encoder = $this->ef->getEncoder($objet);
        $password = $encoder->encodePassword($objet->getPassword(), $objet->getSalt());
        $objet->setPassword($password);
        if ($objet->getIdUtilisateur() != "" || $objet->getIdUtilisateur() != null) {
            $objet->setActif(true);
            $this->em->merge($objet);
        } else {
            $this->em->persist($objet);
        }
        $this->em->flush();
    }

    /**
     * Enregistre un utilisateur dans la BDD
     * et crée son répertoire de sauvegarde
     * @param $sel
     * @return Utilisateur
     */
    public function create($sel)
    {
        $objet = new Utilisateur();
        $objet->setUsername($sel[1]['value']);
        $objet->setSalt(substr(md5(time()), 0, 23));
        $encoder = $this->ef->getEncoder($objet);
        $password = $encoder->encodePassword($sel[2]['value'], $objet->getSalt());
        $objet->setPassword($password);
        $objet->setNom($sel[4]['value']);
        $objet->setPrenom($sel[5]['value']);
        $objet->setActif(($sel[6]['value'] == "false") ? false : true);
        $this->em->persist($objet);
        $this->em->flush();
        // création de l'arborescence si l'utilisateur est actif
        if ($objet->getActif() == true) {
            mkdir($this->targetDir . $objet->getIdUtilisateur() . "/data", 0777, true);
        }
        return $objet;
    }

    /**
     * Retourne tous les utilisateurs en BDD
     * @return array
     */
    public function read()
    {
        return $this->em->getRepository('GediBaseBundle:Utilisateur')->findAll();
    }

    /**
     * Retourne l'entite correspondant à l'id $sel
     * @param $sel
     * @return Utilisateur
     */
    public function findOne($sel)
    {
        /* @var $objet Utilisateur */
        $objet = $this->em->find('GediBaseBundle:Utilisateur', $sel);
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
            ->from('GediBaseBundle:Utilisateur', 'u')
            ->orderBy('u.dateCreation', 'DESC')
            ->setMaxResults($max);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Fonction de recherche d'utilisateurs
     * @param $sel
     * @return array
     */
    public function search($sel)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
            ->from('GediBaseBundle:Utilisateur', 'u')
            ->where('u.prenom =\'' . $sel . '\'')
            ->orWhere('u.nom =\'' . $sel . '\'')
            ->orWhere('u.username =\'' . $sel . '\'');
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
        $qb->select('count(u.idUtilisateur)');
        $qb->from('GediBaseBundle:Utilisateur', 'u');
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Retourne le nombre d'utilisateurs inactifs
     * @return mixed
     */
    public function countInactifs()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('count(u.idUtilisateur)');
        $qb->where('u.actif = 0');
        $qb->from('GediBaseBundle:Utilisateur', 'u');
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Met à jour un utilisateur et crée son repertoire de sauvegarde
     * si il n'était pas actif
     * @param $sel
     * @return Utilisateur
     */
    public function update($sel)
    {
        /* @var $objet Utilisateur */
        $objet = $this->em->find('GediBaseBundle:Utilisateur', $sel[0]['value']);
        $objet->setUsername($sel[1]['value']);
        if ($objet->getPassword() != $sel[2]['value']) {
            $objet->setSalt(substr(md5(time()), 0, 23));
            $encoder = $this->ef->getEncoder($objet);
            $password = $encoder->encodePassword($sel[2]['value'], $objet->getSalt());
            $objet->setPassword($password);
        }
        $objet->setNom($sel[4]['value']);
        $objet->setPrenom($sel[5]['value']);
        $objet->setActif(($sel[6]['value'] == "false") ? false : true);
        $this->em->merge($objet);
        $this->em->flush();
        // création de l'arborescence si l'utilisateur est actif
        if ($objet->getActif() == true && !file_exists($this->targetDir . $objet->getIdUtilisateur())) {
            mkdir($this->targetDir . $objet->getIdUtilisateur() . "/data", 0777, true);
        }
        return $objet;
    }

    /**
     * Supprime un ou plusieurs utilisateurs avec
     * leurs repertoires de sauvegarde
     * @param $sel
     * @return string
     */
    public function delete($sel)
    {
        for ($i = 0; $i <= count($sel) - 1; $i++) {
            if (file_exists($this->targetDir . $sel[$i]['id'])) {
                $this->fs->rmdir_recursive($this->targetDir . $sel[$i]['id']);
            }
            $toDel = $this->em->find('GediBaseBundle:Utilisateur', $sel[$i]['id']);
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
        /* @var $objet Utilisateur */
        $objet = $this->em->find('GediBaseBundle:Utilisateur', $sel);
        switch ($childType) {
            case BaseEnum::PROJET:
                return $objet->getIdUtilisateurFkProjet();
                break;
            case BaseEnum::GROUPE:
                return $objet->getIdUtilisateurFkGroupe();
                break;
            case BaseEnum::GROUPE_PARTAGE:
                return $objet->getIdGroupeUg();
                break;
            case BaseEnum::SHARED_DOCUMENT:
                return $objet->getIdDocumentDu();
                break;
            case BaseEnum::SHARED_PROJET:
                return $objet->getIdDocumentDu();
                break;
            default:
                throw new Exception('ChildType n\'est pas défini');
        }
    }
}
