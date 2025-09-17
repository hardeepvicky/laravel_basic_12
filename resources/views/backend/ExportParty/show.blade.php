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
                            {{ get_value_from_list($field_labels, 'id', 'id') }}
                            :
                            <span>{{ $record->id }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'name', 'name') }}
                            :
                            <span>{{ $record->name }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'mobile', 'mobile') }}
                            :
                            <span>{{ $record->mobile }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'mobile2', 'mobile2') }}
                            :
                            <span>{{ $record->mobile2 }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'email', 'email') }}
                            :
                            <span>{{ $record->email }}</span>
                        </label>
                    </div>
                    <div class="col-md-6 col-xxl-4">
                        <label class="info">
                            Location
                            :
                            <span>
                                {{ $record->country?->getShortName() }}
                                >>
                                {{ $record->state?->getShortName() }}
                                >>
                                {{ $record->city?->getDisplayName() }}
                            </span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'remarks', 'remarks') }}
                            :
                            <span class="more-less" data-max="100">{!! $record->remarks !!}</span>
                        </label>
                    </div>
                    <div class="col-md-6 col-xxl-4">
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'assign_to_user_id', 'assign_to_user_id') }} :
                            <span>{{ $record->assignTo?->getDisplayName() }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'next_followup_date_time', 'next_followup_date_time') }} :
                            <x-Backend.celec-date-time :value="$record->next_followup_date_time"/>
                        </label>
                    </div>
                    <div class="col-md-6 col-xxl-4">
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'status_type', 'status_type') }} :
                            <span>{{ get_value_from_list($status_type_list, $record->status_type) }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'company_name', 'company_name') }}
                            :
                            <span>{{ $record->company_name }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'gst_no', 'gst_no') }}
                            :
                            <span>{{ $record->gst_no }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab_file_block" role="tabpanel"
                aria-labelledby="tab_file">

                <x-Form.start action="{{ route('export-party.change_files', [$record->id]) }}" method="POST" />
                    <div class="form-group mt-2 mb-3">
                        <?php
                        $field = 'partyFile';
                        $label = get_value_from_list($field_labels, $field, $field);
                        $value = implode(',', $record->partyFile?->pluck('document_file_id')->toArray() ?? []);
                        ?>
                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                            :value="$value" multiple="multiple" />
                        <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>

                <x-Backend.document-files :records="$record->documentFile" />
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('.more-less').srParagraph();

        $("#partyFile").browseGallery({
            opener: "#files-choose"
        });
    });
</script>
