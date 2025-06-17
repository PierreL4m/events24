//$.getScript("/build/js/trainee_form.js");
var $ = require('jquery');
require('bootstrap-less/js/tooltip.js');
require('bootstrap-less/js/popover.js');
// var Cookies = require('js-cookie/src/js.cookie.js');

$(document).ready(function() {
	$('.custom-file-input').removeClass('custom-file-input');
	$('.custom-file-label').removeClass('custom-file-label');
	$(document).on('click','.closeJobModal',function() {
		window.$('#jobModal').css('display','none');
		window.$('.modal-backdrop').css('display','none');
		window.$('#jobModal').removeClass('show');
		window.$('#jobModal').modal('toggle');

	});
	$(document).on('click','#contact_btn',function() {
		window.$('.modal').css('display','none');
		window.$('.modal-backdrop').css('display','none');
		window.$('.modal').removeClass('show');
		window.$('.modal').modal('toggle');
	});
	if ($('#job_city').length > 0){

		function displayCitiesJob(val,input){
			if(val.length > 1){
				$.ajax({
					url: "/autocomplete",
					data: {data : val},
					success: function(data){
						$('#job_city').before('<div id="search_results"></div>')
						var $result = $('#search_results');

						$.each(data.cities, function(i,v){
							var name = v.cp + " " +v.name;

							if(v.country != 'France'){
								name = name + " (" + v.country +")";
							}
							$result.append("<div class='city_item' data-id='"+v.id+"'>" + name + "</div>");
						});
					}
				});
			}
		}

		window.setInterval(function(){
			var input = $('#job_city') ;

			if (input.is(':focus')){
				//to do detect return and send cache reset
				var val = $('#job_city').val();

				if (sessionStorage.city_search){
					if (sessionStorage.city_search != val){
						sessionStorage.city_search = val;
						$('#search_results').remove();
						displayCitiesJob(val,input);
					}
				}
				else{
					sessionStorage.city_search = val;
					displayCitiesJob(val,input);
				}
			}
			else if (input.val() != "" && $('#job_city_id').val() == ""){
				//this should not happend if autocomplete is disabled
				input.val("");
			}
		},500);

		$('form[name="job"]').on("click", 'div.city_item',function(){

			$('#search_results').remove();
			$('#job_city').val($(this).html());
			$('#job_city_id').val($(this).attr('data-id'));
		});
		$(document).on('click', '.focus-select', function(){
			$(this).select();
		});

	}

	if ($('#job_contractType').val() != 1) {
		$('#timeContract').css('display','block');
	}else{
		$('#timeContract').css('display','none');
	}
	$('#job_contractType').change(function(){
		if ($(this).val() != 1) {
			$('#timeContract').css('display','block');
		}else{
			$('#timeContract').css('display','none');
		}
	})
	$('#closAalertOpen').click(function(){
		$('#hubAlertOpen').css('display','none');
	})
	$('#registration_slotsFull').css('color','red');
	$('#registration_slotsFull').find('.form-check-label').append(' (complet)');
	function testURL(){
		var pageURL = window.location.href;
		var lastURLSegment = pageURL.substr(pageURL.lastIndexOf('_') + 1);
		//$('.modalTest').attr("id",'myModal' + lastURLSegment);
		var id = lastURLSegment;
		var index = 1;
		var max = $('.linkToModal').attr("last");
		var array = $('.modal').attr("testArray");
		//$('.modalTest').attr("id",'myModal' + id);
		$.ajax({
			type : 'GET',
			url: "/divParticipant/"+id+"/"+index+"_"+max,
			data : {data: array},
			success: function(id){
				$('.modal-body').html(id);
				//$('.flex_container').fadeIn();
			}
		});
	}
	if (window.location.href.lastIndexOf('_') + 1) {
		testURL();
	}
	$('form[name="registration"]').on('submit',function(e){
		$('#register_action').addClass('button_loading');
	});
	// $('#register_action').on('click', function(e){
	// 	e.preventDefault();
	// });
	////////////////////////////////////////////
	/// to do : search where hash? if screwed ///////////
	////////////////////////////////////////////
	var hash = window.location.hash;

	if (hash[hash.length-1] == '?'){
		window.location.hash = '';
	}
	////////////////////////////////////////////
	////////// handle cookie consent ///////////
	////////////////////////////////////////////
	// function dismiss(){
	// 	var consent = Cookies.get('cookieconsent_dismissed');
	//
	// 	if (consent == undefined){
	// 		Cookies.set('cookieconsent_dismissed','yes');
	// 	}
	// 	$('.cc_banner-wrapper').remove();
	// }
	////////////////////////////////////////////
	//////////// common function ///////////////
	////////////////////////////////////////////
	function checkVisible( elm, evalType ) {

		if($(elm).length == 0){
			return false;
		}
		evalType = evalType || "visible";

		var vpH = $(window).height(), // Viewport Height
			st = $(window).scrollTop(), // Scroll Top
			y = $(elm).offset().top,
			elementHeight = $(elm).height();

		if (evalType === "visible") return ((y < (vpH + st)) && (y > (st - elementHeight)));
		if (evalType === "above") return ((y < (vpH + st)));
	}
	////////////////////////////////////////////
	//////////// check if apple ////////////////
	////////////////////////////////////////////
	var isiPhone = navigator.userAgent.toLowerCase().indexOf("iphone");
	var isiPad = navigator.userAgent.toLowerCase().indexOf("ipad");
	var isiPod = navigator.userAgent.toLowerCase().indexOf("ipod");

	var apple = false;
	var iphone;

	if (isiPhone != -1 || isiPad != -1 || isiPod != -1){
		apple = true;
	}

	/////////////////////////////////////////
	/////////////// modales /////////////////
	/////////////////////////////////////////
	function displayModale(){
		var hash = window.location.hash;

		if(hash.search('#fiche') != -1
			|| hash.search('#participant') != -1
			|| hash.search('#exposant') != -1
			|| hash.search('#agenda') != -1
			|| hash.search('#joblink') != -1
		){
			if (hash[hash.length] != '?'){
				var id = hash.substr(hash.lastIndexOf('_') + 1, hash.length);

				if(hash.search('formation_') != -1){
					var modal = $('#myModalformation_' + id)
				}
				else if(hash.search('participant_') != -1){
					var modal = $('#myModal_participant_' + id)
				}
				else if(hash.search('joblink_') != -1){
					var modal = $('#myModal_joblink_' + id)
				}
				else if(hash.search('agenda_') != -1){
					var modal = $('#myModal_agenda_' + id)
				}
				else if(hash.search('contact_') != -1){
					var modal = $('#contactModal')
				}
				else if(hash.search('exposant') != -1){
					var data_id = 'exposant_' + id;
					var element = $('.grid').find('[data-id='+data_id+']');
					var modal = $('#my'+ element.attr('id'));
				}
				else{
					var modal = $('#myModal' + id)
				}
				if(modal != undefined){
					modal.fadeIn(1500);
					$('body').css('overflow','hidden');
				}
			}
		}
	}


	$(document).on('click',"#registerJob", function(event){
		var modal = $('#modalContact')
		modal.fadeIn(1500);
	})
	$(document).on('click',"#contactButton", function(){
		var poste = $(this).attr("poste");
		$("#registration_job_"+poste).attr("checked", "checked");
	})
	$('.linkToModal').click(function(){
		var id = $(this).attr("rel");
		var index = $(this).attr("index");
		var max = $(this).attr("last");
		var array = $('.modal').attr("testArray");
		//$('.modalTest').attr("id",'myModal' + id);
		$.ajax({
			type : 'GET',
			url: "/divParticipant/"+id+"/"+index+"_"+max,
			data : {data: array},
			success: function(id){
				$('.modal-body').html(id);
				//$('.flex_container').fadeIn();
			}
		});
	})
	$(document).on('click',".jobLinkModal", function(event){
		var id = $(this).attr("idJob");
		$.ajax({
			type : 'GET',
			url: "/showJobContent/"+id,
			success: function(id){
				$('#bodyModalJob').html(id);
				//$('.flex_container').fadeIn();
			}
		});
	})
	$(document).on('click',"#prevModal", function(event){
		var id = $(this).attr("idExposant");
		var index = $(this).attr("prevIndex");
		var max = $(this).attr("max");
		var array = $('.modal').attr("testArray");
		//$('.modalTest').attr("id",'myModal' + index);
		$.ajax({
			type : 'GET',
			url: "/prevModal/"+id+"/"+index+"_"+max,
			data : {data: array},
			success: function(id){
				$('.modal-body').html(id);
			}
		});
	})
	$(document).on('click',"#nextModal", function(event){
		var id = $(this).attr("idExposant");
		var index = $(this).attr("nextIndex");
		var max = $(this).attr("max");
		var array = $('.modal').attr("testArray");
		//$('.modalTest').attr("id",'myModal' + index);
		$.ajax({
			type : 'GET',
			url: "/nextModal/"+id+"/"+index+"_"+max,
			data : {data: array},
			success: function(id){
				$('.modal-body').html(id);
			}
		});
	})

	displayModale();



	$(window).bind('hashchange', function() {
		displayModale();
	})
	$(window).bind('change',function(){
		displayModale();
	})
	$('[id^=odal]').on('click',function(e){
		e.preventDefault();
		var modal = $('#myM' + $(this).attr('id'))
		modal.fadeOut();
		$('body').css('overflow','auto');
		history.pushState(null, null, window.location.hash+'?');
	})
	$(document).on('keydown', function(event) {
		if (event.key == "Escape") {
			window.location.href = '#event';
		}
	});
	$('[id^=dal]').on('click',function(e){
		e.preventDefault();
		var modal = $('#myMo' + $(this).attr('id'))
		modal.fadeOut();
		$('body').css('overflow','auto');
		history.pushState(null, null, window.location.hash+'?');
	})
	$('.switchDay').on('click',function(){
		var id = $(this).attr('data-id');
		$.ajax({
			type: "POST",
			url: "/"+id,
			cache: false,

			success: function(data){
				$(window).html(data);
			}
		});
	});
	$('[id^=close_participant_]').on('click',function(e){
		e.preventDefault();
		var modal = $('#myModal_participant_' + $(this).attr('data-id'))
		modal.fadeOut();
		$('body').css('overflow','auto');
		history.pushState(null, null, window.location.hash+'?');
	})
	;
	$('[id^=close_joblink_]').on('click',function(e){
		e.preventDefault();
		var modal = $('#myModal_joblink_' + $(this).attr('data-id'))
		modal.fadeOut();
		$('body').css('overflow','auto');
		history.pushState(null, null, window.location.hash+'?');
	})
	;
	$('[id^=close_agenda_]').on('click',function(e){
		e.preventDefault();
		var modal = $('#myModal_agenda_' + $(this).attr('data-id'))
		modal.fadeOut();
		$('body').css('overflow','auto');
		history.pushState(null, null, window.location.hash+'?');
	})
	;

//////////////////////////////////////////////////////////////
//////////////////// btn to the top ///////////////////////////
///////////////////////////////////////////////////////////////
	$('#btn_top').on('click',function(){
		$('html,body').animate({ scrollTop: 0 }, 'fast');
		return false;
	})

	$(window).on('scroll', function (){
		if ($(window).scrollTop() > 100){
			$('#btn_top').show();
		}
		else{
			$('#btn_top').hide();
		}
	})

//////////////////////////////////////////////////////////////
//////////////////// hide or show ////////////////////////////
//////////////////////////////////////////////////////////////
	function hideOrShow(element,duration){

		if(duration == undefined){
			duration = 0;
		}

		if (element.is(':visible')){
			element.hide(duration);
		}
		else{
			element.show(duration);
		}
	}

//////////////////////////////////////////////////////////////
////////////////////////// menu //////////////////////////////
//////////////////////////////////////////////////////////////
	function hideMenuModale(element){
		element.slideUp();
		$('#menu_fix').find('.menu-bar').show();
		$('#menu_fix').find('.fa-close').hide();
		$('body').css('overflow','auto');
		$('#btn_top').show();

	}

	$('#menu_fix').on('click',function(){
		var element = $('.menu_item_container');

		if (element.is(':visible')){
			hideMenuModale(element);
		}
		else{
			//history.pushState(null, null, window.location.hash);
			element.slideDown();
			$(this).find('.menu-bar').hide();
			$(this).find('.fa-close').show();
			$('body').css('overflow','hidden');
			$('#btn_top').hide();
		}
	})
//////////////////////////////////////////////////////////////
///////////////////////// menus //////////////////////////////
//////////////////////////////////////////////////////////////
	$('.item').on('click',function(e){
		//e.preventDefault();

		var id = $(this).attr('data-scroll');
		hideMenuModale($('.menu_item_container'));
		var scroll = $(id).offset().top - 50;
		$('body,html').animate({
			scrollTop:  scroll
		});

	})

//////////////////////////////////////////////////////////////
///////////////////////// history ////////////////////////////
//////////////////////////////////////////////////////////////
	window.addEventListener('popstate', function(e) {

		var is_visible = false;
		$.each($('[id^=myModal'),function(){
			if ($(this).css('display') != 'none' ){
				is_visible=true;
			}
		})
		if (is_visible){
			$('[id^=myModal').fadeOut();
			$('body').css('overflow','auto');

		}
		if ($('.menu_item').is(':visible')){
			hideMenuModale($('.menu_item_container'));
		}
		if (is_visible || $('.menu_item').is(':visible')){
			if ($(window.location.hash).length > 0){
				$('body,html').animate({
					scrollTop: $(window.location.hash).offset().top - 50
				});
			}
		}
	});

//////////////////////////////////////////////////////////////
/////////////// scrollto anchor and error ////////////////////
//////////////////////////////////////////////////////////////
	if ($('#contact_send').length > 0){
		$('html').scrollTop($('#contact_send').offset().top - 50);
	}
	else if ($('#registration_success').length > 0){
		$('html').scrollTop($('#registration_success').offset().top - 130);
	}
	else if($('.invalid-feedback').length > 0){
		$('html').scrollTop($('.invalid-feedback').first().offset().top - 50);
	}
	else if($(location.hash).length > 0){
		$('html').scrollTop($(location.hash).first().offset().top - 50);
	}

//////////////////////////////////////////////////////////////
///////////////////////// map ////////////////////////////////
//////////////////////////////////////////////////////////////
	$('[data-toggle=popover]').popover({
		html: true,
		placement : "top",
		trigger : "manual",
		container: "body"
	})

	var current = null;
	var t;

	function setUpCoordinates(stand){
		var popover = $('.popover').last();
		var position = stand.offset();

		if (position != undefined){
			var pop_length = popover.width();
			var pop_height = popover.height();
			var margin_left = 8;
			var left = position.left - (pop_length/2) + margin_left;

			margin_top = 10;
			var top = position.top - (pop_height) + margin_top;

			popover.css('left',left);
			popover.css('top',top);
		}
	}

	function showPop(g, c_class){
		$('#pop_'+ c_class).popover('show');
		setUpCoordinates(g);
		$('.' + c_class).next().css('fill','rgb(180, 5, 4)');
	}


	$('[id^=c_]').on("mouseenter",function() {
		var c_class = $(this).attr('id');

		if (current != c_class){
			$.each($('[data-toggle=popover]'),function(){
				if ($(this).attr('id') != 'pop_' + c_class){
					$(this).popover('hide');
				}
			})
			$.each($('[class^=c_]'),function(){
				if ($(this).attr('id') !=  c_class){
					$(this).next().css('fill','#818181');
				}
			})
			showPop($(this),c_class);
		}
		else{
			if ($('#opened_' + c_class).is(':visible')){
				clearTimeout(t);
			}
			else{
				showPop($(this),c_class);
			}
		}
	});

//debug
//showPop($('#c_lomme'),$('#c_lomme').attr('id'));
//
	$('[id^=c_]').on("mouseleave",function() {
		current = $(this).attr('id');
		var pop = $('#pop_' + current);

		t = setTimeout(function(){
			pop.popover('hide');
			$('.' + current ).next().css('fill','#818181');
		}, 500);
	});

	$('[id^=c_]').on("click",function() {
		var a = $('#opened_' + $(this).attr('id')).find('a');

		if (a.attr('href') != undefined){
			window.open(a.attr('href'));
		}

	});

	$("body").delegate( ".popover-content", "mouseenter", function() {
		clearTimeout(t);
	});

	$("body").delegate( ".popover-content", "mouseleave", function() {
		var id = $(this).find('[id^=opened]').attr('id');
		id = id.substr(7, id.length);

		t = setTimeout(function(){
			$('#pop_' + id).popover('hide');
			$('.' + id ).next().css('fill','#818181');
		}, 500);
	});

	$('.point').mouseenter(function(){
		clearTimeout(t);
	});

	$('.st3').mouseenter(function(){
		clearTimeout(t);
	});


//////////////////////////////////////////////////////////////
//////////////////// google map //////////////////////////////
//////////////////////////////////////////////////////////////

	if ($('#google_map').length > 0 ){
		if($('.google_map_dev').length > 0){
			$.ajax({
				url : "https://maps.googleapis.com/maps/api/js?key=AIzaSyDctOe-bfGxpLq5r1lKJyd7B6ibDeuO7mU&callback=initMap",
				dataType: "script",
				async:true,
				defer:true
			});
		}
		else{
			$.ajax({
				url : "https://maps.googleapis.com/maps/api/js?key=AIzaSyCmh6-8Az2p3DH777jNOYWyTnDgtqujNoY&callback=initMap",
				dataType: "script",
				async:true,
				defer:true
			});
		}

	}

//////////////////////////////////////////////////////////////
//////////////////// registration ////////////////////////////
//////////////////////////////////////////////////////////////
	function showOrHide(element){
		if (element.is(':visible')){
			element.hide();
		}
		else{
			element.show();
		}
	}
	$(document).on('click',".field_title.job", function(){
		if($(this).next().is(':visible')){
			console.log('visible');
			$(this).next().hide();
			$.each($(this).children(), function(){
				$(this).removeClass('fa-minus');
				$(this).addClass('fa-plus');


			})
		}
		else{
			console.log('not visible');
			$(this).next().show();
			$.each($(this).children(), function(){
				$(this).removeClass('fa-plus');
				$(this).addClass('fa-minus');
			})
		}
	})


	if ($("#mandatory_registration_banner").length > 0){
		$("#mandatory_registration_banner").on('click',function(){
			$('html,body').animate({ scrollTop: $("#registration").offset().top - 50 }, 'fast');
			//close all modale
			$('[id^=myModal]').css('display','none');
			$('body').css('overflow','auto');
		})

		function showRegistrationBanner(){
			if (checkVisible($('#registration')) || checkVisible($('#l4m_header')) ){
				$('#mandatory_registration_banner').hide();
			}
			else{
				$('#mandatory_registration_banner').show();
			}

			if (checkVisible($('footer'))){
				$('#mandatory_registration_banner').css('bottom', $('footer').innerHeight());
			}
			else{
				$('#mandatory_registration_banner').css('bottom', 0);
			}
		}

		showRegistrationBanner();

		$(window).on('scroll', function (){
			showRegistrationBanner();
		})
	}
// $('#register_actionation').on('click', function(){
// 	alert('here');
// })
//captcha trigger before this
//
//////////////////////////////////////////////////////////////
////////// registration / edit profile ///////////////////////
//////////////////////////////////////////////////////////////
	if ($('#registration_city_name').length > 0){

		function displayCities(val,input){
			if(val.length > 1){
				$.ajax({
					url: "/autocomplete",
					data: {data : val},
					success: function(data){
						$('#registration_city_name').before('<div id="search_results"></div>')
						var $result = $('#search_results');

						$.each(data.cities, function(i,v){
							var name = v.cp + " " +v.name;

							if(v.country != 'France'){
								name = name + " (" + v.country +")";
							}
							$result.append("<div class='city_item' data-id='"+v.id+"'>" + name + "</div>");
						});
					}
				});
			}
		}

		window.setInterval(function(){
			var input = $('#registration_city_name') ;

			if (input.is(':focus')){
				//to do detect return and send cache reset
				val = $('#registration_city_name').val();

				if (sessionStorage.city_search){
					if (sessionStorage.city_search != val){
						sessionStorage.city_search = val;
						$('#search_results').remove();
						displayCities(val,input);
					}
				}
				else{
					sessionStorage.city_search = val;
					displayCities(val,input);
				}
			}
			else if (input.val() != "" && $('#registration_city_id').val() == ""){
				//this should not happend if autocomplete is disabled
				input.val("");
			}
		},500);

		$('form[name="registration"]').on("click", 'div.city_item',function(){

			$('#search_results').remove();
			$('#registration_city_name').val($(this).html());
			$('#registration_city_id').val($(this).attr('data-id'));
		});
		$(document).on('click', '.focus-select', function(){
			$(this).select();
		});

	}
/////////////////////////////////////////////////////////////
////////////// check email registration_form /////////////////
//////////////////////////////////////////////////////////////

	if ($('#registration_email').length > 0 ){
		$('#registration_email').on('keyup', function(){
			if($('#registration_email').val().length > 1){
				$.ajax({
					url: "/reset-password/check-email",
					data: {email : $(this).val()},
					method: "POST",
					success: function(data){
						if($('#incorrect_email').length == 0 && data.exists && data.message != 'adm'){
							$('#registration_email').before(
								'<div id="incorrect_email"> Vous avez déjà crée un compte avec l\'adresse ' +
								$('#registration_email').val()+'</div><a href="/espace-candidat" id="incorrect_email_link"> Connectez-vous pour vous inscrire</a>'
							);
						}
						else if($('#incorrect_email').length > 0 && !data.exists){
							$('#incorrect_email').remove();
							$('#incorrect_email_link').remove();
						}
					}
				});
			}
		})
	}
//////////////////////////////////////////////////////////
////////////// hide / show upload cv /////////////////////
//////////////////////////////////////////////////////////
	if($('#cv_saved').length > 0){
		$('#show_cv').on('click', function(e){
			e.preventDefault();
			$('#hide_this').show();
		})
	}

//////////////////////////////////////////////////////////
//////////////////////// close app_ad /////////////////////
//////////////////////////////////////////////////////////
	function getCookie(name) {
		let matches = document.cookie.match(new RegExp(
			"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
		));
		return matches ? decodeURIComponent(matches[1]) : undefined;
	}

	if($('#close_app_ad').length > 0 /*&& $('#accueil').length == 0*/){
		$('#close_app_ad').on('click', function(){
			$('header').css('margin-top','0')	;
			$('#appli_ad').remove();
			document.cookie = 'displayAd=yes; path=/; max-age=3600;';
		})
	}

	if(getCookie("displayAd")){
		$('header').css('margin-top','0')	;
		$('#appli_ad').css('display','none');
	}
});
$( ".banniereEvent" ).mouseover(function() {
	var color = $(this).attr('color');
	$(this).children('.backgroundPara').css('box-shadow','0px 0px 20px'+color)
});
$(".banniereEvent").mouseout(function() {
	$(this).children('.backgroundPara').css('box-shadow','none')
});
$('#flash_notice').click(function(){
	console.log('oui');
	$('#flash_notice').css('display','none');
})
$('#toTopExposant').on('click',function(){
	$('html,body').animate({ scrollTop: $("#save").offset().top - 25 }, 'fast');
});