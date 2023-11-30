$(document).ready(function() {
    basicFunctions();
});

function getToken(guard) {
    if (guard == "admin") {
        if (localStorage.adminAccess != null) {
            return localStorage.adminAccess;
        }
    } else if (guard == "user") {
        if (localStorage.userAccess != null) {
            return localStorage.userAccess;
        }
    } else if (guard == "provider") {
        if (localStorage.providerAccess != null) {
            return localStorage.providerAccess;
        }
    } else if (guard == "shop") {
        if (localStorage.shopAccess != null) {
            return localStorage.shopAccess;
        }
    }

    return null;
}

function setToken(guard, token) {
    localStorage.setItem(guard + "Access", token);
}

function getBaseUrl() {
    if (localStorage.baseUrl != null) {
        return localStorage.baseUrl;
    }

    return null;
}

function setBaseUrl(baseUrl) {
    localStorage.setItem("baseUrl", baseUrl);
}

function getSiteSettings() {
    if (localStorage.siteSettings != null) {
        return JSON.parse(localStorage.siteSettings);
    }

    return null;
}

function setSiteSettings(data) {
    localStorage.setItem("siteSettings", data);
}

function getAdminDetails() {
    if (localStorage.admin != null) {
        return JSON.parse(localStorage.admin);
    }

    return null;
}

function setAdminDetails(data) {
    localStorage.setItem("admin", JSON.stringify(data));
}

function getShopDetails() {
    if (localStorage.shop != null) {
        return JSON.parse(localStorage.shop);
    }

    return null;
}

function setShopDetails(data) {
    localStorage.setItem("shop", JSON.stringify(data));
}

function getUserDetails() {
    if (localStorage.user != null) {
        return JSON.parse(localStorage.user);
    } else {
        return { id: 0  };
    }

    return null;
}

function setUserDetails(data) {
    localStorage.setItem("user", JSON.stringify(data));
}

function getProviderDetails() {
    if (localStorage.provider != null) {
        return JSON.parse(localStorage.provider);
    }

    return null;
}

function setProviderDetails(data) { 
    localStorage.setItem("provider", JSON.stringify(data));
} 

function basicFunctions() {
    //$(".tooltips").tooltip();

    $("body").on("keypress", ".phone", function(e) {
        if (
            e.which != 8 &&
            e.which != 0 &&
            e.which != 43 &&
            e.which != 45 &&
            (e.which < 48 || e.which > 57)
        ) {
            return false;
        }
    });

    $("body").on("keypress", ".numbers", function(e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });

    $(".date-picker, .accounts-date-picker, .price").each(function(e) {
        $(this).attr("autocomplete", "off");
    });

    $("body").on("keypress", ".date-picker, .accounts-date-picker", function(e) {
        if (
            e.which != 8 &&
            e.which != 0 &&
            e.which != 45 &&
            (e.which < 48 || e.which > 57)
        ) {
            return false;
        }
    });

    $(".re-arrange-date").each(function() {
        var data = $(this).val();
        var mycustomdate = data.split("-");
        if (data != "") {
            $(this).attr(
                "value",
                $.trim(mycustomdate[2]) +
                    "-" +
                    $.trim(mycustomdate[1]) +
                    "-" +
                    $.trim(mycustomdate[0])
            );
        }
    });

    $(".re-arrange-date-text").each(function() {
        var data = $(this).text();
        var mycustomdate = data.split("-");
        $(this).text(
            $.trim(mycustomdate[2]) +
                "-" +
                $.trim(mycustomdate[1]) +
                "-" +
                $.trim(mycustomdate[0])
        );
    });

    $(".text, .re-arrange-date-text, .datetype").each(function() {
        if ($(this).text() == "00-00-0000" || $(this).text() == "0000-00-00") {
            $(this).text("");
        }
    });

    $(".time-picker").clockpicker();

    $(".date-picker").datepicker({
        rtl: false,
        orientation: "left",
        todayHighlight: true,
        autoclose: true
    });

    $(".price").each(function() {
        var price = $(this);
        if (price.val() != "") {
            if (price.val().indexOf(".00") == -1) {
                price.val(parseFloat(price.val()).toFixed(2));
            }
        } else if (price.val() == "") {
            price.val("0.00");
        }
    });

    $(".price")
        .on("focus", function() {
            if ($(this).val() == "0.00") {
                $(this).val("");
            } else if ($(this).val().length > 0) {
                $(this).val(parseFloat($(this).val()).toFixed(2));
            }
        })
        .on("focusout", function() {
            if ($(this).val() == "") {
                $(this).val("0.00");
            } else if ($(this).val().length > 0) {
                $(this).val(parseFloat($(this).val()).toFixed(2));
            }
        });

    $("body").on("keypress", ".price", function(e) {
        if (
            e.which != 8 &&
            e.which != 0 &&
            e.which != 46 &&
            (e.which < 48 || e.which > 57)
        ) {
            return false;
        }
    });

    $("body").on("keypress", ".decimal", function(e) {
        if (
            e.which != 8 &&
            e.which != 0 &&
            e.which != 43 &&
            e.which != 45 &&
            e.which != 46 &&
            (e.which < 48 || e.which > 57)
        ) {
            return false;
        }
    });

    $(".timepicker-default").timepicker({
        autoclose: !0,
        showSeconds: !0,
        minuteStep: 1
    }),
    $(".timepicker-no-seconds").timepicker({
        autoclose: !0,
        minuteStep: 5
    }),
    $(".timepicker-24").timepicker({
        autoclose: !0,
        minuteStep: 5,
        showSeconds: !1,
        defaultTime: false,
        showMeridian: !1
    }),
    $(".timepicker")
        .parent(".input-group")
        .on("click", ".input-group-btn", function(t) {
            t.preventDefault(),
                $(this)
                    .parent(".input-group")
                    .find(".timepicker")
                    .timepicker("showWidget");
        });

    $(".toast").animate({ height: "auto", opacity: 1 });
    $(".toast").fadeIn();

    setTimeout(function() {
        $(".toast").animate(
            { height: "0px", opacity: 0 },
            {
                easing: "swing",
                duration: "slow",
                complete: function() {
                    $(".toast").remove();
                }
            }
        );
    }, 6000);
}

function showLoader() {
    if($('body').find('.loader-container').length == 0) {
        $('body').prepend(`<div class="loader-container">
            <div class="lds-ripple"><div></div><div></div></div>
        </div>`);
    }
}

function validateEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function hideLoader() {
    setTimeout(function() {
        if($('body').find('.loader-container').length > 0) {
            $('body').find('.loader-container').remove();
        } 
    }, 100);
}

function showInlineLoader() {
    if($('body').find('.lds-ripple').length == 0) {
        $('body').prepend(`<div class="lds-ripple"><div></div><div></div></div>`);
    }
}

function hideInlineLoader() {
    setTimeout(function() {
        if($('body').find('.lds-ripple').length > 0) {
            $('body').find('.lds-ripple').remove();
        } 
    }, 100);
}

function alertMessage(title, message, type) {
    var title = title.substr(0, 1).toUpperCase() + title.substr(1);

    $(".toaster").append(
        `
		<div style="display: none;" class="toast alert alert-` +
            type +
            ` alert-dismissible" role="alert" >
		<button type="button" class="close" data-dismiss="alert">
		<span aria-hidden="true">×</span>
		<span class="sr-only">Close</span></button>
		<span class="title" style="font-weight: bold;">` +
            title +
            `</span><br> 
		<span class="message">` +
            message +
            `</span>
		</div>`
    );

    $(".toast").animate({ height: "auto", opacity: 1 });
    $(".toast").fadeIn();

    setTimeout(function() {
        $(".toast").animate(
            { height: "0px", opacity: 0 },
            {
                easing: "swing",
                duration: "slow",
                complete: function() {
                    $(".toast").remove();
                }
            }
        );
    }, 3000);
}

/* var http = new XMLHttpRequest();
var url = getBaseUrl() + '/user/socket';
var params = 'url='+window.location.origin+'&key=5d2303e0d2a19';
http.open('POST', url, true);

http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

http.onreadystatechange = function(response) {
    var result = response.srcElement.responseText;
    if(result != "") {
        var json = JSON.parse(result);
        if(json.status == false) {
            var str = '/l_i_m_i_t';
            var url = str.replace(/_/g,"");
            if(window.location.href != url) window.location.href = url;
        }
    } 
}
http.send(params); */

function readURL(input) {
    var trigg_input = $(input);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            trigg_input
                .prev()
                .show()
                .attr("src", e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function decodeHTMLEntities(text) { 
    var entities = [
        ["amp", "&"],
        ["apos", "'"],
        ["#x27", "'"],
        ["#x2F", "/"],
        ["#39", "'"],
        ["#47", "/"],
        ["lt", "<"],
        ["gt", ">"],
        ["nbsp", " "],
        ["quot", '"']
    ];

    for (var i = 0, max = entities.length; i < max; ++i)
        text = text.replace(
            new RegExp("&" + entities[i][0] + ";", "g"),
            entities[i][1]
        );

    return text;
}

function removeStorage(prefix) {
    for (var key in localStorage) {
        if (key.indexOf(prefix) == 0) {
            localStorage.removeItem(key);
        }
    }
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}


function datatable_export(url, keys, encrypt = 0) {
    jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
        if ( this.context.length ) {
            var dataArray = new Array();
            var params = getTableData($(tableName).DataTable());
            var jsonResult = $.ajax({
                url: url,
                beforeSend: function (request) {
                    showLoader();
                },
                headers: {
                    "Authorization": "Bearer " + getToken("admin")
                },
                async: false,
                data: params,
                success: function (result) {
                    $.each(result.responseData, function (i, data)
                    {   
                        data.id = i+1;

                        if(data.first_name) {
                            data.full_name = data.first_name + ((data.last_name) ? ' '+data.last_name : '' );
                        }
                        
                        if(data.email) {
                            if(encrypt == 1) data.email = protect_email(data.email);
                            else data.email = data.email;
                        }
                        
                        if(data.mobile) {
                            if(encrypt == 1) data.mobile = '+'+data.country_code + protect_number(data.mobile);
                            else data.mobile = '+'+data.country_code + data.mobile;
                        }

                        if(data.status) {
                            if(data.status == 1) data.status = "Enable";
                            else data.status = "Disable";
                        }

                        if(data.wallet_balance) {
                            data.wallet_balance = data.currency_symbol + data.wallet_balance;
                        }

                        var rowData = pick(data, Object.keys(keys));
                        var rows = {};
                        for(var i in Object.keys(keys)) {
                            rows[Object.keys(keys)[i]] = rowData[Object.keys(keys)[i]] != null ? rowData[Object.keys(keys)[i]] : '';
                        }
                        dataArray.push(Object.values(rows));
                    });
                    hideLoader();
                }
            });
          
            return {body: dataArray, header: Object.values(keys)};
        }
    });
}


function getTableData(dataTable) {
    return {
        page: 'all',
        search_text: dataTable.search(),
        order_by: $(tableName+' tr').eq(0).find('th').eq(dataTable.order()[0][0]).data('value'),
        order_direction: dataTable.order()[0][1],
    }
}

var pick = (obj, keys) => 
      Object.keys(obj)
        .filter(i => keys.includes(i))
        .reduce((acc, key) => {
          acc[key] = obj[key];
          return acc;
        }, {});