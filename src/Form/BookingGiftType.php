<?php 

// src/Form/BookingGiftType.php
namespace App\Form;

use App\Entity\BookingGift;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingGiftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('email');
            // Si vous voulez permettre de choisir le cadeau depuis le formulaire, ajoutez-le également ici.
            // Sinon, vous pouvez le définir dans le contrôleur après soumission du formulaire.
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BookingGift::class,
        ]);
    }
}
