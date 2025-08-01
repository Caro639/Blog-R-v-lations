<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Commentaires')
            ->setEntityLabelInSingular('Commentaire')

            ->setPageTitle("index", "Révélations - Administration des commentaires")
            ->setPaginatorPageSize(15);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('content'),
            BooleanField::new('isReply')
                ->setFormTypeOption('disabled', 'disabled'),
            // CollectionField::new('comments')
            //     ->renderExpanded()
            //     ->hideOnForm()
            //     ->setFormTypeOption('disabled', 'disabled'),
            AssociationField::new('user')
                ->setSortProperty('lastName')
                ->setFormTypeOption('disabled', 'disabled'),
            AssociationField::new('post')
                ->setSortProperty('title')
                ->setFormTypeOption('disabled', 'disabled'),
            DateTimeField::new('createdAt')
                // ->hideOnForm()
                ->setFormTypeOption('disabled', 'disabled'),
        ];
    }

}
