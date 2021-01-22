<?php
/**
 * Created by PhpStorm.
 * User: med
 * Date: 09/04/2019
 * Time: 20:52
 */

namespace UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Notification;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class NotificationController extends Controller
{
    public function displayAction(){
        $idu = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $notif = $em->getRepository('UserBundle:Notification')->findByIdm($idu);
        return $this->render("@User/Notification/notification.html.twig",array(
            'notifs'=>$notif
        ));

    }
    public function deleteAction(Request $request ,$id){
        $em = $this->getDoctrine()->getManager();
        $notif = $em->getRepository('UserBundle:Notification')->find($id);
        $em->remove($notif);
        $em->flush();
        return $this->redirectToRoute("association_listmy");
    }

}