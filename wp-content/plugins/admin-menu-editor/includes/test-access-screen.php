<div id="ws_ame_test_access_screen">
	<div id="ws_ame_test_inputs">
		<label class="ws_ame_test_input">
			<span class="ws_ame_test_input_name">Menu item</span>
			<select name="ws_ame_test_menu_item" id="ws_ame_test_menu_item" class="ws_ame_test_input_value"></select>
		</label>

		<label class="ws_ame_test_input">
			<span class="ws_ame_test_input_name">Log in as user</span>
			<input type="text" class="ws_ame_test_input_value" id="ws_ame_test_access_username">
		</label>

		<label class="ws_ame_test_input">
			<span class="ws_ame_test_input_name">Relevant role (optional)</span>
			<select name="ws_ame_test_relevant_actor" id="ws_ame_test_relevant_actor" class="ws_ame_test_input_value"></select>
		</label>

		<div class="ws_ame_test_input">
			<span class="ws_ame_test_input_name">What you want to happen</span>
			<fieldset class="ws_ame_test_input_value">
				<label>
					<input type="radio" name="ws_ame_desired_test_outcome" value="visible" checked>
					Menu is <strong>visible</strong>
				</label><br>
				<label>
					<input type="radio" name="ws_ame_desired_test_outcome" value="hidden">
					Menu is <strong>hidden</strong>
				</label><br>
			</fieldset>
		</div>

		<div id="ws_ame_test_actions">
			<div id="ws_ame_test_button_container">
				<input type="button" class="button-primary" value="Start Test" id="ws_ame_start_access_test">
			</div>
			<div id="ws_ame_test_progress">
				<span class="spinner is-active"></span>
				<span id="ws_ame_test_progress_text">
				Test hasn't started yet.
			</span>
			</div>
		</div>

		<div class="clear"></div>
	</div>

	<div id="ws_ame_test_access_body">
		<div id="ws_ame_test_frame_container">
			<span id="ws_ame_test_frame_placeholder">
				<em>Test page will appear here.</em><br>
			</span>
			<iframe src="" frameborder="0" sandbox="allow-scripts" id="ws_ame_test_access_frame"></iframe>
		</div>

		<div id="ws_ame_test_access_sidebar">
			<span id="ws_ame_test_output_placeholder">
				<em>Analysis will appear here.</em>
			</span>
			<div id="ws_ame_test_output">
				<h4>Result</h4>

				<h4>Analysis</h4>
				<h4>Suggestions</h4>
			</div>
		</div>
	</div>
</div>
