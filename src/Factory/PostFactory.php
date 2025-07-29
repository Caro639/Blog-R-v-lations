<?php

namespace App\Factory;

use App\Entity\Post;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Post>
 */
final class PostFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Post::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'content' => self::faker()->text(800),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year', 'now')),
            'slug' => self::faker()->slug(3, false),
            'title' => self::faker()->sentence(6),
            'user' => UserFactory::random(),
            'image' => self::faker()->optional(0.7)->imageUrl(640, 480, 'architecture', true),
        ];
    }

    /**
     * Crée un post avec des catégories aléatoires
     *
     * @param array $availableCategories Toutes les catégories disponibles
     * @param int $minCategories Nombre minimum de catégories
     * @param int $maxCategories Nombre maximum de catégories
     */
    public function withRandomCategories(array $availableCategories, int $minCategories = 1, int $maxCategories = 3): self
    {
        $numberOfCategories = self::faker()->numberBetween($minCategories, min($maxCategories, count($availableCategories)));
        $randomCategories = self::faker()->randomElements($availableCategories, $numberOfCategories);

        return $this->with(['categorie' => $randomCategories]);
    }

    /**
     * Crée un post avec des catégories liées (parent + enfants)
     *
     * @param array $categoryGroups Groupes de catégories (parent => [enfants])
     */
    public function withRelatedCategories(array $categoryGroups): self
    {
        $parentCategory = self::faker()->randomElement(array_keys($categoryGroups));
        $childCategories = $categoryGroups[$parentCategory];

        // Sélectionner le parent et 1-2 enfants aléatoires
        $selectedCategories = [$parentCategory];
        $numberOfChildren = self::faker()->numberBetween(1, min(2, count($childCategories)));
        $selectedChildren = self::faker()->randomElements($childCategories, $numberOfChildren);

        $selectedCategories = array_merge($selectedCategories, $selectedChildren);

        return $this->with(['categorie' => $selectedCategories]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Post $post): void {})
        ;
    }
}
