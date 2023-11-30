class MainComponent extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            requests: [],
            emergency: [],
            status: "",
            time: 0,
        };
    }

    componentDidMount() {
        this.getStatus();
        var id = '';
        var that = this;
        var socket = io.connect(window.socket_url);

        socket.emit('joinCommonUserRoom', `room_${window.room}_${getUserDetails().id}_USER`);

        socket.on('socketStatus', function (data) {
            if(window.env == 'local') console.log(data);
        });

        socket.on('newRequest', function (data) {
            if(window.env == 'local') console.log('newRequest', data);
            if(data.type == 'TRANSPORT') that.getStatus('socket');
        });

        if(getParameterByName('id') != null) {
            $.ajax({
                url: getBaseUrl() + "/user/transport/request/"+getParameterByName('id'),
                type: "get",
                async: false,
                processData: false,
                contentType: false,
                headers: {
                    Authorization: "Bearer " + getToken("user")
                },
                success: (response, textStatus, jqXHR) => {
                    var data = parseData(response);
                    id = Object.keys(data.responseData.data).length > 0
                    ? data.responseData.data[0].id
                    : "";
                },
                error: (jqXHR, textStatus, errorThrown) => {}
            });
        }

        if(id != '') {
            socket.emit('joinPrivateRoom', `room_${window.room}_R${id}_TRANSPORT`);
        }

        socket.on('rideRequest', function (data) {
            if(window.env == 'local') console.log('rideRequest',data);
            that.getStatus('socket');
        });

        //setInterval(() => this.getStatus(), 5000);
    }

    getStatus(isSocket = null) {
        if(getParameterByName('id') != null) { 
            $.ajax({
                url: getBaseUrl() + "/user/transport/request/"+getParameterByName('id'),
                type: "get",
                processData: false,
                contentType: false,
                headers: {
                    Authorization: "Bearer " + getToken("user")
                },
                success: (response, textStatus, jqXHR) => {
                    var data = parseData(response);
                    if(isSocket != null && Object.keys(data.responseData.data).length == 0) {
                        location.assign(window.location.href.split('?')[0]);
                    }

                    if(data.responseData.data.length > 0){
                        var token = data.responseData.data[0].twilio_token.user;
                        window.setupTwilioClient(token.token, token.To, token.outgoing_caller_id);
                    }

                    this.setState({
                        requests:
                            Object.keys(data.responseData.data).length > 0
                                ? data.responseData.data[0]
                                : [],
                        status:
                            Object.keys(data.responseData.data).length > 0
                                ? data.responseData.data[0].status
                                : "",
                        emergency: Object.keys(data.responseData).length > 0
                        ? data.responseData.emergency
                        : [],
                        time: Object.keys(data.responseData).length > 0
                               ? data.responseData.response_time
                                    : 0
                    });
                },
                error: (jqXHR, textStatus, errorThrown) => {}
            });
        }
    }

    render() {
        let status = this.state.status;

        if (status != "COMPLETED") {
            if (($("element").data("bs.modal") || { isShown: false }).isShown == false ) {
                $("#rating").modal("hide");
            }
        }

        if (status != "") {
            if ($(".ride-section").is(":visible")) {
                $(".ride-section").hide();
            }
        } else {
            $(".ride-section").removeClass('d-none');
            $(".ride-section").show();
        }

        if (status === "SEARCHING") {
            return <SearchComponent request={this.state.requests} time={this.state.time} />;
        } else if (
            status === "ACCEPTED" ||
            status === "STARTED" ||
            status === "ARRIVED"
        ) {
            return <AcceptedComponent request={this.state.requests} emergency={this.state.emergency} />;
        } else if (status === "PICKEDUP") {
            return <RidingComponent request={this.state.requests} emergency={this.state.emergency} />;
        } else if (status === "DROPPED" || status === "COMPLETED" ) {
            return <InvoiceComponent request={this.state.requests} />;
        }

        return null;
    }
}

class SearchComponent extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            time: Math.abs(this.props.time),
        };
    }

    componentDidMount() {
        this.timer = setInterval(this.tick.bind(this), 1000)
    }

    componentWillUnmount () {
        clearInterval(this.timer)
    }

    tick () {
        this.setState({time: ((this.state.time > 0) ? this.state.time - 1 : 0)})

        if(this.state.time <= 0) {
            location.assign(window.location.href.split('?')[0]);
        }
    }

    handleCancel = e => {
        let id = this.props.request.id;

        $.ajax({
            url: getBaseUrl() + "/user/transport/cancel/request",
            type: "post",
            data: {
                id: id
            },
            headers: {
                Authorization: "Bearer " + getToken("user")
            },
            beforeSend: function() {
                showLoader();
            },
            success: (response, textStatus, jqXHR) => {
                hideLoader();
                $("#booking-status").hide();
                var data = parseData(response);
                if (Object.keys(data.responseData).length > 0) {
                    this.setState({
                        requests: data.responseData[0]
                    });
                    initMap();
                }
                location.assign(window.location.href.split('?')[0]);
            },
            error: (jqXHR, textStatus, errorThrown) => {
                $("#booking-status").show();
                alertMessage(textStatus, jqXHR.responseJSON.message, "danger");
            }
        });
    };

    render() {
        return (
            <div id="ride-status" className="tab-pane active col-sm-12 col-md-12 col-lg-12">
                <div className="status-section bg-white p-3">
                    <div className="w-100" id="booking-status">
                        <div className="col-md-12 col-sm-12 col-lg-12 p-0 dis-row align-items-start">
                            <div className="col-md-6 col-sm-12 col-lg-6 p-0">
                                <div className="col-md-12 col-sm-12 col-lg-12 p-0">
                                    <h4 className="text-center">Finding your Driver</h4>
                                    <div className="col-md-12 col-lg-12 col-sm-12 p-0">
                                        <form>
                                            <div className="status mt-4">
                                                <h6>Status</h6>
                                                <div className="pulse">
                                                    <div />
                                                    <div />
                                                    <div />
                                                    <div />
                                                </div>
                                                <p>Finding Your Driver</p>
                                            </div>
                                            <a className="btn btn-primary " onClick={this.handleCancel}> Cancel Request <i className="fa fa-ban" aria-hidden="true" /> </a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-6 col-sm-6 col-lg-6">
                                <dl className="dl-horizontal left-right mt-1">
                                    <dt>Request Id</dt>
                                    <dd>{this.props.request.booking_id}</dd>
                                </dl>
                                <div>
                                    <img style={{ boxShadow: "2px 2px 10px #ccc" }} src={ "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=620x380&maptype=terrian&format=png&visual_refresh=true&markers=icon:"+window.url+"/assets/layout/images/common/marker-start.png%7C"+this.props.request.s_latitude+","+this.props.request.s_longitude+"&markers=icon:"+window.url+"/assets/layout/images/common/marker-end.png%7C"+this.props.request.d_latitude+","+this.props.request.d_longitude+"&path=color:0x191919|weight:3|enc:" + this.props.request.route_key + "&key=" + getSiteSettings().site.browser_key } />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

class AcceptedComponent extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            cancelModal: false,
            emergencyModal: false
        };
    }

    componentDidMount() {

        var chatSocket = io.connect(window.socket_url);
        var id = this.props.request.id;
        var user_id = this.props.request.user_id;
        var provider_id = this.props.request.provider_id;
        var admin_service = 'TRANSPORT';

        var chat_data = this.props.request.chat;

        if(chat_data != null) {
            for(var chat of this.props.request.chat.data) {
                if(chat.type == "user") {
                    $('#message_box').append(`<div class="user-msg"><span class="msg">${chat.message}</span></div>`);
                } else if(chat.type == "provider") {
                    $('#message_box').append(`<div class="provider-msg"><span class="msg">${chat.message}</span></div>`);
                }
            }
        }

        var message_box  = $('body').find('#message_box');
        var height = message_box[0].scrollHeight;
        message_box.scrollTop(height);

    	chatSocket.emit('joinPrivateChatRoom', `room_${window.room}_R${id}_U${user_id}_P${provider_id}_${admin_service}`);
        if(typeof chatSocket._callbacks.new_message == "undefined") {
            chatSocket.on('new_message', function (data) {
                if(data.type == "user") {
                    $('#message_box').append(`<div class="user-msg"><span class="msg">${data.message}</span></div>`);
                    //$('#message_box').append(`<span style='width:100%; float: left;'>${data.user}: ${data.message}</span>`);
                } else if(data.type == "provider") {
                    $('#message_box').append(`<div class="provider-msg"><span class="msg">${data.message}</span></div>`);
                    //$('#message_box').append(`<span style='width:100%; float: left;'>${data.provider}: ${data.message}</span>`);
                } 

                var message_box  = $('body').find('#message_box');
                var height = message_box[0].scrollHeight;
                message_box.scrollTop(height);
            });
        }

    }

    handleCancelModal = e => {
        this.setState({cancelModal:true});
    }

    handleEmergencyModal = e => {
        this.setState({emergencyModal:true});
    }

    handleSendMessage = e => {
        if((e.which == 13 || e.which === undefined) && $('input[name=message]').val() != '') {
            var message = $('input[name=message]');

            var chatSocket = io.connect(window.socket_url);
            var request_id = this.props.request.id;
            var user_id = this.props.request.user_id;
            var provider_id = this.props.request.provider_id;
            var admin_service = 'TRANSPORT';
            var user = `${this.props.request.user.first_name} ${this.props.request.user.last_name}`;
            var provider = `${this.props.request.provider.first_name} ${this.props.request.provider.last_name}`;

            chatSocket.emit('send_message', {room: `room_${window.room}_R${request_id}_U${user_id}_P${provider_id}_${admin_service}`, url: getBaseUrl() + "/chat", salt_key: window.salt_key, id: request_id, admin_service: admin_service, user: user, provider: provider, type: 'user',message:  message.val() });
            
            message.val('');
        }
    }

    render() {
        let rideStatus = "";

        if (this.props.request.status === "ACCEPTED") {
            rideStatus = "Driver accepted your status";
        } else if (this.props.request.status === "STARTED") {
            rideStatus = "Driver accepted the ride";
        } else if (this.props.request.status === "ARRIVED") {
            rideStatus = "Driver reached your location";
        }
        var rows=[];
        var starcount = this.props.request.provider.rating;
        for (var i=0;i<starcount;i++){
            rows.push(<div className="rating-symbol" key={i} style={{display: 'inline-block', position: 'relative'}}>
            <div className="fa fa-star-o" style={{visibility: 'hidden'}}></div>
            <div className="rating-symbol-foreground" style={{display: 'inline-block', position: 'absolute', overflow: 'hidden', left: '0px', right: '0px', top: '0px', width: 'auto'}}>
                <span className="fa fa-star" aria-hidden="true"></span>
            </div>
        </div>);
        }
        return (
            <div>
            <div id="accepted-status" className="tab-pane active col-sm-12 col-md-12 col-lg-12">
                <div className="status-section bg-white p-3">
                    <div className="w-100" id="booking-status">
                        <div className="col-md-12 col-sm-12 col-lg-12 p-0 dis-row  align-items-start">
                            <div className="col-md-6 col-sm-12 col-lg-6 p-0">
                                <div className="col-md-12 col-sm-12 col-lg-12 p-0">
                                <div className="form-register">
                                    <div className="steps clearfix">
                                        <ul role="tablist">
                                            <li id="arrived_tab" className={(this.props.request.status == "ARRIVED" || this.props.request.status == "PICKEDUP" || this.props.request.status == "DROPPED" || this.props.request.status == "COMPLETED")   ? "current" : ""} >
                                                <a id="form-total-t-0" href="#form-total-h-0">
                                                <span className="current-info audible"> </span>
                                                <div className="title">
                                                    <span className="step-icon"><img src="/assets/layout/images/transport/svg/map.svg" /></span>
                                                    <span className="step-text">Arrived</span>
                                                </div>
                                                </a>
                                            </li>
                                            <li id="picked_tab" className={(this.props.request.status == "PICKEDUP" || this.props.request.status == "DROPPED" || this.props.request.status == "COMPLETED")   ? "current" : ""} >
                                                <a id="form-total-t-1" href="#form-total-h-1">
                                                <div className="title">
                                                    <span className="step-icon"><img src="/assets/layout/images/transport/svg/taxi.svg" /></span>
                                                    <span className="step-text">Picked Up</span>
                                                </div>
                                                </a>
                                            </li>
                                            <li id="reached_tab"  className={( this.props.request.status == "DROPPED" || this.props.request.status == "COMPLETED")   ? "current" : ""}>
                                                <a id="form-total-t-2" href="#form-total-h-2">
                                                <div className="title">
                                                    <span className="step-icon"><img src="/assets/layout/images/transport/svg/destination.svg" /></span>
                                                    <span className="step-text">Reached</span>
                                                </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    </div>
                                </div>


                                <div className="col-md-12 col-sm-12 col-lg-12 p-0">
                                <h5 className="text-left">Status {/* <span className="pull-right font-12 c-pointer txt-yellow dis-row"> <i className="material-icons mr-1">my_location</i> Track</span> */}</h5>
                                    <div className="col-md-12 col-lg-12 col-sm-12 p-0">
                                        <form>
                                            <div className="status mt-4">
                                                <h6>{rideStatus}</h6>
                                                {this.props.request.peak == 1 ? <p> Due to peak hours, charges are based on providers availability </p> : ''}
                                            </div>
                                            <div>
                                                { getSiteSettings().transport.ride_otp==1 ?<span className="otp"> OTP : {this.props.request.otp} </span> : ''}
                                                <span onClick={this.handleEmergencyModal} data-toggle="modal" data-target="#emergencyModal" style={{marginBottom: '10px'}} className="pull-right c-pointer txt-yellow dis-row mt-2"> <i className="material-icons">phone</i> Emergency Contact</span>
                                                <span onClick={window.callModal.bind()} data-toggle="modal" data-target="#callModal" style={{marginBottom: '10px'}} className="pull-left call-driver-button c-pointer txt-yellow dis-row mt-2"> <i className="material-icons">phone</i> Call Driver</span>
                                            </div>
                                            <div className="trip-user dis-ver-center col-lg-12 col-md-12 col-sm-12 mb-4">
                                                <div className="dis-column col-lg-6 col-md-6 col-sm-6 p-0">
                                                    <div className="user-img" style={{ backgroundImage: "url(" + this.props .request .provider .picture + ")" }} />
                                                    <h5> { this.props.request.provider.first_name } { this.props.request.provider.last_name } </h5>
                                                    <p> <a href={'tel:'+this.props.request.provider.country_code+this.props.request.provider.mobile }>{ this.props.request.provider.country_code } { this.props.request.provider.mobile }</a>  </p>
                                                    <div className="rating-outer">
                                                        <span style={{cursor: 'default'}}>
                                                        {rows}
                                                        </span>
                                                        <input type="hidden" className="rating" value="1" disabled="disabled" />
                                                    </div>
                                                </div>
                                                <div className="dis-column col-lg-6 col-md-6 col-sm-6 p-0">
                                                    <div className="car-img">
                                                        <img width="100" className="" src={ this.props .request.ride.vehicle_image } alt={ this.props.request.ride.vehicle_name } />
                                                    </div>
                                                    <h5>{ this.props.request.service_type.vehicle.vehicle_no } </h5>
                                                    <div className="rating-outer">
                                                        <p style={{ cursor: "default" }}>
                                                            { this.props.request.ride.vehicle_name }
                                                        </p>
                                                        <input type="hidden" className="rating" value="1" disabled="disabled" />
                                                    </div>
                                                </div>
                                            </div>
                                            <a data-toggle="modal" data-target="#cancelModal" className="btn btn-primary " onClick={this.handleCancelModal} >Cancel <i className="fa fa-ban" aria-hidden="true"></i></a>
                                        </form>

                                        <div id="message_container">

                    <h5 className="text-left mt-3">Chat</h5>
                    <div id="message_box" className="height20vh chat-section mt-1"></div>
                    <div className="message-typing">
                        <input className="form-control" onKeyUp={this.handleSendMessage} name="message" placeholder="Enter Your Message..." />
                        <a className="btn btn-primary "  onClick={this.handleSendMessage} > <i className="fa fa-paper-plane" aria-hidden="true"></i></a>
                    </div>
            
            </div>


                                    </div>
                                </div>
                            </div>
                            <div className="col-md-6 col-sm-6 col-lg-6">
                                <dl className="dl-horizontal left-right mt-1">
                                    <dt>Request Id</dt>
                                    <dd>{this.props.request.booking_id}</dd>
                                    <dt>Time</dt>
                                    <dd>
                                        {this.props.request.assigned_time}
                                    </dd>
                                </dl>
                                <div>
                                    
                                    { this.props.request.is_drop_location == 1 ? <img style={{ boxShadow: "2px 2px 10px #ccc" }} src={ "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=620x380&maptype=terrian&format=png&visual_refresh=true&markers=icon:"+window.url+"/assets/layout/images/common/marker-start.png%7C"+this.props.request.s_latitude+","+this.props.request.s_longitude+"&markers=icon:"+window.url+"/assets/layout/images/common/marker-end.png%7C"+this.props.request.d_latitude+","+this.props.request.d_longitude+"&path=color:0x191919|weight:3|enc:" + this.props.request.route_key + "&key=" + getSiteSettings().site.browser_key } /> : '' }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {this.state.cancelModal ? <CancelComponent request={this.props.request} /> : '' }
            {this.state.emergencyModal ? <EmergencyComponent emergency={this.props.emergency} /> : '' }
            
            
            </div>
        );
    }
}

class RidingComponent extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            emergencyModal: false
        };
    }

    componentDidMount() {

        var chatSocket = io.connect(window.socket_url);
        var id = this.props.request.id;
        var user_id = this.props.request.user_id;
        var provider_id = this.props.request.provider_id;
        var admin_service = 'TRANSPORT';

        var chat_data = this.props.request.chat;

        if(chat_data != null) {
            for(var chat of this.props.request.chat.data) {
                if(chat.type == "user") {
                    $('#message_box').append(`<div class="user-msg"><span class="msg">${chat.message}</span></div>`);
                } else if(chat.type == "provider") {
                    $('#message_box').append(`<div class="provider-msg"><span class="msg">${chat.message}</span></div>`);
                }
            }
        }

        var message_box  = $('body').find('#message_box');
        var height = message_box[0].scrollHeight;
        message_box.scrollTop(height);

    	chatSocket.emit('joinPrivateChatRoom', `room_${window.room}_R${id}_U${user_id}_P${provider_id}_${admin_service}`);
        
        if(typeof chatSocket._callbacks.new_message == "undefined") {
            chatSocket.on('new_message', function (data) {
                if(data.type == "user") {
                    $('#message_box').append(`<div class="user-msg"><span class="msg">${data.message}</span></div>`);
                    //$('#message_box').append(`<span style='width:100%; float: left;'>${data.user}: ${data.message}</span>`);
                } else if(data.type == "provider") {
                    $('#message_box').append(`<div class="provider-msg"><span class="msg">${data.message}</span></div>`);
                    //$('#message_box').append(`<span style='width:100%; float: left;'>${data.provider}: ${data.message}</span>`);
                } 

                var height = message_box[0].scrollHeight;
                message_box.scrollTop(height);
            });
        }

        //initializeTrackMap();
    }

    handleEmergencyModal = e => {
        this.setState({emergencyModal:true});
    }

    handleSendMessage = e => {
        if((e.which == 13 || e.which === undefined) && $('input[name=message]').val() != '') {
            var message = $('input[name=message]');

            var chatSocket = io.connect(window.socket_url);
            var request_id = this.props.request.id;
            var user_id = this.props.request.user_id;
            var provider_id = this.props.request.provider_id;
            var admin_service = 'TRANSPORT';
            var user = `${this.props.request.user.first_name} ${this.props.request.user.last_name}`;
            var provider = `${this.props.request.provider.first_name} ${this.props.request.provider.last_name}`;

            chatSocket.emit('send_message', {room: `room_${window.room}_R${request_id}_U${user_id}_P${provider_id}_${admin_service}`, url: getBaseUrl() + "/chat", salt_key: window.salt_key, id: request_id, admin_service: admin_service, user: user, provider: provider, type: 'user', message:  message.val() });

            
            message.val('');
        }
    }

    render() {
        let rideStatus = "";

        if (this.props.request.status === "ACCEPTED") {
            rideStatus = "Driver accepted your status";
        } else if (this.props.request.status === "STARTED") {
            rideStatus = "Driver started the ride";
        } else if (this.props.request.status === "ARRIVED") {
            rideStatus = "Driver reached your location";
        }

        return (
            <div><div
                id="accepted-status"
                className="tab-pane active col-sm-12 col-md-12 col-lg-12">
                <div className="status-section bg-white p-3">
                    <div className="w-100" id="booking-status">
                        <div className="col-md-12 col-sm-12 col-lg-12 p-0 dis-row  align-items-start">
                            <div className="col-md-6 col-sm-12 col-lg-6 p-0">

                            <div className="col-md-12 col-sm-12 col-lg-12 p-0">
                                <div className="form-register">
                                    <div className="steps clearfix">
                                        <ul role="tablist">
                                            <li id="arrived_tab" className={(this.props.request.status == "ARRIVED" || this.props.request.status == "PICKEDUP" || this.props.request.status == "DROPPED" || this.props.request.status == "COMPLETED")   ? "current" : ""} >
                                                <a id="form-total-t-0" href="#form-total-h-0">
                                                <span className="current-info audible"> </span>
                                                <div className="title">
                                                    <span className="step-icon"><img src="/assets/layout/images/transport/svg/map.svg" /></span>
                                                    <span className="step-text">Arrived</span>
                                                </div>
                                                </a>
                                            </li>
                                            <li id="picked_tab" className={(this.props.request.status == "PICKEDUP" || this.props.request.status == "DROPPED" || this.props.request.status == "COMPLETED")   ? "current" : ""} >
                                                <a id="form-total-t-1" href="#form-total-h-1">
                                                <div className="title">
                                                    <span className="step-icon"><img src="/assets/layout/images/transport/svg/taxi.svg" /></span>
                                                    <span className="step-text">Picked Up</span>
                                                </div>
                                                </a>
                                            </li>
                                            <li id="reached_tab" className="third">
                                                <a id="form-total-t-2" href="#form-total-h-2">
                                                <div className="title">
                                                    <span className="step-icon"><img src="/assets/layout/images/transport/svg/destination.svg" /></span>
                                                    <span className="step-text">Reached</span>
                                                </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    </div>
                                </div>

                                <div className="col-md-12 col-sm-12 col-lg-12 p-0">
                                <h5 className="text-left">Status {/* <span className="pull-right font-12 c-pointer txt-yellow dis-row"> <i className="material-icons mr-1">my_location</i> Track</span> */}</h5>
                                    <div className="col-md-12 col-lg-12 col-sm-12 p-0">
                                        <form>
                                            <div className="status mt-4">
                                                <h6>On Ride</h6>
                                                {this.props.request.peak == 1 ? <p> Due to peak hours, charges are based on providers availability </p> : '' }
                                            </div>
                                            <div>
                                                <span style={{padding: '5px 15px 6px'}}></span>
                                                <span onClick={this.handleEmergencyModal} data-toggle="modal" data-target="#emergencyModal" style={{marginBottom: '10px'}} className="pull-right c-pointer txt-yellow dis-row mt-2"> <i className="material-icons">phone</i> Emergency Contact</span>
                                                <span onClick={window.callModal.bind()} data-toggle="modal" data-target="#callModal" style={{marginBottom: '10px'}} className="pull-left call-driver-button c-pointer txt-yellow dis-row mt-2"> <i className="material-icons">phone</i> Call Driver</span>
                                            </div>
                                            <div className="trip-user dis-ver-center col-lg-12 col-md-12 col-sm-12 mb-4">
                                                <div className="dis-column col-lg-6 col-md-6 col-sm-6 p-0">
                                                    <div className="user-img" style={{ backgroundImage: "url(" + this.props.request.provider.picture +")" }} />
                                                    <h5>
                                                        { this.props.request.provider.first_name } { this.props.request.provider.last_name }
                                                    </h5>

                                                    <div className="rating-outer">
                                                        <span style={{ cursor: "default" }} >
                                                            { (this.props.request.provider.rating).toFixed(2) }
                                                            <div className="rating-symbol" style={{ display: "inline-block", position: "relative" }} >
                                                                <div className="fa fa-star-o" style={{ visibility: "hidden" }} />
                                                                <div className="rating-symbol-foreground" style={{ display: "inline-block", position: "absolute", overflow: "hidden", left: "0px", right: "0px", top: "0px", width: "auto" }} >
                                                                    <span className="fa fa-star" aria-hidden="true" />
                                                                </div>
                                                            </div>
                                                        </span>
                                                        <input type="hidden" className="rating" value="1" disabled="disabled" />
                                                    </div>
                                                </div>
                                                <div className="dis-column col-lg-6 col-md-6 col-sm-6 p-0">
                                                    <div className="car-img">
                                                        <img width="100" src={this.props.request.ride.vehicle_image} alt={this.props.request.ride.vehicle_name} />
                                                    </div>
                                                    <h5>
                                                        { this.props.request.service_type.vehicle.vehicle_no }
                                                    </h5>
                                                    <div className="rating-outer">
                                                        <p style={{ cursor: "default" }}  >
                                                            { this.props.request.ride.vehicle_name }
                                                        </p>
                                                        <input type="hidden" className="rating" value="1" disabled="disabled" />
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div id="message_container">

                    <h5 className="text-left mt-3">Chat</h5>
                    <div id="message_box" className="height20vh chat-section mt-1"></div>
                    <div className="message-typing">
                        <input className="form-control" onKeyUp={this.handleSendMessage} name="message" placeholder="Enter Your Message..." />
                        <a className="btn btn-primary "  onClick={this.handleSendMessage} > <i className="fa fa-paper-plane" aria-hidden="true"></i></a>
                    </div>
            
            </div>
                            </div>
                            <div className="col-md-6 col-sm-6 col-lg-6">
                                <dl className="dl-horizontal left-right mt-1">
                                    <dt>Request Id</dt>
                                    <dd>{this.props.request.booking_id}</dd>
                                    <dt>Time</dt>
                                    <dd>
                                        { this.props.request.assigned_time }
                                        
                                    </dd>
                                </dl>
                                <div>
                                {this.props.request.is_drop_location == 1 ? <div id="track_map" style={{ width: "100%", height: "400px", boxShadow: "2px 2px 10px #ccc" }} ><img style={{ boxShadow: "2px 2px 10px #ccc" }} src={ "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=620x380&maptype=terrian&format=png&visual_refresh=true&markers=icon:"+window.url+"/assets/layout/images/common/marker-start.png%7C"+this.props.request.s_latitude+","+this.props.request.s_longitude+"&markers=icon:"+window.url+"/assets/layout/images/common/marker-end.png%7C"+this.props.request.d_latitude+","+this.props.request.d_longitude+"&path=color:0x191919|weight:3|enc:" + this.props.request.route_key + "&key=" + getSiteSettings().site.browser_key } /></div> : '' }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {this.state.emergencyModal ? <EmergencyComponent emergency={this.props.emergency} /> : '' }
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
    	if(this.props.request.paid === 1 && this.props.request.payment_mode === "CASH") {
           $("#rating").modal();
        }
    }

    componentDidUpdate(prevProps) {
        if(this.props.request.paid === 1 && this.props.request.payment_mode === "CASH") {
            $("#rating").modal();
         }
    }
    

    handlePayment = () => {

         let id = this.props.request.id;
         let total=this.props.request.payment.total;
         var data = new FormData();

        var handler = PaystackPop.setup({ 
            key: window.public_key, 
            email: window.email,
            amount: parseInt(this.props.request.payment.total) * parseInt(100),
            metadata: {
                custom_fields: [
                    {
                        display_name: "Mobile Number",
                        variable_name: "mobile_number",
                        value: window.mobile 
                    }
                ]
            },
            callback: function (response) {
                data.append('id',id);
                data.append('reference',response.reference);
                console.log(data);
                
               $.ajax({
                  type:'POST',
                  url: getBaseUrl() + "/user/transport/payment",
                  data: data,
                  processData: false,
                  contentType: false,
                  headers: {
                        Authorization: "Bearer " + getToken("user")
                  },
                  beforeSend: function (request) {
                    showLoader();
                   },
                   success: (data, textStatus, jqXHR) => {
                    $("#rating").modal();
                    $('#confirm-invoice').text('Paid');
                    $('#confirm-invoice').removeAttr('id', '#confirm-invoice');
                    hideLoader();
                },
                error: (jqXHR, textStatus, errorThrown) => { hideLoader(); }
               
               });
                
           
            },
            onClose: function () {
                //when the user close the payment modal
                alert('Transaction cancelled');
                return false;
            }
        });
        handler.openIframe();
    }



    handleRating = e => {
        let id = this.props.request.id;
        let rating = $("input[name=rating]:checked").val();
        let comment = $("textarea[name=comment]").val();
        let admin_service = this.props.request.service_type.admin_service;

        $.ajax({
            url: getBaseUrl() + "/user/transport/rate",
            type: "post",
            headers: {
                Authorization: "Bearer " + getToken("user")
            },
            beforeSend: function() {
                showLoader();
            },
            data: {
                id, rating, comment, admin_service
            },
            success: (data, textStatus, jqXHR) => {
                $("#rating").modal('hide');
                $(".ride-section").removeClass('d-none');
                $("#ride-book").removeClass('d-none');
                $("#confirm-ride").addClass('d-none');
                $(".ride-section").show();
                $("#root").html("");
                hideLoader();
                initMap();
                location.assign(window.location.href.split('?')[0]);
                
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
            <div id="invoice-section" className="tab-pane active dis-column">
                <div className="col-md-12 col-sm-12 col-lg-12 p-0">
                    <h4 className="text-center">Invoice</h4>
                    <div className="field-box ">
                        <input className="form-control" type="text" placeholder={this.props.request.s_address} readOnly />
                    </div>
                </div>
                <div className="col-md-12 col-sm-12 col-lg-12 p-0">
                    <div className="field-box">
                        <input
                            className="form-control" type="text" placeholder={this.props.request.d_address} readOnly />
                    </div>
                </div>
                <div className="col-md-12 col-sm-12 col-lg-12 p-0">
                    <form id="ride_creation">
                        <dl className="dl-horizontal left-right mt-5">
                            {this.props.request.booking_id ? (
                                <span>
                                    <dt>Booking ID</dt>
                                    <dd>{this.props.request.booking_id}</dd>
                                </span>
                            ) : ( "" )}

                            {this.props.request.service_type ? (
                                <span>
                                    <dt>Service Type</dt>
                                    <dd>
                                        { this.props.request.ride.vehicle_name }
                                    </dd>
                                </span>
                            ) : ( "" )}

                            {this.props.request.provider ? (
                                <span>
                                    <dt>Driver Name</dt>
                                    <dd>
                                        {this.props.request.provider.first_name} {this.props.request.provider.last_name}
                                    </dd>
                                </span>
                            ) : ( "" )}

                            {this.props.request.payment_mode ? (
                                <span>
                                    <dt>Payment Mode</dt>
                                    <dd>{this.props.request.payment_mode}</dd>
                                </span>
                            ) : ( "" )}

                            <hr />

                            {this.props.request.distance ? (
                                <span>
                                    <dt>Total Distance</dt>
                                    <dd>{(this.props.request.distance).toFixed( 2 )} Kms</dd>
                                </span>
                            ) : ( "" )}

                            {this.props.request.payment ? (
                                <span>
                                    <dt>Base Fare</dt>
                                    <dd>{this.props.request.currency}{ (this.props.request.payment.fixed).toFixed( 2 ) }</dd>
                                </span>
                            ) : ( "" )}

                            {this.props.request.payment.distance ? (
                                <span>
                                    <dt>Distance Fare</dt>
                                    <dd>
                                    { this.props.request.currency }{ (this.props.request.payment.distance).toFixed( 2 ) }
                                    </dd>
                                </span>
                            ) : ( "" )}

                            {this.props.request.payment.discount ? (
                                <span>
                                    <dt>Promocode</dt>
                                    <dd>{this.props.request.currency}{ (this.props.request.payment.discount).toFixed( 2 ) }</dd>
                                </span>
                            ) : (
                                ""
                            )}

                            {this.props.request.payment.toll_charge ? (
                                <span>
                                    <dt>Toll Charge</dt>
                                    <dd>{this.props.request.currency}{ (this.props.request.payment.toll_charge).toFixed( 2 ) }</dd>
                                </span>
                            ) : ( "" )}

                            {this.props.request.payment.tax ? (
                                <span>
                                    <dt>Tax Fare</dt>
                                    <dd>{this.props.request.currency}{ (this.props.request.payment.tax).toFixed( 2 ) }</dd>
                                </span>
                            ) : ( "" )}
                            {this.props.request.payment.wallet ? (
                                <span>
                                    <dt>Wallet Detection</dt>
                                    <dd>{this.props.request.currency}{ (this.props.request.payment.wallet).toFixed( 2 ) }</dd>
                                </span>
                            ) : (
                                ""
                           )}

                            {this.props.request.payment.total ? (
                                <span>
                                    <dt>Total</dt>
                                    <dd>{this.props.request.currency}{ (this.props.request.payment.total).toFixed( 2 ) }</dd>
                                </span>
                            ) : ( "" )}
                            <hr />
                            {this.props.request.payment.payable ? (
                                <span>
                                    <dt>
                                        <h6>Amount to be Paid</h6>
                                    </dt>
                                    <dd>
                                        <h6>
                                        {this.props.request.currency}{ (this.props.request.payment.payable).toFixed( 2 ) }
                                        </h6>
                                    </dd>
                                </span>
                            ) : ( "" )}
                        </dl>
                        <br />

                        {this.props.request.payment_mode != "CASH" ? this.props.request.paid != 1 ? ( <a id="confirm-invoice" onClick={this.handlePayment} className="btn btn-secondary btn-block" > Pay Now <i className="fa fa-check" aria-hidden="true" /> </a> ) : ( <a data-toggle="modal" data-target="#rating" className="btn btn-secondary btn-block" > Rate Now <i className="fa fa-check" aria-hidden="true" /> </a> ) : ''}
                    </form>
                </div>
                {/* <!-- Rating Modal --> */}
                <div className="modal" id="rating">
                    <div className="modal-dialog">
                        <div className="modal-content">
                            {/* <!-- Rating Header --> */}
                            <div className="modal-header">
                                <h4 className="modal-title">Provider Rating</h4>
                                <button type="button" className="close" data-dismiss="modal" > &times; </button>
                            </div>
                            {/* <!-- Rating body --> */}
                            <div className="modal-body">
                                <div className="dis-column col-lg-12 col-md-12 col-sm-12 p-0 ">
                                    <div className="trip-user dis-column w-100">
                                        <div className="user-img" style={{ backgroundImage: "url(" + this.props.request.provider.picture + ")" }} /> <h5>
                                            { this.props.request.provider.first_name } { this.props.request.provider.last_name }
                                        </h5>

                                        <div className="rating-outer">
                                            <span style={{ cursor: "default" }}>
                                                { (this.props.request.provider.rating).toFixed(2) }
                                                <div className="rating-symbol" style={{ display: "inline-block", position: "relative" }} >
                                                    <div className="fa fa-star-o" style={{ visibility: "hidden" }} />
                                                    <div className="rating-symbol-foreground"
                                                        style={{ display: "inline-block", position: "absolute", overflow: "hidden", left: "0px", right: "0px", top: "0px", width: "auto" }}  >
                                                        <span className="fa fa-star" aria-hidden="true" />
                                                    </div>
                                                </div>
                                            </span>
                                            <input type="hidden" className="rating" value="1" disabled="disabled" />
                                        </div>
                                    </div>
                                    <div className="trip-user w-100 dis-column">
                                        <h5>Rate Your Driver</h5>
                                        <div className="rating-outer">
                                             <fieldset className="rating">
                                                <input type="radio" id="star5" name="rating" value="5" /><label className = "full" htmlFor="star5" ></label>
                                                <input type="radio" id="star4" name="rating" value="4" /><label className = "full" htmlFor="star4" ></label>
                                                <input type="radio" id="star3" name="rating" value="3" /><label className = "full" htmlFor="star3" ></label>
                                                <input type="radio" id="star2" name="rating" value="2" /><label className = "full" htmlFor="star2" ></label>
                                                <input type="radio" defaultChecked="checked" id="star1" name="rating" value="1" /><label className = "full" htmlFor="star1" ></label>
                                             </fieldset>
                                          </div>
                                        <p />
                                    </div>
                                    <div className="comments-section field-box mt-3">
                                        <textarea name="comment" className="form-control" maxLength="255" rows="4" cols="50" placeholder="Leave Your Comments..."
                                        />
                                        <small>(Maximum characters: 255)</small>
                                        <span style={{color:"red"}}>{this.state.commentLength}</span>
                                    </div>
                                </div>
                            </div>
                            {/* <!-- Rating footer --> */}
                            <div className="modal-footer">
                                <a className="btn btn-secondary btn-block" onClick={this.handleRating} > Submit
                                    <i className="fa fa-check-square" aria-hidden="true" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                {/* <!-- Rating Modal --> */}
            </div>
        );
    }
}

class EmergencyComponent extends React.Component {
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        $("#emergencyModal").modal();
    }

    render() {
        return (
            <div className="modal" id="emergencyModal">
                <div className="modal-dialog">
                    <div className="modal-content">
                    {/* <!-- Emergency Contact Header --> */}
                    <div className="modal-header">
                        <h5 className="modal-title">Emergency Contact</h5>
                        <button type="button" className="close" data-dismiss="modal">&times;</button>
                    </div>
                    {/* <!-- Emergency Contact body --> */}
                    <div className="modal-body">
                    <div>{getSiteSettings().site.sos_number}</div>
                    {/*this.props.emergency.map((value, index) => {
                       return <div key={index+'i'}>{value.number}</div>
                    })*/}
                    </div>
                    </div>
                </div>
            </div>
        );
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
            url: getBaseUrl() + "/user/reasons?type=TRANSPORT",
            type: "get",
            data: { },
            headers: {
                Authorization: "Bearer " + getToken("user")
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

        if(this.state.reason != '') {
            $.ajax({
                url: getBaseUrl() + "/user/transport/cancel/request",
                type: "post",
                data: {
                    id: id,
                    reason: this.state.reason
                },
                headers: {
                    Authorization: "Bearer " + getToken("user")
                },
                beforeSend: function() {
                    showLoader();
                },
                success: (data, textStatus, jqXHR) => {
                    $("#cancelModal").modal('hide');
                    $("#booking-status").hide();
                    if (Object.keys(data.responseData).length > 0) {
                        this.setState({
                            requests: data.responseData[0]
                        });
                    }
                    location.assign(window.location.href.split('?')[0]);
                    hideLoader();
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    $("#booking-status").show();
                    hideLoader();
                }
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
