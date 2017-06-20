$(document).ready(function () {
    STUDIP.table($('#plugintable'), {sortable: true});

    $(document).on('dialog-open', function (event, parameters) {
        $('a.usage-proposal').click(function (e) {
            e.preventDefault();
            $('input#used_at').val($.trim(e.target.text));
        });
    });
});

STUDIP.PluginMarket = {
    addCollaborator: function (user_id, name) {
        jQuery.ajax({
            url: STUDIP.ABSOLUTE_URI_STUDIP + "plugins.php/pluginmarket/myplugins/add_user",
            data: {
                "user_id": user_id
            },
            success: function (html) {
                jQuery(html).hide().appendTo("#plugincollaborators").fadeIn();
            }
        });
        return false;
    }
};