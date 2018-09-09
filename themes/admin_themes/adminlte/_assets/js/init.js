$(document).ajaxStart(function() { Pace.restart(); });
$(".select2").select2();
$('.datepicker').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    endDate: '+1d'
});
$('.slimscroll').slimScroll({
    height: '200px',
    position: 'left'
});