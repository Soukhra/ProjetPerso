<?php

namespace App\Form;


use App\Entity\Address;

use App\Entity\Transport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user']; //injection des données de l'utilisateur

        $builder //construction du formulaire
            ->add('address', EntityType::class, [
                'class'=> Address::class,
                'required'=>true,
                'choices'=>$user->getAddresses(),
                'multiple'=>false,
                'expanded'=>true
            ])//adress est le nom de la table
            ->add('transport', EntityType::class, [
                'class'=> Transport::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>true
            ])//options du champs, requis, choix multiple = non un seul choix, étendu = oui
            ->add('informations', TextareaType::class, [
                'required'=>false
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => null, // Définissez la valeur par défaut pour l'option 'user'
            // Configure your form options here
        ]);
    }
}
