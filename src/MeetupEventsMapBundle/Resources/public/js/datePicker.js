/**
 * Script updates the date so that the start date can never be greater than the end date.
 */
$("#date_range_startDate").datepicker({
    autoclose: true,
}).on('changeDate', function (selected) {
    var startDate = euroDateStringToDate($("#date_range_startDate").val());
    var endDate = euroDateStringToDate($("#date_range_endDate").val());

    if (startDate.getTime() > endDate.getTime()) {
        $('#date_range_endDate').datepicker('update', startDate);
    }
});

$("#date_range_endDate").datepicker({
    autoclose: true,
}).on('changeDate', function (selected) {
    var startDate = euroDateStringToDate($("#date_range_startDate").val());
    var endDate = euroDateStringToDate($("#date_range_endDate").val());

    if (startDate.getTime() > endDate.getTime()) {
        $('#date_range_startDate').datepicker('update', endDate);
    }
});

function euroDateStringToDate(dateString)
{
    var parts = dateString.split("-");
    var dt = new Date(
        parseInt(parts[2], 10),
        parseInt(parts[1], 10) - 1,
        parseInt(parts[0], 10)
    );

    return dt;
}
