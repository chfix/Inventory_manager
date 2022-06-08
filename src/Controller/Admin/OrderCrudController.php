<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }


   
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            AssociationField::new('Product')->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                $queryBuilder->where('entity.active = true');
            }),

            MoneyField::new('unity_price')->setCurrency('EUR')->hideOnForm(),

            IntegerField::new('quantity'),

            MoneyField::new('total_price')->setCurrency('EUR')->hideOnForm(),


            DateTimeField::new('updatedAt')->hideOnForm(),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }

    

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Order) return;
        $entityInstance->setCreatedAt(new \DateTimeImmutable);

        $product = $entityInstance->getProduct();

        
        $uprice = $product->getPrice();
        $entityInstance->setUnityPrice($uprice);

        $pqte = $product->getQuantity();
        $qte = $entityInstance->getQuantity();

        $oqte = ($pqte-$qte);

        if ($pqte >= $qte)
        {
            $product->setQuantity($oqte);
        $entityInstance->setTotalPrice($qte*$uprice);
        parent::persistEntity($em, $entityInstance); 
        }
        else {
            $this->addFlash(
            'notice',
            'Product quantity requested isnt available, try choosing a smaller quantity'
        );
        }
        
            
    }

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Order) return;
        $entityInstance->setUpdatedAt(new \DateTimeImmutable);
        $product = $entityInstance->getProduct();

        $uprice = $product->getPrice();
        $pqte = $product->getQuantity();
        $qte = $entityInstance->getQuantity();

        $oqte = ($pqte-$qte);

        if ($pqte >= $qte)
        {
        $product->setQuantity($oqte);
        $entityInstance->setTotalPrice($qte*$uprice);
        
        parent::updateEntity($em, $entityInstance);
        }
        else {
            $this->addFlash(
            'notice',
            'Product quantity requested isnt available, try choosing a smaller quantity'
        );
    }

    }
}