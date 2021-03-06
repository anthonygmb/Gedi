<?php

namespace Gedi\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('idUtilisateur', HiddenType::class, array('label' => false));
        $builder->add('username', EmailType::class, array('label' => false));
        $builder->add('password', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe ne concordent pas',
            'options' => array('attr' => array('class' => 'password-field')),
            'required' => true,
            'first_options' => array('label' => 'Mot de passe'),
            'second_options' => array('label' => 'Confirmation de mot de passe'),
        ));
        $builder->add('nom', TextType::class, array('label' => false));
        $builder->add('prenom', TextType::class, array('label' => false));
        $builder->add('actif', CheckboxType::class, array('required' => false, 'label' => false));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gedi\BaseBundle\Entity\Utilisateur'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gedi_basebundle_utilisateur';
    }

    public function getUsername()
    {
        return 'username';
    }

    public function getPassword()
    {
        return 'password';
    }

    public function getNom()
    {
        return 'nom';
    }

    public function getPrenom()
    {
        return 'prenom';
    }
}
