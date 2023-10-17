<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GiftSelectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Votre nom complet',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            // Supposons que vous passiez l'ID du cadeau sélectionné comme une option au formulaire
            ->add('giftId', HiddenType::class, [
                'data' => $options['data']['giftId'] ?? null,  // Assurez-vous de gérer cette partie correctement en fonction de votre logique
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Réserver ce cadeau',
            ]);
    }
}
