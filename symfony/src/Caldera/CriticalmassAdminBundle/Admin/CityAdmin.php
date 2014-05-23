<?php

namespace Caldera\CriticalmassAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CityAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('city', 'text', array('label' => 'Stadt'))
            ->add('title', 'text', array('label' => 'Bezeichnung'))
            ->add('description', 'text', array('label' => 'Beschreibung'))
            ->add('url', 'text', array('label' => 'Webseite'))
            ->add('facebook', 'text', array('label' => 'facebook-Seite'))
            ->add('twitter', 'text', array('label' => 'Twitter-Konto'))
            ->add('latitude', 'text', array('label' => 'Breitengrad'))
            ->add('longitude', 'text', array('label' => 'Längengrad'))
            ->add('slugs', 'sonata_type_model', array('expanded' => false, 'by_reference' => false, 'multiple' => true));
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('city')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('city')
        ;
    }
}