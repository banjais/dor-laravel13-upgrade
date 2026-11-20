<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotKnowledgeSource;
use App\Models\Office;
use App\Services\Chatbot\ChatbotKnowledgeIndexer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class ChatbotKnowledgeController extends Controller
{
    private function ensureSuperAdmin(): void
    {
        abort_unless(
            Auth::check() && (int) Auth::user()->user_type_id === 2,
            403
        );
    }

    public function index()
    {
        $this->ensureSuperAdmin();

        $sources = ChatbotKnowledgeSource::where('source_type', '!=', 'appsheet')
            ->withCount('documents')
            ->orderByDesc('id')
            ->paginate(20);

        $offices = Office::getPublishedOffices();

        return view('chatbot_settings.admin.knowledge', compact(
            'sources',
            'offices'
        ));
    }

    public function upload(Request $request, ChatbotKnowledgeIndexer $indexer)
    {
        $this->ensureSuperAdmin();

        $uploadErrors = $_FILES['files']['error'] ?? [];
        if (!is_array($uploadErrors)) {
            $uploadErrors = [$uploadErrors];
        }

        foreach ($uploadErrors as $uploadError) {
            if (in_array((int) $uploadError, [
                UPLOAD_ERR_INI_SIZE,
                UPLOAD_ERR_FORM_SIZE,
            ], true)) {
                return back()->with('error', 'File is too large (maximum 300 MB).');
            }
        }

        $data = $request->validate([
            'office_id' => ['nullable', 'integer'],
            'files' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $files, $fail) {
                    $totalBytes = 0;

                    foreach ((array) $files as $file) {
                        if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                            $totalBytes += (int) $file->getSize();
                        }
                    }

                    if ($totalBytes > 300 * 1024 * 1024) {
                        $fail('Combined document size must not exceed 300 MB per upload.');
                    }
                },
            ],
            'files.*' => [
                'required',
                'file',
                'max:307200',
                'mimes:pdf,docx,xlsx,pptx',
            ],
            'is_public' => ['nullable', 'boolean'],
        ], [
            'office_id.integer' => 'Please select a valid office.',
            'files.required' => 'Please add at least one document.',
            'files.min' => 'Please add at least one document.',
            'files.*.file' => 'A selected file could not be uploaded.',
            'files.*.max' => 'A document is too large (maximum 300 MB).',
            'files.*.mimes' => 'Use PDF, DOCX, XLSX or PPTX documents.',
        ]);

        $officeId = isset($data['office_id']) && $data['office_id'] !== ''
            ? (int) $data['office_id']
            : null;

        if ($officeId !== null) {
            $officeExists = collect(Office::getPublishedOffices())
                ->contains(fn ($office) => (int) $office->id === $officeId);

            if (!$officeExists) {
                return back()->with('error', 'Please select a valid active office.');
            }
        }

        $indexed = 0;
        $failed = 0;

        foreach ($data['files'] as $file) {
            $source = null;
            $path = null;

            try {
                $source = ChatbotKnowledgeSource::create([
                    'office_id' => $officeId,
                    'source_type' => 'file',
                    'name' => $file->getClientOriginalName(),
                    'storage_disk' => 'local',
                    'storage_path' => null,
                    'language_code' => 'both',
                    'status' => 'pending',
                    'is_enabled' => true,
                    'is_public' => $request->boolean('is_public'),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                $folder = 'chatbot-knowledge/' . ($officeId ?? 'general');
                $path = $file->store($folder, 'local');

                if (!$path) {
                    $source->update([
                        'status' => 'failed',
                        'last_error' => 'Upload failed. Please try again.',
                    ]);
                    $failed++;
                    continue;
                }

                $source->update(['storage_path' => $path]);
            } catch (Throwable $e) {
                report($e);

                if ($source) {
                    $source->update([
                        'status' => 'failed',
                        'last_error' => 'Upload failed. Please try again.',
                    ]);
                }

                if ($path) {
                    Storage::disk('local')->delete($path);
                }

                $failed++;
                continue;
            }

            try {
                $result = $indexer->indexDocument([
                    'source_id' => $source->id,
                    'title' => $source->name,
                    'storage_disk' => 'local',
                    'storage_path' => $path,
                    'document_type' => 'document',
                    'mime_type' => $file->getMimeType(),
                    'source_key' => 'upload-' . $source->id,
                    'metadata' => [
                        'original_name' => $file->getClientOriginalName(),
                    ],
                ]);

                $success = in_array(
                    $result['status'] ?? '',
                    ['indexed', 'unchanged'],
                    true
                );

                $source->update([
                    'status' => $success ? 'indexed' : 'failed',
                    'last_error' => $success
                        ? null
                        : mb_substr(
                            (string) ($result['error'] ?? 'Indexing failed.'),
                            0,
                            500
                        ),
                    'updated_by' => Auth::id(),
                ]);

                $success ? $indexed++ : $failed++;
            } catch (Throwable $e) {
                report($e);

                $source->update([
                    'status' => 'failed',
                    'last_error' => 'Indexing failed. Please retry.',
                    'updated_by' => Auth::id(),
                ]);

                $failed++;
            }
        }

        return back();
    }

    public function registerUrl(Request $request)
    {
        $this->ensureSuperAdmin();

        $data = $request->validate([
            'office_id' => ['nullable', 'integer'],
            'is_public' => ['nullable', 'boolean'],
            'urls' => ['required', 'array', 'min:1'],
            'urls.*.type' => ['required', 'in:website,google_sheets'],
            'urls.*.name' => ['required', 'string', 'max:200'],
            'urls.*.source_url' => ['required', 'url', 'max:2000'],
        ], [
            'office_id.integer' => 'Please select a valid office.',
            'urls.required' => 'Please add at least one URL.',
            'urls.*.name.required' => 'Please enter a source name.',
            'urls.*.source_url.required' => 'Please enter a URL.',
            'urls.*.source_url.url' => 'Please enter a valid URL.',
        ]);

        $officeId = isset($data['office_id']) && $data['office_id'] !== ''
            ? (int) $data['office_id']
            : null;

        if ($officeId !== null) {
            $officeExists = collect(Office::getPublishedOffices())
                ->contains(fn ($office) => (int) $office->id === $officeId);

            if (!$officeExists) {
                return back()->withErrors([
                    'office_id' => 'Please select a valid active office.',
                ]);
            }
        }

        foreach ($data['urls'] as $index => $item) {
            $parts = parse_url($item['source_url']);

            if (
                !is_array($parts)
                || strtolower($parts['scheme'] ?? '') !== 'https'
                || empty($parts['host'])
                || isset($parts['user'])
                || isset($parts['pass'])
            ) {
                return back()->withErrors([
                    'urls' => 'Only HTTPS URLs without embedded credentials are accepted.',
                ]);
            }

        }

        foreach ($data['urls'] as $item) {

            ChatbotKnowledgeSource::create([
                'office_id' => $officeId,
                'source_type' => $item['type'],
                'name' => $item['name'],
                'source_url' => $item['source_url'],
                'language_code' => 'both',
                'status' => 'pending',
                'is_enabled' => true,
                'is_public' => $request->boolean('is_public'),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        return back();
    }

    public function reindex(
        ChatbotKnowledgeSource $source,
        ChatbotKnowledgeIndexer $indexer
    ) {
        $this->ensureSuperAdmin();

        abort_unless($source->source_type === 'file', 422);

        if (!$source->is_enabled) {
            return back()->with('error', 'Enable this source before indexing.');
        }

        if (
            !$source->storage_path
            || !Storage::disk('local')->exists($source->storage_path)
        ) {
            return back()->with('error', 'The private source file is missing.');
        }

        try {
            $result = $indexer->indexDocument([
                'source_id' => $source->id,
                'title' => $source->name,
                'storage_disk' => 'local',
                'storage_path' => $source->storage_path,
                'document_type' => 'document',
                'source_key' => 'upload-' . $source->id,
            ]);

            $success = in_array($result['status'] ?? '', ['indexed', 'unchanged'], true);

            $source->update([
                'status' => $success ? 'indexed' : 'failed',
                'last_error' => $success
                    ? null
                    : mb_substr((string) ($result['error'] ?? 'Indexing failed.'), 0, 2000),
                'updated_by' => Auth::id(),
            ]);

            return back()->with(
                $success ? 'success' : 'error',
                $success ? 'Source indexed successfully.' : 'Indexing failed.'
            );
        } catch (Throwable $e) {
            report($e);

            $source->update([
                'status' => 'failed',
                'last_error' => 'Indexing failed. Check application logs.',
            ]);

            return back()->with('error', 'Indexing failed. Check application logs.');
        }
    }

    public function toggle(ChatbotKnowledgeSource $source)
    {
        $this->ensureSuperAdmin();

        $source->update([
            'is_enabled' => !$source->is_enabled,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Source status updated.');
    }

    public function destroy(ChatbotKnowledgeSource $source)
    {
        $this->ensureSuperAdmin();

        if ($source->storage_disk === 'local' && $source->storage_path) {
            Storage::disk('local')->delete($source->storage_path);
        }

        $source->delete();

        return back()->with('success', 'Knowledge source and its indexed documents deleted.');
    }
}
