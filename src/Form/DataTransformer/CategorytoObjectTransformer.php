<?php
namespace App\Form\DataTransformer;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CategoryToObjectTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (Category) to a string (number).
     *
     * @param  Category|null $category
     */
    public function transform($category): string
    {
        if (null === $category) {
            return '';
        }

        return $category->getId();
    }

    /**
     * Transforms a string from the form to an object (category).
     *
     * @param  string $categoryform
     * @throws TransformationFailedException if object (category) is not found.
     */
    public function reverseTransform($categoryform): ?Category
    {
        // no category number? It's optional, so that's ok
        if (!$categoryform) {
            return null;
        }

        $category = $this->entityManager
            ->getRepository(Category::class)
            // query for the category with this id
            ->find($categoryform)
        ;

        if (null === $category) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An category with number "%s" does not exist!',
                $categoryform
            ));
        }

        return $category;
    }
}