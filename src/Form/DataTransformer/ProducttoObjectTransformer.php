<?php
namespace App\Form\DataTransformer;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ProducttoObjectTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (product) to a string (prod).
     *
     * @param  Product|null $product
     */
    public function transform($product): string
    {
        if (null === $product) {
            return '';
        }

        return $product->getId();
    }

    /**
     * Transforms a string from the form to an object (product).
     *
     * @param  string $productform
     * @throws TransformationFailedException if object (product) is not found.
     */
    public function reverseTransform($productform): ?Product
    {
        // no product number? It's optional, so that's ok
        if (!$productform) {
            return null;
        }

        $product = $this->entityManager
            ->getRepository(Product::class)
            // query for the product with this id
            ->find($productform)
        ;

        if (null === $product) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An product with name "%s" does not exist!',
                $productform
            ));
        }

        return $product;
    }
}