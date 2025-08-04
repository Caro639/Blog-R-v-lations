<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\PostRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

final class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    /**
     * home of website with last posts and categories
     * @param \App\Repository\PostRepository $postRepository
     * @param \App\Repository\CategorieRepository $categorieRepository
     * @return Response
     */
    public function index(
        PostRepository $postRepository,
        CategorieRepository $categorieRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {

        $lastPost = $postRepository->findLastPost(1);

        $posts = $paginator->paginate(
            $postRepository->findBy([], ['createdAt' => 'DESC']),
            $request->query->getInt('page', 1),
            10
        );

        // Récupérer toutes les catégories parentes pour le widget sidebar
        $categories = $categorieRepository->findBy(['parent' => null], ['name' => 'ASC']);

        // Formulaire de recherche
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        // Recherche par catégorie via URL (ex: /?category=1) - conservé pour les liens des badges
        $categoryId = $request->query->get('category');
        $selectedCategory = null;
        if ($categoryId) {
            $category = $categorieRepository->find($categoryId);
            if ($category) {
                $selectedCategory = $category;
                // Recherche dans cette catégorie et ses sous-catégories
                $posts = $paginator->paginate(
                    $postRepository->findByCategoryAndChildrenQuery($category),
                    $request->query->getInt('page', 1),
                    10
                );
            }
        }

        // Recherche textuelle unifiée (titre + contenu + catégories)
        elseif ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide, utiliser la recherche avec pagination
            $posts = $paginator->paginate(
                $postRepository->findBySearch($searchData),
                $request->query->getInt('page', 1),
                10
            );
        }

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'lastPost' => $lastPost,
            'categories' => $categories,
            'searchDataForm' => $form,
            'selectedCategory' => $selectedCategory,
        ]);
    }
}
