<div class="form-buttons mb-4">
    @if(in_array("submit_and_redirect_to_summary", $buttonList))
        <button type="submit" class="btn btn-primary" name="redirect_to_index" value="1">Submit and Go To Summary</button>
    @endif
    
    @if(in_array("submit", $buttonList))
        <button type="submit" class="btn btn-primary">Submit</button>
    @endif

    @if(in_array("next", $buttonList))
        <button type="submit" class="btn btn-primary" name="next" value="1">Save & Next</button>
    @endif
</div>