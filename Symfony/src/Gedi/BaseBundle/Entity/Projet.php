<?php

namespace Gedi\BaseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Projet
 *
 * @ORM\Table(name="Projet", uniqueConstraints={@ORM\UniqueConstraint(name="un_path_titre_type_doc", columns={"path", "nom"})}, indexes={@ORM\Index(name="fk_utilisateur_projet", columns={"id_utilisateur_fk_projet"})})
 * @ORM\Entity
 */
class Projet
{
    /**
     * Id du projet
     * @var integer
     *
     * @ORM\Column(name="id_projet", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProjet;

    /**
     * Nom du projet
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom;

    /**
     * Date de création du projet
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=false)
     */
    private $dateCreation = 'CURRENT_TIMESTAMP';

    /**
     * Date de modification du projet
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime", nullable=true)
     */
    private $dateModification;

    /**
     * Propriétaire du projet
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="idUtilisateurFkProjet")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_utilisateur_fk_projet", referencedColumnName="id_utilisateur")
     * })
     */
    private $idUtilisateurFkProjet;

    /**
     * Groupe ayant accès au projet
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Groupe", mappedBy="idProjetGp")
     */
    private $idGroupeGp;

    /**
     * Documents du projet
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Document", mappedBy="idProjetFkDocument", cascade={"all"})
     */
    private $idProjetFkDocument;

    /**
     * Un projet contient plusieurs projets.
     * @ORM\OneToMany(targetEntity="Projet", mappedBy="parent")
     */
    private $children;

    /**
     * Plusieurs projets ont un projet parent
     * @var Projet
     * @ORM\ManyToOne(targetEntity="Projet", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id_projet")
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idGroupeGp = new ArrayCollection();
        $this->dateCreation = new \DateTime();
        $this->dateModification = new \DateTime();
        $this->children = new ArrayCollection();
        $this->idProjetFkDocument = new ArrayCollection();
    }

    /**
     * Get idProjet
     *
     * @return integer
     */
    public function getIdProjet()
    {
        return $this->idProjet;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Projet
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Projet
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     *
     * @return Projet
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get idUtilisateurFkProjet
     *
     * @return Utilisateur
     */
    public function getIdUtilisateurFkProjet()
    {
        return $this->idUtilisateurFkProjet;
    }

    /**
     * Set idUtilisateurFkProjet
     *
     * @param $idUtilisateurFkProjet
     *
     * @return Projet
     */
    public function setIdUtilisateurFkProjet($idUtilisateurFkProjet)
    {
        $this->idUtilisateurFkProjet = $idUtilisateurFkProjet;

        return $this;
    }

    /**
     * Add idGroupeGp
     *
     * @param $idGroupeGp
     *
     * @return Projet
     */
    public function addIdGroupeGp($idGroupeGp)
    {
        $this->idGroupeGp[] = $idGroupeGp;

        return $this;
    }

    /**
     * Remove idGroupeGp
     *
     * @param $idGroupeGp
     */
    public function removeIdGroupeGp($idGroupeGp)
    {
        $this->idGroupeGp->removeElement($idGroupeGp);
    }

    /**
     * Get idGroupeGp
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdGroupeGp()
    {
        return $this->idGroupeGp;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdProjetFkDocument()
    {
        return $this->idProjetFkDocument;
    }

    /**
     * Add idProjetFkDocument
     *
     * @param $IdProjetFkDocument
     *
     * @return Projet
     */
    public function addIdProjetFkDocument($IdProjetFkDocument)
    {
        $this->idProjetFkDocument[] = $IdProjetFkDocument;

        return $this;
    }

    /**
     * Remove idProjetFkDocument
     *
     * @param $IdProjetFkDocument
     */
    public function removeIdProjetFkDocument($IdProjetFkDocument)
    {
        $this->idProjetFkDocument->removeElement($IdProjetFkDocument);
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * Ajouter un children
     * @param $children
     * @return $this
     */
    public function addChildren($children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Supprimer un children
     * @param $children
     */
    public function removeChildren($children)
    {
        $this->children->removeElement($children);
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Transforme le projet en tableau associatif
     * @return array
     */
    public function toArray()
    {
        $array = array(
            "idProjet" => $this->idProjet,
            "nom" => $this->nom,
            "proprietaire" => $this->idUtilisateurFkProjet->getIdUtilisateur()
        );
        return $array;
    }
}
