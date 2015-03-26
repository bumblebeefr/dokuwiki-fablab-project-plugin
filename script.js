(function($){
	var _level1 = $("#dw__toc li.level1");
	if(_level1.length ==2){
	    $(_level1[0]).append($(_level1[1]).html());
	    $(_level1[1]).remove();
	}


	function hideDokuwikiToc() {
	    if($('.projectBox').length >0) {
		var elements = document.getElementsByTagName('div');
		for(var i=0; i < elements.length; i++) {
		    if(elements[i].id == 'dw__toc') {
		        $(elements[i]).remove();
		        break;
		    }
		}
		$("#dw__toc").remove();
	    }
	}
	$(function(){
		//remove dowkuwiki native toc when a projectBow is visible
		if($(".projectBox").length >0) {
			$("#dw__toc").remove();
		}

		//add the ability to toggle visbiiility of projectbox
		dw_page.makeToggle('.projectBox > h3','.projectBox > div');

		//List display
		$(".plugin_include_content .projectBox div.projectBoxPicture a").each(function(){
			$(this).parents(".projectBox").replaceWith($(this).addClass("projectBoxPicture"));
		});
		$("a.projectBoxPicture:odd").addClass("odd");
	});
})(jQuery);
