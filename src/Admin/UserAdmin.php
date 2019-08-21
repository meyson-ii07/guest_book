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
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdmin extends AbstractAdmin
{



    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('promote', $this->getRouterIdParameter().'/promote')
            ->add('demote' ,$this->getRouterIdParameter().'/demote');
    }
    protected function configureFormFields(FormMapper $formMapper)
    {

        $formMapper->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('plain_password', PasswordType::class)
            ->add('status', ChoiceFieldMaskType::class, [
                'choices' => [
                    'User' => 1,
                    'Moderator' => 2,
                    'Admin' => 3,
                ]
            ])
            ->end();
    }
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('user_name')
            ->add('status')
            ->add('email')
            ->add('created_at');
    }
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('user_name')
            ->add('rolesString')
            ->add('email')
            ->add('created_at')
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                    'promote' => [
                        'template' => 'Admin/user/promote.html.twig'
                    ],
                    'demote' =>[
                        'template' => 'Admin/user/demote.html.twig'
                    ]
                ],
            ]);
    }


}