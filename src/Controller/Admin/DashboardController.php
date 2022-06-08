<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
            ->setController(ProductCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Inventory Management Website | Admin Dashboard');
    }

    public function configureMenuItems(): iterable
    {        

        //yield MenuItem::section('Products');

        yield MenuItem::subMenu('Manage Products', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Create Product', 'fas fa-plus', Product::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show All Products', 'fas fa-eye', Product::class)
        ]);

        //yield MenuItem::section('Categories');

        yield MenuItem::subMenu('Manage Categories', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Create Category', 'fas fa-plus', Category::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show All Categories', 'fas fa-eye', Category::class)
        ]);

        //yield MenuItem::section('Orders');

        yield MenuItem::subMenu('Manage Product Orders', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Create A Product Order', 'fas fa-plus', Order::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show All Product Orders', 'fas fa-eye', Order::class)
        ]);
        
    }
}