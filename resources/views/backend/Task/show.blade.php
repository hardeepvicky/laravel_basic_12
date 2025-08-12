<div class="card">
    <div class="card-header card-no-border pb-0">
        {{ $record->name }}
        <div class="card-header-right">
            <ul class="list-unstyled card-option" style="width: 230px;">
                <li><i class="fa-solid fa-maximize full-card"></i></li>
                <li><i class="fa-solid fa-minus minimize-card"></i></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-primary active" id="tab_basic" data-bs-toggle="tab"
                    href="#tab_basic_block" role="tab" aria-controls="tab_basic_block"
                    aria-selected="true">
                    Basic Detail
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-primary" id="tab_file" data-bs-toggle="tab" href="#tab_file_block" role="tab"
                    aria-controls="tab_file_block" aria-selected="true">
                    Files
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active show fade" id="tab_basic_block" role="tabpanel"
                aria-labelledby="tab_basic">
                <div class="row mt-2">
                    <div class="col-md-6 col-xxl-4">
                        <label class="info">
                            User :
                            <span>{{ $record->department?->getDisplayName() }}</span> >
                            {{ $record->assignToUser?->getDisplayName() }}
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'priority', 'priority') }}
                            :
                            <span>{{ $record->priority }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'tag_name', 'tag_name') }}
                            :
                            <span>{{ implode(', ', $record->tags->pluck('name')->toArray()) }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'status', 'status') }}
                            :
                            <span>{{ get_value_from_list($status_list, $record->status) }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'description', 'description') }}
                            :
                            <span>{!! $record->description !!}</span>
                        </label>
                        <h5>
                            Link Users
                        </h5>
                        <ol>
                            @foreach ($record->user as $user)
                                <li>{{ $user->getDisplayName() }}</li>
                            @endforeach
                        </ol>
                    </div>
                    <div class="col-md-6 col-xxl-4">
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'duration', 'duration') }} :
                            <span>{{ $record->duration }}
                                {{ get_value_from_list($duration_type_list, $record->duration_type) }}</span>
                        </label>
                        <x-Backend.celec_date_time
                            label="{{ get_value_from_list($field_labels, 'start_date_time', 'start_date_time') }}"
                            :value="$record->start_date_time" />
                        <x-Backend.celec_date_time
                            label="{{ get_value_from_list($field_labels, 'end_date_time', 'end_date_time') }}"
                            :value="$record->end_date_time" />
                    </div>
                    <div class="col-md-6 col-xxl-4">
                        <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab_file_block" role="tabpanel"
                aria-labelledby="tab_file">

                <x-Form.start action="{{ route('task.change_files', ['task_id' => $record->id]) }}" method="POST" />
                    <div class="form-group mt-2 mb-3">
                        <?php
                        $field = 'taskFile';
                        $label = get_value_from_list($field_labels, $field, $field);
                        $value = implode(',', $record->taskFile?->pluck('document_file_id')->toArray());
                        ?>
                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                            :value="$value" multiple="multiple" />
                        <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>

                <x-Backend.document-files :records="$document_files" />
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.more-less').srParagraph();

        $("#taskFile").browseGallery({
            opener: "#files-choose"
        });
    });
</script>