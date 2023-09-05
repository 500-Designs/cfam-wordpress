module.exports = {
    init: function () {
        
        var hasFilter = false;

        function listPostContent(postType, isSort = false, searchParam = '', categoryId = '') {
            // console.log(postType);
            const x = ['literature', 'form'];

            let tabContentList = $('#tab-' + postType + ' [tab-content-list]');
            let currentPage = tabContentList.data('current-page');

            let orderBy = tabContentList.data('order-by');
            let order = tabContentList.data('order');

            let getUrl = "/wp-json/wp/v2/" + postType + '?orderby=' + orderBy + '&order=' + order;

            if (searchParam) {
                getUrl += '&search=' + searchParam;
            } else if (categoryId) {
                getUrl += '&categories=' + categoryId;
                $(".tab-content__search").val('');
            } else {
                getUrl += '&page=' + currentPage;
            }

            if (x.includes(postType)) {
                $.getJSON(getUrl, function (data) {
                    if (!hasFilter) {
                        setupCategoryFilter();                                    
                    }

                    if (postType === 'literature' && !hasFilter) {
                        renderCategoryNames(data);
                        hasFilter = true;
                    }

                    if (data.length > 0) {
                        let content = '';

                        $.each(data, function (index, item) {
                            content += '<div class="tab-content-list-item col-lg-3 col-md-6 mb-md-5 mb-3">';
                            content += '<div class="col-12 mb-4">';
                            content += '<a href="' + item.file_url + '" target="_blank">';
                            content += item.featured_image_html;
                            content += '</a>';
                            content += '</div>';
                            content += '<div class="col-12 mb-5">';
                            content += '<h4 class="text-decoration-underline">';
                            content += '<a href="' + item.file_url + '" target="_blank">';
                            content += item.title.rendered;
                            content += '</a></h4>'; // Added closing tag
                            content += '</div>';
                            content += '<div class="col-12">';
                            content += '<a href="' + item.file_url + '" class="" download="">Download&nbsp;<i class="icon-download"></i></a>';
                            content += '</div>';

                            if (index !== data.length - 1) {
                                content += '<hr />';
                            }

                            content += '</div>';
                        });

                        if (searchParam || isSort) {
                            tabContentList.html(content);
                        } else {
                            tabContentList.empty().append(content);
                        }
                    } else {
                        tabContentList.text('No data to show.');
                    }

                    if (data.length === 10) {
                        $('[tab-content-load-more]').show();
                    } else {
                        $('[tab-content-load-more]').hide();
                    }

                    if (currentPage > 1) {
                        $('[tab-content-load-previous]').show();
                    } else {
                        $('[tab-content-load-previous]').hide();
                    }

                });
            } else {
                $.get("/wp-json/wp/v2/sec_filing", function (data) {

                    let content = '';

                    const monthNames = ["January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"
                    ];

                    $.each(data, function (index, item) {
                        let d = new Date(item.date);
                        content += '<tr><td>';
                        content += '<a href="' + item.url + '" class="" target="_blank">';
                        content += item.title.rendered;
                        content += '<span class="ps-3 btn-label label-after">';
                        content += '<i class="icon-new-tab"></i>';
                        content += '</span></a></td>'; // Added closing tags
                        content += '<td>';
                        content += monthNames[d.getMonth()] + ' ' + d.getDate() + ", " + d.getFullYear();
                        content += '<td>';
                        content += '<a href="' + item.url + '" class="link" target="_blank">';
                        content += '<i class="icon-new-tab"></i> Open Link</a></td></tr>'; // Added closing tags

                        if (index !== data.length - 1) {
                            content += '<hr />';
                        }
                    });

                    $('#tab-' + postType + ' .custom-table tbody').empty().append(content);
                });
            }
        }

        // Function initial run
        listPostContent('literature');
        
       

        function renderCategoryNames(data) {
            $("#LiteratureFilter").show();
            let categoryIds = [];
            data.forEach(post => {
                if (post.categories) {
                    categoryIds.push(...post.categories);
                }
            });

            categoryIds = [...new Set(categoryIds)]; // remove duplicates
            console.log("renderCategoryNames...");
            categoryIds.forEach(categoryId => {
                $.get("/wp-json/wp/v2/categories/" + categoryId, function (categoryData) {
                    $("#LiteratureFilter select").append(new Option(categoryData.name, categoryId));
                });
            });
            hasFilter == true;
        }

        function setupCategoryFilter() {
            $("#LiteratureFilter select").change(function () {
                let selectedCategoryId = $(this).val();
                let postType = $('[tabs-nav-item].active [tabs-nav-item-link]').data('post-type');
                console.log("selectedCategoryId: ", selectedCategoryId);
                // If a category was selected, then fetch posts again with the category filter
                if (selectedCategoryId) {
                    listPostContent(postType, false, '', selectedCategoryId);
                } else {
                    // If no category was selected, fetch all posts
                    listPostContent(postType);
                }
            });
        }
        
        $('[tabs-nav-item-link]').click(function () {
            if ($(this).data('tab-type') === 'post_list')
                listPostContent($(this).data('post-type'));
        });

        $('[tab-content-sort]').change(function () {
            let postType = $('[tabs-nav-item].active [tabs-nav-item-link]').data('post-type');
            let sortVal = $(this).val();
            let tabContentList = $('#tab-' + postType + ' [tab-content-list]');

            switch (sortVal) {
                case "1":
                    tabContentList.data('order-by', 'date');
                    tabContentList.data('order', 'desc');
                    listPostContent(postType, true);
                    break;
                case "2":
                    tabContentList.data('order-by', 'date');
                    tabContentList.data('order', 'asc');
                    listPostContent(postType, true);
                    break;
                default:
                    tabContentList.data('order-by', 'title');
                    tabContentList.data('order', 'asc');
                    listPostContent(postType, true);
                    break;
            }
        });

        $('[tab-content-search]').on('keyup', function () {
            let postType = $('[tabs-nav-item].active [tabs-nav-item-link]').data('post-type');
            let searchParam = $(this).val();
            let charLength = searchParam.length;

            if (charLength > 2) {
                listPostContent(postType, false, searchParam);
            }
        });

        $('[tab-content-load-more]').on('click', function () {
            let postType = $('[tabs-nav-item].active [tabs-nav-item-link]').data('post-type');
            let tabContentList = $('#tab-' + postType + ' [tab-content-list]');
            let currentPage = tabContentList.data('current-page');
            tabContentList.data('current-page', currentPage + 1);

            listPostContent(postType);
        });

        $('[tab-content-load-previous]').on('click', function () {
            let postType = $('[tabs-nav-item].active [tabs-nav-item-link]').data('post-type');
            let tabContentList = $('#tab-' + postType + ' [tab-content-list]');
            let currentPage = tabContentList.data('current-page');

            if (currentPage > 1) {
                tabContentList.data('current-page', currentPage - 1);
                listPostContent(postType);
            }
        });
    },
};
