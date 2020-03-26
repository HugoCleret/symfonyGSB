<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as SFType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType; 

class AjoutFormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut',null,array('label'=>'Date'))
            ->add('nbreHeures',null,array('label' =>"Durée de la formation"))
            ->add('departement',null,array('label'=>'Departement'))
            ->add('ville',null,array('label'=>'Ville'))
            ->add('produit', EntityType::class, array(
                'class' => 'App\Entity\Produit',
                'choice_label' => 'libelle', 'label' => "Produit"
            ))
            ->add('repas', CheckboxType::class, [
                'label'    => 'Repas compris',
                'required' => false,
            ])
            ->add('Ajouter la formation', SFType\SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
