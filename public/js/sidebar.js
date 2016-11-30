$(document).ready(function(){

  $('.sidePanel').each(function(){
    $('#'+this.id).hide();
    $('#'+this.id+'sub').hide();
  });

  $('#sidebar.sidebar-nav li a').click(function(){
    $("#sidebar.sidebar-nav").find(".active").removeClass("active");
    $(this).parent().addClass("active");
    $('.sidePanel').each(function(){
      $('#'+this.id).hide();
      $('#'+this.id+'sub').hide();
    });
    var id = $(this).attr("href");
    if($(id+'sub').length>0){
      $(id+'sub').show();
    }
    $(id).show();
  });

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
