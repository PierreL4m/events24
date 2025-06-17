var $ = require('jquery');
require('jquery-ui/ui/widgets/datepicker.js');
require('jquery-ui/ui/i18n/datepicker-fr.js');
require('bootstrap-less');
// $.getScript("/build/js/trainee_form.js");
var tinymce = require('tinymce/tinymce');
// A theme is also required
require('tinymce/themes/modern/theme');
require('tinymce/plugins/paste');
require('tinymce/plugins/link');
require('tinymce/plugins/lists');

var invalid_image_format = 'Les formats acceptés sont jpg, png, gif, eps, psd, pdf ou ai. Merci de réesayer avec un fichier valide.';
var image_format_type = ['jpg', 'png','gif', 'eps', 'psd', 'ai', 'pdf'] ;

function checkImageFormat(input){
  var file = input[0].files[0];
  var name = file.name;
  var ok = false ;

  $.each(image_format_type,function(i,v){
    if (name.endsWith(v)){
      ok = true;
    }
  })
  if(!ok){
    alert(invalid_image_format);
    input.val("");
  }
}

function urlParams(name){
  var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);

  if (!results){
    return null;
  }
  var exists = "1" in results ;
  if (!exists ){
    return null;
  }

  return results[1] || 0;
}

$(document).ready(function() {



  $('#sendRecallSubscribe').click(function(){
    var id = $(this).attr("value");
    $.ajax({
      type : 'POST',
      url: "/admin/section/subscribePublic/"+id,
      success: function(){
        location.reload();
    }
  });
  })

$('#participation_load').click(function(){
  var id = $('#load_participation_participation').val();
  $.ajax({
    type : 'POST',
    url: "/admin/participation/edit_form/"+id,
    success: function(data){
      $('#form_edit').html(data);
  }
});
})
  $('#sortable').mouseleave(function(){
    $('.ui-sortable-handle').each(function(){
    var event = $(this).attr('event');
    var id = $(this).attr('value');
    var sectionPosition = this.rowIndex;
    $.ajax({
      type : 'POST',
      url: "/admin/section/reorderSection/"+event+"/"+id+"_"+sectionPosition,
      success: function(){
    }
  });

    })
  })
  $('.searchCandidat').click(function() {
    var mail = $('#searchCandidatTxt').val();
    $.ajax({
      type : 'POST',
      url: "/admin-recruteurs/infosByMail/"+mail,
      success: function(data){
        var numbersArray = data.split(',');
        $.each(numbersArray, function(index, value) {
          console.log(index + ': ' + value);
          $('#registration_lastname').val(numbersArray[1]);
          $('#registration_firstname').val(numbersArray[0]);
          $('#registration_email').val(numbersArray[2]);
          $('#registration_phone').val(numbersArray[3]);
        });
    }
  });
  })

  $('.validPrintBadge').click(function() {
    var id = $(this).attr('participation');
    var nbBadges = $(this).prev('.nbBadgesTxt').val();
    $(location).attr('href','/admin/tech-file/print-one-badge/'+id+'_'+nbBadges);
  })

  $('#printSelectedBadges').click(function(){
    var participationArray = [];
    var nbBadges = [];
    var event = $(this).attr('event');
    $(".checkBadge").each(function(){
      if($(this).is(":checked")){
        participationArray.push($(this).attr('participation'));
        nbBadges.push($(this).next('.nbBadgesTxt').val());
      }
    });
    $(location).attr('href','/admin/tech-file/print-selected-badges/'+event+'_'+participationArray+'_'+nbBadges);
  })

  $('#sendSelectedRs').click(function(){
    var participationArray = [];
    var event = $(this).attr('event');
    $(".checkBadge").each(function(){
      if($(this).is(":checked")){
        participationArray.push($(this).attr('participation'));
      }
    });
    if(participationArray.length > 0){
      alert('L\'envoie des visuels s\'est correctement passé !');
      $(location).attr('href','/admin/tech-file/send-network-pub/'+event+'_'+participationArray);
    }else{
      alert('Merci de cocher au moins une entreprise pour l\'envoie ou cliquer sur "Tout cocher" pour envoyer les visuels à l\'ensemble des exposants');
    }
  })

  $('#checkAll').click(function(){
    $(':checkbox.checkBadge').prop('checked', true);
  })
  $('#unCheckAll').click(function(){
    $(':checkbox.checkBadge').prop('checked', false);
  })
  if (document.getElementById("fos_user_resetting_form_plainPassword_first")) {
    var myInput = document.getElementById("fos_user_resetting_form_plainPassword_first");
    var letter = document.getElementById("letter");
    var capital = document.getElementById("capital");
    var number = document.getElementById("number");
    var length = document.getElementById("length");

    // When the user clicks on the password field, show the message box
    myInput.onfocus = function() {
      document.getElementById("message").style.display = "block";
    }

    // When the user clicks outside of the password field, hide the message box
    myInput.onblur = function() {
      document.getElementById("message").style.display = "none";
    }

    // When the user starts to type something inside the password field
    myInput.onkeyup = function() {
      // Validate lowercase letters
      var lowerCaseLetters = /[a-z]/g;
      if(myInput.value.match(lowerCaseLetters)) {
        letter.classList.remove("invalid");
        letter.classList.add("valid");
      } else {
        letter.classList.remove("valid");
        letter.classList.add("invalid");
      }

      // Validate capital letters
      var upperCaseLetters = /[A-Z]/g;
      if(myInput.value.match(upperCaseLetters)) {
        capital.classList.remove("invalid");
        capital.classList.add("valid");
      } else {
        capital.classList.remove("valid");
        capital.classList.add("invalid");
      }

      // Validate numbers
      var numbers = /[0-9]/g;
      if(myInput.value.match(numbers)) {
        number.classList.remove("invalid");
        number.classList.add("valid");
      } else {
        number.classList.remove("valid");
        number.classList.add("invalid");
      }

      // Validate length
      if(myInput.value.length >= 8) {
        length.classList.remove("invalid");
        length.classList.add("valid");
      } else {
        length.classList.remove("valid");
        length.classList.add("invalid");
      }
    }
  }

  $('.displayAll').click(function(){
    window.location.href = window.location.href;
  });

  //////////////////////////////////////////////////////////////
  /////////////// common functions //////////////////////////////
  //////////////////////////////////////////////////////////////
  function showOrHide(element){
    if (element.is(':visible')){
      element.hide();
    }
    else{
      element.show();
    }
  }

  var pacman = "<img src='/images/pacman.gif' alt='chargement' class='pacman'>";
  var loader = '<div id="cooking_container"><h1 class="loader">Cooking in progress..<br>Merci de patienter</h1><div id="cooking"><div class="bubble"></div><div class="bubble"></div><div class="bubble"></div><div class="bubble"></div><div class="bubble"></div><div id="area"><div id="sides"><div id="pan"></div><div id="handle"></div></div><div id="pancake"><div id="pastry"></div></div></div></div></div>';
  var public_loader = "<img src='/images/loader.gif' alt='chargement' class='loader'>";

  function disableClick(){
    $("a").click(function(e) {
      e.preventDefault();
    });
    $("button").click(function(e) {
      e.preventDefault();
    });
  }

  function showPacman(element, no_disable){
    element.on('click', function(){
      if($('input:invalid').length == 0 || element.hasClass('append_loader')){
        if (no_disable != undefined){
          disableClick();
        }
        $(this).after(pacman);
        $(this).hide();
      }
    });
  }

  function showLoader(element, no_disable){
    element.on('click', function(){
      if($('input:invalid').length == 0 || element.hasClass('append_loader_public')){
        if (no_disable != undefined){
          disableClick();
        }
        $(this).after(public_loader);
        $(this).hide();
      }
    });
  }

  function showCooking(element, no_disable){
    element.on('click', function(e){
      if($('input:invalid').length == 0 ){
        //e.preventDefault();
        if (no_disable != undefined){
          disableClick();
        }
        $('body').prepend(loader);
        $('html,body').animate({ scrollTop: 0 }, 'fast');
      }
    });
  }

  if($('#menu_admin').length > 0){
    // showPacman($('submit'));
    // showPacman($('[id~=save]'));
    showCooking($('submit'));
    showCooking($('[id~=save]'));

  }

  showPacman($('.append_loader'));
  showLoader($('.append_loader_public'));
  //////////////////////////////////////////////////////////////
  ////////////////////// tiny mce //////////////////////////////
  //////////////////////////////////////////////////////////////
  tinyMCE.baseURL = "../../../tinymce" ;
  //to do fix this base url should be unique
  var selectors = [
    '#place_description',
    '#participation_presentation',
    '#participation_info',
    '#participation_description',
    '#section_description',
    '#job_presentation'
  ];
  $.each(selectors, function(i,v){
    tinymce.init({
      selector: v,
      plugins: ['paste', 'link', 'lists'],
      convert_urls: false,
      // relatives_urls : false,
      // remove_script_host: false,
      paste_as_text: true,
      setup: function(editor) {
        editor.on('keyup', function(e) {
          var content = tinyMCE.activeEditor.getContent( { format : 'html' } );
          var p = content.split('<p>').length;
          console.log(tinyMCE.activeEditor.getContainer().id);
          if( p <= 10){
            $('#'+tinyMCE.activeEditor.getContainer().id).css('border','solid green 2px');
            $("div[quality]").attr('class','optimalContent');
            $("div[quality]").html('<p>Le contenu proposé permet un affichage optimal au public</p>');
          }else if (p <= 15) {
            $('#'+tinyMCE.activeEditor.getContainer().id).css('border','solid orange 2px');
            $("div[quality]").attr('class','correctContent');
            $("div[quality]").html('<p>Le contenu proposé permet un affichage correct au public</p>');
          }else{
            $('#'+tinyMCE.activeEditor.getContainer().id).css('border','solid red 2px');
            $("div[quality]").attr('class','minimalContent');
            $("div[quality]").html('<p>Le contenu proposé cause un affichage minimal au public</p>');
          }
        });
      }
    });
  })
  // to do fix this
  // var full = location.pathname;
  // var path = full.substr(full.lastIndexOf("/") + 1);
  //

  tinyMCE.baseURL = "../../../../tinymce" ;
  var selectors = [
    '#participant_section_description',
    '#mail_body',
    '#agenda_description',
    '#joblink_description'
  ];
  $.each(selectors, function(i,v){
    tinymce.init({
      selector: v,
      plugins: ['paste', 'link', 'lists'],
      // relatives_urls : false,
      // remove_script_host: false,
      paste_as_text: true,
      convert_urls: false
    });
  })

  //////////////////////////////////////////////////////////////
  //////////////////// google map //////////////////////////////
  //////////////////////////////////////////////////////////////

  if ($('#google_map').length > 0){
    $.ajax({
      url : "https://maps.googleapis.com/maps/api/js?key=AIzaSyDyEOl1mHUxCsZ9glafEPYTWj9Mua1YX9U&callback=initMap",
      dataType: "script",
      async:true,
      defer:true
    });
  }

  //////////////////////////////////////////////////////////////
  ///////////////// confirmation modale ////////////////////////
  //////////////////////////////////////////////////////////////
  var confirmation_modal = '<div id="confirmation_modal">'+
  ' <div class="flex_container">'+
  '<!-- Modal content-->'+
  '<div class="modal_content">'+
  '<p class="data_msg">Etes vous-sur de vouloir </p>'+
  '<div class="btn_container">'+
  '<button id="modal_confirmed" type="submit" class="btn btn-success"><i class="fa fa-check"></i> Oui</button>'+
  '<button type="button" id="modal_cancel" class="btn btn-danger"><i class="fa fa-remove"></i> Non</button>'+
  '</div>'+
  '</div>'+
  '</div>'+
  '</div>';
  ///////////////////confirmation modale //////////////////////////
  $(".a_confirm").on('click', function(e){
    e.preventDefault();
    $(this).after(confirmation_modal);


    if($(this).data('warning')){
      $('.data_msg').before($(this).data('warning'));
      $('.data_msg').append('pour les deux jours ?');
    }
    else if($(this).data('no-text')){
      $('.data_msg').html($(this).data('msg'));
    }
    else{
      $('.data_msg').append($(this).data('msg'));
    }

    if ($(this).hasClass('is_form')){
      var is_form = true;
    }
    else{
      var is_form = false;
    }
    var href = $(this).attr('href');

    $('#modal_confirmed').on('click', function(){
      $.ajax({
        type : 'GET',
        url: href,
        success: function(){
          location.reload();
        }
      });
    })
    $('#modal_cancel').on('click', function(){
      $('#confirmation_modal').remove();
      $('.pacman').remove();
      $('.a_confirm').show();
    })
  });
  //////////////////////////////////////////////////////////////
  ///////////////// close flash message ////////////////////////
  //////////////////////////////////////////////////////////////
  $('#close_flash').on('click', function(e){
    $('#flash_notice').fadeOut();
  });


  function getMaxJob(id) {
    $.ajax({

      url: '/admin-exposant/ajax-get-max-jobs',
      type: 'POST',
      data: {'id' : id},
      cache: false,
      async: false,

      success: function(data){
        max_jobs = data.max;
      }
    });
  }

  /////////////////////handle embedded form//////////////////////////

  function handleEmbeddedForm(id, addFirst, deleteFirst,has_max_job){

    if (addFirst == undefined){
      addFirst = false;
    }

    if (has_max_job == undefined){
      max_jobs=false;
    }


    var collectionId = $('#' + id );
    var collectionContainer = '#' + id + '_' ;
    var $addUrlLink = $('<a href="#" class="btn-custom"><span class="glyphicon glyphicon-plus"> </span> </a>');
    var $newLinkLi = collectionId.append($addUrlLink);

    // Get the div that holds the collection of tags
    var $collectionHolder = collectionId;
    var i=0;

    $collectionHolder.find("[id^="+ id + "_]").each(function() {

      if(i==0 && (deleteFirst == undefined || !deleteFirst)){
        addSiteFormDeleteLink($(collectionContainer + i),i);
      }
      else if(i>0){
        addSiteFormDeleteLink($(collectionContainer + i),i);
      }
      i++;
    });
    $collectionHolder.append($newLinkLi);
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    // on plus click
    $addUrlLink.on('click', function(e) {
      e.preventDefault();
      var ok = true;

      if ($.isNumeric(max_jobs)){

        if($("[id^="+ id + "_]").length == max_jobs * 4){ //4 fields
          var ok = false;
        }
      }

      if(ok) {
        addUrlForm($collectionHolder, $newLinkLi);
      }
    });

    if (addFirst && $("[id^="+ id + "_]").length == 0){
      addUrlForm($collectionHolder, $newLinkLi);
    }

    function addUrlForm($collectionHolder, $newLinkLi) {
      var prototype = $collectionHolder.data('prototype');
      var index = $collectionHolder.data('index');
      var newForm = prototype.replace(/__name__/g, index);
      $collectionHolder.data('index', index + 1);




      var $newFormLi = collectionId.append(newForm);
      $newLinkLi.before($newFormLi);

      console.log($("[id^="+ id + "_]").length);
      if ($.isNumeric(max_jobs) && ($("[id^="+ id + "_]").length == max_jobs * 4 )
    ){ //4 fields
      return;
    }
    collectionId.append($addUrlLink);

    addSiteFormDeleteLink($(collectionContainer + index),index);


  }


  function addSiteFormDeleteLink($siteFormLi,index) {
    var $removeFormA = $("<a href='#' class='btn-delete'><span class='glyphicon glyphicon-remove'></span></a>");
    $siteFormLi.append($removeFormA);
    $removeFormA.on('click', function(e) {
      e.preventDefault();
      $(collectionContainer + index).parent().remove();
    });
  }

}


/////////////////////end handle embedded form///////////////////////////
if ($('#place_colors').length > 0){
  handleEmbeddedForm('place_colors',false,true);

  // $('#save').on('click',function(){
  //   $('[id=^place_colors_').each(function(){
  //     var id = $(this).attr('id');
  //     if ($(this)).find(id+_name)
  //   })
  // })
}

if ($('#user_responsableBises').length > 0){
  handleEmbeddedForm('user_responsableBises');
}

if ($('#participation_sites').length > 0){
  handleEmbeddedForm('participation_sites',true,true);
}

if ($('#mail_extra_mails').length > 0){
  handleEmbeddedForm('mail_extra_mails');
}

if ($('#participation_jobs').length > 0){
  getMaxJob($('#participation_id').attr('data-id'));
  handleEmbeddedForm('participation_jobs',true,true,true);
}

if ($('#participation_id').length > 0 && $('#participation_maxJobs').length > 0 ) {
  $('#participation_maxJobs').on('focusout', function () {
    var max = $('#participation_maxJobs').val();

    if (max && $.isNumeric(max)) {

      var max = $(this).val();
      $.ajax({
        type: "POST",
        url: "/admin/participation/increment-max-jobs",
        data: {max: max, 'id': $('#participation_id').attr('data-id')},
        cache: false,
      });
    } else {
      $('#participation_maxJobs').after("<p class='text-danger'>Merci de saisir un nombre");
    }

  });
}
//
// //////////////////////////////////////////////////////////////
// ///////////////////// go to top   ////////////////////////////
// //////////////////////////////////////////////////////////////
//
// $('body').delegate('#btn_top','click',function(){
//   $('html,body').animate({ scrollTop: 0 }, 'fast');
// })
//
// $(window).on('scroll', function (){
//   if ($(window).scrollTop() > 100 ){
//     if($('#btn_top').length == 0){
//       $('header').after('<span id="btn_top"><i class="fa fa-arrow-circle-up" aria-hidden="true"></i></span>');
//     }
//   }
//   else if ($('#btn_top').length > 0){
//     $('#btn_top').remove();
//   }
// })
// //////////////////////////////////////////////////////////////
// ////////////////// go to bottom   ////////////////////////////
// //////////////////////////////////////////////////////////////
//
// if(screen.height < $('body').outerHeight()) {
//   $('header').after('<span id="btn_bottom"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></span>')
// }
// $('#btn_bottom').on('click',function(){
//   $('html,body').animate({ scrollTop: $(document).height()-$(window).height() }, 'fast');
// })
//////////////////////////////////////////////////////////////
////////////// event autofill dates //////////////////////////
//////////////////////////////////////////////////////////////
if ($('#event_date').length > 0){
  function fillDate(date,element){
    $(element+'_day').val(date.getDate());
    $(element+'_month').val(date.getMonth() + 1);
    $(element+'_year').val(date.getFullYear());
  }

  $('#complete').on('click',function(e){

    e.preventDefault();
    var day = $('#event_date_date_day').val();
    var month = $('#event_date_date_month').val();
    var year = $('#event_date_date_year').val();

    var online_date = new Date(year,month -1 ,day);
    var offline_date = new Date(year,month -1 ,day);
    var first_recall = new Date(year,month -1 ,day);
    var second_recall = new Date(year,month -1 ,day);

    online_date.setDate(online_date.getDate()-31);
    fillDate(online_date,'#event_online_date');

    offline_date.setDate(offline_date.getDate()+1);
    fillDate(offline_date,'#event_offline_date');

    first_recall.setDate(first_recall.getDate()-51);
    fillDate(first_recall,'#event_firstRecallDate');

    second_recall.setDate(second_recall.getDate()-38);
    fillDate(second_recall,'#event_secondRecallDate');

  })
  $('#break').on('click',function(){
    showOrHide($('.break_fields'));
  })

  $('#firstRecall').on('click',function(){
    showOrHide($('#firstRecallContainer'));
  })

  $('#secondRecall').on('click',function(){
    showOrHide($('#secondRecallContainer'));
  })
}
//////////////////////////////////////////////////////////////
////////////// event check image size ////////////////////////
//////////////////////////////////////////////////////////////
$('#event_banner_file').on('change',function(){
  var file = $(this)[0].files[0];

  if (file.size >= 280000000){
    alert('La taille de votre fichier ne doit pas excéder 280M. Merci de réesayer avec un fichier plus petit.');
    $(this).val("");
  }
  else{
    checkImageFormat($(this));
  }
})
$('#event_socialLogo_file').on('change',function(){
  var file = $(this)[0].files[0];

  if (file.size >= 8000000){
    alert('La taille du logo partage sur facebook ne doit pas excéder 8M. Merci de réesayer avec un fichier plus petit.');
    $(this).val("");
  }
  else{

    if(!file.name.endsWith(jpg)){
      alert('Merci de choisir une image au format jpg');
      input.val("");
    }
  }
})

$('#event_invitation_file').on('change',function(){
  var file = $(this)[0].files[0];

  if (file.size >= 20000000){
    alert('La taille de l\'invitation ne doit pas excéder 2M. Merci de réesayer avec un fichier plus petit.');
    $(this).val("");
  }
  else{
    checkImageFormat($(this));
  }
})
//////////////////////handle image preview//////////////////////////////
function handleFileSelect(evt) {
  var files = evt.target.files; // FileList object
  // Loop through the FileList and render image files as thumbnails.
  for (var i = 0; f = files[i]; i++) {
    // Only process image files.
    if (!f.type.match('image.*')) {
      continue;
    }

    var reader = new FileReader();
    // Closure to capture the file information.
    reader.onload = (function(theFile) {
      return function(e) {
        // Render thumbnail instead of logo actuel.
        $('#participation_logo').append(['<img src="', e.target.result,
        '" title="', escape(theFile.name), '"/>'].join(''));

        //$('.participation_logo').parent().prev().html('Logo à charger');
      };
    })(f);

    // Read in the image file as a data URL.
    reader.readAsDataURL(f);

  }
}
/////////////////////end handle image preview///////////////////////////////

//////////////////////////////////////////////////////////////
//////////////////// event show //////////////////////////////
//////////////////////////////////////////////////////////////
$('#invitation_model').on('click',function(){
  if (!$('#invitation_model').hasClass('zoom')){
    $('#invitation_model').addClass('zoom');
    $('#invitation_container').addClass('zoom_container');
    // $('#invitation_model').css('');
  }
  else{
    $('#invitation_model').removeClass('zoom');
    $('#invitation_container').removeClass('zoom_container');
  }
})

if (typeof(anchor) != 'undefined'){
  if (anchor){
    $('html, body').animate({
      scrollTop: $("#"+anchor).offset().top - 30
    }, 1);
  }
}

function resetLastLogin(element){
  element.after(pacman);
  element.remove();
  id = element.attr('data-id')

  $.ajax({
    type: "POST",
    url: "/admin/user/reset-login",
    data: {id : id},
    cache: false,

    success: function(data){
      $('.pacman').remove();
      $('#'+data['id']+'login').html('Jamais connecté');
    }
  });
}
$('.reset_last_login').on('click',function(){
  resetLastLogin($(this));
})

function resetLastLogin(element){
  element.after(pacman);
  element.remove();
  id = element.attr('data-id')

  $.ajax({
    type: "POST",
    url: "/admin/user/reset-login",
    data: {id : id},
    cache: false,

    success: function(data){
      $('.pacman').remove();
      $('#'+data['id']+'login').html('Jamais connecté');
    }
  });
}
$('.reset_last_login').on('click',function(){
  resetLastLogin($(this));
})

function sendFill(element){
  element.after(pacman);
  element.remove();
  id = element.attr('data-id')

  $.ajax({
    type: "POST",
    url: "/admin/participation/send-fill",
    data: {id : id},
    cache: false,

    success: function(data){
      $('.pacman').remove();
      $('#'+data['id']+'sentOn').html('Le mail de notification vient d\'être envoyé');
    }
  });
}

$('.send_fill').on('click',function(){
  sendFill($(this));
})



  $('.reload_scan').on('click',function(){
      var id = $(this).attr('data-id');
    var event = $(this).attr('event-id');
      $.ajax({
        type: "POST",
        url: "/admin/event/reload-scan/"+id+"/"+event,
        cache: false,

        success: function(){
          location.reload();
        }
      });
  });

function redoRecall(element){
  element.after(pacman);
  element.remove();
  id = element.attr('data-id')

  $.ajax({
    type: "POST",
    url: "/admin/participation/redo-recall",
    data: {id : id},
    cache: false,

    success: function(data){
      $('.pacman').remove();
      $('#'+data['id']+'recall').html('La fiche a été remise dans les mails de relance');
    }
  });
}

$('.reset_updated').on('click',function(){
  redoRecall($(this));
})
$('#send_fills').on('click',function(){
  $(this).after(pacman);
})

function sendFillToMe(element){
  element.after(pacman);
  element.hide();
  id = element.attr('data-id')

  $.ajax({
    type: "POST",
    url: "/admin/participation/send-fill-to-me",
    data: {id : id},
    cache: false,

    success: function(data){
      $('.pacman').remove();
      $('#'+data['id']+'sent_to_me').html('Le mail de notification viens d\'être envoyé');
      $('#'+data['id']+'sent_to_me').show();
    }
  });
}
$('.send_fill_to_me').on('click',function(){
  sendFillToMe($(this));
})
//////////////////////////////////////////////////////////////
////////////// participation logo ////////////////////////////
//////////////////////////////////////////////////////////////
$('#participation_logo_file').on('change',function(e){
  checkImageFormat($(this));
  handleFileSelect(e);
})
//////////////////////////////////////////////////////////////
////////////// participation load ////////////////////////////
//////////////////////////////////////////////////////////////
$('#display_participation_load_form').on('click',function(e){
  $('form[name=load_participation').show();
  $(this).hide();
})


//////////////////////////////////////////////////////////////
////////////////////// tech files ////////////////////////////
//////////////////////////////////////////////////////////////
$('#edit_ack').on('click',function(){
  if ($(this).hasClass('btn')){
    $(this).remove();
  }
  else{
    $(this).hide();
  }
  $("#edit_ack_container").show();
});

$('#ack_date_container').delegate('#edit_ack','click', function(){
  $(this).hide();

  $("#edit_ack_container").show();
});

$('#save_ack').on('click',function(){
  var date = $('#date_ack').val();

  if (date){
    var id = $(this).attr('data-id');
    $('#edit_ack_container').after(pacman);
    $('#edit_ack_container').hide();

    $.ajax({
      type: "POST",
      url: "/admin/tech-file/ack",
      data: {id :id, date : date},
      cache: false,

      success: function(data){
        $('.pacman').remove();
        $('#edit_ack').show();
        var date = data['date'];

        if($('#defined_date_ack').length > 0){
          $('#defined_date_ack').html(date);
        }
        else{
          $('#ack_date_container').append('Date de retour de l\'accusé de récéption : <span id="defined_date_ack">' + date + '</span><i class="fa fa-edit" id="edit_ack"></i>');
        }
      }
    });
  }
});
function sendMail(){
  $('.send-mail').on('click',function(e){
    e.preventDefault();
    var id = $(this).attr('data-id');
    var email = $(this).attr('data-email');
    var type = $(this).attr('data-type');

    if (email == undefined){
      email=null;
    }
    $(this).remove();

    $.ajax({
      type: "POST",
      url: '/admin/email/send-files',
      data: {id : id, email : email, type: type},
      cache: false,

      success: function(data){
        $('.pacman').remove();
        var email = data['email'];
        var id = data['id'];
        var ok = '<i class="fa fa-check"></i> Envoyé';

        if (email){
          $('[sent-to-me-id='+id+']').html(ok);
        }
        else{
          $('[sent-id='+id+']').html(ok);
        }
      }
    });
  })
}
if ($('#tech_file_index').length > 0){

  var anchor = urlParams('anchor');

  if(anchor){
    $('html,body').animate({ scrollTop: $("#" + anchor).offset().top - 25 }, 'fast');
  }
  showPacman($('.send-mail',true));
  showPacman($('.send_all_mail',true));
  showPacman($('.generate'));

  sendMail();

  $('.generate').on('click',function(e){
    e.preventDefault();
    var id = $(this).attr('data-id');

    $.ajax({
      type: "POST",
      url: "/admin/tech-file/generate-badge",
      data: {id :id},
      cache: false,

      success: function(data){
        $('.pacman').remove();
        $('.generate').show();
        var id = data['id'];
        var src = data['src'];
        $('[badge-id='+id+']').html("<img src="+src+">");

      }
    });
  })
}
$( "#date_ack" ).datepicker();

$('#ask_edit_ack').on('click',function(){
  $('html,body').animate({ scrollTop: $(".ack").offset().top - 25 }, 'fast');
  $("#edit_ack").click();
});

$('#ask_tech_guide').on('click',function(){
  $('html,body').animate({ scrollTop: $(".tech_guide").offset().top - 25 }, 'fast');
});

//////////////////////////////////////////////////////////////
///////////////////////// bat ////////////////////////////////
//////////////////////////////////////////////////////////////
if ($('#bats').length > 0){
  $( "#date_bat" ).datepicker();
  sendMail();
  showPacman($('.send_all_bat'));

  $('#edit_bat').on('click',function(){
    if ($(this).hasClass('btn')){
      $(this).remove();
    }
    else{
      $(this).hide();
    }
    $("#edit_bat_container").show();
  });

  $('#bat_date_container').delegate('#edit_bat','click', function(){
    $(this).hide();

    $("#edit_bat_container").show();
  });

  $('#save_bat').on('click',function(){
    var date = $('#date_bat').val();

    if (date){
      var id = $(this).attr('data-id');
      $('#edit_bat_container').after(pacman);
      $('#edit_bat_container').hide();

      $.ajax({
        type: "POST",
        url: "/admin/bat/date",
        data: {id :id, date : date},
        cache: false,

        success: function(data){
          $('.pacman').remove();
          $('#edit_bat').show();
          var date = data['date'];

          if($('#defined_date_bat').length > 0){
            $('#defined_date_bat').html(date);
          }
          else{
            $('#bat_date_container').append('Date de retour du BAT : <span id="defined_date_bat">' + date + '</span><i class="fa fa-edit" id="edit_bat"></i>');
          }
        }
      });
    }
  });
  $('#ask_edit_bat').on('click',function(){
    $('html,body').animate({ scrollTop: $(".bat").offset().top - 25 }, 'fast');
    $("#edit_bat").click();
  });
}
//////////////////////////////////////////////////////////////
///////////////////////// bat ////////////////////////////////
//////////////////////////////////////////////////////////////
if ($('#edit_profile').length == 0){
  $('#register_action').on('click', function(e){
    e.preventDefault();
  })
}

$('#contact_action').on('click', function(e){
  e.preventDefault();
})
//////////////////////////////////////////////////////////////
///////////////////////// bilan ////////////////////////////////
//////////////////////////////////////////////////////////////
if ($('#bilan').length > 0){
  $("#notice_mp3").on("click", function(){
    showOrHide($("#notice"));
  })
}
//////////////////////////////////////////////////////////////
////////// menu admin / candidate /exposant  /////////////////
//////////////////////////////////////////////////////////////
if ($('#menu_mobile').length > 0){

  $("#menu_mobile").on("click", function(){
    if(!$('.nav.navbar-nav>li:nth-child(2)').is(':visible')){//first child is menu_mobile
      $('.nav.navbar-nav>li').css('display','block');
    }
    else{
      $('.nav.navbar-nav>li').css('display','none');
      $('#menu_mobile').css('display','block');
    }

  })
}
//////////////////////////////////////////////////////////////
//////////////// edit candidate comment //////////////////////
//////////////////////////////////////////////////////////////
if($('#candidate_profile').length > 0){
  // handle comment
  $('#show_edit').on('click',function(e){
    e.preventDefault();
    $(this).hide();
    $('#comment_loading').show();
    $('#save_comment').show();
    $('#comment').hide();

    if ($('#no_comment').length > 0){
      $('#no_comment').remove();
    }
  });

  var $comment_text = $('#comment_loading').text();

  $("#comment_loading").on("change paste keyup select", function(){
    $comment_text = ($(this).val());
  });

  $('#save_comment, #save_bottom').on('click',function(e){
    if($('#comment_loading').is(':visible')){
      if($comment_text && $comment_text !=""){
        if($(this).attr('id') == 'save_comment'){
          e.preventDefault();
        }

        $('#save_comment').hide();
        $('.simple_loader').show();

        $.ajax({
          type: "POST",
          url: "/admin-exposant/editer-commentaire",
          data: {id :$('#save_comment').attr('data-id'), comment : $comment_text},
          cache: false,

          success: function(comment){
            $('#comment_loading').hide();
            $('#show_edit').show();
            $('#comment').html($comment_text);
            $('#comment').show();
            $('.simple_loader').hide();
          }
        });
      }
      else{
        alert('Merci de saisir un commentaire avant de valider');
      }
    }
  });

  //handle favorites
  $(".raw_likes>div>.fa").on("click", function(){
    var current = $('[class*="current"]'); // the current icon value
    var clicked = $(this);
    var icon_value = clicked.attr('data-value');

    if(current.length > 0){
      var current_child = current.find('.fa');
      var old_icon_value = current_child.attr('data-value');
      var undo = false;

      if(icon_value == old_icon_value){
        undo = true;
        icon_value=0;
      }
    }

    $.ajax({
      type: "POST",
      url: "/admin-exposant/update-icon",
      data: {id :$('#save_comment').attr('data-id'), value : icon_value},
      cache: false,

      success: function(value){
        if(icon_value != value){
          alert('Veuillez nous excusez votre modification n\'a pas été prise en compte');
        }
        else{
          //if there is a like
          if(current.length > 0){
            //handle old current value
            var child = current.find('.fa');
            var child_class = child.attr('class');
            child.removeClass(child_class);

            if(child_class.indexOf('thumbs') !== -1 && child_class.indexOf('thumbs-o') === -1){
              var new_class= child_class.replace("thumbs", "thumbs-o");
            }
            else{
              var new_class= child_class + "-o" ;
            }
            child.addClass(new_class);
            child.parent().removeClass('current');
          }

          //handle new clicked value
          if(!undo){
            var child_class = clicked.attr('class');
            clicked.removeClass(child_class);

            var new_class= child_class.replace("-o", "");
            clicked.addClass(new_class);
            clicked.parent().addClass('current');
          }
          //set right icon to profile content box
          $('#candidate_fa_icon i').removeClass();
          if(!undo){
            $('#candidate_fa_icon i').addClass(new_class);
          }
        }
      }
    });
  })
}
  $('#toTopExposant').on('click',function(){
    $('html,body').animate({ scrollTop: $("#save").offset().top - 25 }, 'fast');
  });
});//end of $(document).ready(function(){});
