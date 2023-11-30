$( document ).ready(function() {
    $('input').attr('autocomplete','off');
});




function checkAuthentication(guard) {

    $( document ).ajaxError(function( event, jqXHR, settings, exception ) {
        if ( jqXHR.status== 401 ) {
            if (jqXHR.status == 401 && getToken(guard) != null && getToken(guard) != 'false') {
                refreshToken(guard);
            } else if (jqXHR.status == 401) {
                //window.location.replace("/"+guard+"/login");
            }
        } else if (jqXHR.status == 403) {
            window.location.replace("/access-denied");
        }
    });
}

function refreshToken(guard) {
    $.ajax({
        url: getBaseUrl() + "/"+guard+"/refresh",
        type: "post",
        headers: {
            Authorization: "Bearer " + getToken(guard)
        },
        success: function(response, textStatus, jqXHR) {
            var data = parseData(response);
            setToken(guard, data.responseData.access_token);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            removeStorage(guard);
            window.location.replace("/"+guard+"/login");
        }
    });
}

function saveRow(url, table, data, guard = "admin", page) { 
   
  $.ajax({
        url: url,
        type: "post",
        data: data,
        processData: false,
        contentType: false,
        headers: {
            Authorization: "Bearer " + getToken(guard)
        },
        beforeSend: function (request) {
            showInlineLoader();
        },
        success: function(response, textStatus, jqXHR) {
            var data = parseData(response);
            if (table != null) {
            var info = $('#data-table').DataTable().page.info();
            table.order([[ 0, 'asc' ]] ).draw( false );
            }

            $(".crud-modal").modal("hide");
            alertMessage("Success", data.message, "success");
            hideInlineLoader();
           
            if(page!=undefined){
                if(page=='/admin/dashboard'){
                    if(data.responseData.length != 0){
                        localStorage.setItem('admin', JSON.stringify(data.responseData));
                    }
                 }
                 if(page=='store/order'){
                    page = '/user/home/';
                 }

                setTimeout(function(){
                    window.location.replace(page);
                  }, 1000);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            
            if (jqXHR.status == 401 && getToken(guard) != null) {
                refreshToken(guard);
            } else if (jqXHR.status == 401) {
                window.location.replace("/admin/login");
            } else if (jqXHR.status == 403) {
                window.location.replace("/access-denied");
            }

            if (jqXHR.responseJSON) {
                if(page=='store/order'){
                    $('.commentLength').html(jqXHR.responseJSON.message);
                    hideInlineLoader();
                    return false;
                }else{
                    alertMessage(textStatus, jqXHR.responseJSON.message, "danger");
                    hideInlineLoader();
                }
            }
            
        }
    });
}

function setData(url, guard = "admin") {

    $.ajax({
        url: url,
        type: "get",
        headers: {
            Authorization: "Bearer " + getToken(guard)
        },
        beforeSend: function (request) {
            showInlineLoader();
        },
        data: {},
        success: function(response, textStatus, jqXHR) {

            var data = parseData(response).responseData;

            $("#password").rules('remove', 'required');
            $("#password_confirmation").rules('remove', 'required');

            if(data.is_featured == 1){
                $(".featured_image").show();
            }
            if(data.expiration && data.expiration_date){ 
                if(data.expiration_date){
                    data.expiration = data.expiration_date;
                }
            }

            if(data.picture){ 
                $('.image-placeholder img').attr('src', data.picture);
            }

            if(data.vehicle_image){
                $('.image-placeholder img').attr('src', data.vehicle_image);
            }

            if(data.vehicle_marker){
                $('.image-placeholder img').attr('src', data.vehicle_marker);
            }

            if(data.icon){
                $('.image-placeholder img').attr('src', data.icon);
            }

            if(data.featured_image){
                $('.image-placeholder img').attr('src', data.featured_image);
            }

            if(data.image){
                $('.image-placeholder img').attr('src', data.image);
            }
                     
            for (var i in Object.keys(data)) {
               
                if (($("[name=" + Object.keys(data)[i] + "]").length)) {
                   
                    var node = $("[name=" + Object.keys(data)[i] + "]").prop(
                        "nodeName"
                    );
                    var type = $("[name=" + Object.keys(data)[i] + "]").attr(
                        "type"
                    );
                    
                    if (
                        (node == "INPUT" && type == "text") ||
                        (node == "INPUT" && type == "email") ||
                        (node == "INPUT" && type == "number") ||
                        (node == "INPUT" && type == "hidden") ||
                        (node == "INPUT" && type == "color") ||
                        (node == "TEXTAREA")
                    ) {

                        $("[name=" + Object.keys(data)[i] + "]").val(
                            Object.values(data)[i]
                        );
                    } else if (node == "INPUT" && type == "radio") {
                      
                        $(
                            "[name=" +
                                Object.keys(data)[i] +
                                "][value=" +
                                Object.values(data)[i] +
                                "]"
                        ).prop("checked", true);
                    } else if (node == "INPUT" && type == "file") {
                        if (
                            Object.values(data)[i] != "" &&
                            Object.values(data)[i] != null
                        ) {
                            $("[name=" + Object.keys(data)[i] + "]")
                                .closest("div")
                                .find("img")
                                .first()
                                .attr("src", Object.values(data)[i])
                                .show();
 
                            $("[name=" + Object.keys(data)[i] + "]").rules('remove', 'required');

                        }
                    } else if(node == "INPUT" && type == "checkbox") {
                        if(Object.values(data)[i] == 1){
                            $("[name=" + Object.keys(data)[i] + "]" ).attr("checked",true);
                        }else{
                            $("[name=" + Object.keys(data)[i] + "]" ).attr("checked",false);
                        }
                        $("[name=" + Object.keys(data)[i] + "]" ).val(Object.values(data)[i]);
                    }
                    
                    else if (node == "SELECT") {   
                        $("select[name=" +
                                Object.keys(data)[i] +
                                "]  option[value='" +
                                Object.values(data)[i] +
                                "']"
                        ).attr("selected",true);                   
                        $("[name=" +
                                Object.keys(data)[i] +
                                "]  option[value='" +
                                Object.values(data)[i] +
                                "']"
                        ).prop("selected", true);
                    }

                if(Object.keys(data)[i] == 'country_id'){
                     
                    $('#'+Object.keys(data)[i]).attr('readonly',true);
                    $('#'+Object.keys(data)[i]).css('pointer-events','none');
                }

                if(Object.keys(data)[i]=='city_id'){
                    
                    loadstatecity('city_id',data['city_data'],Object.values(data)[i]);
                }else if(Object.keys(data)[i]=='state_id'){
                    loadstatecity('state_id',data['state_data'],Object.values(data)[i]);
                } 
                

                if(Object.keys(data)[i]=='admin_service'){

                    var admin_service = Object.values(data)[i];
                }
                if(Object.keys(data)[i]=='menu_type_id'){
                    
                   loadServiceList('menu_type_id',data['service_list'],Object.values(data)[i], admin_service);
                }
                if(Object.keys(data)[i]=='zone_id'){
                    
                    loadzoneList('zone_id',data['zone_data'],Object.values(data)[i]);
                 }
                

                if(Object.keys(data)[i]=='service_subcategory_id'){
                    
                   loadServiceSubcategory('service_subcategory_id',data['service_subcategory_data'],Object.values(data)[i]);
                }

                if(Object.keys(data)[i]=='mobile'){
                    var countryData = window.intlTelInputGlobals.getCountryData();
                    var result = $.grep(countryData, function(e){ return e.dialCode == data.country_code; });
                    $(".phone").intlTelInput("setCountry", result[0].iso2);
                }   
             

                }else if($("#" +Object.keys(data)[i]).length){
                    $("#" + Object.keys(data)[i]).val(Object.values(data)[i]);
                }
            }
            hideInlineLoader();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.status == 401 && getToken(guard) != null) {
                refreshToken(guard);
            } else if (jqXHR.status == 401) {
                window.location.replace("/admin/login");
            } else if (jqXHR.status == 403) {
                window.location.replace("/access-denied");
            }
            hideInlineLoader();
        }
    });
 }

function loadstatecity(attr,data,selected_val){
    $("#"+attr).empty();
    $("#"+attr).append('<option>-- pleaseSelect --</option>');

    $.each(data,function(key,item){
        
        var selected="";
        if(item.city==undefined){
            var city_id=item.id;
            var city_name=item.city_name;
        }else{
            var city_id=item.city.id;
            var city_name=item.city.city_name;
        } 

         if(item.state==undefined){
            var state_id=item.id;
            var state_name=item.state_name;
        }else{
            var state_id=item.state.id;
            var state_name=item.state.state_name;
        } 
      
        if(attr=="city_id"){  
            if(selected_val==city_id){
               
                selected="selected";
            }
            
            $("#"+attr).append('<option value="'+city_id+'" '+selected+ ' >'+city_name+'</option>');
        } else {
            if(selected_val==state_id){
                selected="selected";
            }
           
            $("#"+attr).append('<option value="'+state_id+'" '+selected+ ' >'+state_name+'</option>');  
        }   
});

}

function loadServiceSubcategory(attr,data,selected_val){
    $("#"+attr).empty();
    $("#"+attr).append('<option>-- Please Select --</option>');

    $.each(data,function(key,item){
        
        var selected="";

        if(selected_val==item.id){
           
            selected="selected";
        }
        
        $("#"+attr).append('<option value="'+item.id+'" '+selected+ ' >'+item.service_subcategory_name+'</option>');
     
    });

}
 
function loadzoneList(attr,data,selected_val){
    $("#"+attr).empty();
    $("#"+attr).append('<option>-- Please Select --</option>');

    $.each(data,function(key,item){
        var selected="";
        if(selected_val==item.id){
           selected="selected";
        }
        console.log(selected);
        $("#"+attr).append('<option value="'+item.id+'" '+selected+ ' >'+item.name+'</option>');
    });

}

function loadServiceList(attr,data,selected_val,serviceId){
    $("#"+attr).empty();
    $("#"+attr).append('<option>-- Please Select --</option>');

    if(serviceId == 3){

        $.each(data,function(key,item){
        
        var selected="";

        if(selected_val==item.id){
           
            selected="selected";
        }
        $("#"+attr).append('<option value="'+item.id+'" '+selected+ ' >'+item.service_category_name+'</option>');
     
    });
 
    }else if(serviceId == 1){
        $.each(data,function(key,item){
        
        var selected="";

        if(selected_val==item.id){
           
            selected="selected";
        }
       
        $("#"+attr).append('<option value="'+item.id+'" '+selected+ ' >'+item.ride_name+'</option>');
        
    });
 
    }
}

function deleteRow(id, url, table, guard = "admin") {
    $(".delete-modal").modal("show");
    $(".delete-modal-btn")
        .off()
        .on("click", function() {

            var confirm = $(this).data('value');

            var data = {};
            data._method = "delete";
            data.id = id;
            

            if(confirm == 1) {
                data.confirm = confirm;
            }
            
            $.ajax({
                url: url,
                type: "post",
                headers: {
                    Authorization: "Bearer " + getToken(guard)
                },
                data: data,
                beforeSend: function (request) {
                    showInlineLoader();
                },
                success: function(response, textStatus, jqXHR) {
                    var data = parseData(response);
                    var info = $('#data-table').DataTable().page.info();
                    
                    $(".delete-modal").modal("hide");
                    if(data.responseData.status=="1"){
                        $(".confirm-modal").modal("show");
                        $('.confirm-modal .modal-body').html("");
                        $('.confirm-modal .modal-body').html(data.message);
                    }else{
                        $(".confirm-modal").modal("hide");
                        table.order([[ info.page, 'asc' ]] ).draw( false );
                        alertMessage("Success", data.message, "success");
                    }
                    
                    hideInlineLoader();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == 401 && getToken(guard) != null) {
                        refreshToken(guard);
                    } else if (jqXHR.status == 401) {
                        window.location.replace("/admin/login");
                    } else if (jqXHR.status == 403) {
                        window.location.replace("/access-denied");
                    }
                    $(".delete-modal").modal("hide");
                    alertMessage(textStatus, jqXHR.statusText, "danger");
                    hideInlineLoader();
                }
            });
        });
}


function parseData(data) {
    try {
        return JSON.parse(data);
    } catch (e) { }

    return data;
}

protect_email = function (user_email) {
    var avg, splitted, part1, part2;
    splitted = user_email.split("@");
    part1 = splitted[0];
    avg = part1.length / 2;
    part1 = part1.substring(0, (part1.length - avg));
    part2 = splitted[1];
    return part1 + "****@" + part2;
};

protect_number=function(number){
    var str = number ? number.substring(0, number.length-4) : '';
    return str+'****';
}