<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user_index")
     */
    public function index(Request $request,MessageRepository $messageRepository,PaginatorInterface $paginator): Response
    {
        $user_id = $this->getUser()->getId();
        $query  = $messageRepository->createQueryBuilder('m')
            ->andWhere('m.user = :user')
            ->setParameter('user', $this->getUser())
            ->andWhere('m.status = :status')
            ->setParameter('status',1)
            ->getQuery();


        $messages = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            25 /*limit per page*/
        );
        return $this->render('message/index.html.twig', [
            'messages' => $messages,
        ]);
    }
}
