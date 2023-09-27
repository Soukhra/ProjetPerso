<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Transport;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TransportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Transport::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nomTransporteur'),
            TextField::new('description')->onlyOnForms(),
            DateTimeField::new('updatedAt')->setFormat('d/M/Y à H:m:s')->hideOnForm(),
            MoneyField::new('prix')->setCurrency('EUR')->setNumDecimals(2),
        ];
    }
    public function createEntity(string $entityFqcn)
    {
        $transport =new $entityFqcn;
        $transport->setUpdatedAt(new DateTime);
        return $transport;
    }
    
}
