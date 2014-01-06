$(document).ready(function(){
    $(".title h3 a, .articles ul.nav li a").click(function(event){
        event.preventDefault();
        var linkLocation = this.href;
        var linkId = linkLocation.match(/\/article\/(.*)/)[1];
        var path = Routing.generate('_ajax_update_article_views', { });


         $.post(path,{ "id": linkId} ,function(request){
             var temp = JSON.parse(request);
             if(temp.code == 100 || temp.success){
                 window.location = linkLocation;
             }
         });
    });

    $("a#showMoreArticles").click(function(){
        var currentPage = $("span#pageNum").html();
        var page = parseInt(currentPage) + 1;
        var path = Routing.generate('_load_more_article', { page: page });

        $("#loaderContainer").html("<img src='http://stars.mmotorg.ru/img/loader.gif'>").fadeOut("slow", function() {
            $.post(path, { "page": page }, function(request){
                if(!$.isEmptyObject(request)){
                    $("#moreArticlesContainer").before(request);
                    $("span#pageNum").html(page);
                }
            });
        });

        return false;
    });

    $("form#searchForm").submit(function(){
        var keywordData;
        var inputElement = "input#form_keyword";
        keywordData = $(inputElement).val();

        $("input").focus(function() {
            $(this).removeClass("has_error");
            $("input#form_keyword").popover('destroy')
        });

        if(keywordData){
            if(keywordData.length < 3){
                $(inputElement).addClass("has_error");
                myMopover("This value to short");
                return false;
            }
        }else{
            $(inputElement).addClass("has_error");
            myMopover("This value should not be blank");
            return false;
        }
    });


    var uriString = document.URL.match(/\/search\/(.*)\//);
    if(uriString){
        var keyword = uriString[1].replace("%20", " ");
        if(keyword){
            $('div.search_article_container').highlight(keyword);
        }
    }

    function myMopover(message){
        $("input#form_keyword").popover({
            content: message,
            placement: "left"
        }).popover('show');
    }
});
