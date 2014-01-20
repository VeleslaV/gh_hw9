<?php

namespace Veles\HomeWorkBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ArticleAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('category', 'entity', array(
                'class' => 'Veles\HomeWorkBundle\Entity\Category',
                'property' => 'title'
            ))
            ->add('title', 'text', array('label' => 'Article Title'))
            ->add('img', 'vlabs_file', array(
                'required' => false
            ))
            ->add('views', 'hidden', array(
                'data' => '0',
                'read_only' => true
            ))
            ->add('body')
            ->add('tags', 'sonata_type_model', array(
                'class' => 'VelesHomeWorkBundle:Tag',
                'property' => 'title',
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false
            ))
            ->add('created')
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('title')
            ->add('img')
            ->add('created')
        ;
    }
}