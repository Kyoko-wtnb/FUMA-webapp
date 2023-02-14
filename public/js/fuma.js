// Global functions and methods

$(document).ready(function(){
	// popover
	var cnt = 10;
	$('.infoPop')
		.each(function(){
			$(this)
				.attr('data-trigger', 'focus')
				.attr('role', 'button')
				.attr('tabindex', cnt)
				.popover();
			cnt = cnt + 1;
		});

	if(loggedin==1){
		var timer = new InactivityTimer("/logout", 7200000); // 2 hour timeout 
		timer.stop();
		timer.start();
	}

	//app info
	$('#appInfo').on('click', function(){
		$.ajax({
			url: '/appinfo',
			type: 'GET',
			error: function(){
				alert("appInfo error")
			},
			success: function(data){
				data = JSON.parse(data)
				$('#FUMAver').html(data.ver);
				$('#FUMAuser').html(data.user);
				$('#FUMAs2g').html(data.s2g);
				$('#FUMAg2f').html(data.g2f);
				$('#FUMArun').html(data.run);
				$('#FUMAque').html(data.que);
			}
		})
	})
});

function InactivityTimer(path, delay){
	var timeout;
	function logout(){
		swal("Session timeout", "Please login again.", "error")
		document.getElementById("fuma-logout-link").click();
	}

	function start(){
		if(!timeout){
			timeout = setTimeout(logout, delay || 86400000); // Default 24 hours
		}
	}

	function stop(){
		if (timeout){
			clearTimeout(timeout);
			timeout = null;
		}
	}

	function reset(){
		stop();
		start();
	}

	this.start = start;
	this.stop = stop;
	this.reset = reset;

	document.addEventListener("mousemove", reset);
	document.addEventListener("keypress",  reset);
}
