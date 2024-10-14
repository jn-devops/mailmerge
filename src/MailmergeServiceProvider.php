<?php

namespace Homeful\Mailmerge;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Homeful\Mailmerge\Commands\MailmergeCommand;

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
