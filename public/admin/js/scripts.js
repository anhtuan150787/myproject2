$(document).ready(function() {

    $('div.captcha img').click(function(){
        $.get('/application/captcha/index', function(data) {
            $('div.captcha img').attr('src', data.url);
            $('input[name="Captcha[id]"]').val(data.id);
        });

    });

    $(".checkbox_all").change(function () {
        var chec = false;
        if ($(this).is(":checked")) {
            chec = true;
        }
        $(".checkbox_item").prop('checked', chec);
    });

    $('#btn_del_list').click(function(){
        var url = $("#frm_table").attr('action');

        if ($(".checkbox_item:checked").length == 0) {
            showMessagePopup('Vui lòng chọn đối tượng cần xóa');
            return;
        }

        $("#frm_table").attr('action', url + '/delete');
        $("#frm_table").submit();
    });

    if ($('.datepicker').length > 0) {
        $(".datepicker").daterangepicker({
            singleDatePicker: !0,
            singleClasses: "picker_4",
            locale: {format: "DD/MM/YYYY"},
        }, function (a, b, c) {
            console.log(a.toISOString(), b.toISOString(), c)
        });
    }

    if ($('.tags').length > 0) {
        $(".tags").tagsInput({width: "auto"});
    }
});

function confirmDelete() {
    if (!confirm('Bạn có chắc chắn muốn xóa?')) {
        return false;
    }
}

function showConfirm(functionName, object) {
    $('#message_confirm_body_modal').html('Bạn có chắc chắn thực hiện tác vụ này?');
    $('#btn_message_confirm_modal').trigger('click');

    if (functionName == 'deleteItem') {
        $('#btn_message_confirm_modal_action').attr('onclick', 'location.href="' + $(object).attr('data-href') + '"');
    }
}

function showMessagePopup(message) {
    $('#message_body_modal').html(message);
    $("#btn_message_modal").trigger('click');
}

function readURLImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.picture-upload-view')
                .attr('src', e.target.result)
                .width(50)
                .height(50);
        };

        reader.readAsDataURL(input.files[0]);
    }
}