<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')

            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
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
            ])

            ->add('firstname', TextType::class, [
                'label' => 'Prénom:',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom de famille:',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
