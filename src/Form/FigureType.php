<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Groupe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TypeTextType::class,
                [
                    'label' => 'Titre',
                    'required' => true,
                ])

            // Ajout d'un champs d'une autre entitée sous forme de choix déroulant
            ->add('groupe', EntityType::class,
                [
                    'class' => Groupe::class,
                    'choice_label' => 'titre',
                    'label' => 'difficulté',
                ])
            ->add('description', TextareaType::class,
                [
                    'label' => 'Description',
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])

            // On ajoute le champ "images" dans le formulaire
            // Il n'est pas lié à la base de données (mapped à false)
            ->add('images', FileType::class, [
                    'label' => false,
                    'multiple' => true,
                    'mapped' => false,
                    'required' => false,
                ])

            ->add('video', TypeTextType::class, [
                    'mapped' => false,
                    'label' => 'url de la vidéo',
                    'required' => false,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
