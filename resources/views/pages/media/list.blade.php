<div class="modal fade" tabindex="-1" role="dialog" id="modal-media">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Select Image</h4>
      </div>
      <div class="modal-body">
        <div class="clearfix">
        	<ul class="nav nav-tabs" role="tablist">
				<li class="active"><a href="#media" role="tab" data-toggle="tab">Media</a></li>
				<li><a href="#upload" role="tab" data-toggle="tab">Upload</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane fade in active" id="media">
					<div class="loading">
	        			<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
	        		</div>
					<div class="col-md-9 media-wrapper">
		        		<div class="col-md-12" style="margin: 20px 0px;">
		        			<div class="pull-right">
		        				<input type="text" name="keyword" placeholder="Search ..." class="form-control">
		        			</div>
		        		</div>

		        		<div style="display: inline-block; width: 100%">
		        			
			        		<div class="preview" style="width: 100%">
			        			<div class="template no-gutter" style="width: 100%">
				        			<div class="col-md-2">
					        			<div class="progress-wrapper">
				        					<div id="progress-format1" class="progress">
												<div class="progress-bar progress-bar-success"  data-dz-uploadprogress style="width: 0%;">0%</div>
											</div>
				        				</div>
			        					<img data-dz-thumbnail class="img img-responsive">
			        				</div>
			        			</div>
			        		</div>

			        		<span class="load-data image-browser no-gutter">
			        			
			        		</span>

		        		</div>
		        	</div>
		        	<div class="col-md-3 attachment-details no-gutter">
		        		<div class="details" style="display: none">
			        		<div class="row no-gutter">
			        			<h4 class="text-muted">Attachment Details</h4>
			        			<div class="col-md-6">
			        				<img class="img img-responsive thumbnail" style="object-fit: cover">
			        			</div>
			        			<div class="col-md-6">
			        				<ul>
			        					<li class="filename"></li>
			        					<li class="created_at"></li>
			        					<li class="filesize"></li>
			        				</ul>
			        			</div>
			        			<div class="col-md-12 text-center" style="margin-top: 20px">
			        				<button type="button" class="btn btn-primary btn-sm">Edit Image</button>
									<button type="button" class="btn btn-danger btn-sm remove-img">Remove</button>
			        			</div>
			        		</div>
			        		<div class="col-md-12">
		        				<hr>
		        				<input type="text" name="media_id" hidden="hidden">
			        			<div class="form-group">
			        				<label class="control-label">Title</label>
			        				<input type="text" name="filename" value="Uploaded File" class="form-control">
			        			</div>
			        			<div class="form-group">
			        				<label class="control-label">Caption</label>
			        				<textarea name="caption" class="form-control"></textarea>
			        			</div>
			        			<div class="form-group">
			        				<label class="control-label">Alt</label>
			        				<input type="text" name="alt" class="form-control">
			        			</div>
			        			<div class="form-group">
			        				<label class="control-label">Description</label>
			        				<textarea name="image_description" class="form-control"></textarea>
			        			</div>
		        			</div>
		        		</div>
		        	</div>
		        </div>

		        <div class="tab-pane fade" id="upload">
					<div class="col-md-12">
						<div class="drag-and-drop">
							<p>Drag and drop file(s) here <br>
							or
							</p>
							<button class="btn btn-primary" type="button" id="browse-file">Browse File(s)</button>
						</div>
					</div>
				</div>

				</div>
				
			</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="btn-use">Use selected image</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->