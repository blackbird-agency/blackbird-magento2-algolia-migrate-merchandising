<?php
/**
 * MigrateMerchandising
 *
 * @copyright Copyright © 2025 Blackbird. All rights reserved.
 * @author    emilie (Blackbird Team)
 */
declare(strict_types=1);


namespace Blackbird\AlgoliaMigrateMerchandising\Console\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use  Blackbird\AlgoliaMigrateMerchandising\Api\Data\MigrateMerchandisingServiceInterface;

class MigrateMerchandising extends Command
{
    public function __construct(
        protected MigrateMerchandisingServiceInterface $migrateMerchandisingService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('blackbird:migrate_algolia')
            ->setDescription('Migrate Algolia rules to Merchandising')
            ->addArgument('file', InputArgument::REQUIRED, 'Data json absolute file path')
            ->addArgument('store_code', InputArgument::REQUIRED, 'Store Code');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Start Migrations for index </info>');
        try {
            $this->migrateMerchandisingService->migrate($input->getArgument('store_code'), $input->getArgument('file'));
            $output->writeln('Migration completed successfully.');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
        return Command::SUCCESS;
    }
}
