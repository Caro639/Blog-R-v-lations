<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class CategorieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Catégories')
            ->setEntityLabelInSingular('Catégorie')

            ->setPageTitle("index", "Révélations - Administration des utilisateurs")
            ->setPaginatorPageSize(15);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name'),
            AssociationField::new('parent')
                ->hideOnIndex()
                // ->setSortProperty('null')
                ->setFormTypeOption('disabled', 'disabled'),
            ArrayField::new('parent')
                ->hideOnForm()
                ->setFormTypeOption('disabled', 'disabled'),
            AssociationField::new('categories'),
            // ->setSortProperty('categories.name'),
            ArrayField::new('categories')
                ->hideOnForm(),
            CollectionField::new('posts')
                ->setFormTypeOption('disabled', 'disabled'),
        ];
    }

}
