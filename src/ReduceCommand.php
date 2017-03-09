<?php

namespace ICalReducer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReduceCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('reduce')
            ->setDescription('reduce an ical file to only have future events')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
