@extends($layout)

@section('content')
    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    <input type="hidden" name="add_via" value="{{ $add_via }}" />

    <div class="row">
        <div class="col-md-12 col-xl-12 col-xxl-6">
            <div class="form-group mb-3">
                <?php
                $field = 'document_category_id';
                $label = "Category";
                ?>
                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$document_category_list"
                    value="{{ $model->{$field} }}" class="modal-select2" mandatory="true" />
            </div>
        </div>
        <div class="col-md-12 col-xl-12 col-xxl-6">
            <div class="form-group mb-3">
                <?php
                $field = 'file_original';
                $label = "File";
                ?>
                <x-Inputs.file-field :id="$field" :name="$field" :label="$label" :value="$model->{$field}"
                    placeholder="Enter {{ $label }}" mandatory="true" />
            </div>
        </div>
        <div class="col-md-12 col-xl-12 col-xxl-6">
            <div class="form-group mb-3">
                <?php
                $field = 'name';
                $label = "Name";
                ?>
                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$model->{$field}"
                    placeholder="Enter {{ $label }}" mandatory="true" />
            </div>
        </div>
    </div>
    </form>

    <script>
        $(function() {
            $("#file_original").fileValidator({
                maxSize: '2048m',
                onValidation: function(files) {
                    $(this).attr('class','form-control');
                },
                onInvalid: function(validationType, file)
                {
                    $(this).addClass('invalid ' + validationType);
                },
            }).change(function (){
                var fileName = $(this).val();
                $("#name").val(getFilenameFromPath(fileName));
            });
        })
    </script>
@endsection