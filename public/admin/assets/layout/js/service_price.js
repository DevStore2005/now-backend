var tableName = '#data-table';
var table = $(tableName);
showLoader();
$(document).ready(function() {
    $('.add').on('click', function(e) {
        e.preventDefault();
        $.get(MyNameSpace.config.adminurl + "/service-list/create", function(data) {
            $('.crud-modal .modal-container').html("");
            $('.crud-modal .modal-container').html(data);
        });
        $('.crud-modal').modal('show');
    });

    $('body').on('click', '.edit', function(e) {
        e.preventDefault();
        $.get(MyNameSpace.config.adminurl + "/service-list/"+$(this).data('id')+"/edit", function(data) {
            $('.crud-modal .modal-container').html("");
            $('.crud-modal .modal-container').html(data);
        });
        $('.crud-modal').modal('show');
    });

    table = table.DataTable( { 
        "processing": true,
        "serverSide": true,
        "pageLength": 10,
        "ajax": {
            "url": getBaseUrl() + "/admin/service/listing",
            "type": "GET",
            'beforeSend': function (request) {
                showLoader();
            },
            "headers": {
                "Authorization": "Bearer " + getToken("admin")
            },
            data: function(data){
                var info = $(tableName).DataTable().page.info();
                delete data.columns;
                data.page = info.page + 1;
                data.search_text = data.search['value'];
                data.order_by = $(tableName+' tr').eq(0).find('th').eq(data.order[0]['column']).data('value');
                data.order_direction = data.order[0]['dir'];                
            },
            dataFilter: function(response){        
                var json = parseData(response);  
                json.recordsTotal = json.responseData.total;
                json.recordsFiltered = json.responseData.total;
                json.data = json.responseData.data;
                hideLoader();
                return JSON.stringify( json ); // return JSON string
            }
        },
        "columns": [
            { "data": "id" ,render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
              }
            },
            { "data": "service_name" },
            { "data": "service_category_id", render: function (data, type, row, meta) {
                return row.service_category.service_category_name; 
            }},
            { "data": "service_subcategory_id", render: function (data, type, row,meta) {
                return row.sub_categories[0].service_subcategory_name; 
            }},
            /*{ "data": "picture", render: function (data, type, row) {
                if(data){
                    return "<img src='"+data+"' class='input_img picture-src img-responsive imgsmall' title='' style='height: 50px;width:50px;' />";
                }else{
                    return "NA";
                }
            }},*/
            { "data": "service_status" ,render: function (data, type, row) {
                
                return data==1?'Enable':'Disable';
            }
            },
            { "data": function (data, type, row) {
              
                if(data.service_status ==1){
                    var status ="Disable";
                }else{
                    var status="Enable";
                }
              
                return "<button onclick='priceCountries("+data.id+")' data-toggle='modal' data-target='#service_type_service' class='btn btn-block btn-outline-primary price'>Price</button><button data-id='"+data.id+"' class='btn btn-block btn-success edit'>Edit</button><button data-id='"+data.id+"' data-value='"+data.service_status+"' class='btn btn-block btn-warning status'>"+status+"</button>";
                
            }}
        ],
        responsive: true,
        paging:true,
            info:true,
            lengthChange:false,
            dom: 'Bfrtip',
            buttons: [{
               extend: "copy",
               exportOptions: {
                   columns: [":visible :not(:last-child)"]
               }
            }, {
               extend: "csv",
               exportOptions: {
                   columns: [":visible :not(:last-child)"]
               }
            }, {
               extend: "excel",
               exportOptions: {
                   columns: [":visible :not(:last-child)"]
               }
            }, {
               extend: "pdf",
               exportOptions: {
                   columns: [":visible :not(:last-child)"]
               }
            }]
    } );

    $('body').on('click', '.status', function() {
        var id = $(this).data('id');
        var value = $(this).data('value');

         $(".status-modal").modal("show");
            $(".status-modal-btn")
                .off()
                .on("click", function() {
                
                    
                    var url = getBaseUrl() + "/admin/service/listing/"+id+'/updateStatus?status='+value;

                    $.ajax({
                        type:"GET",
                        url: url,
                        headers: {
                            Authorization: "Bearer " + getToken("admin")
                        },
                        'beforeSend': function (request) {
                            showInlineLoader();
                        },
                        success:function(data){
                            $(".status-modal").modal("hide");
                            
                            var info = $('#data-table').DataTable().page.info();
                            table.order([[ info.page, 'asc' ]] ).draw( false );
                            alertMessage("Success", data.message, "success");
                            hideInlineLoader();
                        }
                    });
                });
        
    });
    $("#maxQty").prop('disabled', true);
    $("#maxQty").prop('required', false);
});
function priceCountries(id) {
    var url = getBaseUrl() + "/admin/service/get-service-price/"+id;
    $.ajax({
        url: url,
        type: "GET",
        async : false,
        beforeSend: function (request) {
            showInlineLoader();
        },
        headers: {
            Authorization: "Bearer " + getToken("admin")
        },
        success: function(data) {
            var countryCityList ='';
            $.each( data.responseData,function(key,value){
                countryCityList += `<label class="country_list_style">`+value.country.country_name+`</label>`;
                $.each( value.company_country_cities,function(key1,value1){
                    if(key == 0 && key1 ==0){
                        var cityActiveClass ="active";
                        $("#countryId").val(value.country.id);
                        $("#cityId").val(value1.city.id);
                        $("#serviceId").val(id);
                        getData(id,value1.city.id,value.country.id,value.currency);
                    }else{
                        var cityActiveClass ='';
                    }
                countryCityList +=  `<a href="#" class="list-group-item cityActiveClass  `+cityActiveClass+`" data-value="`+value.currency+`" onclick="getData(`+id+`,`+value1.city.id+`,`+value.country.id+`)" id="`+value1.city.id+`"><span>`+value1.city.city_name+`</span></a>`;
                });
            });
            $('.myprice').empty().append(`<div class="form-group">
                    <div class="select_city nav-wrapper"><div class="list-group">
                        `+countryCityList+`
                    </div>
                    </div>
                </div>
            `);
            hideInlineLoader();
        }
    });
}

function getData(serviceId,cityId,countryId,currency_value){

    $('.cityActiveClass').removeClass("active");
    $('#'+cityId).addClass(" active");
    var currency_value= currency_value ? currency_value:$('#'+cityId).data('value');
    $('.currency').text(currency_value);
    var service_price_id='';
    var fixed='';
    var price='';
    var minute='';
    var hour='';
    var distance='';
    var url = getBaseUrl() + "/admin/service/pricing/service_id/city_id"
    url = url.replace('service_id', serviceId);
    url = url.replace('city_id', cityId);
    $("#cityId").val(cityId);
    $("#countryId").val(countryId);
    $("#serviceId").val(serviceId);
    setData( url ); 
    var calculator = 'FIXED';
    $("#service_price_id").val(service_price_id);
    $.ajax({
        url: url,
        type: "GET",
        async : false,
        headers: {
            Authorization: "Bearer " + getToken("admin")
        },
        success: function(data) {
            if(data.responseData.length !=0){
                $("#countryId").val(countryId);
                $("#serviceId").val(serviceId);
                service_price_id = data.responseData.id;
                $("#service_price_id").val(service_price_id);
                calculator = data.responseData.fare_type;    
                allowQty = data.responseData.allow_quantity;
                if(allowQty == 0){
                    $('#allowQty').val(0);
                    $('#allowQty').prop('checked', false);
                    $("#maxQty").prop('disabled', true);
                    $("#maxQty").prop('required', false);
                }else{
                    $('#allowQty').val(1);
                    $('#allowQty').prop('checked', true);
                    $("#maxQty").prop('disabled', false);
                    $("#maxQty").prop('required', true);
                }
                
            }else{
                $("#fixed, #minute, #price, #distance, #commission, #fleetCommission, #maxQty").val('');
                $("#calculator").val('FIXED');
                $("#price,#distance").prop('disabled', false);
                $("#price,#distance").prop('required', true);
            }
        }

    });
    // $('.priceBody').empty().append(``);

    priceInputs(calculator);

}

$('body').on('click', '.addService', function(event) {
    event.preventDefault();
        $.ajax({
        url: getBaseUrl() + "/admin/service/pricings",
        type: 'POST',
        data: $('#servicePriceFormId').serialize(),
        headers: {
            Authorization: "Bearer " + getToken("admin")
        },
        success: function(response, textStatus, xhr) {
            alert(xhr.responseJSON.message);
        $(".modal").modal("hide");

        },
        error: function(xhr, textStatus) {
            alert(xhr.responseJSON.message);
        }
    });
});

$('body').on('change', '#calculator', function(event) {   
    cal=$(this).val();
    priceInputs(cal);
});

function priceInputs(cal){
    // console.log(cal);
    if(cal=='FIXED'){
		$(".allowdiv").show();
        $("#distance, #price, #minute").val('');
        $("#minute").prop('disabled', true); 
        $("#minute").prop('required', false); 
        $("#distance,#price").prop('disabled', true);
        $("#distance,#price").prop('required', false);
        $("#price,#distance").prop('disabled', true);
        $("#price,#distance").prop('required', false);
        $("#changecal").text('BP * Qty'); minute
    }
    else if(cal=='HOURLY'){
        $(".allowdiv").hide();
        $("#price, #distance").val('');
        $("#price,#distance").prop('disabled', true);
        $("#price,#distance").prop('required', false);
        $("#minute").prop('disabled', false);
        $("#minute").prop('required', true);
        $("#changecal").text('BP +(PM * TM)');
    }
    else if(cal=='DISTANCETIME'){
        $(".allowdiv").hide(); 
        $("#minute, #price, #distance").val('');
        $("#price,#distance,#minute").prop('disabled', false);
        $("#price,#distance,#minute").prop('required', true);
        $("#changecal").text('BP +(PM * TM) + (BD * TD)');
    }
    else{
        $(".allowdiv").show();
        $("#minute, #price, #distance").val('');
        $("#price,#distance").prop('disabled', false);
        $("#price,#distance").prop('required', true);
        $("#minute").prop('disabled', true);
        $("#minute").prop('required', false);
        $("#changecal").text('BP * Qty');
    }
}

$('#allowQty').on('change', function() { 
    cb = $(this);
    cb.val(cb.prop('checked'));
    var qty = cb.prop('checked');
    if(qty){
        cb.val(1);
        $("#maxQty").prop('disabled', false);
        $("#maxQty").prop('required', true);
    }else{
        cb.val(0);
        $("#maxQty").prop('disabled', true);
        $("#maxQty").prop('required', false);
    }
});