<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;

class ClearLogs extends Command
{

    private $disk;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:clear 
                            {--keep-last : Whether the last log file should be kept}
                            {--delete : Whether the log files should be deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove or clear every log files in the log directory';
    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $disk
     */
    public function __construct(Filesystem $disk)
    {
        parent::__construct();
        $this->disk = $disk;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $files = $this->getLogFiles();

        if ($this->option('keep-last') && $files->count() >= 1) {
            $files->shift();
        }

        if ($this->option('delete')) {
            $deleted = $this->delete($files);
            if (!$deleted) {
                $this->info('There was no log file to delete in the logs folder');
            } elseif ($deleted == 1) {
                $this->info('1 log file has been deleted');
            } else {
                $this->info($deleted . ' log files have been deleted');
            }
        } else {
            $replaced = $this->replace($files, null);
            if (!$replaced) {
                $this->info('There was no log file to replace in the logs folder');
            } elseif ($replaced == 1) {
                $this->info('1 log file has been replaced');
            } else {
                $this->info($replaced . ' log files have been replaced');
            }
        }
    }

    /**
     * Get a collection of log files sorted by their last modification date.
     */
    private function getLogFiles(): Collection
    {
        return Collection::make(
            $this->disk->allFiles(storage_path('logs'))
        )->sortBy('mtime');
    }

    /**
     * Delete the given files.
     */
    private function delete(Collection $files): int
    {
        return $files->each(function ($file) {
            $this->disk->delete($file);
        })->count();
    }

    /**
     * Replace content the given files.
     */
    private function replace(Collection $files, $content): int
    {
        return $files->each(function ($file) use ($content) {
            $this->disk->replace($file, $content);
        })->count();
    }
}
