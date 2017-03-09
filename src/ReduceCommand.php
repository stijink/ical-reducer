<?php

namespace ICalReducer;

use ICal\ICal;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReduceCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('reduce')
            ->setDescription('reduce an ical file to only have future events')
            ->setDefinition(
                new InputDefinition(array(
                    new InputArgument('source', InputArgument::REQUIRED),
                    new InputArgument('destination', InputArgument::REQUIRED),
                ))
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->reduce($input->getArgument('source'), $input->getArgument('destination'));
    }

    private function reduce(string $source, string $destination)
    {
        $iCal = new ICal($source);
        $interval = $iCal->eventsFromRange(date('Y-m-d'), false);
        $events = $iCal->sortEventsWithOrder($interval);

        $start = '
BEGIN:VCALENDAR
VERSION:2.0
PRODID:https://github.com/stijink/ical-reducer
METHOD:PUBLISH
';
        file_put_contents($destination, trim($start));

        foreach ($events as $event) {
            $eventData = $event->printData("%s: %s\n");
            file_put_contents($destination, "BEGIN:VEVENT\n" . $eventData . "END:VEVENT\n", FILE_APPEND);
        }

        file_put_contents($destination, "END:VCALENDAR\n", FILE_APPEND);
    }
}
