@if($errors->any())
    {{--<div class="container">--}}
    <div class="row">
        <div class="col-md-10">
            <div class="alert alert-danger" role="alert">
                <ol>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ol>
            </div>
        </div>
        {{--</div>--}}
    </div>
@endif