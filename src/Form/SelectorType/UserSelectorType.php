<?php

namespace App\Form\SelectorType;

use App\Form\DataTransformer\UserToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSelectorType extends AbstractType
{
    private UserToNumberTransformer $transformer;

    public function __construct(UserToNumberTransformer $transformer) {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['invalid_massage' => 'The selected user does not exist']);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}