<div class="row" id="import_process_table">
    <div class="col-sm-12">
        <x-form id="import-process-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    {{ $headingTitle }}
                </h4>

                <div class="p-20">
                    <input type="hidden" name="file" value="{{ $file }}">
                    <input type="hidden" name="has_heading" value="{{ $hasHeading ? 1 : 0 }}">

                    <div class="alert alert-info">
                        Associez chaque champ attendu a la colonne correspondante du fichier importe.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Champ</th>
                                    <th>Obligatoire</th>
                                    <th>Colonne du fichier</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($columns as $column)
                                    <tr>
                                        <td>{{ $column['name'] }}</td>
                                        <td>{{ $column['required'] }}</td>
                                        <td>
                                            <select class="form-control" name="columns[{{ $column['id'] }}]" {{ $column['required'] === 'Yes' ? 'required' : '' }}>
                                                <option value="">@lang('app.select')</option>
                                                @if ($hasHeading)
                                                    @foreach ($fileHeading as $index => $heading)
                                                        @php
                                                            $normalizedHeading = \Illuminate\Support\Str::of((string) $heading)->lower()->slug('_')->toString();
                                                        @endphp
                                                        <option value="{{ $index }}" {{ $normalizedHeading === $column['id'] ? 'selected' : '' }}>
                                                            {{ $heading }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    @foreach (($importSample[0] ?? []) as $index => $sample)
                                                        <option value="{{ $index }}">
                                                            Colonne {{ $index + 1 }} @if ($sample !== null && $sample !== '') - {{ \Illuminate\Support\Str::limit((string) $sample, 40) }} @endif
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-none" id="import-progress-wrapper">
                        <div class="progress">
                            <div class="progress-bar" id="import-progress-bar" role="progressbar" style="width: 0%">0%</div>
                        </div>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="import-process-submit" class="mr-3" icon="check">
                        @lang('app.import')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="$backRoute" class="border-0">{{ $backButtonText }}</x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $('body').off('click', '#import-process-submit').on('click', '#import-process-submit', function () {
        const url = "{{ $processRoute }}";

        $.easyAjax({
            url: url,
            container: '#import-process-form',
            type: 'POST',
            disableButton: true,
            blockUI: true,
            buttonSelector: '#import-process-submit',
            data: $('#import-process-form').serialize(),
            success: function (response) {
                if (response.status === 'success' && response.batch) {
                    $('#import-progress-wrapper').removeClass('d-none');
                    checkImportProgress(response.batch.name, response.batch.id);
                }
            }
        });
    });

    function checkImportProgress(name, id) {
        $.easyAjax({
            url: "{{ route('manager.hr.import.process.progress', [':name', ':id']) }}".replace(':name', name).replace(':id', id),
            type: 'GET',
            success: function (response) {
                const progress = response.progress || 0;
                $('#import-progress-bar').css('width', progress + '%').text(progress + '%');

                if (progress < 100) {
                    setTimeout(function () {
                        checkImportProgress(name, id);
                    }, 1200);
                } else {
                    window.location.href = "{{ $backRoute }}";
                }
            }
        });
    }
</script>
