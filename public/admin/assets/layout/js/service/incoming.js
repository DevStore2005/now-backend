class MainComponent extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            requests: [],
            account_status: "",
            service_status: "",
            status: "",
            FareType:"",
            time: 0,
        };
    }

    componentDidMount() {
        this.getStatus();
        // setInterval(() => this.getStatus(), 5000);

        var id = '';
        var that = this;
        var socket = io.connect(window.socket_url);
        $.ajax({
            url: getBaseUrl() + "/provider/check/serve/request",
            type: "get",
            async: false,
            processData: false,
            contentType: false,
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            success: (response, textStatus, jqXHR) => {
                var data = parseData(response);
                id = data.responseData.requests.id;
            },
            error: (jqXHR, textStatus, errorThrown) => {}
        });

        socket.emit('joinPrivateRoom', `room_${window.room}_R${id}_SERVICE`);

        socket.on('socketStatus', function (data) {
            if(window.env == 'local') console.log(data);
        });

        socket.on('serveRequest', function (data) {
            that.getStatus();
        });
    }

    getStatus() {
        $.ajax({
            url: getBaseUrl() + "/provider/check/serve/request",
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
                    service_status: data.responseData.service_status,
                    FareType: data.responseData.FareType,
                    requests:
                    (data.responseData.requests != null && Object.keys(data.responseData.requests).length > 0)
                            ? data.responseData.requests
                            : [],
                    time:
                    (data.responseData.requests != null && Object.keys(data.responseData.requests).length > 0)
                                ? data.responseData.requests.time_left_to_respond
                                : 0,
                    status:
                    (data.responseData.requests != null && Object.keys(data.responseData.requests).length > 0)
                            ? data.responseData.requests.status
                            : "",
                    provider: data.responseData.provider_details
                });
            },
            error: (jqXHR, textStatus, errorThrown) => {}
        });
    }

    render() {
        let status = this.state.status;
        //console.log(this.state.status);
        if(this.state.account_status == "DOCUMENT" || this.state.account_status == "CARD" || this.state.account_status == "SERVICES" || this.state.account_status == "ONBOARDING") {
            window.location.replace("/provider/document");
        } else {
            if (status === "SEARCHING") {
                window.location.replace("/provider/home");
                // return <RequestComponent request={this.state.requests} time={this.state.time} />;
            } else if (status === "ACCEPTED" ||status === "STARTED" || status === "ARRIVED" || status === "PICKEDUP") {
                return <StartedComponent request={this.state.requests} provider={this.state.provider} FareType={this.state.FareType} />;
            } else if (status === "DROPPED") {
                return <StartedComponent request={this.state.requests} provider={this.state.provider} FareType={this.state.FareType} />;
            } else if (status === "COMPLETED") {
                return <InvoiceComponent request={this.state.requests} provider={this.state.provider} FareType={this.state.FareType}  />;
            }
        }
        return <NoServiceComponent request={this.state.requests} />;;
    }
}

class StartedComponent extends React.Component {
    constructor(props) {
        super(props);
        // console.log(this.props);
        this.state = {
            cancelModal: false
        };

    }

    componentDidMount() {
        var chatSocket = io.connect(window.socket_url);
        var id = this.props.request.id;
        var user_id = this.props.request.user_id;
        var provider_id = this.props.request.provider_id;
        var admin_service = 'SERVICE';
        var status = this.props.request.status;
        if(status == "ACCEPTED" || status == "STARTED" || status == "ARRIVED" || status=="PICKEDUP") {
            $('#message_container').show();
        }else if(status=="DROPPED"){
            $('#message_container').hide();
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
        var status = this.props.request.status;
        if(status == "ACCEPTED" || status == "STARTED" || status == "ARRIVED" || status == "PICKEDUP" ) {
            $('#message_container').show();
        }else if(status=="DROPPED"){
            $('#message_container').hide();
        }
    }

    handleUpdateStatus = (status, e) => {

        let id = this.props.request.id;

        let otp = $("input[name=otp]").val();
        // console.log(status);
        if(status == "ARRIVED" || status == "PICKEDUP" || status == "DROPPED") $('#arrived_tab').addClass('current');
        
        if(status == "PICKEDUP" || status == "DROPPED") $('#picked_tab').addClass('current');

        if(status == "DROPPED") $('#reached_tab').addClass('current');

        if(status == "PAYMENT") $('#payment_tab').addClass('current');

        if(status == "PICKEDUP") {
            this.setState({
                status: "PICKEDUP"
            });
        }
        
        $.ajax({
            url: getBaseUrl() + "/provider/update/serve/request",
            type: "post",
            data: {
                _method: "PATCH", id, status, otp
            },
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            beforeSend: function() {
                showLoader();
            },
            success: (data, textStatus, jqXHR) => {
                hideLoader();
                return <InvoiceComponent request={this.props.request}  provider={this.state.provider} />;
            },
            error: (jqXHR, textStatus, errorThrown) => {
                hideLoader();
                if(status == "ARRIVED") $('#arrived_tab').removeClass('current');
                else if(status == "PICKEDUP") $('#picked_tab').removeClass('current');
                else if(status == "DROPPED") $('#reached_tab').removeClass('current');
                else if(status == "PAYMENT") $('#payment_tab').removeClass('current');
                if(status == "PICKEDUP") {
                    this.setState({
                        status: "ARRIVED"
                    });
                    // $("#otpModal").modal();
                }
            }
        });
    }
    
    handleOtpUpdate = (status, e) => {        
        let id = this.props.request.id;
        let otpState = getSiteSettings().service.serve_otp;
        let otp = $("input[name=otp]").val();
        var formdata = new FormData();
        formdata.append("id",id);
        formdata.append("status",status);
        if($("input[name=before_picture]").length > 0)
            formdata.append('before_picture', $("input[name=before_picture]")[0].files[0]);
        var other_data = $('form').serializeArray();
        $.each(other_data,function(key,input){
            formdata.append(input.name,input.value);
        });
        if((otpState == 1 && otp != "") || otpState == 0) {
            $.ajax({
                url: getBaseUrl() + "/provider/update/serve/request",
                type: "POST",
                data: formdata,
                contentType: false,
                processData: false,
                headers: {
                    Authorization: "Bearer " + getToken("provider")
                },
                beforeSend: function() {
                    showLoader();
                },
                success: (data, textStatus, jqXHR) => {
                    hideLoader();
                    return <InvoiceComponent request={this.props.request}  provider={this.state.provider}  />;
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    hideLoader();
                    if(status == "ARRIVED") $('#arrived_tab').removeClass('current');
                    else if(status == "PICKEDUP") $('#picked_tab').removeClass('current');
                    else if(status == "DROPPED") $('#reached_tab').removeClass('current');
                    if(status == "PICKEDUP") {
                        this.setState({
                            status: "ARRIVED"
                        });
                        // $("#otpModal").modal();
                    }
                    if (jqXHR.responseJSON) {
                        alertMessage(textStatus, jqXHR.responseJSON.message, "danger");
                    }
                }
            });
        }else{
            var alertTitle = 'Required';
            var alertMsg = 'OTP is required';
            alertMessage(alertTitle, alertMsg, "danger");
        }
    }

    handleEndUpdate = (status, e) => {

        let id = this.props.request.id;
        var formdata = new FormData();
        formdata.append("id",id);
        formdata.append("status",status);
        if($("input[name=after_picture]").length > 0)
            formdata.append('after_picture', $("input[name=after_picture]")[0].files[0]);
        var other_data = $('form').serializeArray();
        $.each(other_data,function(key,input){
            formdata.append(input.name,input.value);
        });
        $.ajax({
            url: getBaseUrl() + "/provider/update/serve/request",
            type: "POST",
            data: formdata,
            contentType: false,
            processData: false,
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            beforeSend: function() {
                showLoader();
            },
            success: (data, textStatus, jqXHR) => {
                hideLoader();
                return <InvoiceComponent request={this.props.request} />;
            },
            error: (jqXHR, textStatus, errorThrown) => {
                hideLoader();
                if(status == "ARRIVED") $('#arrived_tab').removeClass('current');
                else if(status == "PICKEDUP") $('#picked_tab').removeClass('current');
                else if(status == "DROPPED") $('#reached_tab').removeClass('current');
                if(status == "PICKEDUP") {
                    this.setState({
                        status: "ARRIVED"
                    });
                }
                if (jqXHR.responseJSON) {
                    alertMessage(textStatus, jqXHR.responseJSON.message, "danger");
                }
            }
        });
    }

    handlePayment = e => {
            this.handleUpdateStatus('PAYMENT');       
    }

    handleCancelModal = e => {
        this.setState({cancelModal:true});
    }

    handleUploadChange = e => {
        this.setState({
            file: URL.createObjectURL(e.target.files[0]),
            class: 'imgLoad',
          })
    }
    handleUploadChange1 = e => {
        this.setState({
            file1: URL.createObjectURL(e.target.files[0]),
            class: 'imgLoad',
          })
    }
    handleSendMessage = e => {
        if((e.which == 13 || e.which === undefined) && $('input[name=message]').val() != '') {
            var chatSocket = io.connect(window.socket_url);
            var message = $('input[name=message]');
            var request_id = this.props.request.id;
            var user_id = this.props.request.user_id;
            var provider_id = this.props.request.provider_id;
            var admin_service = 'SERVICE';
            var user = `${this.props.request.user.first_name} ${this.props.request.user.last_name}`;
            var provider = `${this.props.provider.first_name} ${this.props.provider.last_name}`;
            
            chatSocket.emit('send_message', {room: `room_${window.room}_R${request_id}_U${user_id}_P${provider_id}_${admin_service}`, url: getBaseUrl() + "/chat", salt_key: window.salt_key, id: request_id, admin_service: admin_service, type: 'provider', user: user, provider: provider, message:  message.val() });

            message.val('');
        }
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
    <section className="z-1 pickup-section">
        <div className="row">
            <div className="service-card b-none col-xs-12 col-sm-12 col-md-12 col-lg-6 available m-0">
                <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0">
                    <div className="form-register">
                        <div className="steps clearfix">
                        <ul role="tablist">
                           <li role="tab" aria-disabled="false" id="arrived_tab" className={(this.props.request.status == "ARRIVED" || this.props.request.status == "PICKEDUP" || this.props.request.status == "DROPPED" || this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="true">
                              <a id="form-total-t-0" href="#form-total-h-0" aria-controls="form-total-p-0">
                                 <span className="current-info audible"> </span>
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/transport/svg/map.svg" /></span>
                                    <span className="step-text">Arrived</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="picked_tab" className={(this.props.request.status == "PICKEDUP" || this.props.request.status == "DROPPED")   ? "current" : ""} aria-selected="false">
                              <a id="form-total-t-1" href="#form-total-h-1" aria-controls="form-total-p-1">
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/service/svg/maintenance.svg" /></span>
                                    <span className="step-text">Service Started</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="reached_tab" className={( this.props.request.status == "DROPPED")   ? "current" : ""} aria-selected="false">
                              <a id="form-total-t-2" href="#form-total-h-2" aria-controls="form-total-p-2">
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/service/svg/breakfast-delivery-service.svg"/></span>
                                    <span className="step-text">Service Ended</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="payment_tab" className={( this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="false">
                              <a id="form-total-t-2" href="#form-total-h-2" aria-controls="form-total-p-2">
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/service/svg/purse.svg" /></span>
                                    <span className="step-text">Payment Received</span>
                                 </div>
                              </a>
                           </li>
                        </ul>
                        </div>
                    </div>
                    <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0">    
                    <form id="service_creation" encType="multipart/form-data">                 
                        <img src={this.props.request.user.picture} className="user-img" />
                        <div className="user-right">
                        <h5>{this.props.request.user.first_name} {this.props.request.user.last_name}</h5>
                        <span className="pull-right c-pointer" style={{float:'right'}} className="dropdown-toggle" data-toggle="dropdown"><i className="material-icons">more_vert</i></span>  
                        <div className="dropdown-menu">
                           <a className="dropdown-item" data-toggle="modal" data-target="#contactModal"><i className="material-icons">phone</i> Contact </a>
                           {this.props.request.status == "STARTED"||this.props.request.status == "ACCEPTED" || this.props.request.status == "ARRIVED" ?<a className="dropdown-item" data-toggle="modal" data-target="#cancelModal" onClick={this.handleCancelModal}> <i className="material-icons">cancel</i> Cancel Service </a> : ""}
                        </div>
                        <div className="rating-outer">
                            <span style={{cursor: 'default'}}>
                            {rows}
                            </span>
                            <input type="hidden" className="rating" value="1" disabled="disabled" />
                        </div>
                        <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0 d-flex">
                            <div className="col-xs-12 col-sm-12 col-md-10 col-lg-11 p-0">
                                <h5>Service Location</h5>
                                <p>{this.props.request.s_address}</p>
                                
                                {this.props.request.status == "ARRIVED" && getSiteSettings().service.serve_otp == 1 && this.props.request.created_type == "USER"  ?<input id="otp" className="form-control float-right numbers"  autoComplete="off" placeholder="Enter OTP" maxLength="4" type="text" name="otp"  />:""}
                                {/* {this.props.request.status == "ARRIVED" ? <input id="start_time" className="form-control" placeholder="Enter Start Time" type="text" name="start_time" />:""} */}
                                {/* {this.props.request.status == "PICKEDUP" ?<input id="end_time" className="form-control"  autoComplete="off" placeholder="Enter End Time" type="text" name="end_time" />:""} */}
                                {this.props.request.status == "PICKEDUP" ? 
                                <div className="">
                                    <input id="extra_charge" className="form-control numbers" placeholder="Extra Amount" type="text" name="extra_charge" />
                                    <textarea id="extra_chargenotes" name="extra_charge_notes" className="form-control" placeholder="Details of extra charges" style={{resize: 'none'}}></textarea>
                                </div>
                                :""}
                                {this.props.request.status == "DROPPED" ? 
                                <div id="invoice" className="dis-column">
                                    <div className="col-md-12 col-sm-12 col-lg-12 p-0">
                                    <h4 className="text-center">Invoice</h4>
                                    
                                <dl className="dl-horizontal left-right mt-5">
                                    <dt>Booking ID</dt>
                                    <dd>{this.props.request.booking_id}</dd>
                                    {this.props.FareType =='FIXED' ? '' :
                                    <dl><dt>Time Consumed(mins) </dt>
                                    <dd>{this.props.request.payment.minute.toFixed(2)}</dd> </dl>
                                    }
                                    <dt>Base Price {this.props.request.quantity >0 ? ' X '+this.props.request.quantity+' (QTY)'  : '' } </dt>
                                    <dd>{this.props.request.user.currency_symbol} {this.props.request.payment.fixed.toFixed(2)}</dd>
                                    <dt>Extra Charges </dt>
                                    <dd>{this.props.request.user.currency_symbol} {this.props.request.payment.extra_charges.toFixed(2)}</dd>
                                    <dt>Tax Amount </dt>
                                    <dd>{this.props.request.user.currency_symbol} {this.props.request.payment.tax.toFixed(2)}</dd>
                                    <dt>Discount Amount </dt>
                                    <dd>{this.props.request.user.currency_symbol} {this.props.request.payment.discount.toFixed(2)}</dd>
                                    <hr />
                                </dl>
                                <dl className="dl-horizontal left-right">
                                    <dt>Amount To Be Paid (including Tax)</dt>
                                    <dd>{this.props.request.user.currency_symbol} {this.props.request.payment.payable.toFixed(2)}</dd>
                                </dl>
                                    </div>
                                </div> :""}
                                
                            </div>
                            {/* <div className="col-xs-12 col-sm-12 col-md-1 col-lg-1 p-0 arrow">
                                <i className="fa fa-location-arrow"></i>
                                </div>  */}  
                        </div>
                        <div className="col-xs-12 col-sm-12 col-md-6 col-lg-12 p-0 d-flex">
                            <div className="col-xs-12 col-sm-12 col-md-6 col-lg-11 p-0">
                                {this.props.request.service.allow_before_image == 1 && this.props.request.status=="ARRIVED"  ?
                                <div className="c-pointer dis-column" id="before_img">
                                    <h5 className="text-left">Before</h5>
                                    <div className="add-document">
                                    {this.state.file ?
                                        <img src={this.state.file} className={this.state.class} id="beforeImageShow" alt="+" />
                                    :
                                        <img src="/assets/layout/images/service/svg/add.svg" className="add-icon" id="beforeImageShow" alt="+" />
                                    }
                                    </div>
                                    <div className="fileUpload up-btn profile-up-btn">                   
                                        <input type="file" id="profile_img_upload_btn" name="before_picture" className="upload" onChange={this.handleUploadChange} accept="image/x-png, image/jpeg" />
                                    </div>
                                    <p>Upload the product image before start servicing</p>
                                </div>
                                :""}
                               {this.props.request.service.allow_after_image == 1 && this.props.request.status=="PICKEDUP" ?
                                <div className="c-pointer dis-column" id="after_img">
                                    <h5 className="text-left">After</h5>
                                    <div className="add-document">
                                    {this.state.file1 ?
                                        <img src={this.state.file1} className={this.state.class} id="afterImageShow" alt="+" />
                                    :
                                        <img src="/assets/layout/images/service/svg/add.svg" className="add-icon" id="afterImageShow" alt="+" />
                                    }
                                    </div>
                                    <div className="fileUpload up-btn profile-up-btn">                   
                                        <input type="file" id="profile_img_upload_btn" name="after_picture" className="upload" onChange={this.handleUploadChange1} accept="image/x-png, image/jpeg" />
                                    </div>
                                    <p>Upload the product image after complete servicing</p>
                                </div>
                                :""}
                            </div>
                        </div>
                        {/* <!-- Popup Modal --> */}   
                        <div className="modal" id="contactModal">
                           <div className="modal-dialog">
                              <div className="modal-content">
                                 <div className="modal-header">
                                    <h4 className="modal-title">Contact</h4>
                                    <button type="button" className="close" data-dismiss="modal">&times;</button>
                                 </div>
                                 <div className="modal-body">
                                    {/* <select className="form-control" name="cancel" id="contact">
                                       <option value="en" selected="">User Contact</option>
                                       <option value="ar">Lorem Ipsum</option>
                                       <option value="en">Lorem Ipsum</option>
                                       <option value="ar">Lorem Ipsum</option>
                                    </select> */}
                                    <p>Contact Mobile Number : {this.props.request.user.country_code}{this.props.request.user.mobile}</p>
                                 </div>
                                 {/* <div className="modal-footer">
                                    <a  className="btn btn-primary btn-block" data-toggle="modal" data-target="#contactModal">Submit <i className="fa fa-check" aria-hidden="true"></i></a>
                                 </div> */}
                              </div>
                           </div>
                        </div>                        
                     </div>
                     </form>
                     <div id="toaster" className="toaster"></div>
                    <div className="actions col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        {this.props.request.status == "ACCEPTED" ? <a id="arrived" className="btn btn-primary btn-md"  onClick={this.handleUpdateStatus.bind(this, 'ARRIVED')}>Tap When Arrived <i className="fa fa-check" aria-hidden="true"></i></a> : ""}
                        {this.props.request.status == "ARRIVED" ?<a id="start_serv" className="btn btn-primary btn-md"  onClick={this.handleOtpUpdate.bind(this, 'PICKEDUP')} >Start Service <i className="fa fa-check" aria-hidden="true"></i></a> : ""}
                        {this.props.request.status == "PICKEDUP" ?<a id="stop_serv" className="btn btn-primary btn-md mr-2"  onClick={this.handleEndUpdate.bind(this, 'DROPPED')}>End Service <i className="fa fa-check" aria-hidden="true"></i></a>: ""}
                        {(this.props.request.status == "DROPPED" && this.props.request.payment_mode == "CASH" ) ?<a id="confirm_pay" className="btn btn-primary btn-md mr-2" onClick={this.handlePayment}>Confirm Payment <i className="fa fa-check" aria-hidden="true"></i></a>: ""}
                        {(this.props.request.status == "DROPPED" && this.props.request.payment_mode == "CARD" ) ?<h5>Waiting For Payment</h5>: ""}
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
               </div>
            </div>
            <div  className="col-sm-12 col-md-12 col-lg-6 map-section">
               <img style={{ height:'500px',width:'550px',boxShadow: "2px 2px 10px #ccc" }} src={ "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=620x380&maptype=terrian&format=png&visual_refresh=true&markers=icon:"+window.url+"/assets/layout/images/common/marker-start.png%7C"+this.props.request.s_latitude+","+this.props.request.s_longitude+"&key=" + getSiteSettings().site.browser_key } />
            </div>
            {this.state.cancelModal ? <CancelComponent request={this.props.request} /> : '' }
                </div>
    </section>
        );
    }
}

class InvoiceComponent extends React.Component {
    constructor(props) {
        super(props);
        console.log(this.props);
        this.state = {
            cancelModal: false,
            commentLength:''
        };
    }

    componentDidMount() {
        if(this.props.request.status == "COMPLETED") $("#rating").modal();
    }

    handlePayment = e => {
        
        let id = this.props.request.id;

        $.ajax({
            url: getBaseUrl() + "/provider/payment",
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
                $("#rating").modal("show");
            },
            error: (jqXHR, textStatus, errorThrown) => {
                hideLoader();
            }
        });
    };

    handleRating = e => {
        let id = this.props.request.id;
        let rating = $("input[name=rating]:checked").val();
        let comment = $("textarea[name=comment]").val();
        let admin_service = this.props.provider.service.admin_service;
        
        $.ajax({
            url: getBaseUrl() + "/provider/rate/serve",
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
                hideLoader();
                this.setState({
                    commentLength:''
                });
                $("#rating").modal("hide");
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
                            <div className="form-register">
                                <div className="steps clearfix">
                                <ul role="tablist">
                           <li role="tab" aria-disabled="false" id="arrived_tab" className={(this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="true">
                              <a id="form-total-t-0" href="#form-total-h-0" aria-controls="form-total-p-0">
                                 <span className="current-info audible"> </span>
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/transport/svg/map.svg" /></span>
                                    <span className="step-text">Arrived</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="picked_tab" className={(this.props.request.status == "COMPLETED" )   ? "current" : ""} aria-selected="false">
                              <a id="form-total-t-1" href="#form-total-h-1" aria-controls="form-total-p-1">
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/service/svg/maintenance.svg" /></span>
                                    <span className="step-text">Service Started</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="reached_tab" className={( this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="false">
                              <a id="form-total-t-2" href="#form-total-h-2" aria-controls="form-total-p-2">
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/service/svg/breakfast-delivery-service.svg"/></span>
                                    <span className="step-text">Service Ended</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="payment_tab" className={( this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="false">
                              <a id="form-total-t-2" href="#form-total-h-2" aria-controls="form-total-p-2">
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/service/svg/purse.svg" /></span>
                                    <span className="step-text">Payment Received</span>
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
                                    <dt>Base Price </dt>
                                    <dd>{this.props.request.user.currency_symbol} {this.props.request.payment.fixed.toFixed(2)}</dd>
                                    <dt>Extra Charges </dt>
                                    <dd>{this.props.request.user.currency_symbol} {this.props.request.payment.extra_charges.toFixed(2)}</dd>
                                    <dt>Tax Amount </dt>
                                    <dd>{this.props.request.user.currency_symbol} {this.props.request.payment.tax.toFixed(2)}</dd>
                                    <hr />
                                </dl>
                                <dl className="dl-horizontal left-right">
                                    <dt>Total (including Tax)</dt>
                                    <dd>{this.props.request.currency} {this.props.request.payment.total}</dd>
                                </dl>
                                <br />
                                <div className="col-lg-12 col-md-12 col-sm-12 p-0 invoice-payment">
                                    <div className="total-amount" style={{width:'auto'}}>
                                    <span>{this.props.request.currency} {this.props.request.payment.payable}</span>
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
                                {this.props.request.payment_mode == "CASH" ? <a id="confirm-invoice" onClick={this.handlePayment} className="btn btn-purple btn-block">Paid <i className="fa fa-check" aria-hidden="true"></i></a> : <h4 style={{textAlign: "center", width: "100%", float: "left"}}>Waiting for Payment</h4> }
                                
                            </form>
                        </div>
                        {/* <!-- Rating Modal --> */}
                        <div className="modal" id="rating">
                            <div className="modal-dialog">
                                <div className="modal-content">
                                    {/* <!-- Rating Header --> */}
                                    <div className="modal-header">
                                    <h4 className="modal-title">User Rating</h4>
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
                                                {(this.props.request.user.rating)}
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
                                            <textarea className="form-control" rows="4" cols="50" maxLength="255" name="comment" placeholder="Leave Your Comments..."></textarea>
                                             <small>(Maximum characters: 255)</small>
                                            <span style={{color:"red"}}>{this.state.commentLength}</span>
                                        </div>
                                    </div>
                                    </div>
                                    <div className="modal-footer">
                                    <a className="btn btn-primary btn-block" onClick={this.handleRating}>Submit <i className="fa fa-check-square" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    <div  className="col-sm-12 col-md-12 col-lg-6 map-section">
                        <img style={{ boxShadow: "2px 2px 10px #ccc" }} src={ "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=620x380&maptype=terrian&format=png&visual_refresh=true&markers=icon:"+window.url+"/assets/layout/images/common/marker-start.png%7C"+this.props.request.s_latitude+","+this.props.request.s_longitude+"&key=" + getSiteSettings().site.browser_key } />
                    </div>
            </div>
        );
    }
}


class NoServiceComponent extends React.Component {

    render() {
        return <div className="service-card col-xs-12 col-sm-12 col-md-12 col-lg-6 no-service">
                <h5>No Services Available</h5>
            </div>;
    }
}

class CancelComponent extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            reasons: [],
            reason: ''
        };
    }

    componentDidMount() {

        $.ajax({
            url: getBaseUrl() + "/provider/reasons?type=SERVICE",
            type: "get",
            data: { },
            headers: {
                Authorization: "Bearer " + getToken("provider")
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
            },
            error: (jqXHR, textStatus, errorThrown) => {}
        });

        $("#cancelModal").modal();
    }



    handleCancel = e => {
        
        let id = this.props.request.id;

        if(this.state.reason != '') {
            $.ajax({
                url: getBaseUrl() + "/provider/cancel/serve/request",
                type: "post",
                data: {
                    id: id,
                    reason: this.state.reason
                },
                headers: {
                    Authorization: "Bearer " + getToken("provider")
                },
                beforeSend: function() {
                    showLoader();
                },
                success: (data, textStatus, jqXHR) => {
                    hideLoader();
                    $("#cancelModal").modal('hide');
                    if (Object.keys(data.responseData).length > 0) {
                        this.setState({
                            requests: data.responseData[0]
                        });
                    }
                },
                error: (jqXHR, textStatus, errorThrown) => {hideLoader();}
            });
        }else{
            var alertTitle = 'Required';
            var alertMsg = 'Reason is required';
            alertMessage(alertTitle, alertMsg, "danger");
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
