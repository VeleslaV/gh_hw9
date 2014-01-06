<?php

namespace Veles\HomeWorkBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', 'entity', array(
                'class' => 'VelesHomeWorkBundle:Category',
                'property' => 'title',
            ))
            ->add('title', 'text')
            //->add('img', 'text')
            ->add('views', 'integer', array(
                'data' => '0',
                'read_only' => true,
            ))
            ->add('tags', 'entity', [
                'class' => 'VelesHomeWorkBundle:Tag',
                'property' => 'title',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function(\Veles\HomeWorkBundle\Entity\TagRepository $er){
                    return $er->createQueryBuilder('t')
                    ->orderBy('t.name', 'DESC');
                },
            ])
            ->add('body', 'textarea');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Veles\HomeWorkBundle\Entity\Article',
        ));
    }

    public function getName()
    {
        return 'article';
    }
}