<?php

namespace GenericBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Hub3eEmailingCompleteProfilCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hub3e:emailing:completeProfil')
            ->setDescription('Envoi email demandant aux apprenant de completer leur profil.')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $apprenants = $em->getRepository('GenericBundle:User')->findByRole('ROLE_APPRENANT');
        foreach($apprenants as $apprenant){
            if(!$apprenant->getInfo()->getProfilcomplet()){
                if($apprenant->getEtablissement())
                {
                    foreach($apprenant->getEtablissement()->getQcmdef() as $qcmdef){
                        $questions = $em->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef'=>$qcmdef));
                        foreach($questions as $question){
                            $reponses = $em->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef'=>$question));

                            if($reponses )
                            {
                                if(count(array_intersect($reponses,$apprenant->getReponsedef()->toArray())) ==0){
                                    //$output->writeln($apprenant->getEmail());
                                    $message = \Swift_Message::newInstance()
                                        ->setSubject('Email')
                                        ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
                                        ->setTo($apprenant->getEmail())
                                        ->setBody($this->getContainer()->get('templating')->render('GenericBundle:Mail:EmailCompleterProfil.html.twig',array('apprenant'=>$apprenant))
                                            ,'text/html'
                                        );
                                    $this->getContainer()->get('mailer')->send($message);
                                    goto a;
                                }
                            }

                        }
                    }
                }

                $candidatures = $em->getRepository('GenericBundle:Candidature')->findBy(array('user'=>$apprenant));
                foreach($candidatures as $candidature){
                    foreach($candidature->getFormation()->getQcmdef() as $qcmdef){
                        $questions = $em->getRepository('GenericBundle:Questiondef')->findBy(array('qcmdef'=>$qcmdef));
                        foreach($questions as $question){
                            $reponses = $em->getRepository('GenericBundle:Reponsedef')->findBy(array('questiondef'=>$question));

                            if($reponses )
                            {
                                if(count(array_intersect($reponses,$apprenant->getReponsedef()->toArray())) ==0){
                                    //$output->writeln($apprenant->getEmail());
                                    $message = \Swift_Message::newInstance()
                                        ->setSubject('Email')
                                        ->setFrom(array('symfony.atpmg@gmail.com'=>"HUB3E"))
                                        ->setTo($apprenant->getEmail())
                                        ->setBody($this->getContainer()->get('templating')->render('GenericBundle:Mail:EmailCompleterProfil.html.twig',array('apprenant'=>$apprenant))
                                            ,'text/html'
                                        );
                                    $this->getContainer()->get('mailer')->send($message);
                                    goto a;
                                }
                            }

                        }
                    }
                }
                $apprenant->getInfo()->setProfilcomplet(true);
                $em->flush();
                a:
            }

        }
        if ($input->getOption('option')) {
            // ...
        }

        $output->writeln('Commande executer correctement.');
    }

}
