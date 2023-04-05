$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

$('.image_input').on('change', function () {

    $this = $(this);

    $this.parent().find('.preview_container').show();

    if (this.files && this.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $this.parent().find('.preview_container img').attr('src', e.target.result);
        };

        reader.readAsDataURL(this.files[0]);
    } else {
        alert('Sorry - you\'re browser doesn\'t support the FileReader API');
    }
});

$(document).on('click', '.delete_link', function (event) {

    event.preventDefault();
    var ajax_url = $(this).attr('href');
    var action_name = $(this).attr('data-name');
    var return_url = $(this).attr('data-return-url');

    bootbox.hideAll();

    bootbox.dialog({
        message: "Are you sure to delete this " + action_name + "?",
        title: "Delete " + action_name,
        buttons: {
            yes: {
                label: "Yes",
                className: "btn-primary",
                callback: function () {
                    jQuery.ajax({
                        url: ajax_url,
                        type: 'POST',
                        data: {
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            if (response.deleted == true) {
                                if (typeof return_url !== 'undefined' && return_url != "") {
                                    window.location.href = return_url;
                                } else {
                                    location.reload();
                                }
                            } else {
                                bootbox.alert(response.message);
                            }
                        }
                    });
                }
            },
            cancel: {
                label: "Close",
                className: "btn-default",
                callback: function () {
                    //Example.show("uh oh, look out!");
                }
            }
        }
    });

});
