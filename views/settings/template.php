<div class="herc-bootstrap">
	<h3>Hercules Sentiment Settings</h3>
	{{#if updated}}
	<div class="row">
		<div class="col-md-6">
			<div class="alert alert-success" role="alert"><strong>Success!</strong> Your settings were updated!</div>
		</div>
	</div>
	<div class="clearfix"></div>
	{{/if}}
	<div class="row">
		<div class="col-md-6">
			<form action="{{current_page}}" method="post">
				<div class="form-group">
					<label for="exampleInputEmail1">Auto approve positive comments {{auto_approve_positive_comments}}</label>
					<select class="form-control" name="auto_approve_positive_comments">
						<option value="no" {{#if_eq auto_approve_positive_comments 'no'}}selected{{/if_eq}}>No</option>
						<option value="yes" {{#if_eq auto_approve_positive_comments 'yes'}}selected{{/if_eq}}>Yes</option>
					</select>
					<p class="help-block">*Sentiment analysis is not perfect, you may still want to manually review comments.</p>
				</div>
				<button type="submit" class="btn btn-default">Submit</button>
			</form>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="row" style="margin-top:50px;">
		<div class="col-md-6">
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="GDBHPL4Y24ZXQ">
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
	</div>
</div>