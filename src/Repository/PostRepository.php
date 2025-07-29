<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Categorie;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * search bar query - retourne une Query Builder pour la pagination
     * @param \App\Model\SearchData $searchData
     * @return \Doctrine\ORM\Query
     */
    public function findBySearch(SearchData $searchData)
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.categorie', 'c')
            ->orderBy('p.createdAt', 'DESC');

        if (!empty($searchData->q)) {
            // Recherche dans le titre, le contenu ET les noms de catégories
            $qb->andWhere('
                p.title LIKE :query
                OR p.content LIKE :query
                OR c.name LIKE :query
            ')
                ->setParameter('query', '%' . $searchData->q . '%');
        }

        return $qb->getQuery();
    }

    /**
     * Trouve tous les posts d'une catégorie et de ses sous-catégories
     */
    public function findByCategoryAndChildren(Categorie $category, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.categorie', 'c')
            ->where('c = :category');

        // Si c'est une catégorie parent, inclure aussi ses enfants
        if ($category->getCategories()->count() > 0) {
            $qb->orWhere('c.parent = :category');
        }

        $qb->setParameter('category', $category)
            ->orderBy('p.createdAt', 'DESC');

        // Appliquer la limite si spécifiée
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve tous les posts d'une catégorie et de ses sous-catégories - retourne une Query pour pagination
     */
    public function findByCategoryAndChildrenQuery(Categorie $category)
    {
        // Récupérer tous les IDs de catégories (parent + enfants)
        $categoryIds = [$category->getId()];

        // Ajouter les sous-catégories
        foreach ($category->getCategories() as $subCategory) {
            $categoryIds[] = $subCategory->getId();
        }

        return $this->createQueryBuilder('p')
            ->join('p.categorie', 'c')
            ->where('c.id IN (:categoryIds)')
            ->setParameter('categoryIds', $categoryIds)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();
    }

    /**
     * Trouve les posts par catégories multiples (OR)
     */
    public function findByCategories(array $categories): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.categorie', 'c')
            ->where('c IN (:categories)')
            ->setParameter('categories', $categories)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les posts ayant toutes les catégories spécifiées (AND)
     */
    public function findByAllCategories(array $categories): array
    {
        $qb = $this->createQueryBuilder('p');

        foreach ($categories as $index => $category) {
            $qb->join('p.categorie', 'c' . $index)
                ->andWhere('c' . $index . ' = :category' . $index)
                ->setParameter('category' . $index, $category);
        }

        return $qb->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Statistiques des posts par catégorie
     */
    public function findByPostCountByCategory(?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('c.name as categoryName, c.slug as categorySlug, COUNT(p.id) as postCount')
            ->join('p.categorie', 'c')
            ->groupBy('c.id')
            ->orderBy('postCount', 'DESC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    //affiche le dernier post ajouté
    public function findLastPost(?int $limit = null): Post|null
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }
        return $qb->getQuery()->getOneOrNullResult();

    }
}
//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
