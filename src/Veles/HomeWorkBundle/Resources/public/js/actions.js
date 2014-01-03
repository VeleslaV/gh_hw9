$(document).ready(function(){
    $(".title h3 a").click(function(event){
         event.preventDefault();
         var linkLocation = this.href;
         var linkId = linkLocation.match(/\/article\/(.*)/)[1];
         var path = Routing.generate('_ajax_update_article_views', { });


         $.post(path,{ "id": linkId} ,function(request){
             var temp = JSON.parse(request);
             if(temp.code == 100 || temp.success){
                 //$("#ajaxData").html(temp.id);
                 window.location = linkLocation;
             }
         });
     });
});
