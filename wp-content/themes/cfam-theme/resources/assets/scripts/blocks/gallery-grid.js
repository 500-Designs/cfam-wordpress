module.exports = {
    init: function() {
        $(document).ready(function()
        {
        var galleryModal = '<div id="galleryModal" class="modal-default galleryModal" data-gallery-modal>';
        galleryModal += '<div class="modal-overlay galleryModal-toggle" data-gallery-modal-toggle></div>';
        galleryModal += '<div class="modal-wrapper"><img alt="loader" class="modal-loader" src="/wp-content/themes/lucera-bootstrap/dist/images/loader.gif" data-gallery-modal-loader>';
        galleryModal += '<div class="modal-header d-none" data-gallery-modal-header>';
        galleryModal += '<button class="modal-close galleryModal-toggle" data-gallery-modal-toggle><i class="icon-close"></i></button>';
        galleryModal += '</div>';
        galleryModal += '<div class="modal-body d-none" data-gallery-modal-body>';
        galleryModal += '<div class="modal-content d-none pt-md-4" data-gallery-modal-content>';
        galleryModal += '<div class="galleryModal-image-wrapper">';
        galleryModal += '<img src="https://via.placeholder.com/320" class="galleryModal-image img-fluid" alt="Member Image" data-gallery-modal-image>';
        galleryModal += '</div>';
        galleryModal += '<div class="gallery-item-details-wrapper d-flex align-items-center"><div class="gallery-item-details">';
        galleryModal += '<div class="post-tag text-eyebrow text-uppercase" data-gallery-modal-tag>Investment Tag</div>';
        galleryModal += '<h3 class="galleryModal-name" data-gallery-modal-name>Investment Name</h3>';
        galleryModal += '<p class="galleryModal-location font-bold" data-gallery-modal-location>Location</p>';
        galleryModal += '<ul class="galleryModal-specs">';
        galleryModal += '<li><span class="label font-bold">Investment Fund</span><span>Fund Name</span></li>';
        galleryModal += '<li><span class="label font-bold">Square Feet</span><span>23 Million</span></li>';
        galleryModal += '<li><span class="label font-bold">Fund Manager</span><span>Manager Name</span></li>';
        galleryModal += '<li><span class="label font-bold">Total Gross Assets</span><span>$999.999</span></li>';
        galleryModal += '</ul>';
        galleryModal += '</div></div></div></div></div></div>';
        $('body').append(galleryModal);

        $(document).on("click", "[data-gallery-item-toggle]" , function(e) {
            e.preventDefault();

            var imgLink = $(this).find('img').attr('src');

            $("[data-gallery-modal-image]").attr('src', imgLink);
            $("[data-gallery-modal-tag]").text($(this).find("[data-gallery-item-tag]").text());
            $("[data-gallery-modal-name]").text($(this).find("[data-gallery-item-name]").text());
            $("[data-gallery-modal-location]").text($(this).find("[data-gallery-item-location]").text());


            $(document).find("[data-gallery-modal]").toggleClass("is-visible");
            $(document).find("[data-gallery-modal-content]").toggleClass("d-none");
            window.setTimeout(function() {
                $(document).find("[data-gallery-modal-loader]").toggleClass("d-none");
                $(document).find("[data-gallery-modal-header]").toggleClass("d-none");
                $(document).find("[data-gallery-modal-body]").toggleClass("d-none");
            }, 1000);
        })

        $('[data-gallery-modal-toggle]').on('click', function(e) {
            e.preventDefault();
            // $('.galleryModal-toggle').toggleClass('is-visible');
            $("[data-gallery-modal-name]").text("");
            $("[data-gallery-modal-tag]").text("");
            $("[data-gallery-modal-location]").text("");
            $(document).find("[data-gallery-modal]").toggleClass('is-visible');
            $(document).find("[data-gallery-modal-content").toggleClass("d-none");
            $(document).find("[data-gallery-modal-loader").toggleClass("d-none");
            $(document).find("[data-gallery-modal-header]").toggleClass("d-none");
            $(document).find("[data-gallery-modal-body").toggleClass("d-none");
        })


        console.log("Post Grid Script is running...");
        function post_grid_contents(event,params){
        
          var base_url  = window.location.origin; // Get site base url
          var page_num  = $("[data-gallery-load-more]").attr("page_num"); page_num++;// Get page number
        
          //Check event trigger for additional query parameters
          var query_param = "";
        
          if(event == "filter"){
            query_param += params;
          }
        
          // Setup the query url
          let query_url = base_url+"/wp-json/wp/v2/investment?"+query_param+"page="+page_num+"&per_page=4&_embed";
          console.log(query_url);
          $.getJSON(query_url, function(data) {
        
            //Setup the post layout
            var content = "";
            $.each(data, function(index, item) {

        
            content += '<div class="gallery-item col-md-6">';
            content += '<a class="modal-toggle galleryModal-toggle d-block" data-gallery-item-toggle>';
            content += '<img src="'+item._embedded['wp:featuredmedia']['0'].source_url+'" alt="Name" class="gallery-item--image img-fluid w-100">'; 
            content += '<div class="gallery-item-details">';
            content += '<div class="post-tag text-eyebrow text-uppercase" data-gallery-item-tag>'+item._embedded['wp:term']['0']['0'].name+'</div>';
            content += '<div class="gallery-item-details-title"> <h5 class="gallery-item-details--name" data-gallery-item-name>'+item.title.rendered+'</h5></div>';      
            content += '<div class="gallery-item-details-location"><p class="font-bold" data-gallery-item-location>California, US</p></div>';
            content += '</div>';
            content += '</a>';
            content += '</div>';
        
            });
        
            /* Update the column count and the page number */
            jQuery("[data-gallery-load-more]").attr('page_num',page_num);
            jQuery("[data-gallery-load-more]").attr('event',event);
        
            /* If event is not triggered by load more replace results else append*/
            if(page_num < 2 ){
              $("[data-gallery-list]").html(content);
            }else{
              $("[data-gallery-list]").append(content);
            }
        
            /* Hide Load More button if no items left */
            if (data.length < 4) {
              $("[data-gallery-load-more]").hide();
            }else{
              $("[data-gallery-load-more]").show();
            }
        
          });
        
        }

        /* Filter Function */
        $(document).on("change","[data-filter-by-category]",function(){
            var filterValue = jQuery(this).val();
          
            jQuery("[data-gallery-load-more]").attr('page_num',0);
          
            /* Convert date to ISO */
            if(filterValue == "all"){
              var filterParam = "";
            }else{
                var filterParam = "investmenttype="+filterValue+"&";
                
            }   
            
            post_grid_contents("filter",filterParam);
          
          });
          
          $(document).on("click","[data-gallery-load-more]",function(event){ 
          
            /* Check if search or filter or just load more and get value for param */
            var event = $("[data-gallery-load-more]").attr("event");
          
            if(event == "load_more"){ 
              post_grid_contents(event,""); 
              var filterParam = "";
            }
            if(event == "filter"){ 
          
              var filterValue = $("[data-filter-by-category]").val();
              if(filterValue == "all"){
                var filterParam = "";
              }else{
                var filterParam = "investmenttype="+filterValue+"&";
              }
              post_grid_contents("filter",filterParam); 
            }

         
          });


        }); 
        
    },

			
};


