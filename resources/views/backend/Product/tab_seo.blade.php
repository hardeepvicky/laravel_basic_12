<x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
<input type="hidden" name="tab_name" value="{{ $model->tab_name }}" />

<div class="row">
    <div class="col-xxxl-12">
        <div class="card">
            <div class="card-header card-no-border pb-0">
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-xxl-6">
                        <div class="form-group mb-3">
                            <?php
                            $field = 'seo_title';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                placeholder="Enter {{ $label }}" />
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'meta_main_keyword';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                placeholder="Enter {{ $label }}" />
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'meta_description';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                placeholder="Enter {{ $label }}" />
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'keywords';
                            $label = get_value_from_list($field_labels, $field, 'Keywords');
                            $value = implode(',', $model->keywords?->pluck('name')->toArray()) ?? [];
                            ?>
                            <x-Inputs.choice :id="$field" :name="$field" :label="$label" :value="$value"
                                class="choice " placeholder="Enter {{ $label }}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-Backend.form-common-footer-buttons buttons="next" />
    </div>
</div>
</form>
