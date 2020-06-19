<?php

namespace UVDesk\CommunityPackages\UVDesk\FormComponent\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use UVDesk\CommunityPackages\UVDesk\FormComponent\CustomFields;
use UVDesk\CommunityPackages\UVDesk\FormComponent\CollectionType;

class CustomFieldsType extends AbstractType
{
    private $_input_types = [
                                'text' => 'text',
                                'textarea' => 'textarea',
                                'select' => 'select',
                                'checkbox' => 'checkbox',
                                'radio' => 'radio',
                                'file' => 'file',
                                'date' => 'date',
                                'time' => 'time',
                                'datetime' => 'datetime',
                            ];

    private $_agent_types = [
                                'apps'  => 'apps',
                                'customer' => 'customer',
                                'user' => 'user',
                                'both' => 'both', 
                            ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array(
            'label' => 'Name',
            'attr' => array(
                    'placeholder' => 'Enter type name'
                )
            )
        );

        // $builder->add('dependancy');

        $builder->add('agentType', ChoiceType::class, array(
            'choices' => $this->_agent_types,
            'label' => 'Agent Type',
            'expanded' => false,
            'multiple' => false,
            'empty_data' => 'Customer Panel',
            'attr' => array(
                    'placeholder' => 'Select Agent Type'
                )
            )
        );

        $builder->add('fieldType', ChoiceType::class, array(
            'choices' => $this->_input_types,
            'label' => 'Field Type',
            'multiple' => false,
            'attr' => array(
                    'placeholder' => 'Select Field Type'
                )
            )
        );

        $builder->add('value', TextType::class, array(
            'required' => false,
            'label' => 'Value',
            'attr' => array(
                    'placeholder' => 'Enter default value'
                )
            )
        );

        $builder->add('status', CheckboxType::class, array(
                'required' => false,
                'attr' => [
                            'class' => 'i-check',
                            'brAfterLabel' => true,
                        ]
            )
        );

        $builder->add('required', CheckboxType::class, array(
                'required' => false,
                'attr' => [
                            'class' => 'i-check',
                            'brAfterLabel' => true,
                        ]
            )
        );

        $builder->add('encryption', CheckboxType::class, array(
                'required' => false,
                'attr' => [
                            'class' => 'i-check',
                            'brAfterLabel' => true,
                        ]
            )
        );

        $builder->add('sortOrder', NumberType::class, array(
            'required' => false,
            'label' => 'Sort Order',
            'attr' => array(
                    'placeholder' => 'Enter sort order'
                )
            )
        );

        $builder->add('save', SubmitType::class, array(
            'label' => 'Save',
            'attr' => array(
                    'class'=>'btn btn-info btn-md pull-left'
                )
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UVDesk\CommunityPackages\UVDesk\FormComponent\Entity\CustomFields',
            'cascade_validation' => true,
            'allow_extra_fields' => true
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function getName()
    {
        return 'field';
    }
}