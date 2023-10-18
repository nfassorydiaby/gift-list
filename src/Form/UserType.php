<?php 

// src/Form/UserType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control']
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => false,  // Rendez le champ facultatif si nécessaire
                'attr' => ['class' => 'form-control']
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => false,  // Rendez le champ facultatif si nécessaire
                'attr' => ['class' => 'form-control']
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'multiple' => true,  // Permettre la sélection de plusieurs rôles
                'expanded' => true,  // Utiliser des cases à cocher
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                    // Ajouter d'autres rôles au besoin
                ],
                'attr' => ['class' => 'form-check']  // Modifier cette classe si nécessaire
            ])
            // Si vous souhaitez permettre la définition du mot de passe lors de la création :
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['class' => 'form-control']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Créer un utilisateur',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);

            // Condition pour ajouter le champ mot de passe seulement lors de la création
            if (!$options['is_edit']) {
                $builder->add('password', PasswordType::class, [
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        // autres contraintes...
                    ],
                ]);
            }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false, // La nouvelle option pour différencier l'édition de la création
        ]);
    }
}
