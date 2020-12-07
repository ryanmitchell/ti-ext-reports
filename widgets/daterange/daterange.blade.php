		
    <form id="filter-form" class="form-inline" accept-charset="utf-8" method="GET" action="{{ admin_url('thoughtco/reports/dashboard') }}" role="form">
	    	
		<div class="d-sm-flex flex-sm-wrap w-100 no-gutters">
	        
			<div class="col col-md-6 col-lg-3">
				
				<div class="filter-scope date form-group pr-5 mb-2">
					
					<label for="datepicker-formfixeddate-date-fixed-startdate">@lang('thoughtco.reports::default.label_start_date')</label>
					<div id="datepicker-formfixeddate-fixed-startdate" class="control-datepicker">
                
						<div class="input-group">
							<input type="text" id="datepicker-formfixeddate-date-fixed-startdate" class="form-control" autocomplete="off" value="{{ $startDate->format('d-m-Y') }}" data-control="datepicker" data-format="dd-mm-yyyy">
						    <span class="input-group-prepend" data-original-title="" title="">
						        <span class="input-group-icon" data-original-title="" title=""><i class="fa fa-calendar-o"></i></span> 
						    </span>
						    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}" data-datepicker-value="{{ $startDate->format('Y-m-d') }}">
						</div>
        			
        			</div>						
        			
        		</div>
        		
	        </div>      
	        
			<div class="col col-md-6 col-lg-3">
				
				<div class="filter-scope date form-group pr-5 mb-2">
					
					<label for="datepicker-formfixeddate-date-fixed-enddate">@lang('thoughtco.reports::default.label_end_date')</label>
					<div id="datepicker-formfixeddate-fixed-enddate" class="control-datepicker">
                
						<div class="input-group">
							<input type="text" id="datepicker-formfixeddate-date-fixed-enddate" class="form-control" autocomplete="off" value="{{ $endDate->format('d-m-Y') }}" data-control="datepicker" data-format="dd-mm-yyyy">
						    <span class="input-group-prepend" data-original-title="" title="">
						        <span class="input-group-icon" data-original-title="" title=""><i class="fa fa-calendar-o"></i></span> 
						    </span>
						    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}" data-datepicker-value="{{ $endDate->format('Y-m-d') }}">
						</div>
        			
        			</div>						
        			
        		</div>
        		
	        </div> 		        
	        		        
			<div class="col col-md-1 col-lg-1">
				
				<label>&nbsp;</label>
				<button type="submit" class="btn btn-primary">@lang('thoughtco.reports::default.btn_view')</button>
				
			</div>
    
    	</div>
    	
	</form>
		