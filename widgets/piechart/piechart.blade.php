	<div class="card-title">
		<h1 class="h4"><i class="stat-icon {{ $chartIcon }}"></i> @lang($chartLabel)</h1>
	</div>				
    <div
        class="chart-container"
        data-control="thoughtco-reports-chart"
    >
        <div class="chart-canvas">
			<textarea style="display:none;">{!! json_encode($chartData) !!}</textarea>
            <canvas
				style="width: 100%; height: 200px"
            ></canvas>
        </div>
    </div>	
