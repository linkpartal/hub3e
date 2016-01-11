<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function addUserEcoleAction($from,$id)
    {
        if($from=='ecole')
        {
            $ecole = $this->getDoctrine()->getRepository('GenericBundle:Ecole')->find($id);
            if($ecole->getLogo())
            {
                $ecole->setLogo(base64_encode(stream_get_contents($ecole->getLogo())));
            }
            $licences = $this->getDoctrine()->getRepository('GenericBundle:Licenceecole')->findBy(array('ecoleecole'=>$ecole ));
            $users = $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('ecole'=>$ecole ));
            return $this->render('AdminBundle:Admin:AddUser.html.twig',array('ecole'=>$ecole,'libs'=>$licences,'usersecole'=>$users,'from'=>$from,'id'=>$id));
        }
        if($from=='societe')
        {
            $societe = $this->getDoctrine()->getRepository('GenericBundle:Societe')->find($id);
            $etablissements = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->findBy(array('societe'=>$societe));
            $users = $this->getDoctrine()->getRepository('GenericBundle:User')->findBy(array('societe'=>$societe ));
            $licences = $this->getDoctrine()->getRepository('GenericBundle:Licencesociete')->findBy(array('societe'=>$societe ));
            return $this->render('AdminBundle:Admin:AddUser.html.twig',array('societe'=>$societe,'libs'=>$licences,'usersoc'=>$users,'etablissements'=>$etablissements,'from'=>$from,'id'=>$id));
        }
        else{
            return $this->render('AdminBundle:Admin:AddUser.html.twig',array('from'=>$from,'id'=>$id));
        }

    }

    public function UserAddedAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $newuser = $userManager->createUser();
        $newuser->setUsername($request->get('_Username'));
        $newuser->setEmail($request->get('_mail'));
        $newuser->addRole($request->get('_role'));

        //generate a password
        $tokenGenerator = $this->get('fos_user.util.token_generator');
        $password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars
        $hash =  $this->get('security.password_encoder')->encodePassword($newuser, $password);
        $newuser->setPassword($hash);

        $etab = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_id'));
        if($request->get('_role')=='ROLE_ADMINSOC' || $request->get('_role')=='ROLE_ADMINECOLE')
        {
            $newuser->setTier($etab->getTier());
        }
        if($request->get('_role')=='ROLE_RECRUTE' || $request->get('_role')=='ROLE_TUTEUR')
        {
            $newuser->setEtablissement($etab);
        }


        $em = $this->getDoctrine()->getManager();
        $em->persist($newuser);
        $em->flush();

        //send password
        $message = \Swift_Message::newInstance()
            ->setSubject('Email')
            ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
            ->setTo($request->get('_mail'))
            ->setBody($this->renderView('GenericBundle:Mail:NewUser.html.twig',array('username'=>$request->get('_Username'), 'password'=>$password))
                ,'text/html'
            );
        $this->get('mailer')->send($message);



        return $this->forward('AdminBundle:Default:affichage',array('idliste'=>$request->get('_from'),'id'=>$request->get('_id')));

    }
}
