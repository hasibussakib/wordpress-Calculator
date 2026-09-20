<?php
/**
 * Battery Runtime Calculator — problem-solving tool.
 *
 * @package Batterysizing
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="bs-runtime-root" data-runtime-calc id="bs-runtime-calc">
<div class="bs-purpose bs-card bs-card-pad">
	<p class="bs-kicker"><?php esc_html_e( 'What can I calculate with this tool?', 'batterysizing' ); ?></p>
	<p><?php esc_html_e( 'Find out how long your battery can power your equipment based on battery capacity, load, and system efficiency. If that runtime is not enough, switch to Target runtime and the same numbers tell you the Ah you actually need.', 'batterysizing' ); ?></p>
</div>

<div class="bs-widget bs-runtime">
	<div class="bs-tabs" role="tablist">
		<button type="button" class="bs-tab is-active" role="tab" aria-selected="true" data-mode="runtime">
			<?php esc_html_e( 'How long will it last?', 'batterysizing' ); ?>
			<small><?php esc_html_e( 'Runtime from the battery you have', 'batterysizing' ); ?></small>
		</button>
		<button type="button" class="bs-tab" role="tab" aria-selected="false" data-mode="target">
			<?php esc_html_e( 'I need a target runtime', 'batterysizing' ); ?>
			<small><?php esc_html_e( 'What Ah do I actually need?', 'batterysizing' ); ?></small>
		</button>
	</div>

	<div class="bs-widget-grid">
		<form class="bs-widget-form" data-form onsubmit="return false;">
			<p class="bs-field-group-label"><?php esc_html_e( 'Your battery and load', 'batterysizing' ); ?></p>

			<label class="bs-field">
				<span class="bs-field-label">
					<?php esc_html_e( 'Battery voltage', 'batterysizing' ); ?>
					<button type="button" class="bs-help" data-tip="<?php esc_attr_e( 'Nominal voltage of the battery or bank. 12V is one battery; 24V and 48V are series banks.', 'batterysizing' ); ?>" aria-label="<?php esc_attr_e( 'Help', 'batterysizing' ); ?>">?</button>
				</span>
				<select name="voltage">
					<option value="12" selected>12 V</option>
					<option value="24">24 V</option>
					<option value="36">36 V</option>
					<option value="48">48 V</option>
					<option value="12.8">12.8 V (LiFePO4)</option>
					<option value="25.6">25.6 V (LiFePO4)</option>
					<option value="51.2">51.2 V (LiFePO4)</option>
				</select>
			</label>

			<label class="bs-field" data-for-mode="runtime">
				<span class="bs-field-label">
					<?php esc_html_e( 'Battery capacity', 'batterysizing' ); ?>
					<button type="button" class="bs-help" data-tip="<?php esc_attr_e( 'Amp-hours printed on the battery. Parallel batteries: add the Ah. Series batteries: Ah stays the same.', 'batterysizing' ); ?>" aria-label="<?php esc_attr_e( 'Help', 'batterysizing' ); ?>">?</button>
				</span>
				<div class="bs-input-unit">
					<input type="number" name="capacity" value="100" min="0.1" step="any" inputmode="decimal">
					<span>Ah</span>
				</div>
			</label>

			<label class="bs-field">
				<span class="bs-field-label">
					<?php esc_html_e( 'Load power', 'batterysizing' ); ?>
					<button type="button" class="bs-help" data-tip="<?php esc_attr_e( 'Total watts of everything running at once. Add the labels, or use the homepage load estimator.', 'batterysizing' ); ?>" aria-label="<?php esc_attr_e( 'Help', 'batterysizing' ); ?>">?</button>
				</span>
				<div class="bs-input-unit">
					<input type="number" name="load" value="100" min="0.1" step="any" inputmode="decimal">
					<span>W</span>
				</div>
			</label>

			<label class="bs-field">
				<span class="bs-field-label">
					<span data-for-mode="target" hidden><?php esc_html_e( 'I need this many hours of backup', 'batterysizing' ); ?></span>
					<span data-for-mode="runtime"><?php esc_html_e( 'Hours I actually need (optional)', 'batterysizing' ); ?></span>
					<button type="button" class="bs-help" data-tip="<?php esc_attr_e( 'In runtime mode this is your goal, used to say whether the battery is big enough and what Ah would close the gap. In target mode it is the backup time you are sizing for.', 'batterysizing' ); ?>" aria-label="<?php esc_attr_e( 'Help', 'batterysizing' ); ?>">?</button>
				</span>
				<div class="bs-input-unit">
					<input type="number" name="targetHours" value="8" min="0" step="0.1" inputmode="decimal">
					<span>h</span>
				</div>
			</label>

			<button type="button" class="bs-btn bs-btn-ghost bs-adv-toggle" data-advanced-toggle aria-expanded="false">
				<span data-adv-label><?php esc_html_e( 'Advanced options', 'batterysizing' ); ?></span>
			</button>

			<div class="bs-advanced" data-advanced hidden>
				<p class="bs-field-group-label"><?php esc_html_e( 'Accuracy — only what changes the answer', 'batterysizing' ); ?></p>

				<label class="bs-field">
					<span class="bs-field-label">
						<?php esc_html_e( 'Battery chemistry', 'batterysizing' ); ?>
						<button type="button" class="bs-help" data-tip="<?php esc_attr_e( 'Sets a sensible depth of discharge and whether Peukert (high-current loss) applies. You can still override DoD below.', 'batterysizing' ); ?>" aria-label="<?php esc_attr_e( 'Help', 'batterysizing' ); ?>">?</button>
					</span>
					<select name="chemistry">
						<option value="lifepo4" selected><?php esc_html_e( 'LiFePO4 (lithium)', 'batterysizing' ); ?></option>
						<option value="flooded"><?php esc_html_e( 'Flooded lead-acid', 'batterysizing' ); ?></option>
						<option value="agm"><?php esc_html_e( 'AGM', 'batterysizing' ); ?></option>
						<option value="gel"><?php esc_html_e( 'Gel', 'batterysizing' ); ?></option>
						<option value="other"><?php esc_html_e( 'Other / not sure', 'batterysizing' ); ?></option>
					</select>
				</label>

				<label class="bs-field">
					<span class="bs-field-label">
						<?php esc_html_e( 'Depth of discharge', 'batterysizing' ); ?>
						<button type="button" class="bs-help" data-tip="<?php esc_attr_e( 'How much of the label Ah you actually use. Lead-acid: 50%. LiFePO4: 80–100%.', 'batterysizing' ); ?>" aria-label="<?php esc_attr_e( 'Help', 'batterysizing' ); ?>">?</button>
					</span>
					<div class="bs-slider">
						<input type="range" name="dod" min="20" max="100" step="1" value="90" data-unit="%">
						<output>90 %</output>
					</div>
				</label>

				<label class="bs-field">
					<span class="bs-field-label">
						<?php esc_html_e( 'Inverter / system efficiency', 'batterysizing' ); ?>
						<button type="button" class="bs-help" data-tip="<?php esc_attr_e( 'Share of battery energy that reaches the load. AC through an inverter: 85–95%. Direct DC: 100%.', 'batterysizing' ); ?>" aria-label="<?php esc_attr_e( 'Help', 'batterysizing' ); ?>">?</button>
					</span>
					<div class="bs-slider">
						<input type="range" name="efficiency" min="50" max="100" step="1" value="85" data-unit="%">
						<output>85 %</output>
					</div>
				</label>

				<label class="bs-field">
					<span class="bs-field-label">
						<?php esc_html_e( 'Reserve (leave unused)', 'batterysizing' ); ?>
						<button type="button" class="bs-help" data-tip="<?php esc_attr_e( 'Optional extra unused capacity on top of DoD — for ageing, cold weather, or a safety buffer. 0% if you do not need it.', 'batterysizing' ); ?>" aria-label="<?php esc_attr_e( 'Help', 'batterysizing' ); ?>">?</button>
					</span>
					<div class="bs-slider">
						<input type="range" name="reserve" min="0" max="40" step="1" value="0" data-unit="%">
						<output>0 %</output>
					</div>
				</label>
			</div>

			<div class="bs-form-actions">
				<button type="button" class="bs-btn bs-btn-ghost" data-example><?php esc_html_e( 'Load example', 'batterysizing' ); ?></button>
				<button type="button" class="bs-btn bs-btn-ghost" data-reset><?php esc_html_e( 'Reset', 'batterysizing' ); ?></button>
			</div>
		</form>

		<div class="bs-results" data-results>
			<p class="bs-results-kicker"><?php esc_html_e( 'Result', 'batterysizing' ); ?></p>
			<div data-primary class="bs-result-primary">—</div>
			<p class="bs-assumptions" data-assumptions></p>
			<ul class="bs-result-list" data-secondary></ul>
			<p>
				<button type="button" class="bs-btn bs-btn-ghost-light" data-copy><?php esc_html_e( 'Copy result', 'batterysizing' ); ?></button>
			</p>
		</div>
	</div>
</div>

<div class="bs-runtime-panels">
	<div data-warnings hidden></div>

	<div class="bs-card bs-card-pad">
		<h2><?php esc_html_e( 'What this result means', 'batterysizing' ); ?></h2>
		<p data-meaning></p>
		<h3><?php esc_html_e( 'What you can do next', 'batterysizing' ); ?></h3>
		<ul data-next></ul>
	</div>

	<div class="bs-card bs-card-pad">
		<h2><?php esc_html_e( 'What if the load changes?', 'batterysizing' ); ?></h2>
		<p class="bs-muted"><?php esc_html_e( 'Same battery, different watts. Runtime falls as the load rises — this is the relationship a single number hides.', 'batterysizing' ); ?></p>
		<div data-whatif></div>
	</div>

	<div class="bs-card bs-card-pad">
		<h2><?php esc_html_e( 'Theoretical vs practical', 'batterysizing' ); ?></h2>
		<div class="bs-vs">
			<div>
				<span><?php esc_html_e( 'Theoretical / label', 'batterysizing' ); ?></span>
				<strong data-nominal-vs>—</strong>
				<small><?php esc_html_e( 'V × Ah ÷ load, as if you could use every watt-hour.', 'batterysizing' ); ?></small>
			</div>
			<div>
				<span><?php esc_html_e( 'Estimated practical', 'batterysizing' ); ?></span>
				<strong data-practical-vs>—</strong>
				<small><?php esc_html_e( 'After DoD, reserve, inverter losses, and Peukert on lead-acid.', 'batterysizing' ); ?></small>
			</div>
		</div>
		<p class="bs-muted"><?php esc_html_e( 'This is an estimate, not a prediction. Lead-acid runtime also moves with temperature, age and how hard you pull. Manufacturer limits always win.', 'batterysizing' ); ?></p>
	</div>

	<div class="bs-card bs-card-pad">
		<h2><?php esc_html_e( 'How we calculated this', 'batterysizing' ); ?></h2>
		<p class="bs-muted"><?php esc_html_e( 'Same inputs you entered. Units shown so you can check the arithmetic.', 'batterysizing' ); ?></p>
		<div class="bs-work-wrap">
			<table class="bs-work">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Step', 'batterysizing' ); ?></th>
						<th><?php esc_html_e( 'Formula', 'batterysizing' ); ?></th>
						<th><?php esc_html_e( 'Values', 'batterysizing' ); ?></th>
						<th><?php esc_html_e( 'Result', 'batterysizing' ); ?></th>
					</tr>
				</thead>
				<tbody data-work></tbody>
			</table>
		</div>
	</div>
</div>
</div>
