<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Normalizer;

class NoticeSearchIndexer
{
    public function rebuildNotice(int $noticeId): void
    {
        DB::transaction(function () use ($noticeId) {
            $notice = DB::table('notice')
                ->where('id', $noticeId)
                ->first();

            $existingIds = DB::table('notice_search_indexes')
                ->where('notice_id', $noticeId)
                ->pluck('id');

            if ($existingIds->isNotEmpty()) {
                DB::table('notice_search_terms')
                    ->whereIn('search_index_id', $existingIds)
                    ->delete();
            }

            DB::table('notice_search_indexes')
                ->where('notice_id', $noticeId)
                ->delete();

            if (
                !$notice ||
                (int) $notice->is_active !== 1 ||
                (int) $notice->is_deleted !== 0
            ) {
                return;
            }

            $details = DB::table('notice_detail')
                ->where('notice_id', $noticeId)
                ->whereIn('language_id', [1, 2])
                ->get();

            // Legacy notices without notice_detail: index office name and notice URL.
            if ($details->isEmpty()) {
                foreach ([1, 2] as $languageId) {
                    $office = DB::table('office_detail')
                        ->where('office_id', $notice->office_id)
                        ->where('language_id', $languageId)
                        ->first();

                    $officeName = $office->office_name ?? '';
                    $normalizedText = $this->normalize(
                        $officeName . ' ' . ($notice->notice_url ?? '')
                    );

                    if ($normalizedText === '') {
                        continue;
                    }

                    $indexId = DB::table('notice_search_indexes')
                        ->insertGetId([
                            'notice_id' => $noticeId,
                            'office_id' => $notice->office_id,
                            'language_id' => $languageId,
                            'notice_heading' => null,
                            'office_name' => $officeName,
                            'normalized_text' => $normalizedText,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                    $terms = array_unique(
                        preg_split('/\\s+/u', $normalizedText, -1, PREG_SPLIT_NO_EMPTY)
                    );

                    $rows = [];

                    foreach ($terms as $term) {
                        if (
                            mb_strlen($term, 'UTF-8') > 191 ||
                            mb_strlen($term, 'UTF-8') < 2
                        ) {
                            continue;
                        }

                        $rows[] = [
                            'search_index_id' => $indexId,
                            'language_id' => $languageId,
                            'term' => $term,
                        ];
                    }

                    foreach (array_chunk($rows, 500) as $chunk) {
                        DB::table('notice_search_terms')->insertOrIgnore($chunk);
                    }
                }

                return;
            }

            foreach ($details as $detail) {
                $office = DB::table('office_detail')
                    ->where('office_id', $notice->office_id)
                    ->where('language_id', $detail->language_id)
                    ->first();

                $officeName = $office->office_name ?? '';

                $description = $this->cleanHtml(
                    (string) ($detail->notice_description ?? '')
                );

                $parts = [
                    $detail->notice_heading ?? '',
                    $detail->notice_short_description ?? '',
                    $description,
                    $detail->notice_page_title ?? '',
                    $officeName,
                ];

                $normalizedText = $this->normalize(
                    implode(' ', $parts)
                );

                if ($normalizedText === '') {
                    continue;
                }

                $indexId = DB::table('notice_search_indexes')
                    ->insertGetId([
                        'notice_id' => $noticeId,
                        'office_id' => $notice->office_id,
                        'language_id' => $detail->language_id,
                        'notice_heading' => $detail->notice_heading,
                        'office_name' => $officeName,
                        'normalized_text' => $normalizedText,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                $terms = array_unique(
                    preg_split('/\s+/u', $normalizedText, -1, PREG_SPLIT_NO_EMPTY)
                );

                $rows = [];

                foreach ($terms as $term) {
                    if (
                        mb_strlen($term, 'UTF-8') > 191 ||
                        mb_strlen($term, 'UTF-8') < 2
                    ) {
                        continue;
                    }

                    $rows[] = [
                        'search_index_id' => $indexId,
                        'language_id' => $detail->language_id,
                        'term' => $term,
                    ];
                }

                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table('notice_search_terms')->insertOrIgnore($chunk);
                }
            }
        });
    }

    private function cleanHtml(string $value): string
    {
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = iconv('UTF-8', 'UTF-8//IGNORE', $value) ?: '';
        }

        $value = strip_tags($value);

        return html_entity_decode(
            $value,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );
    }

    private function normalize(string $value): string
    {
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = iconv('UTF-8', 'UTF-8//IGNORE', $value) ?: '';
        }

        if (class_exists(Normalizer::class)) {
            $value = Normalizer::normalize($value, Normalizer::FORM_C) ?: $value;
        }

        $value = mb_strtolower($value, 'UTF-8');

        $value = preg_replace('/[^\p{L}\p{M}\p{N}]+/u', ' ', $value);

        return trim(preg_replace('/\s+/u', ' ', $value));
    }
}
