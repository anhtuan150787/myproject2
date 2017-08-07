$(document).ready(function() {
    $(".checkbox_all").change(function () {
        var chec = false;
        if ($(this).is(":checked")) {
            chec = true;
        }
        $(".checkbox_item").prop('checked', chec);
    });

    $('#btn_del_list').click(function(){
        var url = $("#frm_table").attr('action');

        if ($(".checkbox_item:checked").length == 0)
        {
            alert('Vui lòng chọn đối tượng cần xóa.');
            return;
        }

        $("#frm_table").attr('action', url + '/delete');
        $("#frm_table").submit();
    });
});

function confirmDelete() {
    if (!confirm('Bạn có chắc chắn muốn xóa?')) {
        return false;
    }
}