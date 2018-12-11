<?php

namespace App\Command;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * To schedule this command add fallowing cron to your crontab
 * crontab: * * * * * php /path-to-your-project/bin/console app:order-auto-reject >> /dev/null 2>&1
 */
class OrderAutoRejectCommand extends Command
{
    // to make your command lazily loaded, configure the $defaultName static property,
    // so it will be instantiated only when the command is actually called.
    protected static $defaultName = 'app:order-auto-reject';

    private $entityManager;
    private $orderRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
    }

    protected function configure()
    {
        $this->setDescription('Checks orders and updates status as auto rejected if due');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $dueOrders = $this->orderRepository->findOrdersDue();

        foreach ($dueOrders as $dueOrder) {
            $this->orderRepository->updateOrderStatus(
                $dueOrder['id'],
                Order::STATUS_AUTO_REJECTED
            );
        }

        $io->success(sprintf('Total %d order\'s status updated as \'%s\'', count($dueOrders), Order::STATUS_AUTO_REJECTED));
    }
}
