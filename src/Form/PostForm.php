<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez un titre',
                    'class' => 'form-control mt-4',
                ],
                'label' => 'Titre de votre article',
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 150])
                ]
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Contenu de votre article',
                    'class' => 'form-control mt-4',
                ],
                'label' => 'Description de votre article',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le contenu ne peut pas être vide.']),
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Veuillez choisir des fichiers images uniquement',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/*',
                            'image/jpeg,',
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/jpg',
                            'image/webp',
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Le fichier n\'est pas valide, assurez vous d\'avoir un fichier au format PDF, PNG, JPG, JPEG',
                    ])
                ],
            ])

            //test tom select TODO
            ->add('categorie', EntityType::class, [
                // 'mapped' => false,
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                // 'expanded' => true,
                'label' => 'Catégories',
                'attr' => [
                    'class' => 'form-select mt-4 mb-4'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-dark mt-4',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
