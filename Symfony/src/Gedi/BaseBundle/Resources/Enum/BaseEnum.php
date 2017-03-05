<?php

namespace Gedi\BaseBundle\Resources\Enum;

abstract class BaseEnum
{
    const ENREGISTREMENT = "enregistré"; // demande d'enregistrement
    const UPLOAD = "uploadé"; // demande d'enregistrement de fichiers
    const DOWNLOAD = "téléchargé"; // demande de téléchargement de fichier
    const MODIFICATION = "modifié"; // demande de modification
    const SUPPRESSION = "supprimé"; // demande de suppression
    const UTILISATEUR = "utilisateur"; // demande d'optention des utilisateurs d'une entité
    const DOCUMENT = "document"; // demande d'optention des documents d'une entité
    const PROJET = "projet"; // demande d'optention des projets d'une entité
    const GROUPE = "groupe"; // demande d'optention des groupes d'une entité
    const DOCUMENT_PROJET = "document_projet"; // demande d'optention des documents et des projets
    const GET = "get"; // demande d'informations sur une entité
    const SHARED_DOCUMENT = "shared_document"; // demande d'optention des documents partagés
    const SHARED_PROJET = "shared_projet"; // demande d'optention des projets partagés
    const RECHERCHER = "rechercher"; // demande de recherche d'entité
    const AJOUTER_MEMBRE = "ajouter_membre"; // demande d'ajout de membre à un groupe
    const GROUPE_PARTAGE = "groupe_partage"; // demande d'optention des groupes partagés
}
