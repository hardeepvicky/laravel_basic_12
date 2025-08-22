<x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
<input type="hidden" name="tab_name" value="{{ $model->tab_name }}" />

<div class="row">
    <div class="col-xxxl-12">
        <div class="card">
            <div class="card-header card-no-border pb-0">
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-xxl-8">
                        <div class="form-group mb-3">
                            <div class="textarea-wrapper">
                                <?php
                                $field = 'description';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-area :id="$field" :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" class="auto-suggesstion-list" rows="4"
                                    placeholder="Enter {{ $label }}" />
                                <div id="{{ $field . '-suggestion-box' }}" class="suggestion-box hidden"></div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'description_html';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-Backend.form-common-footer-buttons/>
    </div>
</div>
</form>

<script>
    $(function(){
        suggestion_list($("#description"), $("#description-suggestion-box"), [], "/tags/ajax_get_list");

        $("#description_html").summernote({
            height: 600,
        });
    });
</script>