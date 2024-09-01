<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('draft', CheckboxType::class, [
                'label' => 'Brouillon',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom du jeu',
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('year', IntegerType::class, [
                'label' => 'Année de sortie',
            ])
            ->add('date', DateType::class, [
                'label' => 'Date du publication',
            ])
            ->add('studio', StudioType::class, [
                'label' => null,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $now = new \DateTime();

        $resolver->setDefaults([
            'data_class' => Game::class,
            'data' => Game::create(
                '',
                '',
                (int) $now->format('Y'),
                $now,
                [
                    'name' => '',
                    'url' => '',
                ],
                true,
            ),
        ]);
    }
}
