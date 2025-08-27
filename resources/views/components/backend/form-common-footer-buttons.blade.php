<div class="form-buttons mb-4">    
    @if(in_array("submit_and_redirect_to_edit", $buttonList))
        <button type="submit" class="btn btn-primary" name="redirect_to_edit" value="1">Submit & Edit</button>
    @endif
    
    @if(in_array("submit_and_add", $buttonList))
        <button type="submit" class="btn btn-primary" name="redirect_to_add"  value="1">Submit & Add</button>
    @endif

    @if(in_array("submit_and_redirect_to_summary", $buttonList))
        <button type="submit" class="btn btn-primary" name="redirect_to_index" value="1">Submit & List</button>
    @endif

    @if(in_array("submit", $buttonList))
        <button type="submit" class="btn btn-primary">Submit</button>
    @endif
</div>