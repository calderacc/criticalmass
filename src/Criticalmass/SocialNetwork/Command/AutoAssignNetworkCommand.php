<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Command;

use App\Criticalmass\SocialNetwork\NetworkDetector\NetworkDetectorInterface;
use App\Entity\SocialNetworkProfile;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AutoAssignNetworkCommand extends Command
{
    protected ManagerRegistry $doctrine;
    protected NetworkDetectorInterface $networkDetector;

    public function __construct(ManagerRegistry $doctrine, NetworkDetectorInterface $networkDetector)
    {
        $this->doctrine = $doctrine;
        $this->networkDetector = $networkDetector;

        parent::__construct(null);

    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:social-network:auto-assign')
            ->setDescription('Auto-assign networks')
            ->addOption('only-diffs', null, InputOption::VALUE_NONE)
            ->addOption('auto-assign', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $profiles = $this->doctrine->getRepository(SocialNetworkProfile::class)->findAll();

        $table = new Table($output);
        $table->setHeaders([
            'Id',
            'Identifier',
            'Saved Network',
            'Detected Network',
        ]);

        /** @var SocialNetworkProfile $profile */
        foreach ($profiles as $profile) {
            $detectedNetwork = $this->networkDetector->detect($profile->getIdentifier());

            if ($detectedNetwork && $detectedNetwork->getIdentifier() === $profile->getNetwork() && $input->getOption('only-diffs')) {
                continue;
            }

            if ($detectedNetwork && $detectedNetwork->getIdentifier() !== $profile->getNetwork() && $input->getOption('auto-assign')) {
                $profile->setNetwork($detectedNetwork->getIdentifier());
            }

            $table->addRow([
                $profile->getId(),
                $profile->getIdentifier(),
                $profile->getNetwork(),
                $detectedNetwork ? $detectedNetwork->getIdentifier() : 'unkown',
            ]);
        }

        if ($input->getOption('auto-assign')) {
            $this->doctrine->getManager()->flush();
        }

        $table->render();
    }
}
