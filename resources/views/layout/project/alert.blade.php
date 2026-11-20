@if (count($errors) > 0)
    <div class="alert alert-error">
	    <button class="close" data-dismiss="alert">×</button>
	    <strong>Error! </strong>
	    <ul>
	    @foreach ($errors->all() as $error)
	        <li>{{ $error }}</li>
	    @endforeach
	    </ul>
    </div>
@endif 

@if (Session::get('message') && (Session::get('type')=='success'))
<div class="alert alert-success">
	<button class="close" data-dismiss="alert">×</button>
	<strong>Success! </strong>
	{{ Session::get('message') }}
</div>
@endif

@if (Session::get('message') && (Session::get('type')=='error'))
<div class="alert alert-error">
	<button class="close" data-dismiss="alert">×</button>
	<strong>Error! </strong>
	{{ Session::get('message') }}
</div>
@endif
												