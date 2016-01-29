STUDIP.SidebarGraphicsGenerator = {
    file: null,
    setFile: function (input) {
        var files = input.files;
        var file = files[0];
        jQuery("#downloader").attr("download", "sidebar-" + file.name.substr(0, file.name.lastIndexOf(".")) + ".png");

        var reader = new FileReader;
        reader.onload =  function () {
            STUDIP.SidebarGraphicsGenerator.file = new Image();
            STUDIP.SidebarGraphicsGenerator.file.src = reader.result;
            jQuery("#icon").attr("src", reader.result);
            window.setTimeout(STUDIP.SidebarGraphicsGenerator.drawImage, 200)
        };
        reader.readAsDataURL(file);
        jQuery("#save_instructions").show();
    },
    drawImage: function () {
        var canvas = window.document.getElementById("sidebar_image");
        var ctx = canvas.getContext("2d");
        ctx.clearRect(0,0,520,200);
        ctx.globalAlpha = 1;
        ctx.fillStyle = jQuery("#color").val();
        ctx.fillRect(0,0,520,200);
        var gradient = ctx.createLinearGradient(0,0,520,0);
        gradient.addColorStop(0, "rgba(255,255,255,0.0)");
        gradient.addColorStop(1, "rgba(255,255,255,0.1)");
        ctx.fillStyle = gradient;
        ctx.fillRect(0,0,520,200);

        if (STUDIP.SidebarGraphicsGenerator.file !== null) {
            var icon = jQuery("#icon")[0];
            var pre_icon = window.document.getElementById("pre_icon");
            var pre_icon_ctx = pre_icon.getContext("2d");
            pre_icon_ctx.globalAlpha = 1;
            pre_icon_ctx.clearRect(0, 0, 320, 320);
            pre_icon_ctx.drawImage(icon, 0, 0, 320, 320);

            ctx.globalCompositeOperation = "overlay";

            ctx.globalAlpha = 0.9;
            ctx.drawImage(pre_icon, 250, -100, 320, 320);

            ctx.globalAlpha = 0.35;
            ctx.drawImage(pre_icon, 20, 50, 300, 300);

            ctx.globalCompositeOperation = "source-over";
            ctx.globalAlpha = 1;
            ctx.drawImage(pre_icon, 60, 30, 70, 70);
        }
        ctx.globalAlpha = 0.5;
        ctx.fillStyle = jQuery("#color").val();
        ctx.fillRect(0,140,520,60);
    }
};
jQuery(STUDIP.SidebarGraphicsGenerator.drawImage);