<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieController extends AbstractController
{

    #[Route('/categorie', name: 'app_categorie')]
    /**
     * show categories and their subcategories home page of all categories
     *
     *
     * @param \App\Repository\CategorieRepository $categorieRepository
     * @return Response
     */
    public function index(CategorieRepository $categorieRepository): Response
    {
        // Méthode optimisée : récupère toutes les catégories parentes avec leurs enfants en une seule requête
        $categoriesGrouped = $categorieRepository->findAllCategoriesGrouped();

        // Alternative : méthode manuelle
        $parentCategories = $categorieRepository->findBy(['parent' => null], ['name' => 'ASC']);

        // $categoriesManual = [];
        // foreach ($parentCategories as $parent) {
        //     $categoriesManual[] = [
        //         'parent' => $parent,
        //         'children' => $parent->getCategories()->toArray() // Utilise la relation Doctrine
        //     ];
        // }

        return $this->render('categorie/index.html.twig', [
            'categoriesGrouped' => $categoriesGrouped,
            // 'categoriesManual' => $categoriesManual,
            'parentCategories' => $parentCategories,
        ]);
    }


    // todo css header + card height meme hauteur + button
    #[Route('/categorie/posts', name: 'app_categorie_posts')]
    /**
     * show posts by categories parents limited to 4 posts per category
     * @param \App\Repository\CategorieRepository $categorieRepository
     * @param \App\Repository\PostRepository $postRepository
     * @return Response
     */
    public function postByCategories(
        CategorieRepository $categorieRepository,
        PostRepository $postRepository
    ): Response {
        // Récupérer toutes les catégories parentes
        $parentCategories = $categorieRepository->findBy(['parent' => null], ['name' => 'ASC']);

        // Récupérer les posts par catégorie (limité à 4 par catégorie)
        $postsByCategories = [];
        foreach ($parentCategories as $category) {
            // Récupérer les posts de cette catégorie et ses sous-catégories (limité à 4)
            $posts = $postRepository->findByCategoryAndChildren($category, 4);

            if (!empty($posts)) {
                $postsByCategories[] = [
                    'category' => $category,
                    'posts' => $posts,
                    'totalPosts' => count($postRepository->findByCategoryAndChildren($category)) // Total pour afficher "voir plus"
                ];
            }
        }

        return $this->render('categorie/post_categories.html.twig', [
            'postsByCategories' => $postsByCategories,
            'parentCategories' => $parentCategories,
        ]);
    }

    // todo paginer les posts et slugifier les catégories + css
    #[Route('/categorie/{id}', name: 'app_categorie_show', methods: ['GET'])]
    /**
     * Récupère une catégorie avec ses sous-catégories et les posts associés + badges sous-catégories
     * @param \App\Repository\CategorieRepository $categorieRepository
     * @param \App\Repository\PostRepository $postRepository
     * @param int $id
     * @return Response
     */
    public function show(
        CategorieRepository $categorieRepository,
        PostRepository $postRepository,
        int $id
    ): Response {
        // Récupérer la catégorie avec ses sous-catégories
        $category = $categorieRepository->findWithSubCategories($id);
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }
        // Récupérer les posts de cette catégorie et ses sous-catégories
        $posts = $postRepository->findByCategoryAndChildren($category);
        return $this->render('categorie/show.html.twig', [
            'category' => $category,
            'posts' => $posts,
        ]);
    }
}
