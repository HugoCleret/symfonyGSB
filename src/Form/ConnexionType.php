<?php

namespace App\Form;

use App\Entity\Visiteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as SFType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; 

class ConnexionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
                ->add('login', null, array('label' => 'Login'))
                ->add('mdp', null, array('label' => 'Mot de passe'))
                // ->add('statut',ChoiceType::class,[
                //     'choices' => [
                //         'Employé'=>'employe',
                //         'Visiteur'=>'visiteur'
                //     ],
                // ])
                ->add('Connexion', SFType\SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Visiteur::class,
        ]);
    }

};
