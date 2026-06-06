<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Evenements;
use App\Entity\Commandes;
use App\Entity\Tickets;
use App\Entity\Categories;
use App\Controller\Admin\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Controller\Admin\EvenementsCrudController;
use App\Controller\Admin\CommandesCrudController;
use App\Controller\Admin\TicketsCrudController;
use App\Controller\Admin\CategoriesCrudController;


#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EasyOrganis');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa fa-user');
        yield MenuItem::linkTo(EvenementsCrudController::class, 'Evenements', 'fa fa-calendar');
        yield MenuItem::linkTo(CommandesCrudController::class, 'Commandes', 'fa fa-shopping-cart');
        yield MenuItem::linkTo(TicketsCrudController::class, 'Tickets', 'fa fa-ticket');
        yield MenuItem::linkTo(CategoriesCrudController::class, 'Categories', 'fa fa-list');
    }
}
