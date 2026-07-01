<?php

namespace DcodeGroup\XeroIntegration\Commands;

use Illuminate\Console\Command;

class XeroIntegrationCommand extends Command
{
    public $signature = 'xero-integration';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
