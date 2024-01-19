$(document).ready(function(){
	
	$.ajax({
		method: 'post',
		url: 'save.php',
		data: {
			ip: ip,
			city: city,
			gadget: gadget
		},
		success: function(data){
			loadStat()
		}
	})
	
	function loadStat(){
		$.ajax({
			method: 'get',
			url: 'getstat.php',
			success: function(stat){
				printStat(JSON.parse(stat))
			}
		})
	}
	
	function printStat(stat){
		
		(async function() {
			let data = [];
		  
			for (key in stat.time){
				data.push({
					time: key,
					count: Object.keys(stat.time[key]).length
				})
			}
			
			let cities = [];
		  
			for (key in stat.city){
				cities.push({
					city: key,
					count: stat.city[key]
				})
			}
			  

			new Chart(
				document.getElementById('stat'),
				{
				  type: 'line',
				  data: {
					labels: data.map(row => row.time),
					datasets: [
					  {
						label: 'Посещаемость',
						data: data.map(row => row.count)
					  }
					]
				  }
				}
			);
			
			new Chart(
				document.getElementById('cities'),
				{
				  type: 'polarArea',
				  data: {
					labels: cities.map(row => row.city),
					datasets: [
					  {
						label: 'По городам',
						data: cities.map(row => row.count)
					  }
					]
				  }
				}
			);
		})();
		
	}
	
})
