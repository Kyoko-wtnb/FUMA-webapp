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
		var timer = new InactivityTimer("/logout", 86400000);
		timer.stop();
		timer.start();
	}
});

function InactivityTimer(path, delay){
	var timeout;
	function logout(){
		swal("Session timeout", "Please login again.", "error")
		window.location.href= path || "/logout";
	}

	function start(){
		if(!timeout){
			timeout = setTimeout(logout, delay || 86400000);
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
