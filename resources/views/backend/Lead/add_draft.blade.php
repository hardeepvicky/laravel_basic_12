@extends($layout)

@section('content')
    <?php
    use App\Models\Lead;
    
    $page_header_links = [];
    $page_header_links[] = ['title' => 'List', 'url' => route($routePrefix . '.index')];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    <div class="row">
        <div class="col-xxl-10">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">                            
                            <div class="form-group mb-3">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" placeholder="Enter {{ $label }}" />
                            </div>

                            <div class="form-group mb-3">
                                <?php
                                $field = 'mobile';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    class="validate-mobile" placeholder="Enter {{ $label }}" autocomplete="off" />
                            </div>

                            <div class="d-flex align-items-center my-4">
                                <hr class="flex-grow-1">
                                <span class="px-3 text-muted text-sm">or</span>
                                <hr class="flex-grow-1">
                            </div>
                            
                            <div class="form-group mb-3">
                                <?php
                                $field = 'email';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label"
                                    class="validate-email" placeholder="Enter {{ $label }}" />
                            </div>

                            <div class="d-flex align-items-center my-4">
                                <hr class="flex-grow-1">
                                <span class="px-3 text-muted text-sm">or</span>
                                <hr class="flex-grow-1">
                            </div>

                            <div class="form-group mb-3">
                                <?php
                                $field = 'url';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" placeholder="Enter {{ $label }}" />
                            </div>

                            <div class="d-flex align-items-center my-4">
                                <hr class="flex-grow-1">
                                <span class="px-3 text-muted text-sm">or</span>
                                <hr class="flex-grow-1">
                            </div>

                            <div class="form-group mb-3">
                                <?php
                                $field = 'image';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.file-field :name="$field" :label="$label"
                                    placeholder="Enter {{ $label }}" />
                            </div>

                            <div class="d-flex align-items-center my-4">
                                <hr class="flex-grow-1">
                                <span class="px-3 text-muted text-sm">or</span>
                                <hr class="flex-grow-1">
                            </div>

                            <div class="form-group mb-3">
                                <?php
                                $field = 'video';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.file-field :name="$field" :label="$label" 
                                    placeholder="Enter {{ $label }}" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
    </form>

    <script>
        $(function() {
            var leadSearchEngine = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.whitespace,
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: '/lead-ajax_get_list?query=%QUERY',
                    wildcard: '%QUERY',
                    transform: function(response) {
                        var result = $.map(response['data'], function(item) {
                            return item;
                        });

                        console.log(result)
                        return result;
                    }
                },
            });

            $('#mobile').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                name: 'mobile',
                display : 'display',
                source: leadSearchEngine,
                templates: {
                    suggestion: function(data) {
                        return `<div>
                            <a href="${data.url}">
                                <span> ${data.display}</span>
                            </a>
                        </div>
                    `;
                    }
                }
            }).bind('typeahead:select', function(ev, suggestion) {
                if (typeof suggestion.url == "string")
                {
                    $.loader.init().setInfo("Redirecting....").show();
                    window.location.href = suggestion.url;
                }
            });

            $.events.execAtLast(function() {
            });
        })
    </script>
@endsection
