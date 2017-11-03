<?php

namespace Criticalmass\Bundle\AppBundle\Admin;

use AppBundle\Entity\HelpCategory;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class HelpCategoryAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Content', ['class' => 'col-md-6'])
            ->add('title', TextType::class)
            ->add('intro', TextAreaType::class, ['required' => false])
            ->end()
            ->with('Settings', ['class' => 'col-md-6'])
            ->add('parent', EntityType::class, [
                'class' => HelpCategory::class
            ])
            ->add('language', TextType::class)
            ->add('side', TextType::class, ['required' => false])
            ->add('position', NumberType::class, ['required' => false])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('intro')
            ->add('language')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('language')
        ;
    }
}
