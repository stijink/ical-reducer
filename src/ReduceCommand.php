<?php

namespace ICalReducer;

use ICal\EventObject;
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

        $start = "
BEGIN:VCALENDAR
VERSION:2.0
PRODID:https://github.com/stijink/ical-reducer
METHOD:PUBLISH";
        file_put_contents($destination, trim($start));

        foreach ($events as $event) {
            $eventData = $this->formatEvent($event);
            file_put_contents($destination, "\nBEGIN:VEVENT\n" . $eventData . "END:VEVENT", FILE_APPEND);
        }

        file_put_contents($destination, "\nEND:VCALENDAR", FILE_APPEND);
    }

    private function formatEvent(EventObject $event) : string
    {
        $data = array(
            'SUMMARY'       => $event->summary,
            'DTSTART'       => $event->dtstart,
            'DTEND'         => $event->dtend,
            'DURATION'      => $event->duration,
            'DTSTAMP'       => $event->dtstamp,
            'UID'           => $event->uid,
            'CREATED'       => $event->created,
            'LAST-MODIFIED' => $event->lastmodified,
            'DESCRIPTION'   => $event->description,
            'LOCATION'      => $event->location,
            'SEQUENCE'      => $event->sequence,
            'STATUS'        => $event->status,
            'TRANSP'        => $event->transp,
            'ORGANIZER'     => $event->organizer,
            'ATTENDEE'      => $event->attendee,
        );

        $data   = array_map('trim', $data); // Trim all values
        $data   = array_filter($data);      // Remove any blank values
        $output = '';

        foreach ($data as $key => $value) {
            $output .= "${key}:${value}\n";
        }

        return $output;
    }
}
