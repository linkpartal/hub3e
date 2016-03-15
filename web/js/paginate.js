(function($){

    $.fn.customPaginate = function(options)
    {
        var paginationContainer = this;
        var itemsToPaginate;
           
            
        var defaults = {
       
            itemsPerPage : 10
           
        };
        
        var settings = {};
        
        $.extend(settings, defaults, options);
           
        var itemsPerPage = settings.itemsPerPage;
        
        itemsToPaginate = $(settings.itemsToPaginate);
        var numberOfPaginationLinks = Math.ceil((itemsToPaginate.length / itemsPerPage));
        
        $("<ul style='max-width:200px;overflow:hidden;height:40px;'></ul>").prependTo(paginationContainer);
           
        for(var index = 0; index < numberOfPaginationLinks; index++)
        {
            paginationContainer.find("ul").append("<li>"+ (index+1) + "</li>");
        }
           
        itemsToPaginate.filter(":gt(" + (itemsPerPage - 1)  + ")").hide();

        paginationContainer.find("ul li").first().addClass(settings.activeClass).end().on('click', function(){
			   
            var $this = $(this);
			   
            $this.addClass(settings.activeClass);
			   
            $this.siblings().removeClass(settings.activeClass);
           
            var linkNumber = $this.text();
            if(linkNumber > 3){
                var numberToHide = paginationContainer.find("ul li").filter(":lt(" + (linkNumber-3 )  + ")");
                $.merge(numberToHide, itemsToPaginate.filter(":gt(" + (linkNumber  + 2)  + ")"));
            }

            var itemsToHide = itemsToPaginate.filter(":lt(" + ((linkNumber-1) * itemsPerPage)  + ")");
            $.merge(itemsToHide, itemsToPaginate.filter(":gt(" + ((linkNumber * itemsPerPage) - 1)  + ")"));
                
            var itemsToShow = itemsToPaginate.not(itemsToHide);
            var numberToShow = paginationContainer.find("ul li").not(numberToHide);
            $("html,body").animate({scrollTop:"0px"}, function(){
                itemsToHide.hide();
                itemsToShow.show();
                if(linkNumber > 3) {
                    numberToHide.hide();
                    numberToShow.show();
                }
            });
        });
           
    }

}(jQuery));