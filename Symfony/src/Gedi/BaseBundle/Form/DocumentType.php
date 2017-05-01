<?php

namespace Gedi\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('idDocument', HiddenType::class, array('label' => false));
        $builder->add('nom', TextType::class, array('label' => false));
        $builder->add('typeDoc', TextType::class, array('required' => false, 'label' => false));
        $builder->add('tag', TextType::class, array('required' => false, 'label' => false));
        $builder->add('resume', TextareaType::class, array('required' => false, 'label' => false));
        $builder->add('idUtilisateurFkDocument', HiddenType::class, array('label' => false));
        $builder->add('idProjetFkDocument', HiddenType::class, array('label' => false));
        $builder->add('fichier', FileType::class, array('label' => false));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gedi\BaseBundle\Entity\Document'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'data';
    }
}
