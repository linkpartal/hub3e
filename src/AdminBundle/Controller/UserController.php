<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GenericBundle\Entity\Etablissement;
use GenericBundle\Entity\Notification;
use GenericBundle\Entity\Tier;


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


        if($request->get('_role')=='ROLE_ADMINSOC' || $request->get('_role')=='ROLE_ADMINECOLE')
        {
            $etab = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_id'));
            $newuser->setTier($etab->getTier());
        }
        if($request->get('_role')=='ROLE_RECRUTE' || $request->get('_role')=='ROLE_TUTEUR')
        {
            $etab = $this->getDoctrine()->getRepository('GenericBundle:Etablissement')->find($request->get('_id'));
            $newuser->setEtablissement($etab);
        }


        $em = $this->getDoctrine()->getManager();
        $em->persist($newuser);
        $em->flush();

        $superadmins = $this->getDoctrine()->getRepository('GenericBundle:User')->findByRole('ROLE_SUPER_ADMIN');

        foreach($superadmins as $admin){
            $notif = new Notification();
            $notif->setEntite($newuser->getId());
            $notif->setType('Utilisateur');
            $notif->setUser($admin);
            $em->persist($notif);
            $em->flush();
        }

        //send password
        $message = \Swift_Message::newInstance()
            ->setSubject('Email')
            ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
            ->setTo($request->get('_mail'))
            ->setBody($this->renderView('GenericBundle:Mail:NewUser.html.twig',array('username'=>$request->get('_Username'), 'password'=>$password))
                ,'text/html'
            );
        $this->get('mailer')->send($message);

        if($request->get('_id'))
        {
            return $this->redirect($this->generateUrl('metier_user_affiche',array('id'=>$request->get('_id'))));
        }
        else
        {
            return $this->redirect($this->generateUrl('metier_user_admin'));
        }


    }

    public function expiredAction($id){
        $utilis = $this->getDoctrine()->getRepository('GenericBundle:User')->find($id);
        $reponse = new JsonResponse();
        if($utilis->isExpired())
        {
            $utilis->setExpired(false);
            $reponse->setData(array('succes'=>'0'));
        }
        else{
            $utilis->setExpired(true);
            $reponse->setData(array('succes'=>'1'));
        }
        $this->getDoctrine()->getEntityManager()->flush();

        return $reponse;
    }

    public function modifierAction($id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $licencedef = $this->getDoctrine()->getRepository('GenericBundle:Licencedef')->findAll();
        $userid = $this->getDoctrine()->getRepository('GenericBundle:User')->find($id);





        $tiers = $this->getDoctrine()->getRepository('GenericBundle:Tier')->findAll();
        return $this->render('AdminBundle:Admin:modifierUtilisateur.html.twig',array('licencedef'=>$licencedef,'user'=>$userid
        ));
    }
    public function userModifAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('GenericBundle:User')->findOneBy(array('id'=>$request->get('_ID')));

        $user->setCivilite($request->get('_Civilite'));
        $user->setNom($request->get('_Nom'));
        $user->setPrenom($request->get('_Prenom'));
        $user->setTelephone($request->get('_Tel'));
        $user->setUsername($request->get('_Username'));
        $user->setEmail($request->get('_Mail'));






        $em->flush();

        return $this->forward('AdminBundle:Default:affichageUser',array('id'=>$request->get('_ID')));
    }


    public function supprimeruserAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('GenericBundle:User')->find($id);

        if(!$user)
        {
            throw new Exception('Aucun utilisateur ne posséde l\'id ' . $id);
        }


        $em->remove($user);
        $em->flush();






        return $this->render('AdminBundle:Admin:iFrameContent.html.twig');
    }

    public function adressesAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository('GenericBundle:User')->find($id);
        $user = $em->getRepository('GenericBundle:User')->findBy(array('tier'=>$user->getTier()));
        $adresses = array();
        foreach($user as $value)
        {
            $adresse = array('id'=>$value->getId(),'adresse' => $value->getAdresse());
            array_push($adresses, json_encode($adresse) );
        }
        $reponse = new JsonResponse();
        return $reponse->setData(array('adresses'=>$adresses));
    }
}
