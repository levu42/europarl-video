<style type="text/css">
	@import url("<?php echo path_to_api() ?>/ui.css");
</style>
<div class="row">
	<div class="span6 box">
		<h2>Search plenary:</h2>
		<form action="#" method="get" class="form-horizontal">
			<input type="hidden" name="api" value="europarl-video">
			<!--<input type="hidden" name="function" value="search-plenary-by-mep">-->
			<input type="hidden" name="output" value="html">
			<fieldset>
				<legend>Search by</legend>
				<div class="control-group">
					<div class="controls" id="search-plen-mep-controls">
						<label class="radio">
							<input type="radio" name="function" value="search-plenary-by-mep" id="search-plenary-by-mep">
							Search by MEP
						</label>
						<p>
							<?php echo europarl_video_mep_select('search-plen-mep'); ?>
						</p>
					</div>
				</div>
				<div class="control-group">
					<div class="controls" id="search-plen-keyword-controls">
						<label class="radio">
							<input type="radio" name="function" value="search-plenary-by-keyword" id="search-plenary-by-keyword">
							Search by keyword
						</label>
						<p>
							<input type="text" name="subject" id="search-plen-keyword" class="input-large">
						</p>
						<p class="help-block">e.g. <q>ACTA</q></p>
					</div>
				</div>
				<div class="control-group">
					<div class="controls" id="search-plen-date-controls">
						<label class="radio">
							<input type="radio" name="function" value="search-plenary-by-date" id="search-plenary-by-date">
							Search by date
						</label>
						<p>
							<input type="text" name="date" id="search-plen-date" class="input-large">
						</p>
						<p class="help-block">Format: <b>YYYY-MM-DD</b>, e.g. 2012-12-21</p>
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend class="toggle-open">
					<i class="icon-chevron-up" style="margin-top: .3em;"></i>
					Options
				</legend>
				<div class="control-group">
					<label class="control-label" for="search-plen-mep-lang">Language</label>
					<div class="controls">
						<?php echo europarl_video_lang_select('search-plen-mep-lang'); ?>
						<p class="help-block">Select the language of the search results. The videos have all languages included.</p>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="search-plen-mep-cache">Caching</label>
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" name="skip-cache" id="search-plen-cache" value="skip-cache">
							Skip cache	
						</label>
						<p class="information help-block" style="font-size: 100%;">
							If the result isn't cached yet, this request may take very long (sometimes even a few minutes), please be patient.
						</p>
					</div>
				</div>
			</fieldset>
			<fieldset id="search-plen-timeframe">
				<legend class="toggle-open closed">
					<i class="icon-chevron-down" style="margin-top: .3em;"></i>
					Optional: Timeframe
				</legend>
				<div class="control-group">
					<label class="control-label" for="search-plen-mep-startdate">
						From
					</label>
					<div class="controls">
						<input type="text" name="startdate" id="search-plen-mep-startdate" class="input-large">
						<p class="help-block">Format: <b>YYYY-MM-DD</b>, e.g. 2012-01-01</p>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="search-plen-mep-enddate">
						Until
					</label>
					<div class="controls">
						<input type="text" name="startdate" id="search-plen-mep-enddate" class="input-large">
						<p class="help-block">Format: <b>YYYY-MM-DD</b>, e.g. 2012-01-30</p>
					</div>
				</div>
			</fieldset>
			<div class="form-actions">
				<input type="submit" value="Search" class="btn btn-primary">
			</div>
		</form>
	</div>
	<div class="span6 box">
		<h2>Search committees:</h2>
		<form action="#" method="get" class="form-horizontal">
			<input type="hidden" name="api" value="europarl-video"><input type="hidden" name="function" value="search-committees"><input type="hidden" name="output" value="html">
			<div class="control-group">
				<label class="control-label" for="search-committees-committee">Committee</label>
				<div class="controls">
					<?php echo europarl_video_committee_select('search-committees-committee'); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="search-committees-lang">Language</label>
				<div class="controls">
					<?php echo europarl_video_lang_select('search-committees-lang'); ?>
					<p class="help-block">Select the language of the search results. The videos have all languages included.</p>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="search-committees-cache">Caching</label>
				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" name="skip-cache" id="search-committees-cache" value="skip-cache">
						Skip cache	
					</label>
					<p class="information help-block" style="font-size: 100%;">
						If the result isn't cached yet, this request may take very long (sometimes even a few minutes), please be patient.
					</p>
				</div>
			</div>
			<fieldset>
				<legend class="toggle-open closed">
					<i class="icon-chevron-down" style="margin-top: .3em;"></i>
					Optional: Timeframe
				</legend>
				<div class="control-group">
					<label class="control-label" for="search-plen-date-startdate">
						From
					</label>
					<div class="controls">
						<input type="text" name="startdate" id="search-plen-date-startdate" class="input-large">
						<p class="help-block">Format: <b>YYYY-MM-DD</b>, e.g. 2012-01-01</p>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="search-plen-date-enddate">
						Until
					</label>
					<div class="controls">
						<input type="text" name="startdate" id="search-plen-date-enddate" class="input-large">
						<p class="help-block">Format: <b>YYYY-MM-DD</b>, e.g. 2012-01-30</p>
					</div>
				</div>
			</fieldset>
			<div class="form-actions">
				<input type="submit" value="Search" class="btn btn-primary">
			</div>
		</form>
	</div>
</div>

<script src="<?php echo path_to_api() ?>/ui.js"></script>
