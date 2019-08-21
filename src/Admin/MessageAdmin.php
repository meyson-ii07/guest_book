<?php
/**
 * Created by PhpStorm.
 * User: meyson
 * Date: 19.08.2019
 * Time: 16:48
 */

namespace App\Admin;


use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessageAdmin extends  AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'status',
    ];
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('text', TextareaType::class)
            ->add('user', EntityType::class,['class' => User::class,])
            ->end();
    }
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('text')
            ->add('created_at')
            ->add('status');
    }
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('verify', $this->getRouterIdParameter().'/verify');
    }
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('text','string', ['template' => 'Admin/message/message_text.html.twig' ] )
            ->addIdentifier('created_at')
            ->add('user.email')
            ->add('user.user_name')
            ->add('user.status')
            ->add('image_name',null ,['template' => 'Admin/message/message_image.html.twig'])
            ->addIdentifier('status')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'verify' => [
                        'template' => 'Admin/message/verify.html.twig'
                    ]
                ]
            ]);
    }
}