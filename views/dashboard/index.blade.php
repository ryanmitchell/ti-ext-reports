<div class="row-fluid">
								
	<div class="list-filter border-top-0 mb-5" id="filter-list-filter">
		
	    <form id="filter-form" class="form-inline" accept-charset="utf-8" method="GET" action="<?= admin_url('thoughtco/reports/dashboard'); ?>" role="form">
		    	
			<div class="d-sm-flex flex-sm-wrap w-100 no-gutters">
		        
				@if (sizeof($this->getLocations()) > 1)
				<div class="col col-md-6 col-lg-4">
					
					<div class="filter-scope form-group pr-5 mb-2">
						
						<label for="fld-location">@lang('thoughtco.reports::default.label_location')</label>
						<select name="location" class="form-control select2-hidden-accessible" id="fld-location">
							@foreach ($this->getLocations() as $key=>$location)
							<option value="{{ $key }}" @if ($key == $locationParam) selected @endif>{{ $location }}</option>'; 
							@endforeach
            			</select>
            			
            		</div>
            		
		        </div>	
				@endif  
		        
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
		
	</div>
        
	<div class="row mx-1">
			
		<div class="col col-sm-4">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-counter sale">
					<i class="stat-icon  bg-green text-white fa fa-money-bill"></i>
					<span class="stat-number">{{ $results->total_sales }}</span>
					<span class="stat-text">@lang('thoughtco.reports::default.text_total_sales')</span>
				</div>
			</div>
		</div>

		<div class="col col-sm-4">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-counter sale">
					<i class="stat-icon  bg-blue text-white fa fa-shopping-basket"></i>
					<span class="stat-number">{{ $results->total_orders }}</span>
					<span class="stat-text">@lang('thoughtco.reports::default.text_total_orders')</span>
				</div>
			</div>
		</div>

		<div class="col col-sm-4">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-counter sale">
					<i class="stat-icon  bg-warning text-white fa fa-line-chart"></i>
					<span class="stat-number">{{ $results->quantity_of_items }}</span>
					<span class="stat-text">@lang('thoughtco.reports::default.text_total_items')</span>
				</div>
			</div>
		</div>
								 
	</div>
	
	<div class="row mx-1 mt-2">
					
		<div class="col col-sm-4">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-counter sale">
					<i class="stat-icon  bg-blue text-white fa fa-store"></i>
					<span class="stat-number">{{ currency_format($results->pickup_orders->value) }}</span>
					<span class="stat-text">@lang('thoughtco.reports::default.text_collection_orders')</span>
				</div>
			</div>
		</div>

		<div class="col col-sm-4">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-counter sale">
					<i class="stat-icon  bg-blue text-white fa fa-shipping-fast"></i>
					<span class="stat-number">{{ currency_format($results->delivery_orders->value) }}</span>
					<span class="stat-text">@lang('thoughtco.reports::default.text_delivery_orders')</span>
				</div>
			</div>
		</div>
		
		<div class="col col-sm-4">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-counter sale">
					<i class="stat-icon  bg-danger text-white fa fa-exclamation-circle"></i>
					<span class="stat-number">{{ currency_format($results->cancelled_orders->value) }}</span>
					<span class="stat-text">@lang('thoughtco.reports::default.text_cancelled_orders')</span>
				</div>
			</div>
		</div>		
								 
	</div>
	
	<div class="row mx-1 mt-4">
		
		<div class="col col-sm-6">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
					<h1 class="h4"><i class="stat-icon fa fa-users"></i> @lang('thoughtco.reports::default.text_top_customers')</h1>
				</div>				
				<div class="list-group list-group-flush">
				@foreach ($results->top_customers as $item)
					<div class="list-group-item bg-transparent">
						<b>{{ $item->name }}</b> <em class="pull-right">{{ currency_format($item->value) }}</em>
					</div>
				@endforeach
				</div>
			</div>
		</div>
		
		<div class="col col-sm-6">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
					<h1 class="h4"><i class="stat-icon fa fa-users"></i> @lang('thoughtco.reports::default.text_bottom_customers')</h1>
				</div>
				<div class="list-group list-group-flush">
				@foreach ($results->bottom_customers as $item)
					<div class="list-group-item bg-transparent">
						<b>{{ $item->name }}</b> <em class="pull-right">{{ currency_format($item->value) }}</em>
					</div>
				@endforeach
				</div>
			</div>
		</div>
		
	</div>
	
	<div class="row mx-1 mt-4">

		<div class="col col-sm-6">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
				    <h1 class="h4"><i class="stat-icon fa fa-shopping-bag"></i> @lang('thoughtco.reports::default.text_best_selling_items')</h1>
				</div>
				<div class="list-group list-group-flush">
				@foreach ($results->top_items as $item)
					<div class="list-group-item bg-transparent">
						<b>{{ $item->name }}</b>  <em class="pull-right">{{ $item->quantity }}</em>
					</div>
				@endforeach
				</div>
			</div>
		</div>
		
		<div class="col col-sm-6">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
					<h1 class="h4"><i class="stat-icon fa fa-shopping-bag"></i> @lang('thoughtco.reports::default.text_worst_selling_items')</h1>
				</div>
				<div class="list-group list-group-flush">
				@foreach ($results->bottom_items as $item)
					<div class="list-group-item bg-transparent">
						<b>{{ $item->name }}</b>  <em class="pull-right">{{ $item->quantity }}</em>
					</div>
				@endforeach
				</div>
			</div>
		</div>
		
	</div>
	
	<div class="row mx-1 mt-4">
				
		<div class="col col-sm-3">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
					<h1 class="h4"><i class="stat-icon fa fa-calendar"></i> @lang('thoughtco.reports::default.text_orders_by_day')</h1>
				</div>
			    <div
			        class="chart-container"
			        data-control="thoughtco-reports-chart"
			    >
			        <div class="chart-canvas">
						<textarea style="display:none;">{{ json_encode($results->orders_by_day_data) }}</textarea>
			            <canvas
			                id="order-by-day"
							style="width: 100%; height: 200px"
			            ></canvas>
			        </div>
			    </div>			
			</div>
		</div>

		<div class="col col-sm-3">
			<div class="card bg-light p-3 shadow-sm">
	            <div class="card-title">
	                <h1 class="h4"><i class="stat-icon fa fa-clock"></i> @lang('thoughtco.reports::default.text_orders_by_hour')</h1>
	            </div>
			    <div
			        class="chart-container"
			        data-control="thoughtco-reports-chart"
			    >
			        <div class="chart-canvas">
						<textarea style="display:none;">{{ json_encode($results->orders_by_hour_data) }}</textarea>
			            <canvas
			                id="order-by-hour"
							style="width: 100%; height: 200px"
			            ></canvas>
			        </div>
			    </div>
			</div>
		</div>

		<div class="col col-sm-3">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
					<h1 class="h4"><i class="stat-icon fa fa-stream"></i> @lang('thoughtco.reports::default.text_orders_by_category')</h1>
				</div>
			    <div
			        class="chart-container"
			        data-control="thoughtco-reports-chart"
			    >
			        <div class="chart-canvas">
						<textarea style="display:none;">{{ json_encode($results->orders_by_category_data) }}</textarea>
			            <canvas
			                id="order-by-category"
							style="width: 100%; height: 200px"
			            ></canvas>
			        </div>
			    </div>	
			</div>
		</div>

		<div class="col col-sm-3">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
					<h1 class="h4"><i class="stat-icon fa fa-money-check-alt"></i> @lang('thoughtco.reports::default.text_orders_by_payment_type')</h1>
				</div>
			    <div
			        class="chart-container"
			        data-control="thoughtco-reports-chart"
			    >
			        <div class="chart-canvas">
						<textarea style="display:none;">{{ json_encode($results->orders_by_payment_method_data) }}</textarea>
			            <canvas
			                id="order-by-payment-method"
							style="width: 100%; height: 200px"
			            ></canvas>
			        </div>
			    </div>	
			</div>
		</div>
				
	</div>
		    	    
</div>
