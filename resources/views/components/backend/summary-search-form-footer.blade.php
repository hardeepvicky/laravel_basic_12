<div class="row">
    <div class="col-sm-4">
<<<<<<< HEAD
=======
        @if(isset($showPaginationDropDown) && $showPaginationDropDown)
>>>>>>> base_v3
        Limit : 
        <select name="pagination_limit" class="pagination_limit">            
            @foreach($paginationList as $k => $t)
                @php
<<<<<<< HEAD
                    $attr = $selectedPaginationLimit == $k ? 'selected="selected"' : "";
=======
                    $attr = "";
                    if (isset($selectedPaginationLimit))
                    {
                        $attr = $selectedPaginationLimit == $k ? 'selected="selected"' : "";
                    }
>>>>>>> base_v3
                @endphp
                <option value="{{ $k }}" {{ $attr }}>{{$t}}</option>
            @endforeach
        </select>
<<<<<<< HEAD
    </div>
    <div class="col-sm-6">
=======
        @endif
    </div>
    <div class="col-sm-8 col-lg-4">
>>>>>>> base_v3
        <div>
            <button type="submit" class="btn btn-primary">Search</button>
            <span class="btn btn-light text-dark border-primary clear_form_search_conditions">Clear</span>
        </div>
    </div>
<<<<<<< HEAD
=======
    <div class="col-sm-6 col-lg-4">
        <div id="search_info"></div>
    </div>
>>>>>>> base_v3
</div>