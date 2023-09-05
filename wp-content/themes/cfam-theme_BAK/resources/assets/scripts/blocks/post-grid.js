console.log("Post Grid Script is running...");

function post_grid_contents(event,params){

  var base_url  = window.location.origin; // Get site base url
  var page_num  = $(".post-grid .btn-post-load-more").attr("page_num"); page_num++;// Get page number
  var col_count = $(".post-grid .btn-post-load-more").attr("col_count"); // Get the column count

  //Check event trigger for additional query parameters
  var query_param = "";
  var archive_title ="";
  if(event == "search"){
    query_param += "&search="+params;
    archive_title = $(".post-grid .filter-section .col-search .input-search").val();
  }

  if(event == "filter"){
    query_param += params;
    archive_title = $(".post-grid .filter-section .col-filter select").val();
  }

  /* Add title if search or filter is the event */
  var content = "";
  if(page_num < 2 ){
    content +="<h4 class='archive-title'>Results for “"+archive_title+"”</h4>";
  }

  // Setup the query url
  let query_url = base_url+"/wp-json/wp/v2/posts?page="+page_num+"&per_page=11&_embed"+query_param;
  
  $.getJSON(query_url, function(data) {
    //Setup the post layout
    $.each(data, function(index, item) {

      /* This code is for ensuring the first column 
      doesnt have a border on the left */
      var col_post_first = "";
      if(col_count == 0 ){
        col_post_first = "col-post-first";
      }
      if(col_count == 4){
        col_count = 0;
        col_post_first = "col-post-first";
      }

      /* Checking if the post is featured for resize of column width */
      var col_class = "";
      if( item._embedded['wp:term']['0']['0'].name == "Featured" ){
        col_class = "col-post-featured col-sm-6 col-lg-6";
      }else{
        col_class = "col-sm-6 col-lg-3";
      }

      content += "<div class='col-post "+col_post_first+" "+col_class+"'>";
        //If has feature image show image
        if ( typeof item._embedded['wp:featuredmedia'] !== 'undefined' ) {
          content += "<img src='"+item._embedded['wp:featuredmedia']['0'].source_url+"' class='post-thumbnail' alt='Thumbnail image for' />"; 
        }

        content += "<span class='badge post-category-badge'>"+item._embedded['wp:term']['0']['0'].name+"</span>";
        content += "<h2 class='post-title'><a href='"+item.link+"'>"+item.title.rendered+"</a></h2>";
        content += "<ul class='post-meta'>";
          //Format date
          let date = new Date(item.modified);
          content += "<li class='meta-date-published'>"+date.toLocaleString('en-US', { day: 'numeric', year: 'numeric', month: 'long', })+"</li>";
        content += "</ul>";
        content += "<p class='post-excerpt'>"+item.excerpt.rendered.replace(/(<([^>]+)>)/gi, "")+"</p>";
        content += "<a class='post-link' href='"+item.link+"'>Read more <i class='icon-angle-right'></i></a>";         
      content += "</div>";

      col_count++;

    });

    /* Update the column count and the page number */
    jQuery(".post-grid .btn-post-load-more").attr('col_count',col_count);
    jQuery(".post-grid .btn-post-load-more").attr('page_num',page_num);
    jQuery(".post-grid .btn-post-load-more").attr('event',event);

    /* If event is not triggered by load more replace results else append*/
    if(page_num < 2 ){

      $(".post-grid .posts-section").html(content);
    }else{
      $(".post-grid .posts-section").append(content);
    }

    /* Hide Load More button if no items left for the next page */ 
    page_num++;
    let nextpage_query_url = base_url+"/wp-json/wp/v2/posts?page="+page_num+"&per_page=11&_embed"+query_param; console.log(query_url);
    $.getJSON(nextpage_query_url, function(nextpage_data) { 
      console.log(nextpage_data.length);
      if(nextpage_data.length == 0 ){
        $('.post-grid .btn-post-load-more').hide();
      }else{
        $('.post-grid .btn-post-load-more').show(); 
      } 
    }).fail(function() { 
      $('.post-grid .btn-post-load-more').hide(); 
    });

  });

}

/* Search Function */
$(document).on("keyup","[data-post-search]",function(event){

  var searchParam = $(this).val();
  var charLength = searchParam.length;

  if(charLength > 2){
    jQuery(".post-grid .btn-post-load-more").attr('col_count',0);
    jQuery(".post-grid .btn-post-load-more").attr('page_num',0);
    post_grid_contents("search",searchParam);
  }else{
    $('.post-grid .posts-section .archive-title').hide();
  }

});

/* Filter Function */
$(document).on("change","[data-post-select]",function(){
  
  var filterValue = jQuery(this).val();

  jQuery(".post-grid .btn-post-load-more").attr('col_count',0);
  jQuery(".post-grid .btn-post-load-more").attr('page_num',0);

  /* Convert date to ISO */
  if(filterValue == "All"){
    var filterParam = "";
  }else{
    var beforeDate = new Date("December 31, "+filterValue+" 23:59:59");
    beforeDate = beforeDate.toISOString();
    var afterDate = new Date("January 1, "+filterValue+" 00:00:00");
    afterDate = afterDate.toISOString();

    var filterParam = "&before="+beforeDate+"&after"+afterDate;
  }

  post_grid_contents("filter",filterParam);

});

$(document).on("click","[data-post-load-more]",function(event){ 

  /* Check if search or filter or just load more and get value for param */
  var event = $(".post-grid .btn-post-load-more").attr("event");

  if(event == "load_more"){ 
    post_grid_contents(event,""); 
  }
  if(event == "search"){ 
    var searchParam = $(".post-grid .filter-section .col-search .input-search").val();
    post_grid_contents("search",searchParam); 
  }
  if(event == "filter"){ 

    var filterValue = $(".post-grid .filter-section .col-filter select").val();

    if(filterValue == "All"){
      var filterParam = "";
    }else{
      var beforeDate = new Date("December 31, "+filterValue+" 23:59:59");
      beforeDate = beforeDate.toISOString();
      var afterDate = new Date("January 1, "+filterValue+" 00:00:00");
      afterDate = afterDate.toISOString();

      var filterParam = "&before="+beforeDate+"&after"+afterDate;
    }
    post_grid_contents("filter",filterParam); 
  }

  


});
