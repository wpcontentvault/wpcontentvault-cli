<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Command
    |--------------------------------------------------------------------------
    |
    | Laravel Zero will always run the command specified below when no command name is
    | provided. Consider update the default command for single command applications.
    | You cannot pass arguments to the default command because they are ignored.
    |
    */

    'default' => NunoMaduro\LaravelConsoleSummary\SummaryCommand::class,

    /*
    |--------------------------------------------------------------------------
    | Commands Paths
    |--------------------------------------------------------------------------
    |
    | This value determines the "paths" that should be loaded by the console's
    | kernel. Foreach "path" present on the array provided below the kernel
    | will extract all "Illuminate\Console\Command" based class commands.
    |
    */

    'paths' => [app_path('Console/Commands')],

    /*
    |--------------------------------------------------------------------------
    | Added Commands
    |--------------------------------------------------------------------------
    |
    | You may want to include a single command class without having to load an
    | entire folder. Here you can specify which commands should be added to
    | your list of commands. The console's kernel will try to load them.
    |
    */

    'add' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Hidden Commands
    |--------------------------------------------------------------------------
    |
    | Your application commands will always be visible on the application list
    | of commands. But you can still make them "hidden" specifying an array
    | of commands below. All "hidden" commands can still be run/executed.
    |
    */

    'hidden' => [
        NunoMaduro\LaravelConsoleSummary\SummaryCommand::class,
        Symfony\Component\Console\Command\DumpCompletionCommand::class,
        Symfony\Component\Console\Command\HelpCommand::class,
        Illuminate\Console\Scheduling\ScheduleRunCommand::class,
        Illuminate\Console\Scheduling\ScheduleListCommand::class,
        Illuminate\Console\Scheduling\ScheduleFinishCommand::class,
        Illuminate\Foundation\Console\VendorPublishCommand::class,
        LaravelZero\Framework\Commands\StubPublishCommand::class,

        \Illuminate\Database\Console\Migrations\MigrateCommand::class,
        \Illuminate\Database\Console\Migrations\FreshCommand::class,
        \Illuminate\Database\Console\Migrations\RefreshCommand::class,
        \Illuminate\Database\Console\Migrations\ResetCommand::class,
        \Illuminate\Database\Console\Migrations\RollbackCommand::class,
        \Illuminate\Database\Console\Migrations\StatusCommand::class,
        \Illuminate\Database\Console\Migrations\InstallCommand::class,

        \Illuminate\Database\Console\Migrations\MigrateMakeCommand::class,
        \LaravelZero\Framework\Commands\MakeCommand::class,
        \Illuminate\Database\Console\Factories\FactoryMakeCommand::class,
        \Illuminate\Foundation\Console\ModelMakeCommand::class,
        \Illuminate\Database\Console\Seeds\SeederMakeCommand::class,
        \Illuminate\Foundation\Console\TestMakeCommand::class,
        \LaravelZero\Framework\Commands\TestMakeCommand::class,

        \Illuminate\Database\Console\WipeCommand::class,
        \Illuminate\Database\Console\Seeds\SeedCommand::class,

        \LaravelZero\Framework\Commands\BuildCommand::class,
        \LaravelZero\Framework\Commands\InstallCommand::class,
        \LaravelZero\Framework\Commands\RenameCommand::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Removed Commands
    |--------------------------------------------------------------------------
    |
    | Do you have a service provider that loads a list of commands that
    | you don't need? No problem. Laravel Zero allows you to specify
    | below a list of commands that you don't to see in your app.
    |
    */

    'remove' => [
        //
    ],

];
