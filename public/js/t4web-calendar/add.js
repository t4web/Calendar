$(function () {
    const today = new Date();
    const y = today.getFullYear();

    $('#bs-datepicker-inline').multiDatesPicker({
        dateFormat: "yy-mm-dd",
        firstDay: 1,
        stepMonths: 12,
        numberOfMonths: [2,6],
        defaultDate: y+'-01-01',

        onSelect: function(dateText, inst) {
            const data = {date: dateText};

            $.ajax({
                url: '/calendar/ajax/save',
                type: 'POST',
                data: data,
                success: function (response) {
                    if (response.errors) {
                        console.log(errors);

                        return false
                    }

                },
                error: function (response) {
                    console.log(response);
                }
            });
        }
    });

    $.ajax({
        url: '/calendar/ajax/default',
        type: 'POST',
        success: function (response) {

            if (response.errors) {
                console.log(errors);
            } else {
                const dates = [];

                $.each(response.formData, function(k, v) {
                    dates.push(v.date);
                });

                $('#bs-datepicker-inline').multiDatesPicker('addDates', dates);
            }
        },

        error: function (response) {
            console.log(response);
        }
    });
});