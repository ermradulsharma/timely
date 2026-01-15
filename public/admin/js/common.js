function sizeConverter(bytes, decimalPoint) {
    if (bytes == 0) return '0 Bytes';
    var k = 1000,
        dm = decimalPoint || 2,
        sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
        i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm));
}

$(document).ready(function () {
    $('.delete-confirm').on('click', function () {
        bootbox.confirm({
            title: 'Confirm dialog',
            message: 'Native confirm dialog has been replaced with Bootbox confirm box.',
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-primary'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-link'
                }
            },
            callback: function (result) {
                bootbox.alert({
                    title: 'Confirmation result',
                    message: 'Confirm result: ' + result
                });
            }
        });
    });

    // Hide notification message
    $("div.alert").fadeTo(2000, 500).slideUp(500, function () {
        $("div.alert").slideUp(500);
    });

    // Basic datatable
    $('.my-datatable').DataTable({
        "bPaginate": false,
        "searching": false,
        'columnDefs': [{
            'targets': 0,
            // 'className': 'dt-body-center'
        }]
    });
});
