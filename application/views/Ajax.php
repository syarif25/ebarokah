<!-- Required vendors -->
    <script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
	<script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
	<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
	
	<!-- Apex Chart -->
	<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
	
	<script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
	
	<!-- Chart piety plugin files -->
    <script src="<?php echo base_url() ?>assets/vendor/peity/jquery.peity.min.js"></script>
	<!-- Dashboard 1 -->
	<!-- <script src="<?php echo base_url() ?>assets/js/dashboard/dashboard-1.js"></script> -->
	
	<script src="<?php echo base_url() ?>assets/vendor/owl-carousel/owl.carousel.js"></script>
	
    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.28.1/dist/apexcharts.min.js"></script>
    
	<script>
        

(function($) {
    /* "use strict" */
	
 var dlabChartlist = function(){
	
	var screenWidth = $(window).width();
	let draw = Chart.controllers.line.__super__.draw; //draw shadow
	
	var activityChart = function(){
		var activity = document.getElementById("activity");
			if (activity !== null) {
				var activityData = [{
						first: [ 30, 35, 30, 50, 30, 50, 40, 45],
						second: [ 20, 25, 20, 15, 25, 22, 24, 20]
						
					},
					{
						first: [ 35, 35, 40, 30, 38, 40, 50, 38],
						second: [ 30, 20, 35, 20, 25, 30, 25, 20]
						
					},
					{
						first: [ 35, 40, 40, 30, 38, 32, 42, 32],
						second: [ 20, 25, 35, 25, 22, 21, 21, 38]
						
					},
					{
						first: [ 35, 40, 30, 38, 32, 42, 30, 35],
						second: [ 25, 30, 35, 25, 20, 22, 25, 38]
					
					}
				];
				activity.height = 300;
				
				var config = {
					type: "line",
					data: {
						labels: [
							"1",
							"2",
							"3",
							"4",
							"5",
						],
						datasets: [{
								label: "Active",
								backgroundColor: "rgba(82, 177, 65, 0)",
								borderColor: "#3FC55E",
								data: activityData[0].first,
								borderWidth: 6
							},
							{
								label: "Inactive",
								backgroundColor: 'rgba(255, 142, 38, 0)',
								borderColor: "#4955FA",
								data: activityData[0].second,
								borderWidth: 6,
								
							},
							{
								label: "Inactive",
								backgroundColor: 'rgba(255, 142, 38, 0)',
								borderColor: "#F04949",
								data: activityData[0].third,
								borderWidth: 6,
								
							} 
						]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						elements: {
								point:{
									radius: 0
								}
						},
						legend:false,
						
						scales: {
							yAxes: [{
								gridLines: {
									color: "rgba(89, 59, 219,0.1)",
									drawBorder: true
								},
								ticks: {
									fontSize: 14,
									fontColor: "#6E6E6E",
									fontFamily: "Poppins"
								},
							}],
							xAxes: [{
								//FontSize: 40,
								gridLines: {
									display: false,
									zeroLineColor: "transparent"
								},
								ticks: {
									fontSize: 14,
									stepSize: 5,
									fontColor: "#6E6E6E",
									fontFamily: "Poppins"
								}
							}]
						},
						tooltips: {
							enabled: false,
							mode: "index",
							intersect: false,
							titleFontColor: "#888",
							bodyFontColor: "#555",
							titleFontSize: 12,
							bodyFontSize: 15,
							backgroundColor: "rgba(256,256,256,0.95)",
							displayColors: true,
							xPadding: 10,
							yPadding: 7,
							borderColor: "rgba(220, 220, 220, 0.9)",
							borderWidth: 2,
							caretSize: 6,
							caretPadding: 10
						}
					}
				};

				var ctx = document.getElementById("activity").getContext("2d");
				var myLine = new Chart(ctx, config);

				var items = document.querySelectorAll("#user-activity .nav-tabs .nav-item");
				items.forEach(function(item, index) {
					item.addEventListener("click", function() {
						config.data.datasets[0].data = activityData[index].first;
						config.data.datasets[1].data = activityData[index].second;
						config.data.datasets[2].data = activityData[index].third;
						myLine.update();
					});
				});
			}
	
		
	}
	

	url = "<?php echo site_url('Dashboard/barokah_statistik')?>";
	$.ajax({
    
	  url : url,
      type : "GET",
      dataType : "JSON",
      success: function(output)
      {

			// var reservationChart = function(){
			
				var options = {
				series: [{
				name: 'Tunjab',
				data: output.total_tunjab
				}, {
				name: 'TBK',
				data: output.total_tbk
				}, {
					name: 'Kehadiran',
					data: output.total_kehadiran
				}, {
					name: 'Kehormatan',
					data: output.total_kehormatan
				},
				{
					name: 'Tunkel',
					data: output.total_tunkel
				}],
				chart: {
				height: 350,
				type: 'line',
				toolbar:{
					show:false
				}
				},
				// membrikan warna hijau oranye
				// colors:["var(--primary)",'#FF5E4B'],
				dataLabels: {
					// menampilkan angka dichart
				enabled: false
				},
				stroke: {
					width:6,
					curve: 'smooth',
				},
				legend:{
					show:true
				},
				grid:{
					borderColor: '#EBEBEB',
					strokeDashArray: 6,
				},
				markers:{
					strokeWidth: 6,
					hover: {
					size: 15,
					}
				},
				yaxis: {
				labels: {
					offsetX:-12,
					style: {
						colors: '#787878',
						fontSize: '13px',
						fontFamily: 'Poppins',
						fontWeight: 400
						
					}
				},
				},
				xaxis: {
					categories: output.bulan,
				labels:{
					style: {
						colors: '#787878',
						fontSize: '13px',
						fontFamily: 'Poppins',
						fontWeight: 400
						
					},
				}
				},
				fill:{
					type:"",
					opacity:1
				},
				tooltip: {
				x: {
					format: 'dd/MM/yy HH:mm'
				},
				},
				};

				var chart = new ApexCharts(document.querySelector("#reservationChart"), options);
				chart.render();
			// }
	  }
	});

	var donutChart1 = function(){
		$("span.donut1").peity("donut", {
			width: "90",
			height: "90"
		});
	}
	
	
 
	/* Function ============ */
		return {
			init:function(){
			},
			
			
			load:function(){
			activityChart();
			donutChart1();
			
				
			},
			
			resize:function(){
			}
		}
	
	}();

	
		
	jQuery(window).on('load',function(){
		setTimeout(function(){
			dlabChartlist.load();
		}, 1000); 
		
	});

     

})(jQuery);
    </script>