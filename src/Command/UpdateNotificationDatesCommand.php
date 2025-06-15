<?php

namespace App\Command;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-notification-dates',
    description: 'Met à jour les dates des anciennes notifications',
)]
class UpdateNotificationDatesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $notifications = $this->entityManager->getRepository(Notification::class)->findAll();
        $baseDate = new \DateTime('2025-06-14 18:31:28');
        
        foreach ($notifications as $index => $notification) {
            // Ajouter 5 minutes pour chaque notification
            $newDate = clone $baseDate;
            $newDate->modify('+' . ($index * 5) . ' minutes');
            $notification->setDate($newDate);
        }

        $this->entityManager->flush();
        $output->writeln('Les dates des notifications ont été mises à jour.');

        return Command::SUCCESS;
    }
} 