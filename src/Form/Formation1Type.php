<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class Formation1Type extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator){
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
            $builder->add('name');
            $builder
                ->add('image', FileType::class, [
                        'label' => 'Illustration',

                        'mapped' => false,

                        'required' => false,

                        'constraints' => [
                            new File([
                                'maxSize' => '1024k',
                                'mimeTypes' => [
                                    'image/jpg',
                                    'image/jpeg',
                                    'image/png',
                                ],
                                'mimeTypesMessage' => 'Please upload a valid Image document',
                            ])
                        ],
                    ]);
            $builder->add('content');
            $builder->add('capacity');
            $builder->add('price');
            //  ->add('createdAt')
            $builder->add('description');
            $builder->add('startDate', DateType::class, [
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
                'input' => 'datetime_immutable',
            ]);
            $builder->add('endDate',  DateType::class, [
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
                'input' => 'datetime_immutable',
            ]);
            $builder->add('speaker');
            $builder->add('createdBy');
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
