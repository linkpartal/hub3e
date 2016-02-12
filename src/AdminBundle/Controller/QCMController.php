<?php

namespace AdminBundle\Controller;

use GenericBundle\Entity\Qcmdef;
use GenericBundle\Entity\Questiondef;
use GenericBundle\Entity\Reponsedef;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class QCMController extends Controller
{
    public function loadAction($id='ajouter')
    {
        if($id == 'ajouter')
        {
            return $this->render('AdminBundle:Admin:GestionQCM.html.twig');
        }
        else{
            $qcm = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->find($id);
            $questions = $this->getDoctrine()->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef'=>$qcm));
            usort($questions,array('\GenericBundle\Entity\Questiondef','sort_questions_by_order'));
            $reponses = array(array());
            for($i = 0; $i < count($questions); $i++)
            {
                $reps = $this->getDoctrine()->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef'=>$questions[$i]));
                usort($reps,array('\GenericBundle\Entity\Reponsedef','sort_reponses_by_order'));
                $reponses[] = $reps;
            }
            return $this->render('AdminBundle:Admin:GestionQCM.html.twig', array('QCM'=>$qcm ,'Questions'=>$questions,'reponses'=>$reponses));
        }

    }



    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        //get qcm s'il existe.
        $qcm = $em->getRepository('GenericBundle:Qcmdef')->findOneBy(array('nom'=>$request->get('_Nom')));
        if(!$qcm)
        {
            //s'il n'existe pas le créer.
            $newqcm = new Qcmdef();
            $newqcm->setNom($request->get('_Nom'));



            $em->persist($newqcm);
            $em->flush();

            $qcm= $newqcm;
        }
        $qcm->setAffinite($request->get('_affinite'));
        $em->flush();

        $questions = array();

        $questionorder = 0;
        if($request->get('questions'))
        {

            foreach($request->get('questions') as $value)
            {
                //get Question s'elle existe.
                $question = $em->getRepository('GenericBundle:Questiondef')->findOneBy(array('question'=>$value,'qcmdef'=>$qcm));
                if(!$question)
                {
                    //S'elle n'existe pas la créer.
                    $newquestion = new Questiondef();
                    $newquestion->setQuestion($value);
                    $newquestion->setQcmdef($qcm);
                    $em->persist($newquestion);
                    $em->flush();
                    $question = $newquestion;
                }
                $question->setOrdre($questionorder++);
                $em->flush();
                array_push($questions,$question);

            }

            $i = 0;
            foreach( $request->get('reponse') as $reponses)
            {
                $reponseorder = 0;

                foreach($reponses as $rep)
                {
                    //get Reponse s'elle existe.
                    $reponse = $em->getRepository('GenericBundle:Reponsedef')->findOneBy(array('reponse'=>$rep,'questiondef'=>$questions[$i]));

                    if(!$reponse)
                    {
                        //S'elle n'existe pas la créer.
                        $newreponse = new Reponsedef();
                        $newreponse->setReponse($rep);
                        $newreponse->setQuestiondef($questions[$i]);
                        $em->persist($newreponse);
                        $em->flush();
                        $reponse = $newreponse;
                    }
                    $reponse->setOrdre($reponseorder++);

                    $em->flush();

                }
                $i++;
            }

        }

        return $this->render('AdminBundle:Admin:iFrameContent.html.twig');
    }

    public function supprimerAction($rep,$qst,$qcm)
    {
        $em = $this->getDoctrine()->getEntityManager();

        //get Formulaire
        $formulaire = $em->getRepository('GenericBundle:Qcmdef')->findOneBy(array('nom'=>$qcm));

        $question = $em->getRepository('GenericBundle:Questiondef')->findOneBy(array('question'=>$qst,'qcmdef'=>$formulaire));

        if($rep == "null")
        {
            $em->remove($question);
        }
        else{
            $reponse = $em->getRepository('GenericBundle:Reponsedef')->findOneBy(array('reponse'=>$rep,'questiondef'=>$question));
            $em->remove($reponse);
        }

        $em->flush();

        $reponse = new JsonResponse();
        return $reponse->setData(array('reponse'=>$rep,'question'=>$qst,'QCM'=>$qcm));

    }

    public function deleteQcmAction($nom){
        $qcm = $this->getDoctrine()->getRepository('GenericBundle:Qcmdef')->findOneBy(array('nom'=>$nom));
        $this->getDoctrine()->getEntityManager()->remove($qcm);
        $this->getDoctrine()->getEntityManager()->flush();
        return $this->render('AdminBundle:Admin:iFrameContent.html.twig');

    }
}
