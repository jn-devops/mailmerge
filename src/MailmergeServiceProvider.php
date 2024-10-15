<?php

namespace Homeful\Mailmerge;

use Homeful\Mailmerge\Commands\MailmergeCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MailmergeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('mailmerge')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_mailmerge_table')
            ->hasCommand(MailmergeCommand::class);
    }
}
