$(document).ready(function () {
    STUDIP.table($('#plugintable'), {sortable: true});

    $(document).on('dialog-open', function (event, parameters) {
        $('a.usage-proposal').click(function (e) {
            e.preventDefault();
            $('input#used_at').val('test');
            $('input#used_at').val($.trim(e.target.text));
        });
    });
});