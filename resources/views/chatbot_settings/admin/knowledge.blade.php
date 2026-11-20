@extends('layout.admin.masters')

@section('content')
<div id="main-content" class="chatbot-admin-compact">
    <div class="container-fluid">
        <h3>Chatbot Settings</h3>

        @include('chatbot_settings.admin.partials.compact_styles')

        <h4>Knowledge Sources</h4>
        <p>Manage official documents and website references for the DoR chatbot.</p>

        @if(request()->query('upload_error') === 'too_large')
            <div class="alert alert-danger">File is too large (maximum 300 MB).</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <style>
            .knowledge-entry-row {
                display: grid;
                gap: 10px;
                align-items: end;
                margin-bottom: 10px;
                padding: 12px;
                border: 1px solid #e1e6eb;
                border-radius: 5px;
                background: #fafbfd;
            }
            .knowledge-document-row {
                grid-template-columns: minmax(250px, 1fr) auto;
            }
            .knowledge-url-row {
                grid-template-columns: minmax(150px, .8fr) minmax(180px, 1fr) minmax(260px, 1.6fr) auto;
            }
            .knowledge-appsheet-fields input,
            .knowledge-entry-row .form-group {
                margin-bottom: 0;
            }
            .knowledge-entry-row .form-control {
                min-width: 0;
            }
            .knowledge-section-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                align-items: center;
                margin-top: 12px;
            }
            .knowledge-scope {
                max-width: 520px;
                margin-bottom: 16px;
            }
            @media (max-width: 767px) {
                .knowledge-document-row,
                .knowledge-url-row,
            }
        </style>

        <div class="card mb-4">
            <div class="card-header"><strong>Add Documents</strong></div>
            <div class="card-body">
                <form method="POST"
                      action="{{ route('admin.chatbot-knowledge.upload') }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="form-group knowledge-scope">
                        <label>Office (optional)</label>
                        <select name="office_id" class="form-control">
                            <option value="">General (All Offices)</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" @selected(old('office_id') == $office->id)>
                                    {{ $office->office_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label>Documents</label>
                    <div id="knowledge-document-rows">
                        <div class="knowledge-entry-row knowledge-document-row">
                            <div class="form-group">
                                <input type="file" name="files[]" class="form-control"
                                       accept=".pdf,.docx,.xlsx,.pptx" required>
                            </div>
                            <button type="button" class="btn btn-outline-danger"
                                    data-remove-document disabled>Remove</button>
                        </div>
                    </div>

                    <template id="knowledge-document-template">
                        <div class="knowledge-entry-row knowledge-document-row">
                            <div class="form-group">
                                <input type="file" name="files[]" class="form-control"
                                       accept=".pdf,.docx,.xlsx,.pptx" required>
                            </div>
                            <button type="button" class="btn btn-outline-danger"
                                    data-remove-document>Remove</button>
                        </div>
                    </template>

                    <div class="knowledge-section-actions">
                        <button type="button" class="btn btn-outline-secondary"
                                id="add-knowledge-document">+ Add</button>
                        <span class="text-muted small">
                            PDF, DOCX, XLSX or PPTX. Maximum combined size: 300 MB per submission.
                        </span>
                    </div>

                    <div class="form-check mt-3 mb-3">
                        <input type="checkbox" name="is_public" value="1"
                               class="form-check-input" id="knowledge-public">
                        <label class="form-check-label" for="knowledge-public">
                            Make available to public chatbot
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">Upload &amp; Index Documents</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><strong>Add Website &amp; Data URLs</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.chatbot-knowledge.url') }}">
                    @csrf

                    <div class="form-group knowledge-scope">
                        <label>Office (optional)</label>
                        <select name="office_id" class="form-control">
                            <option value="">General (All Offices)</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label>URL Sources</label>
                    <div id="knowledge-url-rows">
                        <div class="knowledge-entry-row knowledge-url-row">
                            <div class="form-group">
                                <label>Source Type</label>
                                <select name="urls[0][type]" class="form-control" required>
                                    <option value="website">Website</option>
                                    <option value="google_sheets">Google Sheets</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Source Name</label>
                                <input type="text" name="urls[0][name]" class="form-control"
                                       maxlength="200" placeholder="e.g. Contact Directory" required>
                            </div>
                            <div class="form-group">
                                <label>HTTPS URL</label>
                                <input type="url" name="urls[0][source_url]" class="form-control"
                                       placeholder="https://docs.google.com/spreadsheets/d/e/PUBLISHED_ID/pubhtml?widget=true" required>
                            </div>
                            <button type="button" class="btn btn-outline-danger"
                                    data-remove-url disabled>Remove</button>
                        </div>
                    </div>

                    <template id="knowledge-url-template">
                        <div class="knowledge-entry-row knowledge-url-row">
                            <div class="form-group">
                                <label>Source Type</label>
                                <select name="urls[__INDEX__][type]" class="form-control" required>
                                    <option value="website">Website</option>
                                    <option value="google_sheets">Google Sheets</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Source Name</label>
                                <input type="text" name="urls[__INDEX__][name]" class="form-control"
                                       maxlength="200" placeholder="Source name" required>
                            </div>
                            <div class="form-group">
                                <label>HTTPS URL</label>
                                <input type="url" name="urls[__INDEX__][source_url]" class="form-control"
                                       placeholder="https://..." required>
                            </div>
                            <button type="button" class="btn btn-outline-danger"
                                    data-remove-url>Remove</button>
                        </div>
                    </template>

                    <div class="knowledge-section-actions">
                        <button type="button" class="btn btn-outline-secondary"
                                id="add-knowledge-url">+ Add</button>
                        <span class="text-muted small">
                            Add a website URL or one Google Sheets Published-to-web URL.
                        </span>
                    </div>

                    <div class="form-check mt-3 mb-3">
                        <input type="checkbox" name="is_public" value="1"
                               class="form-check-input" id="knowledge-url-public">
                        <label class="form-check-label" for="knowledge-url-public">
                            Make these sources available to the public chatbot
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Register URL Sources</button>
                    <small class="text-muted d-block mt-2">
                        For Google Sheets, use one Published-to-web spreadsheet URL ending in /pubhtml.
                        Publish only the tabs approved for chatbot use. Website crawling is not enabled yet.
                    </small>
                </form>
            </div>
        </div>

        <script>
        (function () {
            const documentRows = document.getElementById('knowledge-document-rows');
            const documentTemplate = document.getElementById('knowledge-document-template');
            const addDocumentButton = document.getElementById('add-knowledge-document');

            function updateDocumentRemoveButtons() {
                const buttons = documentRows.querySelectorAll('[data-remove-document]');
                buttons.forEach(button => {
                    button.disabled = buttons.length === 1;
                });
            }

            addDocumentButton.addEventListener('click', function () {
                documentRows.appendChild(documentTemplate.content.cloneNode(true));
                updateDocumentRemoveButtons();
            });

            documentRows.addEventListener('click', function (event) {
                const button = event.target.closest('[data-remove-document]');
                if (button && documentRows.querySelectorAll('.knowledge-document-row').length > 1) {
                    button.closest('.knowledge-document-row').remove();
                    updateDocumentRemoveButtons();
                }
            });

            const urlRows = document.getElementById('knowledge-url-rows');
            const urlTemplate = document.getElementById('knowledge-url-template');
            const addUrlButton = document.getElementById('add-knowledge-url');
            let urlIndex = 1;

            function updateUrlRemoveButtons() {
                const buttons = urlRows.querySelectorAll('[data-remove-url]');
                buttons.forEach(button => {
                    button.disabled = buttons.length === 1;
                });
            }

            addUrlButton.addEventListener('click', function () {
                const html = urlTemplate.innerHTML.replace(/__INDEX__/g, String(urlIndex++));
                urlRows.insertAdjacentHTML('beforeend', html);
                updateUrlRemoveButtons();
            });

            urlRows.addEventListener('click', function (event) {
                const button = event.target.closest('[data-remove-url]');
                if (button && urlRows.querySelectorAll('.knowledge-url-row').length > 1) {
                    button.closest('.knowledge-url-row').remove();
                    updateUrlRemoveButtons();
                }
            });

        })();
        </script>

        <h4>Registered Sources</h4>
        @php
            $officeNameById = collect($offices)->keyBy('id');
            $sourceTypeLabels = [
                'file' => 'Document',
                'website' => 'Website',
                'google_sheets' => 'Google Sheets',
            ];
        @endphp
        <style>
            /* Desktop: keep the complete table within the screen width. */
            .knowledge-source-table-wrap {
                width: 100%;
                max-width: 100%;
                overflow: visible;
            }

            .knowledge-source-table {
                width: 100%;
                max-width: 100%;
                min-width: 0;
                table-layout: fixed;
                margin-bottom: 0;
                border-collapse: collapse;
            }

            .knowledge-source-table th,
            .knowledge-source-table td {
                vertical-align: top !important;
                white-space: normal !important;
                overflow-wrap: anywhere;
                word-break: normal;
                padding: 7px 5px !important;
                font-size: 11.5px;
                line-height: 1.4;
            }

            .knowledge-source-url {
                display: block;
                max-width: 100%;
                margin-top: 4px;
                font-size: 11px;
                line-height: 1.35;
                overflow-wrap: anywhere;
                word-break: break-word;
            }

            .knowledge-source-actions {
                display: flex;
                flex-wrap: wrap;
                align-items: flex-start;
                gap: 4px;
                max-width: 100%;
            }

            .knowledge-source-actions form {
                display: inline-block;
                margin: 0;
                max-width: 100%;
            }

            .knowledge-source-actions .btn {
                white-space: normal;
                font-size: 11px;
                padding: 4px 6px;
            }

            /* Tablet and mobile: show every source as a compact card. */
            @media (max-width: 1199px) {
                .knowledge-source-table {
                    display: block;
                    border: 0;
                }

                .knowledge-source-table colgroup,
                .knowledge-source-table thead {
                    display: none;
                }

                .knowledge-source-table tbody {
                    display: grid;
                    grid-template-columns: repeat(
                        auto-fit,
                        minmax(min(100%, 350px), 1fr)
                    );
                    gap: 12px;
                    width: 100%;
                }

                .knowledge-source-table tbody tr {
                    display: block;
                    min-width: 0;
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #dfe5eb;
                    border-radius: 7px;
                    background: #fff;
                    box-shadow: 0 1px 3px rgba(0,0,0,.05);
                }

                .knowledge-source-table tbody td {
                    display: grid;
                    grid-template-columns: minmax(95px, 34%) minmax(0, 1fr);
                    gap: 8px;
                    width: 100%;
                    min-width: 0;
                    border: 0 !important;
                    border-bottom: 1px solid #edf0f3 !important;
                    padding: 8px 4px !important;
                    font-size: 12px;
                }

                .knowledge-source-table tbody td::before {
                    content: attr(data-label);
                    display: block;
                    color: #526171;
                    font-size: 11px;
                    font-weight: 700;
                }

                .knowledge-source-table tbody td[data-label="Actions"] {
                    display: block;
                    border-bottom: 0 !important;
                    padding-top: 10px !important;
                }

                .knowledge-source-table tbody td[data-label="Actions"]::before {
                    margin-bottom: 7px;
                }

                .knowledge-source-table tbody td[colspan] {
                    display: block;
                    text-align: center;
                    border: 0 !important;
                }

                .knowledge-source-table tbody td[colspan]::before {
                    display: none;
                }

                .knowledge-source-actions {
                    gap: 6px;
                }

                .knowledge-source-actions .btn {
                    font-size: 12px;
                    padding: 5px 8px;
                }
            }

            /* Narrow mobile screens: one card per row. */
            @media (max-width: 575px) {
                .knowledge-source-table tbody {
                    grid-template-columns: minmax(0, 1fr);
                }

                .knowledge-source-table tbody td {
                    grid-template-columns: 92px minmax(0, 1fr);
                    gap: 6px;
                    font-size: 12px;
                }

                .knowledge-source-table tbody tr {
                    padding: 6px;
                }

                .knowledge-source-url {
                    font-size: 11px;
                }

                .knowledge-source-actions .btn {
                    font-size: 11px;
                }
            }
        </style>

        <div class="knowledge-source-table-wrap">
            <table class="table table-bordered table-striped knowledge-source-table">
                <colgroup>
                    <col style="width:19%">
                    <col style="width:12%">
                    <col style="width:8%">
                    <col style="width:17%">
                    <col style="width:5%">
                    <col style="width:6%">
                    <col style="width:10%">
                    <col style="width:10%">
                    <col style="width:13%">
                </colgroup>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Office</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Public</th>
                        <th>Documents</th>
                        <th>Last Indexed</th>
                        <th>Last Checked</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sources as $source)
                        <tr>
                            <td data-label="Name">
                                <strong>{{ $source->name }}</strong>
                                @if($source->source_url)
                                    <a class="knowledge-source-url"
                                       href="{{ $source->source_url }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       title="{{ $source->source_url }}">
                                        {{ \Illuminate\Support\Str::limit($source->source_url, 65) }}
                                    </a>
                                @endif
                            </td>
                            <td data-label="Office">{{ $source->office_id ? ($officeNameById->get((int) $source->office_id)?->office_name ?? 'Office unavailable') : 'General (All Offices)' }}</td>
                            <td data-label="Type">{{ $sourceTypeLabels[$source->source_type] ?? ucfirst($source->source_type) }}</td>
                            <td data-label="Status">
                                <span class="badge {{ $source->status === 'indexed' ? 'badge-success' : ($source->status === 'failed' ? 'badge-danger' : 'badge-secondary') }}">
                                    {{ ucfirst($source->status) }}
                                </span>
                                <br>
                                <span class="badge {{ $source->is_enabled ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $source->is_enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                                @if($source->last_error)
                                    <br><small class="text-danger">{{ $source->last_error }}</small>
                                @elseif(in_array($source->source_type, ['website', 'google_sheets'], true) && $source->status === 'pending')
                                    <br><small class="text-muted">Registered; secure fetching/indexing is not enabled yet.</small>
                                @endif
                            </td>
                            <td data-label="Public">{{ $source->is_public ? 'Yes' : 'No' }}</td>
                            <td data-label="Documents">{{ $source->documents_count }}</td>
                            <td data-label="Last Indexed">{{ $source->last_indexed_at?->format('Y-m-d H:i') ?? 'Not indexed' }}</td>
                            <td data-label="Last Checked">{{ $source->last_checked_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td data-label="Actions">
                                <div class="knowledge-source-actions">
                                    @if($source->source_type === 'file')
                                        <form method="POST" action="{{ route('admin.chatbot-knowledge.reindex', $source) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-primary" type="submit">Re-index</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.chatbot-knowledge.toggle', $source) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-secondary" type="submit">
                                            {{ $source->is_enabled ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.chatbot-knowledge.delete', $source) }}"
                                          onsubmit="return confirm('Delete this source and its indexed content?')">
                                        @csrf
                                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">No knowledge sources added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $sources->links() }}
    </div>
</div>
@endsection
