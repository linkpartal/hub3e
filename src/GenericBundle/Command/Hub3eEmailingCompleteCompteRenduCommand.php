<?php

namespace GenericBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Hub3eEmailingCompleteCompteRenduCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hub3e:emailing:completeCompteRendu')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $rendezvous = $em->getRepository('GenericBundle:RDV')->findBy(array('statut'=>1));
        foreach($rendezvous as $rdv){
            if($rdv->getDate1() < date_create('-1hours') ){
                $output->writeln('envoi mail à '. $rdv->getApprenant()->getUsername(). ' et '.$rdv->getTuteur()->getUsername());
            }
        }
        if ($input->getOption('option')) {
            // ...
        }
    }

}
