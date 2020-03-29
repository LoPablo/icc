<?php

namespace App\Form;

use App\Converter\EnumStringConverter;
use App\Converter\UserTypeStringConverter;
use App\Entity\TimetablePeriodVisibility;
use App\Entity\UserTypeEntity;
use App\Sorting\TimetablePeriodVisibilityStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TimetablePeriodType extends AbstractType {

    private $periodVisibilityStrategy;
    private $enumStringConverter;

    public function __construct(TimetablePeriodVisibilityStrategy $periodVisibilityStrategy, EnumStringConverter $enumStringConverter) {
        $this->periodVisibilityStrategy = $periodVisibilityStrategy;
        $this->enumStringConverter = $enumStringConverter;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('externalId', TextType::class, [
                'label' => 'label.external_id'
            ])
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('start', DateType::class, [
                'label' => 'label.start'
            ])
            ->add('end', DateType::class, [
                'label' => 'label.end'
            ])
            ->add('visibilities', SortableEntityType::class, [
                'label' => 'label.visibility',
                'class' => UserTypeEntity::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => function(UserTypeEntity $visibility) {
                    return $this->enumStringConverter->convert($visibility->getUserType());
                },
                'sort_by' => $this->periodVisibilityStrategy
            ]);
    }
}