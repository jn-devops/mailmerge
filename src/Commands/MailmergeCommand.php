<?php

namespace Homeful\Mailmerge\Commands;

use Illuminate\Console\Command;

class MailmergeCommand extends Command
{
    public $signature = 'mailmerge';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
