
    <form id="filter-form" class="form-inline" accept-charset="utf-8" method="GET" action="{{ admin_url('thoughtco/reports/dashboard') }}" role="form">

	<div class="input-daterange input-group" id="widget-datepicker">
		<div class="form-item">	
			<label for="">@lang('thoughtco.reports::default.label_start_date')</label>
			<input 
				id="start-date"
				class="input-sm form-control" 
				type="text"				 
				name="start"
				value="{{ $startDate->format('d/m/Y') }}" 
				
				data-date-format="DD, MM d" />
			<span class="date-text date-start"></span>
		</div>
		
		<div class="form-item">
			<label for="">@lang('thoughtco.reports::default.label_end_date')</label>
			<input 
				id="end-date"
				class="input-sm form-control" 
				type="text" 				 
				name="end" 
				value="{{ $endDate->format('d/m/Y') }}" 				
				data-date-format="DD, MM d" />
			<span class="date-text date-end"></span>
		</div>
	</div>
	<div class="col align-self-end">
		<button type="submit" class="btn btn-primary ">@lang('thoughtco.reports::default.btn_view')</button>

	</div>
   	
    	
	</form>

	<script>
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
</script>