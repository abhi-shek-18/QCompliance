<!DOCTYPE html>

<html lang="en">

<!-- begin::Head -->
<head>
	<meta charset="utf-8" />
	<title>QM Tool</title>
	<meta name="description" content="QuoteNow">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!--begin::Fonts -->
	<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
	<script>
		WebFont.load({
			google: {
				"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
			},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!--end::Fonts -->
	<style type="text/css">
		table tr td
		{
			text-align: justify;
		}
	</style>
	<!--begin::Page Vendors Styles(used by this page) -->
	{!! Html::style('assets/vendors/custom/datatables/datatables.bundle.css') !!}
	{!! Html::style('assets/vendors/base/vendors.bundle.css') !!}

	<!--RTL version:{!! Html::style('assets/vendors/base/vendors.bundle.rtl.css') !!}-->
	{!! Html::style('assets/demo/default/base/style.bundle.css') !!}

	<!--RTL version:{!! Html::style('assets/demo/default/base/style.bundle.rtl.css') !!}-->

	<!--end::Global Theme Styles -->

	<!--begin::Layout Skins(used by all pages) -->
	{!! Html::style('assets/demo/default/skins/header/base/light.css') !!}

	<!--RTL version:{!! Html::style('assets/demo/default/skins/header/base/light.rtl.css') !!}-->
	{!! Html::style('assets/demo/default/skins/header/menu/light.css') !!}

	<!--RTL version:{!! Html::style('assets/demo/default/skins/header/menu/light.rtl.css') !!}-->
	{!! Html::style('assets/demo/default/skins/brand/dark.css') !!}

	<!--RTL version:{!! Html::style('assets/demo/default/skins/brand/dark.rtl.css') !!}-->
	{!! Html::style('assets/demo/default/skins/aside/dark.css') !!}

	<!--RTL version:{!! Html::style('assets/demo/default/skins/aside/dark.rtl.css') !!}-->

	<!--end::Layout Skins -->
	<link rel="shortcut icon" href="{{url('assets/media/logos/logo_default_dark.png')}}" />
</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">
	<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">

		<div class="row">
			<div class="col-md-12">
				<img src="/img/bg.png" style="width: 100%;">
			</div>
		</div>
		<br/>
									<!--begin::Portlet-->
									<div class="kt-portlet kt-portlet--mobile">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													Basic Details
												</h3>
											</div>
										</div>
										<div class="kt-portlet__body">
											<form class="kt-form kt-form--label-right">
											<div class="form-group row">
												<div class="col-lg-4">
													<label>Client:</label>
													<input type="text" class="form-control">
												</div>
												<div class="col-lg-4">
													<label>Partner:</label>
													<input type="text" class="form-control">
												</div>
												<div class="col-lg-4">
													<label>Audit Date & Time:</label>
													<input type="text" class="form-control" placeholder="Enter date and time">
												</div>
											</div>
											<div class="form-group row">
												<div class="col-lg-4">
													<label>Agent Name:</label>
													<input type="text" class="form-control" placeholder="Enter agent">
												</div>
												<div class="col-lg-4">
													<label>QA Name:</label>
													<input type="text" class="form-control">
												</div>
												<div class="col-lg-4">
													<label>TL Name:</label>
													<input type="text" class="form-control" placeholder="Enter date and time">
												</div>
											</div>
											<div class="form-group row">
												<div class="col-lg-4">
													<label>Language:</label>
													<input type="text" class="form-control" placeholder="Enter agent">
												</div>
												<div class="col-lg-4">
													<label>Call Type:</label>
													<input type="text" class="form-control">
												</div>
												<div class="col-lg-4">
													<label>Reason of call:</label>
													<select class="form-control">
														<option>Query</option>
														<option>Request</option>
														<option>Complaint</option>
													</select>
												</div>
											</div>
											<div class="form-group row">
												<div class="col-lg-4">
													<label>Customer Name:</label>
													<input type="text" class="form-control" placeholder="Enter agent">
												</div>
												<div class="col-lg-4">
													<label>Cusotmer contact number:</label>
													<input type="text" class="form-control">
												</div>
												<div class="col-lg-4">
													<label>Service Partner Location:</label>
													<input type="text" class="form-control" placeholder="Enter date and time">
												</div>
											</div>
											</form>
										</div>
									</div>
									<!--end::Portlet-->

									<!--begin::Portlet-->
									<div class="kt-portlet kt-portlet--mobile">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													QM Sheet
												</h3>
											</div>
										</div>
										<div class="kt-portlet__body" style="overflow-y: scroll;">
											<!--begin: Datatable -->
									<table class="table table-striped- table-bordered table-hover">
										<thead>
											<tr>
												<th>Behavior</th>
												<th>Parameter</th>
												<th>Behavior Weightage</th>
												<th>Audit Observation</th>
												<th>Scored</th>
												<th>Failure Reason</th>
												<th>Remarks</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td rowspan="3">
												<div style="width: 150px; display: block;">Be Welcoming</div>
												</td>
												<td>
													<div style="width: 200px; display: block;">Initial warmth & greeting</div>
												</td>
												
												<td rowspan="3">
													15
												</td>
												<td>
													<select class="form-control" style="width:80px;">
														<option value="Yes">Yes</option>
														<option value="No">No</option>
														<option value="FATAL">FATAL</option>
														<option value="N/A">N/A</option>
													</select>
												</td>
												
												
												<td>
													<input type="text" class="form-control" style="width:100px;">
												</td>
												<td>
													<input type="text" class="form-control" style="width:100px;">
												</td>
												<td>
													<input type="text" class="form-control" style="width:100px;">
												</td>
											</tr>
											<tr>
												<td>Identification & Verification</td>
												

												<td>
													<select class="form-control" style="width:80px;">
														<option value="Yes">Yes</option>
														<option value="No">No</option>
														<option value="FATAL">FATAL</option>
														<option value="N/A">N/A</option>
													</select>
												</td>
												
												
												<td>
													<input type="text" class="form-control">
												</td>
												<td>
													<input type="text" class="form-control">
												</td>
												<td>
													<input type="text" class="form-control">
												</td>
											</tr>
											<tr>
												<td>Acknowledgement</td>
												

												<td>
													<select class="form-control" style="width:80px;">
														<option value="Yes">Yes</option>
														<option value="No">No</option>
														<option value="FATAL">FATAL</option>
														<option value="N/A">N/A</option>
													</select>
												</td>
												
												
												<td>
													<input type="text" class="form-control">
												</td>
												<td>
													<input type="text" class="form-control">
												</td>
												<td>
													<input type="text" class="form-control">
												</td>
											</tr>

											<tr>
												<td rowspan="3">
												<div style="width: 150px; display: block;">Be Inquisitive</div>
												</td>
												<td>
													<div style="width: 200px; display: block;">Issue Understanding</div>
												</td>
												
												<td rowspan="3">
													17
												</td>
												<td>
													<select class="form-control" style="width:80px;">
														<option value="Yes">Yes</option>
														<option value="No">No</option>
														<option value="FATAL">FATAL</option>
														<option value="N/A">N/A</option>
													</select>
												</td>
												
												
												<td>
													<input type="text" class="form-control" style="width:100px;">
												</td>
												<td>
													<input type="text" class="form-control" style="width:100px;">
												</td>
												<td>
													<input type="text" class="form-control" style="width:100px;">
												</td>
											</tr>
											<tr>
												<td>Listening Skills</td>
												

												<td>
													<select class="form-control" style="width:80px;">
														<option value="Yes">Yes</option>
														<option value="No">No</option>
														<option value="FATAL">FATAL</option>
														<option value="N/A">N/A</option>
													</select>
												</td>
												
												
												<td>
													<input type="text" class="form-control">
												</td>
												<td>
													<input type="text" class="form-control">
												</td>
												<td>
													<input type="text" class="form-control">
												</td>
											</tr>
											<tr>
												<td>Confirmation of Details</td>
												

												<td>
													<select class="form-control" style="width:80px;">
														<option value="Yes">Yes</option>
														<option value="No">No</option>
														<option value="FATAL">FATAL</option>
														<option value="N/A">N/A</option>
													</select>
												</td>
												
												
												<td>
													<input type="text" class="form-control">
												</td>
												<td>
													<input type="text" class="form-control">
												</td>
												<td>
													<input type="text" class="form-control">
												</td>
											</tr>


											<tr> 
    <td rowspan="3">Be Knowledgeable</td>
    <td>FTR</td>
    
    <td rowspan="3">25</td>
    <td ><font face="Calibri Light"> 
      <select class="form-control">
        <option>Yes</option>
        <option>No</option>
        <option>Fatal</option>
        <option>NA</option>
      </select>
      </font></td>
    
    
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td >&nbsp;</td>
  </tr>

  <tr> 
    <td>System Documentation</td>
    
    <td ><font face="Calibri Light"> 
      <select class="form-control">
        <option>Yes</option>
        <option>No</option>
        <option>Fatal</option>
        <option>NA</option>
      </select>
      </font></td>
    

    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td >Internal/External Handoff</td>
    
    <td ><font face="Calibri Light"> 
      <select class="form-control">
        <option>Yes</option>
        <option>No</option>
        <option>Fatal</option>
        <option>NA</option>
      </select>
      </font></td>
    
    
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td >&nbsp;</td>
  </tr>

  <tr> 
    <td rowspan="4">Be Professional</td>
    <td>Hold/Transfer Procedures</td>
    
    <td rowspan="4">25</td>
    <td ><font face="Calibri Light"> 
      <select class="form-control">
        <option>Yes</option>
        <option>No</option>
        <option>Fatal</option>
        <option>NA</option>
      </select>
      </font></td>
    
    
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td >&nbsp;</td>
  </tr>
  <tr> 
    <td >Call Control</td>
    
    <td ><font face="Calibri Light"> 
      <select class="form-control">
        <option>Yes</option>
        <option>No</option>
        <option>Fatal</option>
        <option>NA</option>
      </select>
      </font></td>
    
    
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td >&nbsp;</td>
  </tr>
  <tr> 
    <td>Language Proficiency</td>
    
    <td ><font face="Calibri Light"> 
      <select class="form-control">
        <option>Yes</option>
        <option>No</option>
        <option>Fatal</option>
        <option>NA</option>
      </select>
      </font></td>
    
    
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>Usage of Customer Name</td>
    
    <td ><font face="Calibri Light"> 
      <select class="form-control">
        <option>Yes</option>
        <option>No</option>
        <option>Fatal</option>
        <option>NA</option>
      </select>
      </font></td>
    
    
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td>&nbsp;</td>
  </tr>

  <tr> 
    <td rowspan="3">Be Memorable</td>
    <td>Sense Check</td>
    
    <td rowspan="3">18</td>
    <td ><font face="Calibri Light"> 
      <select class="form-control">
        <option>Yes</option>
        <option>No</option>
        <option>Fatal</option>
        <option>NA</option>
      </select>
      </font></td>
    
    
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>Further Assistance</td>
    
    <td ><font face="Calibri Light"> 
      <select class="form-control">
        <option>Yes</option>
        <option>No</option>
        <option>Fatal</option>
        <option>NA</option>
      </select>
      </font></td>
    
    
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>Branded Farewell</td>
    
    <td ><font face="Calibri Light"> 
      <select class="form-control">
        <option>Yes</option>
        <option>No</option>
        <option>Fatal</option>
        <option>NA</option>
      </select>
      </font></td>
    
    
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td>&nbsp;</td>
  </tr>

  <tr> 
    <td rowspan="2">In Customer Shoes</td>
    <td>Is there a potential for repeat ?</td>
    
    <td rowspan="2">NA</td>
    <td ><font face="Calibri Light">
      <select class="form-control">
        <option>Yes</option>
        <option>No</option>
        <option>Fatal</option>
        <option>NA</option>
      </select>
      </font></td>
    
    
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>Quality of service</td>
    
    <td><font face="Calibri Light">
      <select class="form-control">
        <option>Poor</option>
        <option>Excellent</option>
        <option>Good</option>
        <option>Bad</option>
      </select>
      </font></td>
    
    
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  
											
											</tbody>
											</table>	
										</div>
									</div>
									<!--end::Portlet-->

									<!--begin::Portlet-->
									<div class="kt-portlet kt-portlet--mobile">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													Audit Result
												</h3>
											</div>
										</div>
										<div class="kt-portlet__body">
											<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
										<thead>
											<tr>
												<th colspan="2">&nbsp;</th>
												<th colspan="2">Scored</th>
												<th colspan="2">Scored %</th>
											</tr>
											<tr>
												<th>Behaviour</th>
												<th>Scorable</th>
												<th>With FATAL</th>
												<th>WithOut FATAL</th>
												<th>With FATAL</th>
												<th>WithOut FATAL</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>Be Welcoming</td>
												<td>0</td>
												<td>0</td>
												<td>10</td>
												<td>N/A</td>
												<td>0</td>
											</tr>
											<tr>
												<td>Be Inquisitive</td>
												<td>0</td>
												<td>0</td>
												<td>10</td>
												<td>N/A</td>
												<td>0</td>
											</tr>
											<tr>
												<td>Be Knowledgeable</td>
												<td>0</td>
												<td>0</td>
												<td>10</td>
												<td>N/A</td>
												<td>0</td>
											</tr>
											<tr>
												<td>Be Professional</td>
												<td>0</td>
												<td>0</td>
												<td>10</td>
												<td>N/A</td>
												<td>0</td>
											</tr>
											<tr>
												<td>Be Memorable</td>
												<td>0</td>
												<td>0</td>
												<td>10</td>
												<td>N/A</td>
												<td>0</td>
											</tr>
											<tr class="text-primary">
												<td>Over All</td>
												<td>0</td>
												<td>0</td>
												<td>10</td>
												<td>N/A</td>
												<td>0</td>
											</tr>
										</tbody>
									</table>
										</div>
									</div>
									<!--end::Portlet-->

									<div class="row">
										<div class="col-md-2">
											<button type="button" class="btn btn-success">Submit</button>&nbsp;&nbsp;&nbsp;
											<button type="button" class="btn btn-danger">Cancel</button>
										</div>
										<div class="col-md-10">
											<p align="right"><a href="{{'/audit_list'}}" class="btn btn-warning">Back to List</a></p>
										</div>
									</div>
	</div>



	<!-- begin::Global Config(global config for global JS sciprts) -->
	
	<script>
		var KTAppOptions = {
			"colors": {
				"state": {
					"brand": "#5d78ff",
					"dark": "#282a3c",
					"light": "#ffffff",
					"primary": "#5867dd",
					"success": "#34bfa3",
					"info": "#36a3f7",
					"warning": "#ffb822",
					"danger": "#fd3995"
				},
				"base": {
					"label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
					"shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
				}
			}
		};
	</script>

	<!-- end::Global Config -->

	<!--begin::Global Theme Bundle(used by all pages) -->
	{!! Html::script('assets/vendors/base/vendors.bundle.js')!!}
	{!! Html::script('assets/demo/default/base/scripts.bundle.js')!!}

	<!--begin::Page Vendors(used by this page) -->
	{!! Html::script('assets/vendors/custom/datatables/datatables.bundle.js')!!}
	<!--end::Page Vendors -->
	<!--begin::Page Scripts(used by this page) -->
	{!! Html::script('assets/app/custom/general/crud/datatables/basic/scrollable.js')!!}
	<!--end::Page Scripts -->
	{!! Html::script('assets/app/bundle/app.bundle.js')!!}
	{!! Html::script('assets/app/custom/general/my-script.js')!!}
	

	<!--begin::Global App Bundle(used by all pages) -->
</body>

<!-- end::Body -->
</html>