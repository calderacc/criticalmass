<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Command;

use Criticalmass\Bundle\AppBundle\Entity\User;
use Criticalmass\Component\ProfilePhotoGenerator\ProfilePhotoGeneratorInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateProfilePhotosCommand extends Command
{
    /** @var Registry $registry */
    protected $registry;

    /** @var ProfilePhotoGeneratorInterface $profilePhotoGenerator */
    protected $profilePhotoGenerator;

    public function __construct(Registry $registry, ProfilePhotoGeneratorInterface $profilePhotoGenerator)
    {
        $this->registry = $registry;
        $this->profilePhotoGenerator = $profilePhotoGenerator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:profile-photo:generate')
            ->setDescription('Generate profile photos')
            ->addOption('overwrite', null,InputOption::VALUE_NONE)
            ->addOption('limit', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $overwrite = $input->getOption('overwrite');
        $limit = $input->getOption('limit');

        $userList = $this->findUsers($overwrite);

        if (0 === count($userList)) {
            $output->writeln(sprintf('<info>%s</info>', 'All profile photos are up to date'));
            return;
        }

        $progress = new ProgressBar($output, count($userList));
        $table = new Table($output);

        /** @var User $user */
        foreach ($userList as $user) {
            $filename = $this->profilePhotoGenerator->setUser($user)->generate();

            $table->addRow([$user->getUsername(), $filename]);
            $progress->advance();

            if ($limit && $progress->getProgress() >= $limit) {
                break;
            }
        }

        $this->registry->getManager()->flush();
        
        $progress->finish();
        $table->render();
    }

    protected function findUsers(bool $all = false): array
    {
        if ($all) {
            return $this->registry->getRepository(User::class)->findAll();
        } else {
            return $this->registry->getRepository(User::class)->findWithoutProfilePhoto();
        }
    }
}
