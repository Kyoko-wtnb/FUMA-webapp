<div id="queryJob" class="sidePanel container" style="padding-top:50px; height:100vh;">
  {!! Form::open(array('url' => 'snp2gene/queryJob')) !!}
  <!-- Query existing job -->
  <h3>Query existing job</h3>
  <div class="panel panel-default">
    <div class="panel-heading">
      <div class="panel-title">Email address and Job title</div>
    </div>
    <div class="panel-body">
      E-mail address: <input type="text" name="JobQueryEmail" id="JobQueryEmail" onkeyup="JobQueryCheck();" onpaste="JobQueryCheck();"  oninput="JobQueryCheck();"/><br/>
      <div id="existing_jobs"></div>
      Job title: <input type="text" name="JobQueryTitle" id="JobQueryTitle" onkeyup="JobQueryCheck();" onpaste="JobQueryCheck();"  oninput="JobQueryCheck();"/><br/>
      <div id="JobQueryChecked"></div>
    </div>
  </div>
  <input class="btn" type="submit" value="Go to Job" name="go2job" id="go2job"/>
  {!! Form::close() !!}
</div>
