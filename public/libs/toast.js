$.toast = {
    element : null,
    init : function(title, msg, link)
    {
        var html = `
         <div class="toast-container position-fixed start-0 bottom-0 p-3 toast-index toast-rtl">
            <div class="toast hide toast fade text-white bg-primary" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header toast-img">                
                    <strong class="me-auto">${title}</strong>
                    <small class="text-body-secondary">just now</small>
                    <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <h6 class="mb-2">${msg}</h6>                                    
        `;

        if (link)
        {
            html += `<a href="${link}" class="btn btn-dark btn-sm">Link</a>
                    </div>
                </div>
            </div>`;
        }
        else
        {
            html += `</div>
                </div>
            </div>`;
        }

        $("body").append(html);

        this.element = new bootstrap.Toast($(".toast-container").last().find(".toast"));
        this.element.show();

        return this;
    },
    find : function (index)
    {
        this.element = new bootstrap.Toast($(".toast-container").eq(index).find(".toast"));
        return this;
    },
    show : function(){
        this.element.show();
        return this;
    }
}