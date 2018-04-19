<?php

declare(strict_types=1);

namespace App\Command;

use App\DataFixtures\AccountFixtures;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestConsumerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:test-consumer');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        for ($i = 0; $i < 1; ++$i) {
            $testData = [
                'recipientId' => rand(1, AccountFixtures::COUNT_ACCOUNTS),
                'billingType' => 'deposit',
                'tid' => md5((string) time()),
                'amount' => 1000,
            ];

            $this->getContainer()->get('old_sound_rabbit_mq.billing_producer')->publish(serialize($testData), $testData['billingType']);
        }


        $firstHalfAccounts = AccountFixtures::COUNT_ACCOUNTS / 2;
        $secondHalfAccounts = $firstHalfAccounts + 1;

        for ($i = 0; $i < 20; $i++) {
            $testData = [
                'recipientId' => rand(1, $firstHalfAccounts),
                'senderId' => rand($secondHalfAccounts, AccountFixtures::COUNT_ACCOUNTS),
                'billingType' => 'debit',
                'tid' => md5((string) time()),
                'amount' => 10,
            ];

            $this->getContainer()->get('old_sound_rabbit_mq.billing_producer')->publish(serialize($testData), $testData['billingType']);
        }

        for ($i = 0; $i < 20; $i++) {
            $testData = [
                'recipientId' => rand($secondHalfAccounts, AccountFixtures::COUNT_ACCOUNTS),
                'senderId' => rand(1, $firstHalfAccounts),
                'billingType' => 'transfer',
                'amount' => 10,
                'tid' => md5((string) time()),
            ];

            $this->getContainer()->get('old_sound_rabbit_mq.billing_producer')->publish(serialize($testData), $testData['billingType']);
        }
    }
}
