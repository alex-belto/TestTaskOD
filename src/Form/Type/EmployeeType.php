<?php

namespace App\Form\Type;

use App\Entity\Employee;
use App\Form\SelectorType\CompanySelectorType;
use App\Form\SelectorType\ProjectSelectorType;
use App\Form\SelectorType\UserSelectorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salary', TextType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('connectedUser', UserSelectorType::class)
            ->add('company', CompanySelectorType::class)
            ->add('project', ProjectSelectorType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Employee::class]);
    }

}