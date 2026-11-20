<?php

namespace App\Http\Controllers;

use App\Models\ChatbotSetting;
use App\Models\Office;
use App\Services\Chatbot\ChatbotKnowledgeRetriever;
use App\Services\Chatbot\ChatbotProviderRouter;
use Illuminate\Http\Request;
use Throwable;

class PublicChatbotController extends Controller
{
    public function message(
        Request $request,
        string $subdomain,
        ChatbotKnowledgeRetriever $retriever,
        ChatbotProviderRouter $router
    ) {
        $settings = ChatbotSetting::current();

        abort_unless($settings->enabled, 404);

        $validated = $request->validate([
            'question' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        $officeId = (int) Office::getIdfromSubdomain($subdomain);

        abort_unless($officeId > 0, 404);

        $question = trim($validated['question']);

        try {
            $result = $retriever->search($question, $officeId, 5);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => 'अहिले आधिकारिक स्रोत खोज्न सकिएन। कृपया पछि प्रयास गर्नुहोस्।',
            ], 503);
        }

        $matches = $result['matches'] ?? [];

        if (empty($matches)) {
            return response()->json([
                'ok' => true,
                'needs_consent' => true,
                'message' => 'DoR को आधिकारिक स्रोतमा जानकारी भेटिएन। बाह्य स्रोतबाट खोजौँ?',
                'sources' => [],
            ]);
        }

        $context = [];

        foreach (array_slice($matches, 0, 5) as $index => $match) {
            $context[] = "SOURCE " . ($index + 1)
                . "\nTitle: " . ($match['title'] ?? 'DoR document')
                . "\nContent: " . mb_substr(
                    (string) ($match['content'] ?? ''),
                    0,
                    2500
                );
        }

        $languageInstruction = match ($settings->response_language) {
            'ne' => 'Answer in Nepali.',
            'en' => 'Answer in English.',
            default => 'Answer in the language used by the question.',
        };

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are the Department of Roads official information assistant. '
                    . 'Answer only from the supplied DoR official source excerpts. '
                    . 'Do not invent facts, dates, figures, policies or references. '
                    . 'If the excerpts do not answer the question, say so clearly. '
                    . $languageInstruction,
            ],
            [
                'role' => 'user',
                'content' => "Question:\n{$question}\n\n"
                    . "DoR Official Sources:\n"
                    . implode("\n\n", $context),
            ],
        ];

        $answer = null;
        $providerLabel = null;

        try {
            // Official-source answers must never fail over to external AI.
            $providerResult = $router->chat($messages, ['internal']);

            $answer = trim((string) ($providerResult['content'] ?? ''));

            if ($settings->provider_info_visible) {
                $providerLabel = $providerResult['provider_name']
                    ?? $providerResult['provider_code']
                    ?? null;
            }
        } catch (Throwable $e) {
            // If internal AI is unavailable, show retrieved official
            // excerpts instead of sending them to an external provider.
            report($e);
        }

        if (! filled($answer)) {
            $answer = implode("\n\n", array_map(
                fn ($item) => (string) ($item['content'] ?? ''),
                array_slice($matches, 0, 3)
            ));
        }

        $sources = collect($matches)
            ->unique('document_id')
            ->map(fn ($item) => [
                'title' => $item['title'] ?? 'DoR Official Document',
                'url' => $item['source_url'] ?? null,
            ])
            ->values()
            ->all();

        return response()->json([
            'ok' => true,
            'needs_consent' => false,
            'answer' => $answer,
            'source_label' => 'DoR Official Sources',
            'sources' => $sources,
            'provider_label' => $providerLabel,
        ]);
    }

    public function externalConsent(Request $request, string $subdomain)
    {
        $settings = ChatbotSetting::current();

        abort_unless($settings->enabled, 404);

        $request->validate([
            'question' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        abort_unless(
            (int) Office::getIdfromSubdomain($subdomain) > 0,
            404
        );

        // Explicit consent is received, but no external web-search
        // integration has been configured yet.
        return response()->json([
            'ok' => false,
            'code' => 'external_search_not_configured',
            'message' => 'बाह्य वेबसाइट खोज सेवा अहिले जडान गरिएको छैन।',
        ], 503);
    }
}
