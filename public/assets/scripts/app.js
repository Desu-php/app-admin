// путь для datatables
if (typeof (url) === "undefined") {

    var url = window.location.href;
    urls = url.split('?', 2)
    if (urls.length > 1) {
     var urlParams = parseUrlParams(urls[1])
    }
    url = urls[0] + "/ajax/get?="

}

var urlForDelete = window.location.href

function Delete(id) {
    swal({
        title: "Удаление",
        text: "Вы точно хотите удалить?",
        icon: "warning",
        buttons: {
            cancel: {
                text: "Нет",
                value: false,
                visible: true,
                closeModal: true,

            },
            confirm: {
                text: "Да",
                value: true,
                visible: true,
                className: "",
                closeModal: true
            },
        },
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: urlForDelete + '/' + id,
                    type: "DELETE",
                    data: ({"_token": $('meta[name="csrf-token"]').attr('content')}),
                    success: function (success) {
                        if (success === false) {
                            console.log(success)
                            swal("Данный заказ невозможно удалить", {
                                icon: "info",
                            });
                            return
                        }
                        swal("Усешно удален", {
                            icon: "success",
                        });
                        $('#dataTable').DataTable().ajax.reload();
                    }
                })

            } else {

            }
        });
}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

if (typeof (orderColumn) === "undefined") {
    var orderColumn = 0
}

if (typeof (order) === "undefined") {
    var order = "asc"
}

if (typeof (columns) !== "undefined") {
    $(function () {

        var table = $('#dataTable').DataTable({
            order: [[orderColumn, order]],
            responsive: true,
            ajax: {
                url,
                data: (urlParams),
            },
            searching: true,
            ordering: true,
            processing: true,
            serverSide: true,
            autoWidth: true,
            lengthChange: true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            columns: columns,
            // dom: '<"table-responsive"t>',
            language: {
                search: '<span>Search:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '→', 'previous': '←'}
            }
        });

        $(document).on('click', '.filtered', function (event) {
            event.stopPropagation()
        });

        $('.filtered').click(function (event) {
            event.stopPropagation()
        });

        $(document).on('input change', '.filtered', function () {
            table.columns($(this).data('column')).search($(this).val()).draw();
        })
    });
}

$('#added_form').submit(function (e) {
    e.preventDefault()
    var formData = $(this).serialize()
    var form = $(this)
    form.find('button').attr('disabled', true)
    validationErrorsClear(form)
    $.ajax({
        url: form.attr('action'),
        data: formData,
        type: form.attr('method'),
        success: function (data) {
            showAlert('success', 'Успешно!', data.message)
            if (form.attr('method').toLowerCase() === 'post') {
                validationErrorsClear(form, true)
            } else {
                validationErrorsClear(form)
            }

        },
        error: function (data) {
            var status = data.status
            var errors = data.responseJSON.errors

            if (status === 400) {
                validationErrorsView(form, errors)
            } else {
                showAlert('danger', 'Ошибка!', data.responseJSON.message)
            }
        },
        complete: function (data) {
            form.find('button').attr('disabled', false)
        }
    })
})


function validationErrorsClear(form, fields = false) {
    $(".invalid-feedback").remove();
    form.find("input").removeClass("is-invalid");
    form.find("select").removeClass("is-invalid");

    if (fields) {
        $(":input", form)
            .not(":button, :submit, :reset, :hidden")
            .val("")
            .removeAttr("checked")
            .removeAttr("selected");
    }
}

function validationErrorsView(form, errors) {
    for (var key in errors) {
        for (var index in errors[key]) {
            form.find("#" + key)
                .addClass("is-invalid")
                .after(
                    '<div class="invalid-feedback">' +
                    errors[key][index].replace(
                        key,
                        form.find("#" + key).attr("placeholder")
                    ) +
                    "</div>"
                );
        }
    }
}

function showAlert(status, text_status, message) {
    var html = `
        <div class="alert alert-${status} alert-dismissible fade show mb-0" role="alert" id="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
    <i class="fa fa-${status === "success" ? "check" : "exclamation"} mx-2"></i>
    <strong id="alert-status">${text_status}</strong> ${message}</div>
    `
    $('#after_alert').after(html)
}

$('.link-icon').click(function (e) {
    e.preventDefault()

    $(this).parent().find('.sidebar-submenu').toggleClass('d-block')
})

function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

function parseUrlParams(url) {
    const parameters = url.split('&');
    const result = {}
    parameters.forEach((item) => {
        const params = item.split('=')
        result[params[0]] = params[1];
    })
    return result
}
