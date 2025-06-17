var $ = require('jquery');
$.getScript("http://chancejs.com/chance.min.js");

$(document).ready(function() {
	///////////////////////////////////////////////////////////
	////////////////////// debug //////////////////////////////
	///////////////////////////////////////////////////////////
	
	$('#fill').on('click',function(e){
		e.preventDefault();

        if ($('input[type=text]').length > 0){
            $('input[type=text]').each(function(){

            	if(!$(this).val() || $(this).val()==' ' && $(this).attr('id')){
                    var id = $(this).attr('id').toLowerCase();
                    
                    if(id.search('firstname') != -1){
                        $(this).val(chance.first());
                    }
                    else if(id.search('lastname') != -1 || id.search('last') != -1){
                        $(this).val(chance.last());
                    }
                    else if(id.search('phone') != -1){
                        $(this).val(chance.phone({ country: "fr" }));
                   }                    
                    else{
                        $(this).val(chance.word());
                    }
            	}
            })
        }
        if ($('input[type=password]').length > 0){
            $('input[type=password]').each(function(){
                $(this).val('Test1234');
            });
        }
        if ($('#registration_mailingEvents_0').length > 0){
            $(this).prop("checked", true);
        }
        if ($('#registration_mailingRecall_0').length > 0){
            $(this).prop("checked", true);
        }
        if ($('#registration_phoneRecall_0').length > 0){
            $(this).prop("checked", true);
        }
        if ($('input[type=email]').length > 0){
            $('input[type=email]').each(function(){
            	if(!$(this).val() || $(this).val()==' '){
            		$(this).val(chance.email({domain: 'l4m.fr'}));
            	}
            })
        }
        if ($('textarea').length > 0){
            $('textarea').each(function(){
                if(!$(this).val() || $(this).val()==' '){
                    $(this).val(chance.paragraph({ sentences: 3 }));
                }
            })
        }

        //registration form
        if($('#registration_mailingEvents').length > 0){            
            $('#registration_mailingEvents_0').attr('checked','checked');
        }
        if($('#registration_mailingRecall').length > 0){            
            $('#registration_mailingRecall_0').attr('checked','checked');
        }
        if($('#registration_phoneRecall').length > 0){            
            $('#registration_phoneRecall_1').attr('checked','checked');
        }
        if($('#registration_city_id').length > 0){            
            $('#registration_city_id').attr('value',1);
        }
        
    });
	
});
