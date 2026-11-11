<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Jobs;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRepository;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Contract\JobRepositoryContract;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\InMemoryJobRepository;

class InMemoryJobRepositoryTest extends TestCase
{
    use JobRepositoryContract;

    private InMemoryJobRepository $repo;

    protected function setUp(): void
    {
        $this->repo = new InMemoryJobRepository();
    }

    protected function repository(): JobRepository
    {
        return $this->repo;
    }
}
