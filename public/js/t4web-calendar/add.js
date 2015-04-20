$(function () {
    initTooltip();
    initEditable();

    $('#holidays').on('click', '#btn-calendar-edit', function(){
        $('#holidays').find('.editable-open').editable('hide');
        $('#holidays').find('.btn-primary').hide();
        $('#holidays').find('#btn-calendar-edit').show();

        $(this).parent().find('#btn-calendar-delete').hide();
        $(this).hide().siblings('.btn-primary').show();
        $(this).closest('tr').find('.editable').editable('show');

        var spanDate = $(this).closest('tr').find('span[data-name="date"]');
        spanDate.hide();

        spanDate.after('<input type="text" placeholder="Date" name="date" class="form-control bs-datepicker" value="'+spanDate.data('value')+'">');

        var date = new Date(spanDate.data('value'));
        initDatapicker(date.getFullYear());
    });


    /**
     * Save holiday
     */
    $('#holidays').on('click', '.btn-primary, .btn-success', function() {
        var btn = $(this);
        var parentTr = $(this).parents('tr');
        var table = $(this).closest('table');

        /* remove error messages */
        if(parentTr.find('td').hasClass('has-error')) {
            parentTr.find('td').removeClass('has-error');

            parentTr.find('td p.help-block').remove();
        }

        /* get form data */
        var formData = [];

        parentTr.find('td').each(function(k, el) {
            var name = $(el).find('.editable-open').data('name');
            var value = $(el).find('.form-control').val();

            name = name || $(el).find('.form-control').attr('name');

            if(name !== undefined) {
                formData[name] = value;
            }
        });

        var data = $.extend({}, formData);
        data.id = $(this).data('id') || 0;

        $.ajax({
            url: '/calendar/ajax/save',
            type: 'POST',
            global: false,
            async: false,
            dataType: 'json',
            data: data,
            success: function(response) {

                /* draw errors */
                if (response.errors) {
                    $.each(response.errors, function (field, errors) {
                        var parent = parentTr.find('.form-control[name=' + field + ']').closest('td');

                        parent.addClass('has-error');

                        $.each(errors, function (key, error) {
                            parent.append('<p class="help-block">' + error + '</p>');
                        });
                    });

                    return false;
                }

                /* if save new record */
                if(btn.hasClass('btn-success')) {
                    var types = getTypesList();
                    var elements = '';

                    $.each(response.formData, function(n, v) {
                        var name = v;
                        if(n == 'type') {
                            name = types[v];
                        }
                        if(n != 'id') {
                            elements += '<td><span data-value="'+v+'" data-name="'+n+'">'+name+'</span></td>';
                        }
                    });

                    var buttons = '<td>' +
                        '<button id="btn-calendar-edit" class="btn btn-xs btn-labeled btn-warning">' +
                        '<span class="btn-label icon fa fa-pencil"></span>Edit' +
                        '</button> ' +
                        '<button data-id="'+response.formData.id+'" style="display: none;" class="btn btn-primary">save</button> ' +
                        '<button id="btn-calendar-delete" data-id="'+response.formData.id+'" class="btn btn-xs btn-labeled btn-danger">' +
                        '<span class="btn-label icon fa fa-times"></span>Delete</button>' +
                        '</td>';

                    parentTr.before('<tr>'+elements+buttons+'</tr>');

                    parentTr.find('input[name="date"]').val('');
                    parentTr.find('input[name="name"]').val('');
                    parentTr.find('select[name="type"]').val(1);

                    initEditable();

                    var className = '';
                    if(response.formData.type == 1) {
                        className = 'active';
                    }
                    if(response.formData.type == 2) {
                        className = 'bg-success';
                    }

                    $('table.table-calendar td[data-date=' + response.formData.date +']').addClass(className);
                    $('table.table-calendar td[data-date=' + response.formData.date +']').attr('data-toggle', 'tooltip');
                    $('table.table-calendar td[data-date=' + response.formData.date +']').attr('data-original-title', response.formData.name);
                } else {
                    btn.closest('tr').find('.editable').editable('hide');
                    btn.hide().siblings('#btn-calendar-edit').show();
                    btn.hide().siblings('#btn-calendar-delete').show();

                    /* type */
                    var types = getTypesList();
                    var spanType = btn.closest('tr').find('span[data-name="type"]');
                    spanType.data('value', response.formData.type);
                    spanType.text(types[response.formData.type]);

                    /* date */
                    var spanDate = btn.closest('tr').find('span[data-name="date"]');
                    spanDate.data('value', response.formData.date);
                    spanDate.text(response.formData.date);
                    spanDate.show();

                    /* name */
                    var spanDate = btn.closest('tr').find('span[data-name="name"]');
                    spanDate.data('value', response.formData.name);
                    spanDate.text(response.formData.name);

                    btn.closest('tr').find('input[name="date"]').remove();

                    var className = '';
                    if(response.formData.type == 1) {
                        className = 'active';
                    }
                    if(response.formData.type == 2) {
                        className = 'bg-success';
                    }

                    $('table.table-calendar td[data-date=' + response.formData.date +']').removeClass('active');
                    $('table.table-calendar td[data-date=' + response.formData.date +']').removeClass('bg-success');

                    $('table.table-calendar td[data-date=' + response.formData.date +']').addClass(className);
                    $('table.table-calendar td[data-date=' + response.formData.date +']').attr('data-original-title', response.formData.name);
                }

                initTooltip();

            }
        });
    });

    /**
     * Delete holiday
     */
    $('#holidays').on('click', '#btn-calendar-delete', function() {
        var parent = $(this).closest('tr');
        var data = {id: $(this).data('id')};
        var date = $(this).data('date');

        $.ajax({
            url: '/calendar/ajax/delete',
            type: 'POST',
            global: false,
            async: false,
            dataType: 'json',
            data: data,
            success: function(response) {

                if (response.errors) {
                    bootbox.alert({
                        message: "Произошла ошибка",
                        className: "bootbox-sm"
                    });
                } else {
                    parent.remove();

                    $('table.table-calendar td[data-date=' + date +']').removeClass('active');
                    $('table.table-calendar td[data-date=' + date +']').removeClass('bg-success');
                    $('table.table-calendar td[data-date=' + date +']').removeAttr('data-original-title');
                    $('table.table-calendar td[data-date=' + date +']').removeAttr('data-toggle');
                }
            }
        });
    });

    /**
     * get types list
     */
    function getTypesList() {
        var result = {};

        $.ajax({
            url: '/calendar/ajax/type-list',
            type: 'GET',
            global: false,
            async: false,
            dataType: 'json',
            success: function(data) {

                result = data.formData;
            },
            error: function (response) {
                bootbox.alert({
                    message: response.statusText,
                    className: "bootbox-sm"
                });
            }
        });

        return result;
    }

});

function initTooltip() {
    $('[data-toggle="tooltip"]').tooltip({
        'container': 'body',
        'placement': 'top'
    });
}

function initDatapicker(year) {
    var nowDate = new Date();

    var year = year || nowDate.getFullYear();

    $('.bs-datepicker').datepicker({
        format: "yyyy-mm-dd",
        weekStart: 1,
        autoclose: true,
        defaultDate: new Date(year, 1, 1),
        startDate: year+"-01-01",
        endDate: year+"-12-31"
    });
}

function initEditable() {
    var types = [];

    var defaults = {
        mode: 'inline',
        toggle: 'manual',
        inputclass: 'form-control',
        showbuttons: false,
        onblur: 'ignore',
        savenochange: true,
        success: function() {
            return false;
        }
    };

    $.extend($.fn.editable.defaults, defaults);

    $('span[data-name="name"]').editable({
        placeholder: 'Name',
        emptytext: ''
    });

    $('span[data-name="type"]').editable({
        type: 'select',
        source: function() {
            if(types.length > 0) {
                return types;
            }

            $.ajax({
                url: '/calendar/ajax/type-list',
                type: 'GET',
                global: false,
                async: false,
                dataType: 'json',
                success: function(data) {
                    $.each(data.formData, function(k, v) {
                        types.push({value: k, text: v});
                    });
                },
                error: function (response) {
                    bootbox.alert({
                        message: response.statusText,
                        className: "bootbox-sm"
                    });
                }
            });
            return types;
        }
    });

}