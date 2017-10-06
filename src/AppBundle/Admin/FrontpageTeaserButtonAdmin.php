<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FrontpageTeaserButtonAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Teaser', ['class' => 'col-md-6'])
            ->add('frontpageTeaser')
            ->add('position', NumberType::class, ['required' => true])
            ->end()

            ->with('Button', ['class' => 'col-md-6'])
            ->add('caption', TextType::class, ['required' => false])
            ->add('link', TextType::class, ['required' => false])
            ->end()

            ->with('Layout', ['class' => 'col-md-6'])
            ->add('icon', TextType::class, ['required' => false])
            ->add('class', TextType::class, ['required' => false])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('caption')
            ->add('frontpageTeaser')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('caption')
            ->add('frontpageTeaser')
            ->add('link')
        ;
    }
}
