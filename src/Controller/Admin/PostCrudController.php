<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Categorie;
use App\Entity\Comment;
use App\Entity\Keyword;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Articles')
            ->setEntityLabelInSingular('Article')

            ->setPageTitle("index", "Révélations - Administration des articles")
            ->setPaginatorPageSize(15);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('title'),
            TextEditorField::new('content'),
            ImageField::new('image')
                ->setUploadDir('/public/image/file/'),
            DateTimeField::new('createdAt')
                ->hideOnForm()
                ->setFormTypeOption('disabled', 'disabled'),
            DateTimeField::new('updatedAt')
                ->hideOnForm()
                ->setFormTypeOption('disabled', 'disabled'),
            AssociationField::new('user')
                ->setFormTypeOption('disabled', 'disabled'),
            CollectionField::new('categorie'),
            AssociationField::new('comments')
                ->hideOnForm()
                ->setFormTypeOption('disabled', 'disabled'),
            CollectionField::new('keyword'),
            AssociationField::new('likes')
                ->hideOnForm()
                ->setFormTypeOption('disabled', 'disabled'),
        ];
    }

}
