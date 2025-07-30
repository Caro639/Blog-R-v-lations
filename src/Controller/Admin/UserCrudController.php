<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Utilisateurs')
            ->setEntityLabelInSingular('Utilisateur')

            ->setPageTitle("index", "Révélations - Administration des utilisateurs")
            ->setPaginatorPageSize(15);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('lastName'),
            TextField::new('firstName'),
            TextField::new('nickName'),
            ImageField::new('photo')
                ->setUploadDir('/public/image/file/'),
            TextField::new('email')
                ->setFormTypeOption('disabled', 'disabled'),
            ArrayField::new('roles')
                ->hideOnIndex()
                ->setFormTypeOption('disabled', 'disabled'),
            DateTimeField::new('createdAt')
                // ->hideOnForm()
                ->setFormTypeOption('disabled', 'disabled'),
            BooleanField::new('isVerified')
                ->setFormTypeOption('disabled', 'disabled'),
            CollectionField::new('posts')
                ->hideOnForm()
                ->setFormTypeOption('disabled', 'disabled'),
            AssociationField::new('comments')
                ->hideOnForm()
                ->setFormTypeOption('disabled', 'disabled'),
            AssociationField::new('likes_user')
                ->hideOnForm()
                ->setFormTypeOption('disabled', 'disabled'),
        ];
    }
}
