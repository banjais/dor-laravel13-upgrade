@extends('layout.client.container')
@section('content')
<?php
	$languageId = Session::get('language_id');
	$home =  'Home';
	$contentHeader =  'Related Pages';
?>



<div class="container">

        <div class="page-header">
        <div class="row">
          <div class="col-md-6 col-sm-6">
            <h2 class="page-title">Search</h2>
          </div> <!-- /.col-md-6 -->
          <div class="col-md-6 col-sm-6 hidden-xs back-home">
          <a href="{{ $abs_site_url . $subdomain }}">&larr;Go back {{ $home }}  </a>

          </div> <!-- /.col-md-6 -->
        </div> <!-- /.row -->
      </div><!-- /.page-header -->


<div class="row">
    <div class="col-md-12">
      <div class="box-content">
       <div class="blog-single">

          <!-- /.blog-thumb -->
          <div class="post-content">
          <script>
  (function() {
    var cx = '004184895998473855276:x5lwkhntaay';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//cse.google.com/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  })();
</script>
<gcse:searchresults-only></gcse:searchresults-only>

		   </div>
          <!-- /.blog-content -->
        </div>
        <!-- /.blog-single -->

      </div>

    </div>
    <!-- /.row -->

    <!-- /.box-content -->

    <!-- /.col-md-8 -->

  </div>
  <!-- /.row -->

</div>
<!-- /.container -->


@stop
