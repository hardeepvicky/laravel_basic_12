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
                    <div class="col-md-6 col-xxl-3">
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
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'company_name', 'company_name') }}
                            :
                            <span>{{ $record->company_name }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'brand_name', 'brand_name') }}
                            :
                            <span>{{ $record->brand_name }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'company_gst_no', 'company_gst_no') }}
                            :
                            <span>{{ $record->company_gst_no }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'remarks', 'remarks') }}
                            :
                            <span class="more-less" data-max="100">{!! $record->remarks !!}</span>
                        </label>
                    </div>
                    <div class="col-md-6 col-xxl-3">
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'country_id', 'country_id') }}
                            :
                            <span>{{ $record->country?->getDisplayName() }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'state_id', 'state_id') }}
                            :
                            <span>{{ $record->state?->getDisplayName() }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'city_id', 'city_id') }}
                            :
                            <span>{{ $record->city?->getDisplayName() }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'purpose_price', 'purpose_price') }} :
                            <span>{{ $record->purpose_price }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'payment_method_type', 'payment_method_type') }} :
                            <span>{{ get_value_from_list($payment_method_type_list, $record->payment_method_type) }}</span>
                        </label>
                    </div>
                    <div class="col-md-6 col-xxl-3">
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'assign_to_user_id', 'assign_to_user_id') }} :
                            <span>{{ $record->assignTo?->getDisplayName() }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'next_followup_date_time', 'next_followup_date_time') }} :
                            <x-Backend.celec-date-time :value="$record->next_followup_date_time" :utcDate="$record->next_followup_date_time_original" :timezone="$record->state?->timezone"/>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'remarks', 'remarks') }} :
                            <span class="more-less" data-max="100">{!! $record->remarks !!}</span>
                        </label>
                    </div>
                    <div class="col-md-6 col-xxl-3">
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'status_type', 'status_type') }} :
                            <span>{{ get_value_from_list($status_type_list, $record->status_type) }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'source_type', 'source_type') }} :
                            <span>{{ get_value_from_list($source_type_list, $record->source_type) }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'source_id', 'source_id') }} :
                            <span>{{ get_value_from_list($source_list, $record->source_id) }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'deal_type', 'deal_type') }} :
                            <span>{{ get_value_from_list($deal_type_list, $record->deal_type) }}</span>
                        </label>
                        <label class="info">
                            {{ get_value_from_list($field_labels, 'dealer_id', 'dealer_id') }} :
                            <span>{{ $record->dealer?->getDisplayName() }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab_file_block" role="tabpanel"
                aria-labelledby="tab_file">

                <x-Form.start action="{{ route('export-lead.change_files', [$record->id]) }}" method="POST" />
                    <div class="form-group mt-2 mb-3">
                        <?php
                        $field = 'leadFile';
                        $label = get_value_from_list($field_labels, $field, $field);
                        $value = implode(',', $record->leadFile?->pluck('document_file_id')->toArray());
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

        $("#leadFile").browseGallery({
            opener: "#files-choose"
        });
    });
</script>
