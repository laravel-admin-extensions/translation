<?php

namespace Encore\Admin\Translation\Console;

use Encore\Admin\Translation\Translation;
use Illuminate\Console\Command;

class ExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:translations:export {group?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export translations to PHP files';

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
        if ($group = $this->argument('group')) {
            $this->manager->exportTranslations($group);
        } else {
            $this->manager->exportAllTranslations();
        }
    }
}
