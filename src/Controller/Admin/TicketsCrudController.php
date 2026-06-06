<?php

namespace App\Controller\Admin;

use App\Entity\Tickets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class TicketsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tickets::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('evenement_id'),
            AssociationField::new('user_id'),
            TextField::new('code_unique'),
            TextField::new('statut'),
            DateTimeField::new('date_achat'),
        ];
    }
}
