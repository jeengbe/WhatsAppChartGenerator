$(document).ready(_ => {

    $("#contactSearch").on("input", function (e) {
        let val = $(this).val();
        sort(val);
    });

    $("#contactList>li:not(#contactSearchO)").on("click", function(e) {
        let jid = $(this).data("jid");
        $.ajax("generate.php", {
            data: {
                jid: jid
            },
            method: "POST",
            success: code => {
                $("#stats").html(code);
            }
        });
    });
});

function sort(val) {
    $("#contactList>li:not(#contactSearchO)").each(function () {
        let text = $(this).text();
        if (text.match(new RegExp(val, "i"))) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}