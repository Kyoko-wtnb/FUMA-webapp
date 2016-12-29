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
      })
});