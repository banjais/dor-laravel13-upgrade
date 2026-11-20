<?php

namespace App\Console\Commands;

use App\Services\NoticeSearchIndexer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BuildNoticeSearchIndex extends Command
{
    protected $signature = 'notice-search:rebuild {--notice= : Rebuild one notice ID}';

    protected $description = 'Build or rebuild the public notice search index';

    public function handle(NoticeSearchIndexer $indexer): int
    {
        $noticeId = $this->option('notice');

        if ($noticeId !== null) {
            if (!ctype_digit((string) $noticeId) || (int) $noticeId < 1) {
                $this->error('Please provide a valid notice ID.');
                return self::FAILURE;
            }

            $indexer->rebuildNotice((int) $noticeId);

            $this->info("Notice {$noticeId} search index rebuilt.");
            return self::SUCCESS;
        }

        $processed = 0;

        DB::table('notice')
            ->where('is_active', '1')
            ->where('is_deleted', '0')
            ->orderBy('id')
            ->chunkById(100, function ($notices) use ($indexer, &$processed) {
                foreach ($notices as $notice) {
                    $indexer->rebuildNotice((int) $notice->id);
                    $processed++;
                }

                $this->line("Processed {$processed} active notices...");
            });

        $this->info("Completed. {$processed} active notices processed.");

        return self::SUCCESS;
    }
}
