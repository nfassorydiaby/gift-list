<?php

namespace App\Form;

use App\Entity\GiftList;
use App\Entity\GiftListTheme;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiftListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Entrez le titre de votre liste',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez votre liste de cadeaux',
                ],
            ])
            ->add('isPrivate', CheckboxType::class, [
                'label' => 'Liste privée?',
                'required' => false,
            ])
            ->add('passsword', PasswordType::class, [
                'label' => 'Mot de passe (si privée)',
                'required' => false,
            ])
            ->add('dateOuverture', DateType::class, [
                'label' => 'Date d\'ouverture',
                'widget' => 'single_text',
            ])
            ->add('dateFinOuverture', DateType::class, [
                'label' => 'Date de fin d\'ouverture',
                'widget' => 'single_text',
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Liste active?',
                'required' => false,
            ])
            ->add('giftListTheme', EntityType::class, [
                'class' => GiftListTheme::class,  // Spécifiez l'entité à utiliser
                'choice_label' => 'nom',  // Spécifiez l'attribut à afficher dans le choix
                'label' => 'Thème de la liste de cadeaux',
                'multiple' => true,  
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GiftList::class,
        ]);
    }
}
