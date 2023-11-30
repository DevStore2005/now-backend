class MainComponent extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            requests: [],
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
            url: getBaseUrl() + "/provider/check/order/request",
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

        socket.emit('joinPrivateRoom', `room_${window.room}_R${id}_ORDER`);

        socket.on('socketStatus', function (data) {
            if(window.env == 'local') console.log(data);
        });

        socket.on('orderRequest', function (data) {
            that.getStatus();
        });
        // setInterval(() => this.getStatus(), 5000);
    }

    getStatus() {
        $.ajax({
            url: getBaseUrl() + "/provider/check/order/request",
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
                    requests:
                        Object.keys(data.responseData.requests).length > 0
                            ? data.responseData.requests
                            : [],
                    time:
                            Object.keys(data.responseData.requests).length > 0
                                ? data.responseData.requests.time_left_to_respond
                                : 0,
                    status:
                        Object.keys(data.responseData.requests).length > 0
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
        if(this.state.account_status == "DOCUMENT" || this.state.account_status == "CARD" || this.state.account_status == "SERVICES" || this.state.account_status == "ONBOARDING") {
            window.location.replace("/provider/document");
        } else {
            if (status === "SEARCHING") {
                window.location.replace("/provider/home");
            } else if (status === "PROVIDEREJECTED" ||status === "PROCESSING" || status === "STARTED" ||status === "REACHED" || status === "PICKEDUP" || status === "ARRIVED"||status === "DELIVERED" ) {
                return <StartedComponent request={this.state.requests} provider={this.state.provider} />;
            } else if (status === "COMPLETED") {
                return <InvoiceComponent request={this.state.requests} provider={this.state.provider}  />;
            }
        }
        return <NoServiceComponent request={this.state.requests} />;;
    }
}

class StartedComponent extends React.Component {
    constructor(props) {
        super(props);
        console.log(this.props);
        this.state = {
            cancelModal: false
        };

    }

    componentDidMount() {
        /*if(this.props.request.status === "ARRIVED") { $("#otpModal").modal("show"); }*/
        let otpState = getSiteSettings().order.order_otp; 
        var chatSocket = io.connect(window.socket_url);
        var id = this.props.request.id;
        var user_id = this.props.request.user_id;
        var provider_id = this.props.request.provider_id;
        var admin_service = 'ORDER';
        var status = this.props.request.status; 
        if(status == "ACCEPTED" || status == "PROCESSING" || status == "STARTED"|| status == "REACHED" || status == "PICKEDUP" || status == "ARRIVED" ) {
           if(otpState=="0" && status=="ARRIVED"){
            $("#otpModal").modal("hide");
            $('#message_container').hide();
           }else if(otpState=="1" && status=="ARRIVED"){
            $("#otpModal").modal("show");
            $('#message_container').hide();

           }else{

            $('#message_container').show();
            }
        } else {
            $('#message_container').show();
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
            $('input[name=message]').val('');
        });
    }

    handleUpdateStatus = (status, e) => {

        let id = this.props.request.id;
        let otpState = getSiteSettings().order.order_otp; 
        let otp = $("input[name=otp]").val();
        if(status == "PROCESSING" || status == "STARTED" || status == "REACHED" || status == "PICKEDUP" || status == "ARRIVED") $('#started_tab').addClass('current');
        
        if(status == "PROCESSING"  || status == "REACHED" || status == "PICKEDUP" || status == "ARRIVED") $('#reached_tab').addClass('current');
        
        if(status == "PICKEDUP" || status == "ARRIVED") $('#picked_tab').addClass('current');

        if(status == "ARRIVED"){
            $('#delivered_tab').addClass('current');
            $('#message_container').hide();
        } 

        if(status == "PAYMENT") $('#payment_tab').addClass('current');

        if(status == "PICKEDUP") {
            this.setState({
                status: "PICKEDUP"
            });
        }
        $.ajax({
            url: getBaseUrl() + "/provider/update/order/request",
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
                if(status == "ARRIVED" && otpState=="1"){
                    $("#otpModal").modal("show");
                }if(status == "ARRIVED" && otpState=="0"){
                    $("#otpModal").modal("hide");
                }
                return <InvoiceComponent request={this.props.request} />;
            },
            error: (jqXHR, textStatus, errorThrown) => {
                hideLoader();
                if(status == "STARTED") $('#started_tab').removeClass('current');
                else if(status == "REACHED") $('#reached_tab').removeClass('current');
                else if(status == "PICKEDUP") $('#picked_tab').removeClass('current');
                else if(status == "ARRIVED") $('#delivered_tab').removeClass('current');
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
        let otpState = getSiteSettings().order.order_otp;
        let otp = $("input[name=otp]").val();
        var formdata = new FormData();
        formdata.append("id",id);
        formdata.append("status",status);
        var other_data = $('form').serializeArray();
        $.each(other_data,function(key,input){
            formdata.append(input.name,input.value);
        });
        if((otpState == 1 && otp != "") || otpState == 0) {
            $.ajax({
                url: getBaseUrl() + "/provider/update/order/request",
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
                    $("#otpModal").modal("hide");
                    // return <InvoiceComponent request={this.props.request} />;
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    hideLoader();
                    if(status == "STARTED") $('#started_tab').removeClass('current');
                    else if(status == "REACHED") $('#reached_tab').removeClass('current');
                    else if(status == "PICKEDUP") $('#picked_tab').removeClass('current');
                    else if(status == "ARRIVED") $('#delivered_tab').removeClass('current');
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

    handlePayment = e => {
            this.handleUpdateStatus('PAYMENT');       
    }

    handleCancelModal = e => {
        this.setState({cancelModal:true});
    }
    handleSendMessage = e => {
        if((e.which == 13 || e.which === undefined) && $('input[name=message]').val() != '') {
            var chatSocket = io.connect(window.socket_url);
            var message = $('input[name=message]');
            var request_id = this.props.request.id;
            var user_id = this.props.request.user_id;
            var provider_id = this.props.request.provider_id;
            var admin_service = 'ORDER';
            var user = `${this.props.request.user.first_name} ${this.props.request.user.last_name}`;
            var provider = `${this.props.provider.first_name} ${this.props.provider.last_name}`;
            
            chatSocket.emit('send_message', {room: `room_${window.room}_R${request_id}_U${user_id}_P${provider_id}_${admin_service}`, url: getBaseUrl() + "/chat", salt_key: window.salt_key, id: request_id, admin_service: admin_service, type: 'provider', user: user, provider: provider, message:  message.val() });

        }
    }
    render() {
        let disputeStatus = "";
        var rows=[];
        let otpState = getSiteSettings().order.order_otp;
        var starcount = this.props.request.stores_details.rating;
        for (var i=0;i<starcount;i++){
            rows.push(<div className="rating-symbol" key={i} style={{display: 'inline-block', position: 'relative'}}>
            <div className="fa fa-star-o" style={{visibility: 'hidden'}}></div>
            <div className="rating-symbol-foreground" style={{display: 'inline-block', position: 'absolute', overflow: 'hidden', left: '0px', right: '0px', top: '0px', width: 'auto'}}>
                <span className="fa fa-star" aria-hidden="true"></span>
            </div>
        </div>);
        }
        var items=[];
        var itemsData = this.props.request.order_invoice.items;
        var itemscount = itemsData.length;

        for (var i=0;i<itemscount;i++){
            var cartaddon=[];
            var addOnData = itemsData[i].cartaddon;
            console.log(addOnData);
            var addoncount = addOnData.length;
            var quantityVal = itemsData[i].quantity != null && itemsData[i].quantity != undefined ? itemsData[i].quantity : 1;
            for (var j=0;j<addoncount;j++){
                cartaddon.push(<div key={j}><dt><small>Addon: { addOnData[j].addon_name }</small> </dt>
                    <dd><small>{ this.props.request.currency } { addOnData[j].addon_price } x { quantityVal }</small></dd></div>);
            }
            items.push(<div key={i}>
                <dl className="dl-horizontal left-right mt-2" key={i}>
                     <dt>{ itemsData[i].product.item_name } </dt>
                     <dd>{ this.props.request.currency } { itemsData[i].item_price } x { quantityVal }</dd>                                
                     <dl key={j}>{cartaddon}</dl>
                  </dl>
            </div>);
            if (this.props.request.status === "PROVIDEREJECTED") {
                disputeStatus = "Dispute raised. Admin will contact.";
            }
        }
        return (
    <section className="z-1 pickup-section">
        <div className="row">
            <div className="service-card b-none col-xs-12 col-sm-12 col-md-12 col-lg-6 available m-0">
                <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0">
                        <div className="status mt-4">
                            <h6 className="txt-blue text-center">{disputeStatus}</h6>
                        </div>
                    <div className="form-register">
                        <div className="steps clearfix">
                        <ul role="tablist">
                            <li role="tab" aria-disabled="false" id="started_tab" className={(this.props.request.status == "STARTED" || this.props.request.status == "REACHED" ||this.props.request.status == "ARRIVED" || this.props.request.status == "PICKEDUP" || this.props.request.status == "DELIVERED" || this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="true">
                              <a id="form-total-t-0" href="#form-total-h-0" aria-controls="form-total-p-0">
                                 <span className="current-info audible"> </span>
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/order/svg/scooter.svg" /></span>
                                    <span className="step-text">Started</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="reached_tab" className={(this.props.request.status == "REACHED" ||this.props.request.status == "ARRIVED" || this.props.request.status == "PICKEDUP" || this.props.request.status == "DELIVERED" || this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="true">
                              <a id="form-total-t-0" href="#form-total-h-0" aria-controls="form-total-p-0">
                                 <span className="current-info audible"> </span>
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/order/svg/scooter.svg" /></span>
                                    <span className="step-text">Arrived</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="picked_tab" className={(this.props.request.status == "PICKEDUP" || this.props.request.status == "ARRIVED" || this.props.request.status == "DELIVERED")   ? "current" : ""} aria-selected="false">
                              <a id="form-total-t-1" href="#form-total-h-1" aria-controls="form-total-p-1">
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/order/svg/meal.svg" /></span>
                                    <span className="step-text">Picked Up</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="delivered_tab" className={( this.props.request.status == "ARRIVED" || this.props.request.status == "DELIVERED")   ? "current" : ""} aria-selected="false">
                              <a id="form-total-t-2" href="#form-total-h-2" aria-controls="form-total-p-2">
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/order/svg/breakfast-delivery-service.svg"/></span>
                                    <span className="step-text">Delivered</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="payment_tab" className={( this.props.request.status == "COMPLETED" || this.props.request.status == "DELIVERED")   ? "current" : ""} aria-selected="false">
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
                        <img src={this.props.request.stores_details.picture} className="user-img" />
                        <div className="user-right">
                        <h5>{this.props.request.stores_details.store_name}</h5>
                        <span className="pull-right c-pointer" style={{float:'right'}} className="dropdown-toggle" data-toggle="dropdown"><i className="material-icons">more_vert</i></span>  
                        <div className="dropdown-menu">
                           <a className="dropdown-item" data-toggle="modal" data-target="#contactModal"><i className="material-icons">phone</i> Contact </a>
                           {this.props.request.status == "STARTED"||this.props.request.status == "PROCESSING" || this.props.request.status == "REACHED" ?<a className="dropdown-item" data-toggle="modal" data-target="#cancelModal" onClick={this.handleCancelModal}> <i className="material-icons">cancel</i> Cancel Order </a> : ""}
                        </div>
                        <div className="rating-outer">
                            <span style={{cursor: 'default'}}>
                            {rows}
                            </span>
                            <input type="hidden" className="rating" value="1" disabled="disabled" />
                        </div>
                        <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0 d-flex">
                            <div className="col-xs-12 col-sm-12 col-md-10 col-lg-11 p-0">
                                <h5>Pickup Location</h5>
                                <p>{this.props.request.pickup.store_location}</p>
                                <h5>Delivery Location</h5>
                                <p>{this.props.request.delivery.flat_no} {this.props.request.delivery.street} {this.props.request.delivery.map_address}</p>
                                <h5>Items</h5>
                                {items}
                                <dl className="dl-horizontal left-right mt-2">
                                    <dt>Packing Charges</dt>
                                    <dd>{ this.props.request.currency } {this.props.request.order_invoice.store_package_amount}</dd>
                                    <dt>Service Tax</dt>
                                    <dd>{ this.props.request.currency } {this.props.request.order_invoice.tax_amount}</dd>
                                    <dt>Delivery Charges</dt>
                                    <dd>{ this.props.request.currency } {this.props.request.order_invoice.delivery_amount}</dd>   
                                    <dt>Discount</dt>
                                    <dd>{ this.props.request.currency } {this.props.request.order_invoice.discount}</dd>   
                                    <hr />
                                    <dt>Total (including tax + Packing) </dt>
                                    <dd id="total_amount">{ this.props.request.currency } {this.props.request.order_invoice.payable}</dd>
                                    <hr />
                                </dl>

                                { this.props.request.leave_at_door == 1 && <div style={{ background: '#FFF6F4', color: '#BC6750', border: '1px solid #BC6750', padding: '10px', textAlign: 'center', fontWeight: 'bold' }}>Please leave the order at door/gate.</div> }

                            </div>
                            {/* <div className="col-xs-12 col-sm-12 col-md-1 col-lg-1 p-0 arrow">
                                <i className="fa fa-location-arrow"></i>
                                </div>  */}  
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
                    {/* <!-- Popup Modal --> */}   
                    
                    <div className="modal" id="otpModal">
                        <div className="modal-dialog">
                            <div className="modal-content">
                                <div className="modal-header">
                                <h4 className="modal-title">Payment Slip</h4>
                                <button type="button" className="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div className="modal-body">
                                    <div className="dis-center mt-2 mb-2">
                                        <img src="/assets/layout/images/order/svg/hand.svg" className="w-30" />
                                    </div>
                                    <form id="ride_creation">
                                        <dl className="dl-horizontal left-right mt-2">
                                            <dt>Amount To Pay</dt>
                                            <dd className="mb-1">{this.props.request.currency}{this.props.request.order_invoice.payable}</dd>
                                            {/* <dt>Enter The Amount Paid</dt>
                                            <dd className="mb-1">$60</dd>
                                            <dt>Balance</dt>
                                            <dd className="mb-1">$7</dd> */}
                                            <dt>Enter OTP</dt>
                                            <dd>
                                                <input id="otp" className="form-control w-50 float-right m-0" maxLength="4" placeholder="*  *  *  *" autoComplete="off"
                                                type="text" name="otp" />
                                            </dd>
                                        </dl>
                                        <br />
                                    </form>
                                </div>
                                <div className="modal-footer">
                                    {/* {this.props.request.status == "ARRIVED" ? */}
                                    <a id="confirm_pay" className="btn btn-primary btn-md mr-2" onClick={this.handleOtpUpdate.bind(this, 'PAYMENT')}>Confirm Payment <i className="fa fa-check" aria-hidden="true"></i></a>
                                    { /* : ""} */ }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                 
                <div id="toaster" className="toaster"></div>
                <div className="actions col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    {this.props.request.status == "PROCESSING" ? <a id="arrived" className="btn btn-primary btn-md"  onClick={this.handleUpdateStatus.bind(this, 'STARTED')}>Started Towards Shop <i className="fa fa-check" aria-hidden="true"></i></a> : ""}
                    {this.props.request.status == "STARTED" ? <a id="started" className="btn btn-primary btn-md"  onClick={this.handleUpdateStatus.bind(this, 'REACHED')}>Reached Shop <i className="fa fa-check" aria-hidden="true"></i></a> : ""}
                    {this.props.request.status == "REACHED" ?<a id="start_serv" className="btn btn-primary btn-md"  onClick={this.handleUpdateStatus.bind(this, 'PICKEDUP')} >Order Picked Up <i className="fa fa-check" aria-hidden="true"></i></a> : ""}
                    {this.props.request.status == "PICKEDUP" ?<a id="stop_serv" className="btn btn-primary btn-md mr-2"  onClick={this.handleUpdateStatus.bind(this, 'ARRIVED')}>Reached Delivery Location<i className="fa fa-check" aria-hidden="true"></i></a>: ""}
                    {this.props.request.status == "DELIVERED" ?<a id="confirm_serv" className="btn btn-primary btn-md mr-2"  onClick={this.handleUpdateStatus.bind(this, 'DELIVERED')}> Delivered & Payment Received <i className="fa fa-check" aria-hidden="true"></i></a>: ""}
                    {(this.props.request.status == "ARRIVED" && otpState =="0")  ?<a id="confirm_serv" className="btn btn-primary btn-md mr-2"  onClick={this.handleUpdateStatus.bind(this, 'PAYMENT')}> Confirm <i className="fa fa-check" aria-hidden="true"></i></a>: ""}
                </div>  
                <div id="message_container">
                    <h5 className="text-left mt-3">Chat</h5>
                    <div id="message_box" className="height20vh chat-section mt-1 bg-white"></div>
                    <div className="message-typing">
                        <input className="form-control" name="message" onKeyUp={this.handleSendMessage} placeholder="Enter Your Message..." />
                        <a className="btn btn-primary "  onClick={this.handleSendMessage} > <i className="fa fa-paper-plane" aria-hidden="true"></i></a>
                    </div>
                </div>               
               </div>
            </div>
            <div  className="col-sm-12 col-md-12 col-lg-6 map-section">
               <img style={{ height:'500px', boxShadow: "2px 2px 10px #ccc" }} src={ "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=620x380&maptype=terrian&format=png&visual_refresh=true&markers=icon:"+window.url+"/assets/layout/images/common/marker-start.png%7C"+this.props.request.stores_details.latitude+","+this.props.request.stores_details.longitude+"&markers=icon:"+window.url+"/assets/layout/images/common/marker-end.png%7C"+this.props.request.delivery.latitude+","+this.props.request.delivery.longitude+"&path=color:0x191919|weight:3|enc:" + this.props.request.route_key + "&key=" + getSiteSettings().site.browser_key } />

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
    	if(this.props.request.paid === 1) $("#rating").modal("show");
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
            success: (data, textStatus, jqXHR) => {
                $("#rating").modal("show");
            },
            error: (jqXHR, textStatus, errorThrown) => {}
        });
    };

    handleRating = e => {
        let id = this.props.request.id;
        let rating = $("input[name=rating]:checked").val();
        let comment = $("textarea[name=comment]").val();
        let admin_service = this.props.request.admin_service;
        
        $.ajax({
            url: getBaseUrl() + "/provider/rate/order",
            type: "post",
            data: {
                id, rating, comment, admin_service
            },
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            success: (data, textStatus, jqXHR) => {
                $("#rating").modal("hide");
                window.location.replace("/provider/home");
            },
            error: (jqXHR, textStatus, errorThrown) => {
                this.setState({
                    commentLength:jqXHR.responseJSON.message
                });
            }
        });
    };

    render() {
        
        var rows=[];
        var starcount = this.props.request.stores_details.rating;
        for (var i=0;i<starcount;i++){
            rows.push(<div className="rating-symbol" key={i} style={{display: 'inline-block', position: 'relative'}}>
            <div className="fa fa-star-o" style={{visibility: 'hidden'}}></div>
            <div className="rating-symbol-foreground" style={{display: 'inline-block', position: 'absolute', overflow: 'hidden', left: '0px', right: '0px', top: '0px', width: 'auto'}}>
                <span className="fa fa-star" aria-hidden="true"></span>
            </div>
        </div>);
        }
        var items=[];
        var itemsData = this.props.request.order_invoice.items;
        var itemscount = itemsData.length;

        for (var i=0;i<itemscount;i++){
            var cartaddon=[];
            var addOnData = itemsData[i].cartaddon;
            var addoncount = addOnData.length;
            var quantityVal = itemsData[i].quantity != null && itemsData[i].quantity != undefined ? itemsData[i].quantity : 1;
            for (var j=0;j<addoncount;j++){
                cartaddon.push(<div key={j}><dt><small>Addon: { addOnData[j].addon }</small> </dt>
                    <dd><small>{ addOnData[j].addon_price } x { quantityVal }</small></dd></div>);
            }
            items.push(<div key={i}>
                <dl className="dl-horizontal left-right mt-2" key={i}>
                     <dt>{ itemsData[i].product.item_name } </dt>
                     <dd>{ itemsData[i].item_price } x { itemsData[i].quantity }</dd>                                
                     <dl key={j}>{cartaddon}</dl>
                  </dl>
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
                            <li role="tab" aria-disabled="false" id="started_tab" className={(this.props.request.status == "STARTED" || this.props.request.status == "REACHED" ||this.props.request.status == "ARRIVED" || this.props.request.status == "PICKEDUP" || this.props.request.status == "DELIVERED" || this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="true">
                              <a id="form-total-t-0" href="#form-total-h-0" aria-controls="form-total-p-0">
                                 <span className="current-info audible"> </span>
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/order/svg/scooter.svg" /></span>
                                    <span className="step-text">Started</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="reached_tab" className={(this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="true">
                              <a id="form-total-t-0" href="#form-total-h-0" aria-controls="form-total-p-0">
                                 <span className="current-info audible"> </span>
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/order/svg/scooter.svg" /></span>
                                    <span className="step-text">Arrived</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="picked_tab" className={(this.props.request.status == "COMPLETED" )   ? "current" : ""} aria-selected="false">
                              <a id="form-total-t-1" href="#form-total-h-1" aria-controls="form-total-p-1">
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/order/svg/meal.svg" /></span>
                                    <span className="step-text">Picked</span>
                                 </div>
                              </a>
                           </li>
                           <li role="tab" aria-disabled="false" id="delivered_tab" className={( this.props.request.status == "COMPLETED")   ? "current" : ""} aria-selected="false">
                              <a id="form-total-t-2" href="#form-total-h-2" aria-controls="form-total-p-2">
                                 <div className="title">
                                    <span className="step-icon"><img src="/assets/layout/images/order/svg/breakfast-delivery-service.svg"/></span>
                                    <span className="step-text">Delivered</span>
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
                            <img src={this.props.request.stores_details.picture} className="user-img" />
                            <div className="user-right">
                            <h5>{this.props.request.stores_details.store_name}</h5>
                            <span className="pull-right c-pointer" style={{float:'right'}} className="dropdown-toggle" data-toggle="dropdown"><i className="material-icons">more_vert</i></span>  
                            <div className="dropdown-menu">
                            <a className="dropdown-item" data-toggle="modal" data-target="#contactModal"><i className="material-icons">phone</i> Contact </a>
                            {this.props.request.status == "STARTED"||this.props.request.status == "PROCESSING" || this.props.request.status == "REACHED" ?<a className="dropdown-item" data-toggle="modal" data-target="#cancelModal" onClick={this.handleCancelModal}> <i className="material-icons">cancel</i> Cancel Order </a> : ""}
                            </div>
                            <div className="rating-outer">
                                <span style={{cursor: 'default'}}>
                                {rows}
                                </span>
                                <input type="hidden" className="rating" value="1" disabled="disabled" />
                            </div>
                            <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0 d-flex">
                                <div className="col-xs-12 col-sm-12 col-md-10 col-lg-11 p-0">
                                    <h5>Pickup Location</h5>
                                    <p>{this.props.request.pickup.store_location}</p>
                                    <h5>Delivery Location</h5>
                                    <p>{this.props.request.delivery.flat_no} {this.props.request.delivery.street} {this.props.request.delivery.map_address}</p>
                                    <h5>Items</h5>
                                    {items}
                                    <dl className="dl-horizontal left-right mt-2">
                                        <dt>Packing Charges</dt>
                                        <dd>{this.props.request.order_invoice.store_package_amount}</dd>
                                        <dt>Service Tax</dt>
                                        <dd>{this.props.request.order_invoice.tax_amount}</dd>
                                        <dt>Delivery Charges</dt>
                                        <dd>{this.props.request.order_invoice.delivery_amount}</dd>
                                        <dt>Discount</dt>
                                        <dd>{ this.props.request.currency } {this.props.request.order_invoice.discount}</dd>   
                                        <hr />
                                        <dt>Total (including tax + Packing) </dt>
                                        <dd id="total_amount">{this.props.request.order_invoice.payable}</dd>
                                        <hr />
                                    </dl>
                                </div>                            
                            </div> 
                            {getSiteSettings().order.order_otp==1 ? 
                            ((this.props.request.order_invoice.payment_mode == "CASH" && getSiteSettings().order.order_otp==1) ? <a id="confirm-invoice" onClick={this.handlePayment} className="btn btn-primary btn-block">Paid <i className="fa fa-check" aria-hidden="true"></i></a> : <h4 style={{textAlign: "center", width: "100%", float: "left"}}>Waiting for Payment</h4> )
                            :""}
                        </div>     
                        </form>
                    </div> 
                    {/* <!-- Rating Modal --> */}
                    <div className="modal" id="rating">
                            <div className="modal-dialog">
                                <div className="modal-content">
                                    {/* <!-- Rating Header --> */}
                                    <div className="modal-header">
                                    <h4 className="modal-title">Rating</h4>
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
                                            <textarea className="form-control" rows="4" maxLength="255" cols="50" name="comment" placeholder="Leave Your Comments..."></textarea>
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
            </div>
            <div  className="col-sm-12 col-md-12 col-lg-6 map-section">
               <img style={{ height:'500px',width:'550px',boxShadow: "2px 2px 10px #ccc" }} src={ "https://maps.googleapis.com/maps/api/staticmap?autoscale=1&size=620x380&maptype=terrian&format=png&visual_refresh=true&markers=icon:"+window.url+"/assets/layout/images/common/marker-start.png%7C"+this.props.request.stores_details.latitude+","+this.props.request.stores_details.longitude+"&markers=icon:"+window.url+"/assets/layout/images/common/marker-end.png%7C"+this.props.request.delivery.latitude+","+this.props.request.delivery.longitude+"&path=color:0x191919|weight:3|enc:" + this.props.request.route_key + "&key=" + getSiteSettings().site.browser_key } />
            </div>
            {this.state.cancelModal ? <CancelComponent request={this.props.request} /> : '' }
                </div>
    </section>
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
            reason: '',
            error_message:''
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
         (this.state.reason == '') ? this.setState({error_message: 'Cancel reason is required!'}) : this.setState({error_message: ''});
        let id = this.props.request.id;

        if(this.state.reason != '') {
            $.ajax({
                url: getBaseUrl() + "/provider/cancel/order/request",
                type: "post",
                data: {
                    id: id,
                    reason: this.state.reason
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
                        <textarea name="cancel" rows="5"  onChange={this.handleChange}  id="contact" class="form-control"></textarea>                        
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
