<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class TopicType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('topicTitle')
            ->add('topicSubject', CKEditorType::class, array(
        'config' => array(
            'uiColor' => '#ffffff',
            //...
        )))
            //->add('topicCat')
          //  ->add('topicBy')
            ->add('topicCat',EntityType::class,array(
                'class'=>'UserBundle\Entity\Categorietopics','choice_label'=>'cat_name','multiple'=>false
            ))
            ->add('file');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\Topic'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userbundle_topic';
    }


}
