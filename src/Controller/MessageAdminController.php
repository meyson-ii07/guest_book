<?php

namespace App\Controller;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Message;

class MessageAdminController extends CRUDController
{
    /**
     * @param $id
     * @return RedirectResponse
     */
    public function verifyAction($id)
    {
        $object = $this->admin->getSubject();
        if (!$object) {
            $this->addFlash('error',sprintf('unable to find the post with id: %s', $id));
        }
        else {
            $object->setStatus(1);
            $this->admin->update($object);
            $this->addFlash('success', 'successfully verified');
        }
        return new RedirectResponse(
            $this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()])
        );
    }
}