<?php

namespace App\Form;

use App\Model\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType as SearchFieldType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', SearchFieldType::class, [
                'label' => 'Rechercher',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher dans les articles et catégories...',
                    'class' => 'form-control me-2',
                ],
            ])
            // ->add('categories', EntityType::class, [
            //     'class' => Categorie::class,
            //     'choice_label' => 'name',
            //     'multiple' => true,
            //     'expanded' => false,
            //     'required' => false,
            //     'label' => 'Catégories',
            //     'attr' => [
            //         'class' => 'form-select',
            //     ],
            //     'placeholder' => 'Toutes les catégories',
            //     // Récupérer seulement les catégories parentes
            //     'query_builder' => function ($repository) {
            //         return $repository->createQueryBuilder('c')
            //             ->where('c.parent IS NULL')
            //             ->orderBy('c.name', 'ASC');
            //     },
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => SearchData::class,
            'csrf_protection' => false, // Désactiver CSRF pour la recherche
            'method' => 'GET', // Utiliser la méthode GET pour la recherche
        ]);
    }
}
