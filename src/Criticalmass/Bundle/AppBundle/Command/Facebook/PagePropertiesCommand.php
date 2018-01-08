<?php

namespace Criticalmass\Bundle\AppBundle\Command\Facebook;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Component\Facebook\PagePropertyReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PagePropertiesCommand extends Command
{
    /** @var PagePropertyReader $pagePropertyReader */
    protected $pagePropertyReader;

    public function __construct(PagePropertyReader $pagePropertyReader)
    {
        $this->pagePropertyReader = $pagePropertyReader;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('criticalmass:facebook:pageproperties')
            ->setDescription('');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->pagePropertyReader->read();

        $table = new Table($output);
        $table
            ->setHeaders(['City', 'Properties'])
        ;

        $assignedCities = $this->pagePropertyReader->getReadCities();

        /** @var City $city */
        foreach ($assignedCities as $city) {
            $table
                ->addRow([
                    $city->getCity(),
                    'read',
                ]);
        }

        $table->render();
    }

}
