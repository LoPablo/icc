<?php

namespace App\Controller\Admin;

use App\Entity\Tuition;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class TuitionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tuition::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Tuition')
            ->setEntityLabelInPlural('Tuition')
            ->setSearchFields(['externalId', 'name', 'displayName', 'id', 'uuid']);
    }

    public function configureFilters(Filters $filters): Filters {
        return $filters
            ->add('section')
            ->add(EntityFilter::new('studyGroup')->canSelectMultiple(true));
    }

    public function configureFields(string $pageName): iterable
    {
        $externalId = TextField::new('externalId');
        $name = TextField::new('name');
        $displayName = TextField::new('displayName');
        $subject = AssociationField::new('subject');
        $teachers = AssociationField::new('teachers');
        $studyGroup = AssociationField::new('studyGroup');
        $id = IntegerField::new('id', 'ID')->hideOnForm();
        $section = AssociationField::new('section');
        $bookEnabled = BooleanField::new('isBookEnabled');

        return [$id, $externalId, $name, $displayName, $subject, $teachers, $bookEnabled, $studyGroup, $section];
    }
}
