<?php declare(strict_types=1);

namespace Criticalmass\Component\Profile\Streak;

use Criticalmass\Bundle\AppBundle\Entity\User;

interface StreakGeneratorInterface
{
    public function setUser(User $user): StreakGeneratorInterface;

    public function calculateCurrentStreak(\DateTime $currentDateTime = null, bool $includeCurrentMonth = false): Streak;

    public function calculateLongestStreak(): Streak;
}
