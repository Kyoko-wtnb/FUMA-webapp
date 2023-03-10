$(document).ready(function(){
	function showPanel(hash){

		// Hide all panels
		$('.sidePanel').each(function(){
		    $('#'+this.id).hide();
		    $('#'+this.id+'sub').hide();
		});

		// Remove active class from menu
		$("#sidebar.sidebar-nav").find(".active").removeClass("active");

		// Show the current tab
		$(hash).show();
		$(hash+'sub').show();

		// Add active class to parent
		$("#sidebar.sidebar-nav a[href='"+hash+"']").parent().addClass("active");
	}

    // Default panel
    // showPanel('#joblist-panel'); //snp2gene page specific

    // Activate tab on click
	$('#sidebar.sidebar-nav li a').click(function(){
		showPanel($(this).attr("href"));
	});

    // Activate correct tab depending on hash //define in each page
    // var hash = window.location.hash;
    // if(hash){
    //   showPanel(hash);
    // }

	$("#menu-toggle").click(function(e) {
		e.preventDefault();
		$("#wrapper").toggleClass("active");
		if($('#wrapper').attr("class")=="active"){
			$('#main_icon').attr("class", "fa fa-chevron-left");
		}else{
			$('#main_icon').attr("class", "fa fa-chevron-right");
		}
	});
});
