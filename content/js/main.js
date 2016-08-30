$(document).ready(function(){
  //formfield toggle script
  $('legend').on('click',function(){
      $(this).parent().find('div:first').toggle();
      var fL = $(this).text().substr(0,1);
      var text = $(this).text().substr(1);
      if(fL=='-')
          $(this).text('+'+text);
      else if(fL=='+')
          $(this).text('-'+text);
  });
  
  //go to patient text box
  $('#goToPatient').on('keypress',function(e){
    var val = $(this).val();
    if(e.keyCode == 13 && val > 0){
      window.location=$(this).attr('href')+'/'+val;
    }
  });
  
//  ajax load of sidebar links
//  $('#sidebar .panel-body a').click(function(){
//    $.get($(this).attr('href'),'ajax=true',function(data){
//      $('#mainContent').html(data);
//    });
//    return false;
//  });

  //load sidebar state from cookie
  var last=$.cookie('sidebarState');
  if (last!=null&&last!='#collapseOne') {
    $("#sidebar .panel-collapse").removeClass('in');
    $(last).collapse("show");
  }
  
  //save sidebar state to cookie
  $(".panel-title a").on('click', function() {
    var active=$(this).attr('href');$.cookie('sidebarState',active,{path:'/'})
  });
});
