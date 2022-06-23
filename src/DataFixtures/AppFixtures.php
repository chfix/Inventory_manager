<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // create 20 products! 20 categories & 20 orders

        for ($i = 0; $i < 20; $i++) {
            $category= new Category();
            $category->setName('test '.$i);
            $category->setActive(true);
            $category->setCreatedAt(new \DateTimeImmutable);
            $manager->persist($category);

            $product = new Product();
            $product->setName('product '.$i);
            $product->setPrice(mt_rand(10, 100));
            $product->setQuantity(mt_rand(100, 2000));
            $product->setCategory($category);
            $product->setCreatedAt(new \DateTimeImmutable);
            $manager->persist($product);

            $order= new Order();
            $order->setCreatedAt(new \DateTimeImmutable);
            $order->setQuantity(mt_rand(10, 80));
            $order->setUnityPrice($product->getPrice());
            $order->setTotalPrice($product->getPrice()*$product->getQuantity());
            $order->setProduct($product);
            $manager->persist($order);
        }
        $manager->flush();
    }
}
