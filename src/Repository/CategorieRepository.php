<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    /**
     * Récupère toutes les catégories parent (sans parent)
     * @return Categorie[]
     */
    public function findParentCategories(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.parent IS NULL')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les sous-catégories d'une catégorie parent
     * @return Categorie[]
     */
    public function findSubCategories(Categorie $parent): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.parent = :parent')
            ->setParameter('parent', $parent)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère toutes les catégories avec leurs sous-catégories en une seule requête
     * @return array
     */
    public function findAllCategoriesGrouped(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.categories', 'children')
            ->addSelect('children')
            ->where('c.parent IS NULL')
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('children.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère une catégorie avec ses sous-catégories
     */
    public function findWithSubCategories(int $id): ?Categorie
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.categories', 'sub')
            ->addSelect('sub')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère toutes les catégories organisées hiérarchiquement
     * @return Categorie[]
     */
    public function findAllHierarchical(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.categories', 'sub')
            ->addSelect('sub')
            ->andWhere('c.parent IS NULL')
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('sub.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Categorie[] Returns an array of Categorie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Categorie
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
