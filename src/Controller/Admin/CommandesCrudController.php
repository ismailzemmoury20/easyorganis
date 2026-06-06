<?php

namespace App\Controller\Admin;

use App\Entity\Commandes;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class CommandesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commandes::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user_id'),
            AssociationField::new('evenement_id'),
            NumberField::new('montant_total'),
            DateTimeField::new('date_commande'),
            TextField::new('statut'),
        ];
        }
}
