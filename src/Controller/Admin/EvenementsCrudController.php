<?php

namespace App\Controller\Admin;

use App\Entity\Evenements;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class EvenementsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Evenements::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom'),
            DateField::new('date'),
            TextField::new('lieu'),
            TextareaField::new('description'),
            TextField::new('image'),
            IntegerField::new('nombres_places'),
            NumberField::new('prix_ticket'),
        ];
    }
}
