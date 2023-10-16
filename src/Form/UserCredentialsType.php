<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class UserCredentialsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('currentPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new SecurityAssert\UserPassword([
                        'message' => 'Mot de passe incorrect',
                    ])
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => ['attr' => ['class' => 'password-field']],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                    new Callback([
                        'callback' => function($password, ExecutionContextInterface $context) {
                            if (!preg_match('/[A-Z]/', $password)) {
                                $context->buildViolation('Votre mot de passe doit contenir au moins une lettre majuscule.')
                                    ->addViolation();
                            }
                            if (!preg_match('/[a-z]/', $password)) {
                                $context->buildViolation('Votre mot de passe doit contenir au moins une lettre minuscule.')
                                    ->addViolation();
                            }
                            if (!preg_match('/[0-9]/', $password)) {
                                $context->buildViolation('Votre mot de passe doit contenir au moins un chiffre.')
                                    ->addViolation();
                            }
                            if (!preg_match('/[@$!%*#?&]/', $password)) {
                                $context->buildViolation('Votre mot de passe doit contenir au moins un caractère spécial (@$!%*#?&).')
                                    ->addViolation();
                            }
                        }
                    ]),
                ],
                'required' => true,
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

