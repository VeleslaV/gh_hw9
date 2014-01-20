$(document).ready(function(){
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

    if($.cookie('admin_bar') == "hidden"){
        hideAdminBar(0);
    }else{
        showAdminBar(0);
    }

    $("div#admin_bar").click(function(){
        if($.cookie('admin_bar') == "hidden"){
            showAdminBar(500);
        }else{
            hideAdminBar(500);
        }
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
    function hideAdminBar(actionTime){
        $("#slideBtn").addClass("slide_hidden").removeClass("slide_visible");
        $("div#admin_bar").animate({ 'margin-top':'-25px' }, actionTime);
        $.cookie('admin_bar', "hidden", { expires: 230, path: '/' });
    }
    function showAdminBar(actionTime){
        $("#slideBtn").addClass("slide_visible").removeClass("slide_hidden");
        $("div#admin_bar").animate({ 'margin-top':'0px' }, actionTime);
        $.cookie('admin_bar', "visible", { expires: 230, path: '/' });
    }
});
