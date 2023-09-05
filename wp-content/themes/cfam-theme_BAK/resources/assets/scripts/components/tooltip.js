module.exports = {
    init: function() {

        // console.log("Tooltip Script is running...");

        jQuery(document).on("click", ".tooltip", function(event) {

            const viewportWidth = window.innerWidth;

            /* Only activate this function if in mobile */
            if (viewportWidth < 780) {

                var hasClass = $(this).children("[tooltiptext]").hasClass("visible");

                if (hasClass == false) {
                    jQuery(this).children("[tooltiptext]").addClass("visible");
                } else {
                    jQuery(this).children("[tooltiptext]").removeClass("visible");
                }
            }

        });
        
        /* Mobile Tab functions */
        $(document).on("click", ".tab-post-list .tabs .btn-dropdown-menu li", function(event) {

            console.log("Im clicking");

        });
    },
};