//Source https://bootstrap-datepicker.readthedocs.io/en/latest/index.html


var dateSelect     = $('#widget-datepicker');
var dateStart      = $('#start-date');
var dateEnd        = $('#end-date');
var spanDepart     = $('.date-start');
var spanReturn     = $('.date-end');
var spanDateFormat = 'ddd, MMMM D yyyy';

dateSelect.datepicker({
  autoclose: true,
  format: "dd/mm/yyyy",
  maxViewMode: 2,
  
}).on('change', function() {
  var start = $.format.date(dateStart.datepicker('getDate'), spanDateFormat);
  var end = $.format.date(dateEnd.datepicker('getDate'), spanDateFormat);
  spanDepart.text(start);
  spanReturn.text(end);
});

