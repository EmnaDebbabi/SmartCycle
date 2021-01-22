<?php
/**
 * Created by PhpStorm.
 * User: med
 * Date: 09/04/2019
 * Time: 20:52
 */

namespace AdminBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\Notification;

class NotificationController extends Controller
{
    public function displayAction(){
        $idu = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $notif = $em->getRepository('UserBundle:Notification')->findByIdm($idu);
        return $this->render("@Admin/Notification/notification.html.twig",array(
            'notifs'=>$notif
        ));

    }

}