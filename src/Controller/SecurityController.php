<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //    $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
//        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
    /**
     * @Route("/forgot",name="security_forgot")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function forgot(Request $request,\Swift_Mailer $mailer)
    {
        $form =$this->createFormBuilder()
            ->add('email', EmailType::class, ['required' => true])
            ->add('confirm',SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $form->getData()['email']]);

            $url = $this->get('router')->getContext()->getHost().':'.$this->get('router')->getContext()->getHttpPort().$this->generateUrl(
                'security_change',
                ['key' => $user->getAuthKey()]
            );

           // dd($url);

            if($user !== null){
                $message = (new \Swift_Message('Change your password'))
                    ->setFrom($user->getEmail())
                    ->setTo($user->getEmail())
                    ->setBody(
                        'To reset your password use this link  '.$url
                    );
                $mailer->send($message);
                $this->addFlash('success', 'Check email for details.');
            }
            else $this->addFlash('error', 'User with this email doesnt exists.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig',[
            'registrationForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/change/{key}",name="security_change")
     * @param $key
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function change($key,Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['auth_key' => $key]);

       //  = $this->createForm(RegistrationFormType::class, $user);
        $form = $this->createFormBuilder()
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password'
                ],
                'second_options' => ['label'=>'Repeat password']
            ])
            ->add('confirm',SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword($passwordEncoder->encodePassword($user,$form->getData()['password']));
            $user->setAuthKey(Helper::generateRandomString(30));
            $this->addFlash('success', 'Password successfully changed.');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/register.html.twig',[
            'registrationForm' => $form->createView(),
        ]);
    }
}
