module.exports = {
    init: function() {
        var teamModal = '<div id="teamModal" class="modal-default teamModal" data-team-modal>';
        teamModal += '<div class="modal-overlay teamModal-toggle" data-team-modal-toggle></div>';
        teamModal += '<div class="modal-wrapper"><img alt="loader" class="modal-loader" src="/wp-content/themes/lucera-bootstrap/dist/images/loader.gif" data-team-modal-loader>';
        teamModal += '<div class="modal-header d-none" data-team-modal-header>';
        teamModal += '<button class="modal-close teamModal-toggle" data-team-modal-toggle><i class="icon-close"></i></button>';
        teamModal += '</div>';
        teamModal += '<div class="modal-body d-none" data-team-modal-body>';
        teamModal += '<div class="modal-content d-none pt-md-4" data-team-modal-content>';
        teamModal += '<div class="row">';
        teamModal += '<div class="col-md-4">';
        teamModal += '<img src="https://via.placeholder.com/320" class="teamModal-image img-fluid" alt="Member Image" data-team-modal-image>';
        teamModal += '</div>';
        teamModal += '<div class="col-md-7 offset-md-1">';
        teamModal += '<h3 class="teamModal-name" data-team-modal-name>Member Name</h3>';
        teamModal += '<p class="teamModal-position text-black mb-0 font-bold" data-team-modal-position>Position</p>';
        teamModal += '<p class="teamModal-company text-black mt-2 mb-2 font-bold" data-team-modal-company>Company</p>';
        teamModal += '<div data-team-modal-bio><div class="teamModal-bio mt-3">';
        teamModal += '</div></div></div></div></div></div></div></div>';
        $('body').append(teamModal);


        $("[data-team-item-toggle]").on('click', function(e) {
            e.preventDefault();

            var imgLink = $(this).find('img').attr('src');

            $("[data-team-modal-image]").attr('src', imgLink);
            $("[data-team-modal-name]").text($(this).find("[data-team-item-name]").text());
            $("[data-team-modal-position]").text($(this).find("[data-team-item-positon]").text());
            $("[data-team-modal-company]").text($(this).find("[data-team-item-company]").text());
            $("[data-team-modal-bio]").html(($(this).find("[data-team-item-about]").html()));

            $(document).find("[data-team-modal]").toggleClass('is-visible');
            $(document).find("[data-team-modal-content]").toggleClass('d-none');
            $(document).find("[data-team-modal-loader]").toggleClass('d-none');
            $(document).find("[data-team-modal-header]").toggleClass('d-none');
            $(document).find("[data-team-modal-body]").toggleClass('d-none');
        })

        $("[data-team-modal-toggle]").on('click', function(e) {
            e.preventDefault();
            // $('.teamModal-toggle').toggleClass('is-visible');
            $("[data-team-modal-image]").attr('src', "");
            $("[data-team-modal-name]").text("");
            $("[data-team-modal-position]").text("");
            $("[data-team-modal-company]").text("");
            $("[data-team-modal-bio]").html("");

            $(document).find("[data-team-modal]").toggleClass('is-visible');
            $(document).find("[data-team-modal-content]").toggleClass('d-none');
            $(document).find("[data-team-modal-loader]").toggleClass('d-none');
            $(document).find("[data-team-modal-header]").toggleClass('d-none');
            $(document).find("[data-team-modal-body]").toggleClass('d-none');
        })
    },
};