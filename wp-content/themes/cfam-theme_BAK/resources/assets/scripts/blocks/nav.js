module.exports = {
    init: function() {

        console.log("NAV is running...");

        $('#tabs-csv-select').change(function() {

            var csv_select = jQuery(this).val();
            console.log(`Changing to: ${csv_select}`);

            $('.nav-section .tab-content').hide();
            $('.nav-section #' + csv_select).show();
        });

    },
};