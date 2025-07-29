<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\CategorieFactory;
use App\Factory\CommentFactory;
use App\Factory\KeyWordFactory;
use App\Factory\PostFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // Création des utilisateurs AVANT tout le reste
        UserFactory::createMany(20);
        UserFactory::createOne([
            'email' => 'admin@revelation.fr',
            'firstName' => 'Caro',
            'lastName' => 'Admin',
            'nickName' => 'AdminCaro',
            'roles' => ['ROLE_ADMIN'],
            'password' => 'password',
            'isVerified' => true
        ]);

        // Création des catégories parent
        $plafondCategory = CategorieFactory::createOne([
            'name' => 'Plafond',
            'slug' => 'plafond',
        ]);

        $murCategory = CategorieFactory::createOne([
            'name' => 'Mur',
            'slug' => 'mur',
        ]);

        $boiserieCategory = CategorieFactory::createOne([
            'name' => 'Boiserie',
            'slug' => 'boiserie',
        ]);

        $tapisserieCategory = CategorieFactory::createOne([
            'name' => 'Tapisserie',
            'slug' => 'tapisserie',
        ]);
        $hauteDecorationCategory = CategorieFactory::createOne([
            'name' => 'Haute Décoration',
            'slug' => 'haute-decoration',
        ]);

        // Création des sous-catégories pour Plafond
        $enduitPlafondCategory = CategorieFactory::createOne([
            'name' => 'enduit de plafond',
            'slug' => 'enduire-un-plafond',
            'parent' => $plafondCategory,
        ]);

        $peinturePlafondCategory = CategorieFactory::createOne([
            'name' => 'peinture de plafond',
            'slug' => 'peindre-un-plafond',
            'parent' => $plafondCategory,
        ]);

        $plafondPoutresCategory = CategorieFactory::createOne([
            'name' => 'Plafond à poutres',
            'slug' => 'poutres',
            'parent' => $plafondCategory,
        ]);

        // Création des sous-catégories pour Mur
        $murPlatreCategory = CategorieFactory::createOne([
            'name' => 'Mur en plâtre',
            'slug' => 'mur-en-platre',
            'parent' => $murCategory,
        ]);

        $murPlacoCategory = CategorieFactory::createOne([
            'name' => 'Mur en placo',
            'slug' => 'mur-en-placo',
            'parent' => $murCategory,
        ]);

        $murPeintureCategory = CategorieFactory::createOne([
            'name' => 'Mur en peinture',
            'slug' => 'mur-en-peinture',
            'parent' => $murCategory,
        ]);

        // Création des sous-catégories pour Boiserie
        $lambrisCategory = CategorieFactory::createOne([
            'name' => 'Lambris',
            'slug' => 'lambris',
            'parent' => $boiserieCategory,
        ]);

        $parquetCategory = CategorieFactory::createOne([
            'name' => 'Parquet',
            'slug' => 'parquet',
            'parent' => $boiserieCategory,
        ]);

        $menuiseriesCategory = CategorieFactory::createOne([
            'name' => 'Menuiseries',
            'slug' => 'menuiseries',
            'parent' => $boiserieCategory,
        ]);

        // Création des sous-catégories pour Tapisserie
        $toileVerreCategory = CategorieFactory::createOne([
            'name' => 'Toile de verre',
            'slug' => 'toile-de-verre',
            'parent' => $tapisserieCategory,
        ]);

        $papierPeintCategory = CategorieFactory::createOne([
            'name' => 'Papier peint',
            'slug' => 'papier-peint',
            'parent' => $tapisserieCategory,
        ]);

        $textureCategory = CategorieFactory::createOne([
            'name' => 'Texture',
            'slug' => 'texture',
            'parent' => $tapisserieCategory,
        ]);

        // Création des sous-catégories pour Haute Décoration
        $stucCategory = CategorieFactory::createOne([
            'name' => 'Stuc',
            'slug' => 'stuc',
            'parent' => $hauteDecorationCategory,
        ]);
        $fresqueCategory = CategorieFactory::createOne([
            'name' => 'Fresque',
            'slug' => 'fresque',
            'parent' => $hauteDecorationCategory,
        ]);
        $ornementationCategory = CategorieFactory::createOne([
            'name' => 'Ornementation',
            'slug' => 'ornementation',
            'parent' => $hauteDecorationCategory,
        ]);
        $enduitDecoratifCategory = CategorieFactory::createOne([
            'name' => 'Enduit décoratif',
            'slug' => 'enduit-decoratif',
            'parent' => $hauteDecorationCategory,
        ]);
        $peintureDecorativeCategory = CategorieFactory::createOne([
            'name' => 'Peinture décorative',
            'slug' => 'peinture-decorative',
            'parent' => $hauteDecorationCategory,
        ]);
        $marbreCategory = CategorieFactory::createOne([
            'name' => 'Marbré',
            'slug' => 'marbre',
            'parent' => $hauteDecorationCategory,
        ]);

        // Récupération de toutes les catégories créées (parent et enfants)
        $allCategories = [
            // Catégories parent
            $plafondCategory,
            $murCategory,
            $boiserieCategory,
            $tapisserieCategory,
            $hauteDecorationCategory,

            // Sous-catégories Plafond
            $enduitPlafondCategory,
            $peinturePlafondCategory,
            $plafondPoutresCategory,

            // Sous-catégories Mur
            $murPlatreCategory,
            $murPlacoCategory,
            $murPeintureCategory,

            // Sous-catégories Boiserie
            $lambrisCategory,
            $parquetCategory,
            $menuiseriesCategory,

            // Sous-catégories Tapisserie
            $toileVerreCategory,
            $papierPeintCategory,
            $textureCategory,

            // Sous-catégories Haute Décoration
            $stucCategory,
            $fresqueCategory,
            $ornementationCategory,
            $enduitDecoratifCategory,
            $peintureDecorativeCategory,
            $marbreCategory,
        ];

        // Création de posts avec attribution aléatoire de catégories
        for ($i = 0; $i < 15; $i++) {
            // Sélection aléatoire de 1 à 3 catégories par post
            $randomCategories = array_rand($allCategories, rand(1, 3));

            if (!is_array($randomCategories)) {
                $randomCategories = [$randomCategories];
            }

            $selectedCategories = [];
            foreach ($randomCategories as $index) {
                $selectedCategories[] = $allCategories[$index];
            }

            PostFactory::createOne([
                'categorie' => $selectedCategories,
                'comments' => CommentFactory::new()->many(rand(0, 8)),
            ]);
        }

        // Posts avec seulement des catégories parent
        PostFactory::createMany(3, [
            'categorie' => [$plafondCategory, $murCategory],
            'comments' => CommentFactory::new()->range(0, 5),
        ]);

        // Posts avec seulement des sous-catégories
        PostFactory::createMany(3, [
            'categorie' => [$stucCategory, $papierPeintCategory, $murPlacoCategory], // 3 sous-catégories
            'comments' => CommentFactory::new()->range(0, 5),
        ]);

        // Posts avec un mélange parent/enfant cohérent
        PostFactory::createMany(2, [
            'categorie' => [$plafondCategory, $enduitPlafondCategory], // Parent + sa sous-catégorie
            'comments' => CommentFactory::new()->range(0, 5),
        ]);


        // KeyWordFactory::createMany(20, );
        KeyWordFactory::createOne([
            'label' => 'Haute Décoration',
            'slug' => 'haute-decoration',
        ]);
        KeyWordFactory::createOne([
            'label' => 'Plafond',
            'slug' => 'plafond',
        ]);
        KeyWordFactory::createOne([
            'label' => 'Mur',
            'slug' => 'mur',
        ]);
        KeyWordFactory::createOne([
            'label' => 'Boiserie',
            'slug' => 'boiserie',
        ]);
        KeyWordFactory::createOne([
            'label' => 'Tapisserie',
            'slug' => 'tapisserie',
        ]);
        KeyWordFactory::createOne([
            'label' => 'enduit de plafond',
            'slug' => 'enduire-un-plafond',
        ]);
        KeyWordFactory::createOne([
            'label' => 'peinture de plafond',
            'slug' => 'peindre-un-plafond',
        ]);
        KeyWordFactory::createOne([
            'label' => 'Mur en placo',
            'slug' => 'mur-en-placo',
        ]);
        KeyWordFactory::createOne([
            'label' => 'Mur en peinture',
            'slug' => 'mur-en-peinture',
        ]);
        KeyWordFactory::createOne([
            'label' => 'Toile de verre',
            'slug' => 'toile-de-verre',
        ]);
        KeyWordFactory::createOne([
            'label' => 'Papier peint',
            'slug' => 'papier-peint',
        ]);
        KeyWordFactory::createOne([
            'label' => 'Menuiseries',
            'slug' => 'menuiseries',
        ]);
        KeyWordFactory::createOne([
            'label' => 'Stuc',
            'slug' => 'stuc',
        ]);

        $manager->flush();
    }
}
