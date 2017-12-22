@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Please proof your identity by uploading requested documents
                    </div>
                    <div class="panel-body">
                        @if(!$kyc)
                            <form action="{{ url('kyc') }}" method="post" enctype="multipart/form-data" class="form-horizontal">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label for="title" class="col-md-4 control-label">Passport Photo</label>
                                    <div class="col-md-6">
                                        <input type="file" name="passport">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="title" class="col-md-4 control-label">Utility Bill Photo</label>
                                    <div class="col-md-6">
                                        <input type="file" name="utility_bill">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="title" class="col-md-4 control-label">Selfie With Passport</label>
                                    <div class="col-md-6">
                                        <input type="file" name="selfie_with_passport">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="title" class="col-md-4 control-label">Submit</label>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        @endif()

                        @if($kyc)
                            <p>Validation process status: <b>{{ ($kyc->verification_passed == 0) ? 'Verification in process' : 'Verified' }}</b></p>

                            <div class="row">
                                <div class="col-md-4">
                                    <p>Passport</p>
                                    <img class="img-responsive" src="{{ Storage::disk('public')->url($kyc->passport) }}" alt="">
                                </div>
                                <div class="col-md-4">
                                    <p>Utility bill</p>
                                    <img class="img-responsive" src="{{ Storage::disk('public')->url($kyc->utility_bill) }}" alt="">
                                </div>
                                <div class="col-md-4">
                                    <p>Selfie with passport</p>
                                    <img class="img-responsive" src="{{ Storage::disk('public')->url($kyc->selfie_with_passport) }}" alt="">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
