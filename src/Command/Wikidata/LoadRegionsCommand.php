<?php declare(strict_types=1);

namespace App\Command\Wikidata;

use App\Criticalmass\Wikidata\RegionFetcher\RegionFetcherInterface;
use App\Criticalmass\Wikidata\WikidataCityEntityFinder\WikidataCityEntityFinder;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Region;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Wikidata\SearchResult;

class LoadRegionsCommand extends Command
{
    /** @var RegionFetcherInterface $regionFetcher */
    protected $regionFetcher;

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct($name = null, RegionFetcherInterface $regionFetcher, RegistryInterface $registry)
    {
        $this->regionFetcher = $regionFetcher;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('criticalmass:wikidata:load-regions')
            ->addArgument('regionSlug', InputArgument::REQUIRED, 'Specify a region slug');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $questionHelper = $this->getHelper('question');

        $region = $this->registry->getRepository(Region::class)->findOneBySlug($input->getArgument('regionSlug'));

        $result = $this->regionFetcher->fetch($region);

        $table = new Table($output);
        $table->setHeaders([
            'Entity id',
            'Label',
            'Description',
        ]);

        /** @var SearchResult $regionData */
        foreach ($result as $regionData) {
            $table->addRow([
                $regionData->id,
                $regionData->label,
                $regionData->description,
            ]);
        }

        $table->render();
    }
}
