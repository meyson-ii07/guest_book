<?php
namespace App\Controller;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminController extends CRUDController
{


    /**
     * @param $id
     * @return RedirectResponse
     */
    public function promoteAction($id){
        $object = $this->admin->getSubject();
        if (!$object) {
            $this->addFlash('error',sprintf('unable to find the user with id: %s', $id));
        }
        if($object->getStatus() == 1) {

            $object->setStatus(2);
            $this->addFlash('success','user promoted');
        }
        else if($object->getStatus() == 2  ) {
            $object->setStatus(3);

            $this->addFlash('success','user promoted');
        }
        else $this->addFlash('error',sprintf('unable to promote the user with id: %s', $id));

        $this->admin->update($object);
        return new RedirectResponse($this->admin->generateUrl('list'));

    }
    /**
     * @param $id
     * @return RedirectResponse
     */
    public function demoteAction($id)
    {
        $object = $this->admin->getSubject();
        if (!$object) {
            $this->addFlash('error', sprintf('unable to find the user with id: %s', $id));
        }
        if($object->getStatus() == 3) {
            $object->setStatus(2);
            $this->addFlash('success','user demoted');
        }
        else if($object->getStatus() == 2) {
            $object->setStatus(1);
            $this->addFlash('success','user demoted');
        }
        else $this->addFlash('error',sprintf('unable to demote the user with id: %s', $id));
        $this->admin->update($object);
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}