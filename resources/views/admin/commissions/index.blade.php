@extends('admin.layout')
@section('title', '')
@section('content')
<div class="row mb-4 mt-20">
    <div class="col-md-12">
	    <div class="card card-small">
	         <div class="card-header border-bottom">
	            <h6 class="m-0 pull-left">Commission</h6>
	        </div>
	        <div class="card-body">
                <div class="row">
                    <div class="col-md-5 mx-auto">
                        <form id="percentageForm" action="{{route('admin.commissions.store', ['locale' => request()->query('locale')])}}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="percentage">Percentage</label>
                                <input type="number" class="form-control" id="percentage" name="percentage" value="{{old('percentage') ?? isset($data) ? $data->percentage : ""}}" placeholder="Percentage" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option {{ isset($data) ? ($data->status == 1 ? "selected" : "") : "" }} value="1">Active</option>
                                    <option {{ isset($data) ? ($data->status == 0 ? "selected" : "") : "" }} value="0">Inactive</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary float-right">Submit</button>
                        </form>
                    </div>
                </div>
	        </div>
  		</div>
    </div>
</div>


<script type="text/javascript">
    $('#percentageForm').validate({
        rules: {
            percentage: {
                required: true,
                number: true,
                min: 0,
                max: 100
            },
            status: {
                required: true
            }
        },
        messages: {
            percentage: {
                required: "Please enter percentage",
                number: "Please enter valid number",
                min: "Please enter min 0",
                max: "Please enter max 100"
            },
            status: {
                required: "Please select status"
            }
        },
        errorPlacement: function (label, element, errorClass, validClass) {
			label.addClass('invalid-feedback');
			label.insertAfter(element);
		},
		highlight: function (element, errorClass) {
			// $(element).parent().addClass('validation-error')
			$(element).addClass('is-invalid').removeClass('is-valid');
		},
		unhighlight: function (element, errorClass) {
			// $(element).parent().removeClass('validation-error')
			$(element).removeClass('is-invalid').addClass('is-valid');
		},
        submitHandler: function (form){
            if(!$(form).valid()) return false;
            $(form).find('button[type="submit"]').attr('disabled', true).html('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            form.submit();
        }
    });
</script>

@endsection
