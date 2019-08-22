<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use \App\Service\FileUploader;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/message")
 */
class MessageController extends AbstractController
{
    /**
     * @Route("/", name="message_index", methods={"GET"})
     * @param Request $request
     * @param MessageRepository $messageRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request,MessageRepository $messageRepository,PaginatorInterface $paginator): Response
    {
        $query  = $messageRepository->createQueryBuilder('m')
            ->andWhere('m.status = :status')
            ->setParameter('status', 1)
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

    /**
     * @Route("/new", name="message_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request,FileUploader $fileUploader): Response
    {

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $fileUploader = new FileUploader($this->getParameter('image_dir'));
            $file = $form['Image']->getData();

            if ($file) {
                $imageFileName = $fileUploader->upload($file);
                $message->setImageName($imageFileName);
            }

            $message->setUser($this->getUser());
            $message->setText(htmlspecialchars($message->getText()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();
            return $this->redirectToRoute('message_index');
        }
        return $this->render('message/create.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="message_show", methods={"GET"})
     */
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="message_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function edit(Request $request, Message $message): Response
    {
        if ($message->getUser()->getId() === $this->getUser()->getId())
        {
            $form = $this->createForm(MessageType::class, $message);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $fileUploader = new FileUploader($this->getParameter('image_dir'));
                $file = $form['Image']->getData();

                if ($file) {
                    $imageFileName = $fileUploader->upload($file);
                    $message->setImageName($imageFileName);
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($message);
                $entityManager->flush();
                return $this->redirectToRoute('message_index');
            }

            return $this->render('message/create.html.twig', [
                'message' => $message,
                'form' => $form->createView(),
            ]);
        }
        else
            return $this->redirectToRoute('message_index');
    }

    /**
     * @Route("/delete/{id}", name="message_delete", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     * @param Request $request
     * @param Message $message
     * @return Response
     */
    public function delete(Request $request, Message $message): Response
    {
        if ($message->getUser()->getId() === $this->getUser()->getId())
        {
            if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($message);
                $entityManager->flush();
            }
        }
            return $this->redirectToRoute('message_index');

    }

    /**
     * Sets the KnpPaginator instance.
     *
     * @param Paginator $paginator
     *
     * @return mixed
     */
    public function setPaginator(Paginator $paginator)
    {
        // TODO: Implement setPaginator() method.
    }
}
