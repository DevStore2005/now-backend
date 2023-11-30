class MainComponent extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            request: [],
            account_status: "",
            service_status: "",
            status: "",
            time: 0,
        };
    }

    componentDidMount() {

        this.getStatus();

        var id = '';
        var that = this;
        var socket = io.connect(window.socket_url);
        $.ajax({
            url: getBaseUrl() + "/provider/check/ride/request",
            type: "get",
            async: false,
            processData: false,
            contentType: false,
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            success: (response, textStatus, jqXHR) => {
                var data = parseData(response);
                if(data.responseData.request.id) {
                    var token = data.responseData.request.twilio_token.provider;
                    window.setupTwilioClient(token.token, token.To, token.outgoing_caller_id);
                    id = data.responseData.request.id;
                    socket.emit('joinPrivateRoom', `room_${window.room}_R${id}_TRANSPORT`);
                    socket.emit('send_location', {room: `room_${window.room}_R${id}_TRANSPORT`, latitude: '13.07322590', longitude: '80.26082090'});
                }
                
            },
            error: (jqXHR, textStatus, errorThrown) => {}
        });

        
        socket.on('socketStatus', function (data) {
            if(window.env == 'local') console.log(data);
        });

        

        socket.on('rideRequest', function (data) {
            that.getStatus();
        });

        //setInterval(() => this.getStatus(), 5000);
    }
    
    getStatus() {
        $.ajax({
            url: getBaseUrl() + "/provider/check/ride/request",
            type: "get",
            processData: false,
            contentType: false,
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            success: (response, textStatus, jqXHR) => {
                var data = parseData(response);

                this.setState({
                    account_status: data.responseData.account_status,
                    waiting_status: data.responseData.waitingStatus,
                    request: data.responseData.request,
                    time:(data.responseData.request != null && Object.keys(data.responseData.request).length > 0 ) ? data.responseData.request.time_left_to_respond : 0,
                    status:
                    (data.responseData.request != null && Object.keys(data.responseData.request).length > 0 )
                            ? data.responseData.request.status
                            : "",
                    provider: data.responseData.provider_details
                });
            },
            error: (jqXHR, textStatus, errorThrown) => {}
        });
    }

    render() {
        let status = this.state.status;
        if(this.state.account_status == "DOCUMENT" || this.state.account_status == "CARD" || this.state.account_status == "SERVICES" || this.state.account_status == "ONBOARDING") {
            window.location.replace("/provider/document");
        } else {
            if(this.state.request == null ) {
               window.location.replace("/provider/home");
            } else if (status === "STARTED" || status === "ARRIVED" || status === "PICKEDUP") {
                return <StartedComponent request={this.state.request} provider={this.state.provider} waiting={this.state.waiting_status} />;
            } else if (status === "COMPLETED" || status === "DROPPED") {
                return <InvoiceComponent request={this.state.request} provider={this.state.provider} />;
            }
        }

        

        return null;
    }
}

class StartedComponent extends React.Component {
    constructor(props) {
        super(props);
 
        this.state = {
            cancelModal: false,
            waiting: this.props.waiting,
            error_message: ''
        };

    }

    

    componentDidMount() {
        var chatSocket = io.connect(window.socket_url);
        var id = this.props.request.id;
        var user_id = this.props.request.user_id;
        var provider_id = this.props.request.provider_id;
        var admin_service = 'TRANSPORT';
        var status = this.props.request.status;

        if(status == "ACCEPTED" || status == "STARTED" || status == "ARRIVED" ) {
            $('#message_container').show();
        } else {
            //$('#message_container').hide();
        }

        var chat_data = this.props.request.chat;

        if(chat_data != null) {
            for(var chat of this.props.request.chat.data) {
                if(chat.type == "user") {
                    $('#message_box').append(`<div class="provider-msg"><span class="msg">${chat.message}</span></div>`);
                } else if(chat.type == "provider") {
                    $('#message_box').append(`<div class="user-msg"><span class="msg">${chat.message}</span></div>`);
                }
            }
        }

        var message_box  = $('#message_box');
        var height = message_box[0].scrollHeight;
        message_box.scrollTop(height);

    	chatSocket.emit('joinPrivateChatRoom', `room_${window.room}_R${id}_U${user_id}_P${provider_id}_${admin_service}`);

        chatSocket.on('new_message', function (data) {
            if(data.type == "user") {
                $('#message_box').append(`<div class="provider-msg"><span class="msg">${data.message}</span></div>`);
                //$('#message_box').append(`<span style='width:100%; float: left;'>${data.user}: ${data.message}</span>`);
            } else if(data.type == "provider") {
                $('#message_box').append(`<div class="user-msg"><span class="msg">${data.message}</span></div>`);
                //$('#message_box').append(`<span style='width:100%; float: left;'>${data.provider}: ${data.message}</span>`);
            }

            
            var height = message_box[0].scrollHeight;
            message_box.scrollTop(height);

        });
    }

    componentDidUpdate(prevProps) {
        if(status == "ACCEPTED" || status == "STARTED" || status == "ARRIVED" ) {
            $('#message_container').show();
        } else {
            //$('#message_container').hide();
        }
    }

    handleUpdateStatus = (status, e) => {

        let id = this.props.request.id;
        let otpState = getSiteSettings().transport.ride_otp;
        let otp = $("input[name=otp]").val();
        let d_address = $("#destination-input").val();
        let d_latitude = $("#destination_latitude").val();
        let d_longitude = $("#destination_longitude").val();

        if( this.props.request.is_drop_location == 0 && (d_latitude == "" && d_longitude == "")) {
            alertMessage("Error", "Please enter drop location", "danger");
            return false;
        }

        let toll_price = $("input[name=toll_price]").val();

        if(status == 'DROPPED' && this.state.waiting == 1) {
            this.setState({error_message: 'Switch off the waiting time before drop'});
            return false;
        } else {
            this.setState({error_message: ''});
        }
        
        if(status == "ARRIVED" || status == "PICKEDUP" || status == "DROPPED") $('#arrived_tab').addClass('current');
        
        if(status == "PICKEDUP" || status == "DROPPED") $('#picked_tab').addClass('current');

        if(status == "DROPPED") $('#reached_tab').addClass('current');

        if (($("element").data("bs.modal") || { isShown: false }).isShown == false) {
            $("#otpModal").modal("hide");
        }

        if(status == "ACCEPTED" || status == "STARTED" || status == "ARRIVED" ) {
            $('#message_container').show();
        } else {
            //$('#message_container').hide();
        }

        
        if(status == "PICKEDUP") {
            this.setState({
                status: "PICKEDUP"
            }); 
        }

        if(otpState == 1) {
            var data = { _method: "PATCH", id, status, toll_price, otp, d_latitude, d_longitude, d_address  };
        } else {
            var data = { _method: "PATCH", id, status, toll_price, d_latitude, d_longitude, d_address  };
        }

        $.ajax({
            url: getBaseUrl() + "/provider/update/ride/request",
            type: "post",
            data: data,
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            beforeSend: function() {
                showLoader();
            },
            success: (data, textStatus, jqXHR) => {
                hideLoader();
                return <InvoiceComponent request={this.props.request} provider={this.props.provider} />;
            },
            error: (jqXHR, textStatus, errorThrown) => {
                hideLoader();
                alertMessage(textStatus, jqXHR.responseJSON.message, "danger");
                if(status == "ARRIVED") $('#arrived_tab').removeClass('current');
                else if(status == "PICKEDUP") $('#picked_tab').removeClass('current');
                else if(status == "DROPPED") $('#reached_tab').removeClass('current');

                if(status == "PICKEDUP") {

                    this.setState({
                        status: "ARRIVED"
                    });

                    $("#otpModal").modal();
                }
            }
        });
    }

    handleOtpModal = e => {

        // if(this.props.request.request_type != "MANUAL") {
            $("#otpModal").modal();
        // } else  {
        //     this.handleUpdateStatus('PICKEDUP');
        // }
        
    }

    handleWaiting = e => {

        let id = this.props.request.id;

        var status = $("input[name=waiting]").val();

        $.ajax({
            url: getBaseUrl() + "/provider/waiting",
            type: "post",
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            data: {
                id, status
            },
            beforeSend: function() {
                showInlineLoader();
            },
            success: (response, textStatus, jqXHR) => {
                hideInlineLoader();
                this.setState({
                    waiting: response.waitingStatus
                }, () => console.log(this.state.waiting) );

            },
            error: (jqXHR, textStatus, errorThrown) => {}
        });

    }

    handleOtp = e => {

        var otp = $("input[name=otp]").val();

        if(otp != "") {
            this.handleUpdateStatus('PICKEDUP');
        }else{
            var alertTitle = 'Required';
            var alertMsg = 'OTP is required';
            alertMessage(alertTitle, alertMsg, "danger");
        }

    }

    handleSendMessage = e => {
        if((e.which == 13 || e.which === undefined) && $('input[name=message]').val() != '') {
            var chatSocket = io.connect(window.socket_url);
            var message = $('input[name=message]');

            var request_id = this.props.request.id;
            var user_id = this.props.request.user_id;
            var provider_id = this.props.request.provider_id;
            var admin_service = 'TRANSPORT';
            var user = `${this.props.request.user.first_name} ${this.props.request.user.last_name}`;
            var provider = `${this.props.provider.first_name} ${this.props.provider.last_name}`;

            chatSocket.emit('send_message', {room: `room_${window.room}_R${request_id}_U${user_id}_P${provider_id}_${admin_service}`, url: getBaseUrl() + "/chat", salt_key: window.salt_key, id: request_id, admin_service: admin_service, type: 'provider', user: user, provider: provider, message:  message.val() });
            
            message.val('');
        }
    }

    setDestination = e => {
        var destinationInput = document.getElementById("destination-input");
        var destinationLatitude = document.getElementById("destination_latitude");
        var destinationLongitude = document.getElementById("destination_longitude");

        var destinationAutocomplete = new google.maps.places.Autocomplete(
            destinationInput
        );
        
        destinationAutocomplete.addListener("place_changed", function(event) {
            var place = destinationAutocomplete.getPlace();
    
            if (place.hasOwnProperty("place_id")) {
                if (!place.geometry) {
                    // window.alert("Autocomplete's returned place contains no geometry");
                    return;
                }
                destinationLatitude.value = place.geometry.location.lat();
                destinationLongitude.value = place.geometry.location.lng();
            } else {
                service.textSearch(
                    {
                        query: place.name
                    },
                    function(results, status) {
                        if (status == google.maps.places.PlacesServiceStatus.OK) {
                            destinationLatitude.value = results[0].geometry.location.lat();
                            destinationLongitude.value = results[0].geometry.location.lng();
                        }
                    }
                );
            }
        });

        setupPlaceChangedListener(destinationAutocomplete, "DEST");

        function setupPlaceChangedListener( autocomplete, mode ) {
            var me = this;
            autocomplete.addListener("place_changed", function() {
                var place = autocomplete.getPlace();
                if (!place.place_id) {
                    // window.alert("Please select an option from the dropdown list.");
                    return;
                }
            });
        };
    }

    handleCancelModal = e => {
        this.setState({cancelModal:true});
    }

    render() {
        var rows=[];
        var starcount = this.props.request.user.rating;
        for (var i=0;i<starcount;i++){
            rows.push(<div className="rating-symbol" key={i} style={{display: 'inline-block', position: 'relative'}}>
            <div className="fa fa-star-o" style={{visibility: 'hidden'}}></div>
            <div className="rating-symbol-foreground" style={{display: 'inline-block', position: 'absolute', overflow: 'hidden', left: '0px', right: '0px', top: '0px', width: 'auto'}}>
                <span className="fa fa-star" aria-hidden="true"></span>
            </div>
        </div>);
        }

        return (
            <div className="row">
                <div  className={this.props.request.is_drop_location == 1 ? "service-card col-xs-12 col-sm-12 col-md-12 col-lg-6 available m-0" : "service-card col-xs-12 col-sm-12 col-md-12 available m-0" } >
                    <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0">
                        <div className="form-register">
                            <div className="steps clearfix">
                                <ul role="tablist">
                                <li id="arrived_tab" role="tab" aria-disabled="false" className={(this.props.request.status == "ARRIVED" || this.props.request.status == "PICKEDUP" || this.props.request.status == "DROPPED" || this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="true">
                                    <a id="form-total-t-0" aria-controls="form-total-p-0">
                                        <span className="current-info audible"> </span>
                                        <div className="title">
                                            <span className="step-icon"><img src="/assets/layout/images/transport/svg/map.svg" /></span>
                                            <span className="step-text">Arrived</span>
                                        </div>
                                    </a>
                                </li>
                                <li id="picked_tab" role="tab" aria-disabled="false" className={(this.props.request.status == "PICKEDUP" || this.props.request.status == "DROPPED" || this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="false">
                                    <a id="form-total-t-1" aria-controls="form-total-p-1">
                                        <div className="title">
                                            <span className="step-icon"><img src="/assets/layout/images/transport/svg/taxi.svg" /></span>
                                            <span className="step-text">Picked Up</span>
                                        </div>
                                    </a>
                                </li>
                                <li id="reached_tab" role="tab" aria-disabled="false" className={(this.props.request.status == "DROPPED" || this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="false">
                                    <a id="form-total-t-2" aria-controls="form-total-p-2">
                                        <div className="title">
                                            <span className="step-icon"><img src="/assets/layout/images/transport/svg/destination.svg" /></span>
                                            <span className="step-text">Reached</span>
                                        </div>
                                    </a>
                                </li>
                                </ul>
                            </div>
                        </div>
                        <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0">
                            <img src={this.props.request.user.picture} className="user-img" />
                            <div className="user-right">
                                <h5>{this.props.request.user.first_name} {this.props.request.user.last_name}</h5>
                                <p> <a href={'tel:'+this.props.request.user.country_code+this.props.request.user.mobile }>{ this.props.request.user.country_code } { this.props.request.user.mobile }</a>  </p>
                                <div className="rating-outer">
                                    <span style={{cursor: 'default'}}>
                                    {rows}
                                    </span>
                                    <input type="hidden" className="rating" value="1" disabled="disabled" />
                                </div>
                            </div>
                        </div>
                        <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0 d-flex">
                            <div className="col-xs-12 col-sm-12 col-md-10 col-lg-11 p-0">
                                <h5>Pickup Location</h5>
                                <p>{this.props.request.s_address}</p>
                                {(this.props.request.is_drop_location == 1 ) ? 
                                <div>
                                    <h5>Drop Location</h5> 
                                    <p>{this.props.request.d_address}</p>
                                </div> 
                                : '' }
                                {(this.props.request.is_drop_location == 0 && this.props.request.status == "PICKEDUP" ) ? 
                                <div>
                                    <h5>Drop Location</h5> 
                                    <p>
                                        <input className="form-control" type="text" onChange={this.setDestination} id="destination-input" />
                                        <input type="hidden" name="d_latitude" id="destination_latitude" />
                                        <input type="hidden" name="d_longitude" id="destination_longitude" />
                                    </p>
                                </div> 
                                : '' }

                                {(this.props.request.someone == 1 && this.props.request.status == "STARTED" || this.props.request.someone == 1 && this.props.request.status == "ARRIVED"  ) ? 
                                <div>
                                    <h5>Booked for a friend</h5> 
                                    <p style={{marginBottom: '0px'}}>Name: {this.props.request.someone_name}</p>
                                    {this.props.request.someone_email && <p style={{marginBottom: '0px'}}>Email: {this.props.request.someone_email}</p> }
                                    {this.props.request.someone_mobile && <p>Mobile: {this.props.request.someone_mobile}</p> }
                                </div> 
                                : '' }

                            </div>
                            {/* <!-- <div className="col-xs-12 col-sm-12 col-md-1 col-lg-1 p-0 arrow">
                                <i className="fa fa-location-arrow"></i>
                            </div> --> */}
                        </div>
                        <div className="actions col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <a id="dropped" className="btn btn-primary btn-md mr-2" onClick={window.callModal.bind()}>Call User </a> 
                        {this.props.request.status == "STARTED" ? <a id="arrived" className="btn btn-primary btn-md mr-2"  onClick={this.handleUpdateStatus.bind(this, 'ARRIVED')}>Arrived <i className="fa fa-check" aria-hidden="true"></i></a> : ""}
                        {(this.props.request.status == "ARRIVED" && getSiteSettings().transport.ride_otp=="1" && this.props.request.created_type !="ADMIN") ? <a id="arrived" className="btn btn-primary btn-md mr-2"  data-toggle="modal" onClick={this.handleOtpModal} >Pick <i className="fa fa-check" aria-hidden="true"></i></a> : ""}
                        {(this.props.request.status == "ARRIVED" && (getSiteSettings().transport.ride_otp=="0" || this.props.request.created_type =="ADMIN")) ? <a id="arrived" className="btn btn-primary btn-md mr-2"   onClick={this.handleUpdateStatus.bind(this, 'PICKEDUP')} >Pick & Start <i className="fa fa-check" aria-hidden="true"></i></a> : ""}  
                        {this.props.request.status == "STARTED" || this.props.request.status == "ARRIVED" ? <a id="cancel"  data-toggle="modal" data-target="#cancelModal" onClick={this.handleCancelModal} className="btn btn-primary btn-md mr-2">Cancel <i className="fa fa-times" aria-hidden="true"></i></a>  : ""}
                        {this.props.request.status == "PICKEDUP" ? 
                        <div>

                        <div className="row">
                            {/*<div className="waiting-toggle col-sm-6">
                                <h5>Waiting&nbsp;Time</h5>
                                <div className="dis-flex-start pr-2"> 
                                    <label className="toggleSwitch nolabel">
                                        <input name="waiting" onChange={this.handleWaiting} type="checkbox" checked={ this.state.waiting == 1 ? 'checked' : ''} value="1" />
                                            <span>
                                                <span>OFF</span>
                                                <span>ON</span>
                                            </span>
                                            <a></a>
                                    </label>
                                </div>
                            </div>
                            <div className="col-sm-6">
                                <input className="form-control price" name="toll_price" placeholder="Enter Toll Charge" />
                            </div> */}
                            { this.state.error_message != '' ?
                            <p>{this.state.error_message}</p>
                            : '' }
                        </div>

                        <a id="dropped" className="btn btn-primary btn-md mr-2" onClick={this.handleUpdateStatus.bind(this, 'DROPPED')}>Tap When Dropped </a> 
                        </div>
                        : ""}
                        </div>
                        {/* <!-- Popup Modal --> */}
                        <div className="modal" id="otpModal">
                            <div className="modal-dialog">
                                <div className="modal-content">
                                <div className="modal-header">
                                    <h4 className="modal-title">Enter OTP</h4>
                                    <button type="button" className="close" data-dismiss="modal">x</button>
                                </div>
                                <div className="modal-body">
                                    <div className="dis-center">
                                    <img src="/assets/layout/images/transport/svg/otp.svg" className="w-30" />
                                    </div>
                                    <input id="otp" className="form-control numbers" style={{letterSpacing: '1em'}} autoComplete="off" maxLength="4" placeholder="****" type="text" name="otp" />
                                </div>
                                <div className="modal-footer">
                                    <a id="startTrip" className="btn btn-primary btn-block" onClick={this.handleOtp}>Start Trip</a>
                                </div>
                                </div>
                            </div>
                        </div>
                        {/* <!-- Popup Modal --> */}
                    </div>
                    <div id="message_container">
                    <h5 className="text-left mt-3">Chat</h5>
                    <div id="message_box" className="height20vh chat-section mt-1 bg-white"></div>
                    <div className="message-typing">
                        <input className="form-control" onKeyUp={this.handleSendMessage} name="message" placeholder="Enter Your Message..." />
                        <a className="btn btn-primary "  onClick={this.handleSendMessage} > <i className="fa fa-paper-plane" aria-hidden="true"></i></a>
                    </div>
                    </div>
                    </div>
                    {this.props.request.is_drop_location == 1 ? 
                <div  className="col-sm-12 col-md-12 col-lg-6 map-section">
                    <img style={{ boxShadow: "2px 2px 10px #ccc", width:"100%" }} src={ "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=620x380&maptype=terrian&format=png&visual_refresh=true&markers=icon:"+window.url+"/assets/layout/images/common/marker-start.png%7C"+this.props.request.s_latitude+","+this.props.request.s_longitude+"&markers=icon:"+window.url+"/assets/layout/images/common/marker-end.png%7C"+this.props.request.d_latitude+","+this.props.request.d_longitude+"&path=color:0x191919|weight:3|enc:" + this.props.request.route_key + "&key=" + getSiteSettings().site.browser_key } />
                </div> : '' }
                {this.state.cancelModal ? <CancelComponent request={this.props.request} /> : '' }



            </div>
        );
    }
}

class InvoiceComponent extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            commentLength:''
        };
    }

    componentDidMount() {
        //if(this.props.request.status == "COMPLETED") $("#rating").modal();
    }

    componentDidUpdate(prevProps) {
        //if(this.props.request.status == "COMPLETED") $("#rating").modal();
    }

    handlePayment = e => {
        
        let id = this.props.request.id;

        $.ajax({
            url: getBaseUrl() + "/provider/transport/payment",
            type: "post",
            data: {
                id
            },
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            beforeSend: function() {
                showLoader();
            },
            success: (data, textStatus, jqXHR) => {
                hideLoader();
                $('#rating').modal()
            },
            error: (jqXHR, textStatus, errorThrown) => { hideLoader(); }
        });
    };

    handleRating = e => {
        let id = this.props.request.id;
        let rating = $("input[name=rating]:checked").val();
        let comment = $("textarea[name=comment]").val();
        let admin_service = this.props.provider.service.admin_service;
        
        $.ajax({
            url: getBaseUrl() + "/provider/rate/ride",
            type: "post",
            data: {
                id, rating, comment, admin_service
            },
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            beforeSend: function() {
                showLoader();
            },
            success: (data, textStatus, jqXHR) => {
                $("#rating").modal("hide");
                hideLoader();
                window.location.replace("/provider/home");
            },
            error: (jqXHR, textStatus, errorThrown) => { 
                this.setState({
                    commentLength:jqXHR.responseJSON.message
                });
                hideLoader(); 
            }
        });
    };

    render() {
        

        return (
            <div className="row">
                    <div id="invoice" className="dis-column col-xs-12 col-sm-12 col-md-12 col-lg-6">
                        <div className="col-md-12 col-sm-12 col-lg-12 p-0">
                            <h4 className="text-center">Invoice</h4>
                            <input className="form-control" type="text" name="pickup-location" value={this.props.request.s_address} placeholder="Pickup" readOnly="readonly" required />
                            <input className="form-control" type="text" name="drop-location" value={this.props.request.d_address}  placeholder="Destination" readOnly="readonly" required />
                            <div className="form-register">
                                <div className="steps clearfix">
                                    <ul role="tablist">
                                        <li id="arrived_tab" role="tab" aria-disabled="false" className={(this.props.request.status == "ARRIVED" || this.props.request.status == "PICKEDUP" || this.props.request.status == "DROPPED" || this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="true">
                                            <a id="form-total-t-0" aria-controls="form-total-p-0">
                                                <span className="current-info audible"> </span>
                                                <div className="title">
                                                    <span className="step-icon"><img src="/assets/layout/images/transport/svg/map.svg" /></span>
                                                    <span className="step-text">Arrived</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li id="picked_tab" role="tab" aria-disabled="false" className={(this.props.request.status == "PICKEDUP" || this.props.request.status == "DROPPED" || this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="false">
                                            <a id="form-total-t-1" aria-controls="form-total-p-1">
                                                <div className="title">
                                                    <span className="step-icon"><img src="/assets/layout/images/transport/svg/taxi.svg" /></span>
                                                    <span className="step-text">Picked Up</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li id="reached_tab" role="tab" aria-disabled="false" className={(this.props.request.status == "PICKEDUP" || this.props.request.status == "DROPPED" || this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="false">
                                            <a id="form-total-t-2" aria-controls="form-total-p-2">
                                                <div className="title">
                                                    <span className="step-icon"><img src="/assets/layout/images/transport/svg/destination.svg" /></span>
                                                    <span className="step-text">Reached</span>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <form id="ride_creation">
                                <dl className="dl-horizontal left-right mt-5">
                                    <dt>Booking ID</dt>
                                    <dd>{this.props.request.booking_id}</dd>
                                    {/* <dt>Total Distance</dt>
                                    <dd>{this.props.request.distance}</dd> */}
                                    {/* <dt>ETA</dt>
                                    <dd>{this.props.request.travel_time}</dd> */}
                                    <hr />
                                    <dt>Total</dt>
                                    <dd id="total_amount">{this.props.request.currency}{this.props.request.payment.payable}</dd>
                                    <hr />
                                </dl>
                                <br />
                                <div className="col-lg-12 col-md-12 col-sm-12 p-0 invoice-payment">
                                    <div className="total-amount">
                                    <span>{this.props.request.currency}{this.props.request.payment.payable}</span>
                                    </div>
                                    <div className="col-lg-10 col-md-10 col-sm-10">
                                    <h5>Payment Mode</h5>
                                    <h4>{this.props.request.payment_mode}</h4>
                                    {/* <select className="form-control" name="payment" id="select_payment">
                                        <option value="en">Cash</option>
                                        <option value="ar">Card</option>
                                    </select> */}
                                    </div>
                                    {/* <!-- <div className="col-lg-1 col-md-1 col-sm-1 p-0 invoice-credit-card">
                                    <img src="../img/svg/credit-card.svg" />
                                    </div> --> */}
                                </div>
                                {this.props.request.payment_mode == "CASH" ? <a id="confirm-invoice" onClick={this.handlePayment} className="btn btn-primary btn-block">Paid <i className="fa fa-check" aria-hidden="true"></i></a> : <a id="confirm-invoice" data-toggle="modal" data-target="#rating" className="btn btn-primary btn-block">Rate User <i className="fa fa-check" aria-hidden="true"></i></a> }
                                
                            </form>
                        </div>
                        {/* <!-- Rating Modal --> */}
                        <div className="modal" id="rating">
                            <div className="modal-dialog">
                                <div className="modal-content">
                                    {/* <!-- Rating Header --> */}
                                    <div className="modal-header">
                                    <h4 className="modal-title">user Rating</h4>
                                    <button type="button" className="close" data-dismiss="modal">x</button>
                                    </div>
                                    {/* <!-- Rating body --> */}
                                    <div className="modal-body">
                                    <div className="dis-column col-lg-12 col-md-12 col-sm-12 p-0 ">
                                        <div className="trip-user dis-column w-100">
                                            <div className="user-img" style={{backgroundImage: 'url('+this.props.request.user.picture+')' }}></div>
                                            <h5>{this.props.request.user.first_name} {this.props.request.user.last_name}</h5>
                                            <div className="rating-outer">
                                                <span style={{cursor: 'default'}}>
                                                { this.props.request.user.rating }
                                                <div className="rating-symbol" style={{display: 'inline-block', position: 'relative'}}>
                                                    <div className="fa fa-star-o" style={{visibility: 'hidden'}}></div>
                                                    <div className="rating-symbol-foreground" style={{display: 'inline-block', position: 'absolute', overflow: 'hidden', left: '0px', right: '0px', top: '0', width: 'auto'}}><span className="fa fa-star" aria-hidden="true"></span></div>
                                                </div>
                                                </span>
                                                <input type="hidden" className="rating" value="1" disabled="disabled" />
                                            </div>
                                        </div>
                                        <div className="trip-user w-100 dis-column">
                                            <h5>Rate Your Customer</h5>
                                            <div className="rating-outer">
                                                <fieldset className="rating">
                                                        <input type="radio" id="star5" name="rating" value="5" /><label className = "full" htmlFor="star5" ></label>
                                                        <input type="radio" id="star4" name="rating" value="4" /><label className = "full" htmlFor="star4" ></label>
                                                        <input type="radio" id="star3" name="rating" value="3" /><label className = "full" htmlFor="star3" ></label>
                                                        <input type="radio" id="star2" name="rating" value="2" /><label className = "full" htmlFor="star2" ></label>
                                                        <input type="radio" defaultChecked="checked" id="star1" name="rating" value="1" /><label className = "full" htmlFor="star1" ></label>
                                                    </fieldset>
                                            </div>
                                            <p></p>
                                        </div>
                                        <div className="comments-section field-box mt-3">
                                            <textarea className="form-control" maxLength="255" rows="4" cols="50" name="comment" placeholder="Leave Your Comments..."></textarea>
                                            <small>(Maximum characters: 255)</small>
                                            <span style={{color:"red"}}>{this.state.commentLength}</span>
                                        </div>
                                    </div>
                                    </div>
                                   {/*  <!-- Rating footer --> */}
                                    <div className="modal-footer">
                                    <a className="btn btn-primary btn-block" onClick={this.handleRating}>Submit <i className="fa fa-check-square" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {/* <!-- Rating Modal --> */}
                        </div>
                        { this.props.request.is_drop_location == 1 ?  <div  className="col-sm-12 col-md-12 col-lg-6 map-section">
                        <img style={{ boxShadow: "2px 2px 10px #ccc",width:"100%" }} src={ "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=620x380&maptype=terrian&format=png&visual_refresh=true&markers=icon:"+window.url+"/assets/layout/images/common/marker-start.png%7C"+this.props.request.s_latitude+","+this.props.request.s_longitude+"&markers=icon:"+window.url+"/assets/layout/images/common/marker-end.png%7C"+this.props.request.d_latitude+","+this.props.request.d_longitude+"&path=color:0x191919|weight:3|enc:" + this.props.request.route_key + "&key=" + getSiteSettings().site.browser_key } />
                    </div> : '' }
            </div>
        );
    }
}


class NoServiceComponent extends React.Component {

    render() {
        return <div className="bg-body col-xs-12 col-sm-12 col-md-12 col-lg-12 no-service dis-center p-5">
                <h5>No Services Available</h5>
            </div>;
    }
}

class CancelComponent extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            reasons: [],
            reason: '',
            error_message:''
        };
    }

    componentDidMount() {

        $.ajax({
            url: getBaseUrl() + "/provider/reasons?type=TRANSPORT",
            type: "get",
            data: { },
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            beforeSend: function() {
                showLoader();
            },
            success: (response, textStatus, jqXHR) => {
                var data = parseData(response);
                var reasons = [];
                if (Object.keys(data.responseData).length > 0) {
                    var result = data.responseData;
                    for(var i in result) {
                        reasons.push({'id': result[i].id, 'reason': result[i].reason});
                    }

                    this.setState({ reasons });
                    
                }
                hideLoader();
            },
            error: (jqXHR, textStatus, errorThrown) => {}
        });

        $("#cancelModal").modal();
    }



    handleCancel = e => {
        
        (this.state.reason == '') ? this.setState({error_message: 'Cancel reason is required!'}) : this.setState({error_message: ''});
        let id = this.props.request.id;
        let reason = this.state.reason;

        if(this.state.reason != '') {
            $.ajax({
                url: getBaseUrl() + "/provider/cancel/ride/request",
                type: "post",
                data: {
                    id: id,
                    reason: reason
                },
                headers: {
                    Authorization: "Bearer " + getToken("provider")
                },
                success: (data, textStatus, jqXHR) => {
                    $("#cancelModal").modal('hide');
                    if (Object.keys(data.responseData).length > 0) {
                        this.setState({
                            requests: data.responseData[0]
                        });
                    }
                },
                error: (jqXHR, textStatus, errorThrown) => {}
            });
        }
        
    };

    handleChange = e => {
        this.setState({
            reason: e.target.value
        });
    }


    render() {
        return (
            <div className="modal" id="cancelModal">
                <div className="modal-dialog">
                    <div className="modal-content">
                    {/* <!-- Emergency Contact Header --> */}
                    <div className="modal-header">
                        <h5 className="modal-title">Reason For Cancellation</h5>
                        <button type="button" className="close" data-dismiss="modal">&times;</button>
                    </div>
                    {/* <!-- Emergency Contact body --> */}
                    <div className="modal-body">
                        <select className="form-control" name="cancel"  onChange={this.handleChange} id="contact">
                            <option value="">Select Reason</option>
                            {this.state.reasons.map((value, index) => {
                                return <option key={index} value={value.reason}>{value.reason}</option>;
                            })}
                        </select>
                        {(this.state.error_message != '') ?
                        <span id="cancel_reason-error" style={{color:"red"}} >{this.state.error_message}</span>
                        : '' }
                    </div>
                    {/* <!-- Emergency Contact footer --> */}
                    <div className="modal-footer">
                        <a className="btn btn-primary btn-block" onClick={this.handleCancel}>Submit <i className="fa fa-check" aria-hidden="true"></i></a>
                    </div>
                    </div>
                </div>
            </div>
        );
    }
}

ReactDOM.render(<MainComponent />, document.getElementById("root"));
