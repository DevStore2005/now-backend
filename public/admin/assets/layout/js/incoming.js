class MainComponent extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            requests: [],
            account_status: "",
            service_status: "",
            status: "",
            time: 0,
            provider: {}
        };
    }

    componentDidMount() {

        this.getStatus();

        var that = this;
        var socket = io.connect(window.socket_url);
        socket.emit('joinCommonRoom', `${window.common_room}`);
        socket.emit('joinCommonProviderRoom', `${window.provider_room}`);
        socket.on('approval', function (data) {
           window.location.replace("/provider/home");
        });
        
        socket.on('socketStatus', function (data) {
            if(window.env == 'local') 
            console.log(data);
        });
        socket.on('newRequest', function (data) {
            that.getStatus();
        });
        
        setInterval(() => this.getStatus(), 10000);
    }

    getStatus() {
        $.ajax({
            url: getBaseUrl() + "/provider/check/request",
            type: "get",
            processData: false,
            contentType: false,
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            success: (response, textStatus, jqXHR) => {
                var data = parseData(response);
                if((data.responseData.requests.length > 0)) {

                    this.setState({
                        account_status: data.responseData.account_status,
                        service_status: data.responseData.service_status,
                        requests:
                            (data.responseData.requests.length > 0)
                                ? data.responseData.requests[0]
                                : {},
                        time:
                            (data.responseData.requests.length > 0)
                                    ? data.responseData.requests[0].time_left_to_respond
                                    : 0,
                        status:
                            (data.responseData.requests.length > 0)
                                ? data.responseData.requests[0].request.status
                                : "",
                        provider: data.responseData.provider_details
                    });

                } else {
                    this.setState({
                        requests: {}
                    });
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {}
        });
    }

    render() {
        console.log(this.state.status);
        let status = this.state.status;
        let providerDetails = getProviderDetails();
        if( Object.keys(this.state.requests).length > 0 ) {
            if(this.state.account_status == "DOCUMENT" || this.state.account_status == "CARD" || this.state.account_status == "SERVICES" || this.state.account_status == "ONBOARDING") {
                //window.location.replace("/provider/document");
            } else {
                if (status == "SEARCHING") {
                    console.log('abaaaac');
                    return <RequestComponent request={this.state.requests} time={this.state.requests.time_left_to_respond} />;
                } else if (status != "" && providerDetails.id == this.state.requests.provider_id) {
                    console.log('bbb');
                    if(this.state.requests.service.admin_service == "TRANSPORT") {
                       window.location.replace("/provider/ride");
                    } else if(this.state.requests.service.admin_service  == "SERVICE") {
                        window.location.replace("/provider/service");
                    }else if(this.state.requests.service.admin_service  == "ORDER") {
                        window.location.replace("/provider/order");
                    }else{
                        window.location.replace("/provider/home");
                    }
                }
            }
        }
        

        return <NoServiceComponent request={this.state.requests} />;
    }
}

class RequestComponent extends React.Component {
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
            location.reload();
        }
    }

    handleAccept = e => {
        let id = this.props.request.request_id;
        let admin_service = this.props.request.admin_service;
        

        $.ajax({
            url: getBaseUrl() + "/provider/accept/request",
            type: "post",
            data: {
                id, admin_service
            },
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            beforeSend: function() {
                showLoader();
            },
            success: (response, textStatus, jqXHR) => {
                hideLoader();
                $(e.target).closest('.service-card').hide();
                var data = parseData(response);
                if(this.props.request.service.admin_service == "TRANSPORT") {
                    window.location.replace("/provider/ride");
                } else if(this.props.request.service.admin_service == "SERVICE") {
                    window.location.replace("/provider/service");
                }else if(this.props.request.service.admin_service == "ORDER") {
                    window.location.replace("/provider/order");
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.log(jqXHR);
                hideLoader();
                // alertMessage(textStatus, jqXHR.responseJSON.message, "danger");
            }
        });
    };

    handleReject = e => {
        let id = this.props.request.request.id;
        let admin_service = this.props.request.admin_service;

        $(e.target).closest('.service-card').hide();

        $.ajax({
            url: getBaseUrl() + "/provider/cancel/request",
            type: "post",
            headers: {
                Authorization: "Bearer " + getToken("provider")
            },
            data: {
                id, admin_service
            },
            success: (data, textStatus, jqXHR) => { 
                location.reload();
            },
            error: (jqXHR, textStatus, errorThrown) => {
                $(e.target).closest('.service-card').show();
                alertMessage(textStatus, jqXHR.responseJSON.message, "danger");
            }
        });
    };

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
            <div className="service-card col-xs-12 col-sm-12 col-md-5 col-lg-4 available">
                <div className="ribbon">{this.props.request.service.display_name}</div>
                <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0 mt-2">
                    <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0">
                    <img src={this.props.request.user.picture} className="user-img" />
                    <div className="user-right">
                        <h5>{this.props.request.user.first_name} {this.props.request.user.last_name}</h5>
                        <div className="rating-outer">
                            <span style={{cursor: 'default'}}>
                            {rows}                                   
                            </span>
                            <p className="float-right">{this.state.time} Secs left</p>
                        </div>
                    </div>
            </div>
            {this.props.request.service.admin_service == "SERVICE" ?
            <div>
            <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0 d-flex">
                <div className="col-xs-12 col-sm-12 col-md-6 col-lg-6 p-0">
                <h5>Service Type</h5>
                <p>{this.props.request.request.service.service_category.service_category_name} > {this.props.request.request.service.servicesub_category.service_subcategory_name} >  {this.props.request.service_details.service_name}</p>
                </div>
            </div>
            </div>
            :'' }
            {this.props.request.service.admin_service == "ORDER" ?
            <div>
                <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0 d-flex">
                    <div className="col-xs-12 col-sm-6 col-md-6 col-lg-6 p-0">
                        <h5>Shop Name</h5>
                        <p>{this.props.request.request.pickup.store_name}</p>
                    </div>
                    <div className="col-xs-12 col-sm-6 col-md-6 col-lg-6 p-0">
                        <h5>Type</h5>
                        <p>{this.props.request.request.pickup.storetype.name}</p>
                    </div>
                </div>
                <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0">
                    <h5>Shop Location</h5>
                    <p>{this.props.request.request.pickup.store_location}</p>
                </div>
                <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0">
                    <h5>Delivery Location</h5>
                    <p>{this.props.request.request.delivery.map_address}</p>
                </div>
            </div>
            :''}

            {this.props.request.service.admin_service == "SERVICE" ?
            <div><h5>Service Location</h5><p>{this.props.request.request.s_address}</p></div> :''}
            {this.props.request.service.admin_service == "TRANSPORT" ?
            <div><h5>Pickup Location</h5><p>{this.props.request.request.s_address}</p></div> :'' }
            

            <div className="actions col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <a className="btn btn-default btn-md mr-2" onClick={this.handleReject}>Reject <i className="fa fa-times" aria-hidden="true"></i></a>
                <a className="btn btn-primary btn-md" onClick={this.handleAccept}>Accept <i className="fa fa-check" aria-hidden="true"></i></a>
            </div>
            </div>
            </div>
        );
    }
}

class NoServiceComponent extends React.Component {

    render() {
        return <div className="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-service dis-center p-5">
                <h5 style={{marginTop:'15%'}}>No Ride Request Yet</h5>
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
            url: getBaseUrl() + "/provider/reasons?type=TRANSPORT",
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
            error: (jqXHR, textStatus, errorThrown) => {
                alertMessage(textStatus, jqXHR.responseJSON.message, "danger");
            }
        });

        $("#cancelModal").modal();
    }



    handleCancel = e => {
        
        let id = this.props.request.id;

        if(this.state.reason != '') {
            $.ajax({
                url: getBaseUrl() + "/provider/cancel/request",
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
