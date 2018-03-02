<?php

namespace Criticalmass\Bundle\AppBundle\Admin;

use Criticalmass\Bundle\AppBundle\Entity\Region;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegionAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Region', ['class' => 'col-md-6'])
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, ['required' => false])
            ->end()
            ->with('Settings', ['class' => 'col-md-6'])
            ->add('slug', TextType::class)
            ->add('parent', EntityType::class, ['class' => Region::class, 'required' => false])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('description')
            ->add('parent');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('parent');
    }
}
