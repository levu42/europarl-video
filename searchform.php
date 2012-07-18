<div class="row">
	<div class="span6">
		<h2>Search plenary by date:</h2>
		<form action="#" method="get" class="form-horizontal">
			<input type="hidden" name="api" value="europarl-video"><input type="hidden" name="function" value="search-plenary-by-date"><input type="hidden" name="output" value="html">
			<div class="control-group">
				<label class="control-label" for="search-plen-date-date">Date</label>
				<div class="controls">
					<input type="text" name="date" id="search-plen-date-date" class="input-xlarge">
					<p class="help-block">Format: <b>YYYY-MM-DD</b>, e.g. 2012-12-21</p>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="search-plen-date-lang">Language</label>
				<div class="controls">
					<?php echo europarl_video_lang_select('search-plen-date-lang'); ?>
					<p class="help-block">Select the language of the search results. The videos have all languages included.</p>
				</div>
			</div>
			<div class="information" style="font-size: 100%;">If the result isn't cached yet, this request may take very long (sometimes even a few minutes), please be patient</div>
			<div class="form-actions">
				<input type="submit" value="Search" class="btn btn-primary">
			</div>
		</form>
	</div>
	<div class="span6">
		<h2>Search plenary by keyword:</h2>
		<form action="#" method="get" class="form-horizontal">
			<input type="hidden" name="api" value="europarl-video"><input type="hidden" name="function" value="search-plenary-by-keyword"><input type="hidden" name="output" value="html">
			<div class="control-group">
				<label class="control-label" for="search-plen-keyword-keyword">Keyword</label>
				<div class="controls">
					<input type="text" name="subject" id="search-plen-keyword-keyword" class="input-xlarge">
					<p class="help-block">e.g. <q>ACTA</q></p>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="search-plen-keyword-lang">Language</label>
				<div class="controls">
					<?php echo europarl_video_lang_select('search-plen-keyword-lang'); ?>
					<p class="help-block">Select the language of the search results. The videos have all languages included.</p>
				</div>
			</div>
			<div class="information" style="font-size: 100%;">If the result isn't cached yet, this request may take very long (sometimes even a few minutes), please be patient</div>
			<div class="form-actions">
				<input type="submit" value="Search" class="btn btn-primary">
			</div>
		</form>
	</div>
</div>
<div class="row">
	<div class="span6">
		<h2>Search plenary by MEP:</h2>
		<form action="#" method="get" class="form-horizontal">
			<input type="hidden" name="api" value="europarl-video"><input type="hidden" name="function" value="search-plenary-by-mep"><input type="hidden" name="output" value="html">
			<div class="control-group">
				<label class="control-label" for="search-plen-mep-mep">MEP</label>
				<div class="controls">
					<?php echo europarl_video_mep_select('search-plen-mep-mep'); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="search-plen-mep-lang">Language</label>
				<div class="controls">
					<?php echo europarl_video_lang_select('search-plen-mep-lang'); ?>
					<p class="help-block">Select the language of the search results. The videos have all languages included.</p>
				</div>
			</div>
			<div class="information" style="font-size: 100%;">If the result isn't cached yet, this request may take very long (sometimes even a few minutes), please be patient</div>
			<div class="form-actions">
				<input type="submit" value="Search" class="btn btn-primary">
			</div>
		</form>
	</div>
</div>
