<?php

namespace Encore\Admin\Translation\Console;

use Encore\Admin\Translation\Translation;
use Illuminate\Console\Command;

class ResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:translations:reset {locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all translations from the database';

    /**
     * @var Translation
     */
    protected $manager;

    /**
     * ResetCommand constructor.
     *
     * @param Translation $manager
     */
    public function __construct(Translation $manager)
    {
        $this->manager = $manager;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $this->manager->resetTranslations($this->argument('locale'));

        $this->info('All translations are deleted');
    }
}
