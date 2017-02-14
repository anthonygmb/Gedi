<?php

namespace Gedi\BaseBundle\Controller;

use Gedi\BaseBundle\Entity\Utilisateur;
use Gedi\BaseBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends Controller
{
    /**
     * Page d'accueil de l'application
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function startAction()
    {
        return $this->render('GediBaseBundle:Base:start.html.twig', array(
        ));
    }

    /**
     * Page d'accueil des utilisateurs connectés
     * Cette page redirige l'utilisateur vers home_admin ou home_user
     * en fonction de son role ROLE_ADMIN ou ROLE_USER
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->render('GediAdminBundle:Admin:home_admin.html.twig');
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->render('GediUserBundle:User:home_user.html.twig');
        }
    }

    /**
     * Page d'enregistrement des nouveaux utilisateurs
     * Les utilisateurs enregistrés ici ne sont pas activés
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        // crée un nouvel utilisateur non activé et
        // renvoie un formulaire de création d'utilisateur
        $utilisateur = new Utilisateur();
        $utilisateurForm = $this->createForm('Gedi\BaseBundle\Form\UtilisateurType', $utilisateur);
        $utilisateurForm->handleRequest($request);

        // enregistre l'utilisateur lorsque le formulaire est soumis
        if ($utilisateurForm->isSubmitted() && $utilisateurForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($utilisateur);
            $em->flush();

            return $this->redirectToRoute('start');
        }

        return $this->render('GediBaseBundle:Base:register.html.twig', array(
            'utilisateur' => $utilisateur,
            'utilisateurForm' => $utilisateurForm->createView(),
        ));
    }

    /**
     * Page de contact de l'application, la page contient un formulaire de contact
     * permettant d'envoyer une demande à l'administrateur du site.
     * L'expéditeur reçoit également une notification d'envoi par email
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function contactAction(Request $request)
    {
        $contact = new Contact();
        $contactForm = $this->createForm('Gedi\BaseBundle\Form\ContactType', $contact);
        $contactForm->handleRequest($request);
        $object = $contact->getObject();
        $email = $contact->getEmail();
        $text = $contact->getText();
        $nom = $contact->getName();
        if ($contactForm->isValid() && $contactForm->isSubmitted()) {
            //Envoi du mail à l'admin
            $message = \Swift_Message::newInstance()
                ->setSubject($object)
                ->setFrom($contact->getEmail())
                ->setTo('gedi.l3imiage@gmail.com')

                //ne pas toucher merci !!
                ->setBody('envoi de l\'adresse : ' . $email . '
' . 'nom : ' . $nom . '
' . 'objet du mail : ' . $object . '

' . $text);
            $this->get('mailer')->send($message);

            // Envoyer un e-mail de confirmation à l'expéditeur
            $message2 = \Swift_Message::newInstance()
                ->setSubject('Confirmation d\'envoi mail GEDI')
                ->setFrom('gedi.l3imiage@gmail.com')
                ->setTo($email)
                ->setBody('Bonjour, votre demande a bien été envoyé, nous vous répondrons dans les meilleurs délais !
                 
Cordialement, l\'équipe Gedi.');
            $this->get('mailer')->send($message2);

            return $this->redirectToRoute('start');
        }

        return $this->render('GediBaseBundle:Base:contact.html.twig', array(
            'contact' => $contact,
            'contactForm' => $contactForm->createView(),
        ));
    }
}
