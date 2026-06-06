<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Evenements;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false;

        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le nom de l\'événement est obligatoire.'),
                ],
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(message: 'La date est obligatoire.'),
                    new GreaterThan('today', message: 'La date doit être dans le futur.'),
                ],
            ])
            ->add('lieu', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le lieu est obligatoire.'),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(message: 'La description est obligatoire.'),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de couverture',
                'mapped' => false,
                'required' => !$isEdit,
                'constraints' => $isEdit ? [] : [
                    new NotBlank(message: 'Une image est obligatoire.'),
                    new File(
                        maxSize: '5M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                        mimeTypesMessage: 'Format accepté : JPG, PNG, WebP.',
                    ),
                ],
            ])
            ->add('nombres_places', IntegerType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le nombre de places est obligatoire.'),
                    new Positive(message: 'Le nombre de places doit être positif.'),
                ],
            ])
            ->add('prix_ticket', NumberType::class, [
                'scale' => 2,
                'constraints' => [
                    new NotBlank(message: 'Le prix du ticket est obligatoire.'),
                    new GreaterThan(value: -1, message: 'Le prix ne peut pas être négatif.'),
                ],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'nomCategorie',
                'placeholder' => 'Choisir une catégorie',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenements::class,
            'is_edit' => false,
        ]);
    }
}
