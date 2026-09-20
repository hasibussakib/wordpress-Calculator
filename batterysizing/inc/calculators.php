<?php
/**
 * Calculator registry — the single source of truth for all 12 calculators.
 *
 * Mirrors src/data + src/lib/batteryMath.ts from the original React project.
 * Each definition feeds: the page template, the JS engine (assets/js/calculator.js),
 * the hub/grid listings and the SEO tags.
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build a field definition.
 */
function batterysizing_field( $key, $label, $default, $opts = array() ) {
	return array_merge(
		array(
			'key'     => $key,
			'label'   => $label,
			'default' => $default,
			'type'    => 'number', // number | select | slider
			'unit'    => '',
			'min'     => 0,
			'step'    => 'any',
			'help'    => '',
			'options' => array(),
			'locked'  => false,
		),
		$opts
	);
}

/**
 * Common field factories so all calculators stay consistent.
 */
function batterysizing_voltage_field( $default = 12, $options = null, $locked = false ) {
	if ( null === $options ) {
		$options = array( 6, 12, 24, 36, 48 );
	}
	$opts = array_map(
		function ( $v ) {
			return array( 'value' => $v, 'label' => $v . 'V' );
		},
		$options
	);
	return batterysizing_field( 'voltage', __( 'Battery voltage', 'batterysizing' ), $default, array(
		'type'    => 'select',
		'options' => $opts,
		'locked'  => $locked,
		'help'    => $locked ? __( 'Fixed for this calculator.', 'batterysizing' ) : '',
	) );
}

function batterysizing_dod_field( $default = 80 ) {
	return batterysizing_field( 'dod', __( 'Depth of discharge (DoD)', 'batterysizing' ), $default, array(
		'type'    => 'select',
		'unit'    => '%',
		'options' => array(
			array( 'value' => 100, 'label' => '100% — LiFePO4 (lithium)' ),
			array( 'value' => 90, 'label' => '90% — LiFePO4 (conservative)' ),
			array( 'value' => 80, 'label' => '80% — AGM / deep cycle' ),
			array( 'value' => 60, 'label' => '60% — Gel' ),
			array( 'value' => 50, 'label' => '50% — Flooded lead-acid' ),
		),
		'help'    => __( 'How much of the capacity you safely use. Lead-acid: 50%. Lithium: 80–100%.', 'batterysizing' ),
	) );
}

function batterysizing_eff_field( $default = 85 ) {
	return batterysizing_field( 'efficiency', __( 'Inverter efficiency', 'batterysizing' ), $default, array(
		'type' => 'slider',
		'unit' => '%',
		'min'  => 50,
		'max'  => 100,
		'step' => 1,
		'help' => __( 'Energy lost converting DC to AC. Typical inverters: 85–95%.', 'batterysizing' ),
	) );
}

/**
 * The full registry. Order = display order on the homepage grid & hub.
 */
function batterysizing_get_calculators() {
	$calculators = array(

		/* 1 — Battery Runtime Calculator */
		array(
			'slug'             => 'battery-runtime-calculator',
			'name'             => __( 'Battery Runtime Calculator', 'batterysizing' ),
			'tagline'          => __( 'How long will my battery run my equipment — and what Ah do I need if it isn’t enough?', 'batterysizing' ),
			'icon'             => 'timer',
			'engine'           => 'runtime',
			'meta_title'       => 'Battery Runtime Calculator — How Long Will a Battery Last?',
			'meta_description' => 'Estimate how long your battery will run a load, then size the Ah you need for a target runtime. Includes usable energy, inverter losses, what-if loads and a practical vs label comparison.',
			'intro'            => '<p>The question is not “what is V × Ah ÷ watts?” It is <strong>how long will this battery run my equipment</strong> — and if that is short, <strong>what capacity would actually cover the hours I need</strong>. Simple mode asks only voltage, amp-hours and load. Advanced options add chemistry, depth of discharge, inverter efficiency and an optional reserve. Results separate the label (theoretical) figure from a practical estimate, show the arithmetic, and warn when the current or DoD looks unhealthy.</p>',
			'formula'          => 'Estimated runtime (h) = ( V × Ah × DoD/100 × (1 − reserve) × η/100 ) ÷ Load (W)   •   Required Ah = ( Load × hours ) ÷ ( V × DoD/100 × (1 − reserve) × η/100 )',
			'steps'            => array(
				'Enter voltage, the Ah on the battery, and the watts you will run at once.',
				'Optionally open Advanced options for chemistry, DoD, inverter efficiency and reserve.',
				'Read estimated hours, usable Wh and battery current — not just one number.',
				'If you need more hours, switch to Target runtime to get the required Ah.',
			),
			'faq'              => array(
				array(
					'q' => 'Why is my real runtime shorter than the calculated one?',
					'a' => 'The practical estimate already folds in DoD, reserve and inverter losses. Lead-acid still gives up extra Ah at high current (Peukert), and cold, age and extra standby loads shave more. Treat the result as planning, not a stopwatch.',
				),
				array(
					'q' => 'How long will a 12V 100Ah battery run a 100W appliance?',
					'a' => 'Flooded lead-acid at 50% DoD and 85% inverter efficiency: about 5.1 hours. The same 100Ah as LiFePO4 at 90% DoD is roughly double. Use Load example, then change chemistry to see it.',
				),
				array(
					'q' => 'What if this runtime is not enough?',
					'a' => 'Switch to “I need a target runtime”, enter the hours you actually need, and read the required Ah. The page also states the gap in hours and the next common battery size.',
				),
				array(
					'q' => 'Should I include inverter efficiency?',
					'a' => 'Yes whenever the load is AC. Inverters waste 5–15% as heat. For a pure DC load (12V fridge, lights) set efficiency to 100%.',
				),
			),
			'related'          => array( 'battery-capacity-calculator', 'battery-backup-calculator', 'ah-to-wh-calculator' ),
		),

		/* 2 — Battery Capacity Calculator */
		array(
			'slug'             => 'battery-capacity-calculator',
			'name'             => __( 'Battery Capacity Calculator', 'batterysizing' ),
			'tagline'          => __( 'What Ah / Wh capacity do you need for a load and backup time?', 'batterysizing' ),
			'icon'             => 'gauge',
			'engine'           => 'capacity',
			'meta_title'       => 'Battery Capacity Calculator (Ah & Wh) — Size Your Battery Bank',
			'meta_description' => 'Calculate the battery capacity in Ah and Wh you need for any load and backup time, with depth of discharge and inverter efficiency built in. Free and instant.',
			'intro'            => '<p>Tell the calculator what you need to run and for how long. It works backwards to the minimum battery capacity in amp-hours (Ah) and watt-hours (Wh), then suggests the next common battery size so you can shop with a concrete number.</p>',
			'formula'          => 'Capacity (Ah) = ( Load (W) × Hours ) ÷ ( V × DoD ÷ 100 × η ÷ 100 )',
			'steps'            => array(
				'Enter the total load in watts.',
				'Enter how many hours of backup you need.',
				'Choose system voltage and your battery chemistry\'s safe depth of discharge.',
				'Get the required Ah and Wh, plus a recommended standard battery size.',
			),
			'faq'              => array(
				array(
					'q' => 'What is the difference between Ah and Wh?',
					'a' => 'Ah (amp-hours) measures charge; Wh (watt-hours) measures energy. Wh = V × Ah, so Wh is comparable across different voltages while Ah is not. A 12V 100Ah battery stores 1,200Wh — the same energy as a 24V 50Ah battery.',
				),
				array(
					'q' => 'Why add a margin when sizing capacity?',
					'a' => 'Batteries lose capacity with age and high discharge rates, and real loads are often higher than nameplate. Adding 20–25% headroom keeps backup time honest after a year of use.',
				),
			),
			'related'          => array( 'battery-runtime-calculator', 'battery-bank-calculator', 'wh-to-ah-calculator' ),
		),

		/* 3 — Battery Backup Calculator */
		array(
			'slug'             => 'battery-backup-calculator',
			'name'             => __( 'Battery Backup Time Calculator', 'batterysizing' ),
			'tagline'          => __( 'Backup hours for your battery + inverter setup.', 'batterysizing' ),
			'icon'             => 'backup',
			'engine'           => 'runtime',
			'defaults'         => array( 'voltage' => 12, 'capacity' => 200, 'load' => 300, 'dod' => 50, 'efficiency' => 85 ),
			'meta_title'       => 'Battery Backup Time Calculator for Home & UPS',
			'meta_description' => 'Calculate battery backup time in hours and minutes for any battery bank, inverter and load. Includes depth of discharge and efficiency so results are realistic.',
			'intro'            => '<p>Planning for load-shedding or outages? Enter your battery bank and the appliances you want to keep running to see your exact backup window. Great for sizing a home UPS or checking whether your existing setup covers the evening peak.</p>',
			'formula'          => 'Backup time (h) = ( V × Ah × DoD ÷ 100 × η ÷ 100 ) ÷ Load (W)',
			'steps'            => array(
				'Add up the watts of everything that must stay on.',
				'Enter your battery bank voltage and total Ah.',
				'Set DoD to 50% for lead-acid or 80–90% for lithium.',
				'Read your backup time — then adjust the load to see what fits.',
			),
			'faq'              => array(
				array(
					'q' => 'How many hours of backup does a 200Ah battery give?',
					'a' => 'A 12V 200Ah lead-acid bank at 50% DoD holds 1,200Wh usable. At 300W through an 85%-efficient inverter that is ≈ 4.7 hours. The same bank in LiFePO4 at 90% DoD gives ≈ 10 hours.',
				),
				array(
					'q' => 'Can I connect two inverters to double backup time?',
					'a' => 'Backup time comes from the battery bank, not the inverter. Double the batteries (in parallel) to double runtime — the inverter only needs to handle the peak wattage.',
				),
			),
			'related'          => array( 'ups-battery-calculator', 'battery-runtime-calculator', 'battery-bank-calculator' ),
		),

		/* 4 — Battery Bank Calculator */
		array(
			'slug'             => 'battery-bank-calculator',
			'name'             => __( 'Battery Bank Calculator', 'batterysizing' ),
			'tagline'          => __( 'Series & parallel counts to build any bank.', 'batterysizing' ),
			'icon'             => 'bank',
			'engine'           => 'bank',
			'meta_title'       => 'Battery Bank Calculator — Series & Parallel Wiring',
			'meta_description' => 'Work out how many batteries you need in series and parallel to hit a target voltage and capacity. Instant battery bank sizing with total Wh and wiring layout.',
			'intro'            => '<p>Building a bank from individual batteries? Set the target system voltage and capacity, choose the battery you are buying, and the calculator returns the series/parallel layout, total batteries and the energy the finished bank stores.</p>',
			'formula'          => 'Series count = Target V ÷ Battery V   •   Parallel strings = Target Ah ÷ Battery Ah   •   Total = Series × Parallel',
			'steps'            => array(
				'Enter the target bank voltage (12, 24 or 48V) and capacity in Ah.',
				'Enter the voltage and Ah of the individual battery you will use.',
				'Get the series/parallel counts and total number of batteries.',
				'Check the total stored energy (Wh) against your backup needs.',
			),
			'faq'              => array(
				array(
					'q' => 'Does series wiring increase capacity?',
					'a' => 'No. Series wiring adds voltage; capacity (Ah) stays the same. Parallel wiring adds capacity; voltage stays the same. Total energy (Wh) grows either way.',
				),
				array(
					'q' => 'Should I build a 12V, 24V or 48V bank?',
					'a' => 'Higher voltage means lower current for the same power — thinner cables, less loss, better inverter efficiency. 12V suits small systems (<1kWh), 24V mid-size, 48V whole-home and solar systems.',
				),
			),
			'related'          => array( 'solar-battery-calculator', 'battery-capacity-calculator', '24v-48v-battery-calculator' ),
		),

		/* 5 — Solar Battery Calculator */
		array(
			'slug'             => 'solar-battery-calculator',
			'name'             => __( 'Solar Battery Calculator', 'batterysizing' ),
			'tagline'          => __( 'Size the solar battery bank & panel array.', 'batterysizing' ),
			'icon'             => 'sun',
			'engine'           => 'solar',
			'meta_title'       => 'Solar Battery Bank Sizing Calculator (Ah, Wh & Panels)',
			'meta_description' => 'Size your off-grid or hybrid solar battery bank: daily consumption, days of autonomy, depth of discharge and sun hours in — required Ah, Wh and solar panel wattage out.',
			'intro'            => '<p>Solar batteries must survive nights and cloudy days. Enter your daily consumption (the appliance load estimator on the homepage can produce it), pick your days of autonomy, and get the bank size in Ah/Wh plus the solar array wattage needed to refill it.</p>',
			'formula'          => 'Bank (Wh) = Daily Wh × Autonomy days ÷ (DoD ÷ 100)   •   Panels (W) = Daily Wh ÷ (Sun hours × 0.75)',
			'steps'            => array(
				'Enter daily consumption in Wh (or kWh — use the estimator to find it).',
				'Set days of autonomy: 1 for grid-hybrid, 2–3 for off-grid.',
				'Choose system voltage and chemistry DoD.',
				'Read the required bank size and recommended solar panel wattage.',
			),
			'faq'              => array(
				array(
					'q' => 'What are days of autonomy?',
					'a' => 'How many consecutive days without sun the bank must cover. Off-grid homes usually size for 2–3 days; grid-tied systems with net metering can get by with 1 or less.',
				),
				array(
					'q' => 'Why divide panel wattage by sun hours × 0.75?',
					'a' => 'Panels only produce nameplate power in perfect conditions. The 0.75 factor covers cloud, dust, heat and charge-controller losses, so the array actually refills the bank on an average day.',
				),
			),
			'related'          => array( 'battery-bank-calculator', 'battery-capacity-calculator', 'lifepo4-battery-calculator' ),
		),

		/* 6 — UPS Battery Calculator */
		array(
			'slug'             => 'ups-battery-calculator',
			'name'             => __( 'UPS Battery Calculator', 'batterysizing' ),
			'tagline'          => __( 'Size UPS batteries & runtime for any load.', 'batterysizing' ),
			'icon'             => 'ups',
			'engine'           => 'ups',
			'meta_title'       => 'UPS Battery Calculator — Runtime & Battery Sizing',
			'meta_description' => 'Calculate UPS backup time or the battery Ah a UPS needs for a given load, with power factor, inverter efficiency and 12V battery count included.',
			'intro'            => '<p>For home and office UPS systems: enter the IT/appliance load in watts (or VA with a power factor), the backup time you want, and the calculator returns the required battery capacity in Ah, the number of 12V batteries, and the minimum UPS rating.</p>',
			'formula'          => 'Ah = ( Load (W) × Hours ) ÷ ( V × DoD ÷ 100 × η ÷ 100 )   •   UPS rating (VA) = W ÷ power factor',
			'steps'            => array(
				'Enter the load in watts (or VA plus power factor).',
				'Choose the backup time you need in hours.',
				'Set the DC bus voltage and battery size you plan to use.',
				'Get required Ah, battery count and recommended UPS VA rating.',
			),
			'faq'              => array(
				array(
					'q' => 'What power factor should I use?',
					'a' => 'Consumer UPS units are typically rated at PF 0.6–0.8, so a "1000VA" UPS delivers 600–800W. Online double-conversion units reach 0.9–1.0. Use the PF from your UPS datasheet.',
				),
				array(
					'q' => 'Why does my UPS beep and shut down early under load?',
					'a' => 'Small UPS batteries (7–9Ah) sag hard under high current — voltage collapses before the rated Ah is delivered. Compute with a 50–60% DoD for short high-current backups, or use larger batteries.',
				),
			),
			'related'          => array( 'battery-backup-calculator', 'battery-runtime-calculator', 'battery-capacity-calculator' ),
		),

		/* 7 — 12V Battery Calculator */
		array(
			'slug'             => '12v-battery-calculator',
			'name'             => __( '12V Battery Calculator', 'batterysizing' ),
			'tagline'          => __( 'Runtime & capacity for any 12V battery.', 'batterysizing' ),
			'icon'             => 'battery12',
			'engine'           => 'runtime_capacity',
			'defaults'         => array( 'voltage' => 12, 'capacity' => 100, 'load' => 50, 'hours' => 4, 'dod' => 50, 'efficiency' => 85 ),
			'meta_title'       => '12V Battery Calculator — Runtime & Capacity (Ah to Watts)',
			'meta_description' => '12V battery calculator for cars, RVs, boats and solar: convert 12V Ah to watts and Wh, calculate runtime for any load, or find the Ah you need. Two-way and instant.',
			'intro'            => '<p>The workhorse voltage for cars, RVs, boats, trolling motors and small solar. Switch between the <strong>Runtime</strong> tab (how long will 12V XAh last?) and the <strong>Capacity</strong> tab (how many Ah do I need?) — every result also shows energy in Wh.</p>',
			'formula'          => 'Runtime (h) = ( 12 × Ah × DoD × η ) ÷ W   •   Ah = ( W × h ) ÷ ( 12 × DoD × η )',
			'steps'            => array(
				'Pick the Runtime or Capacity tab.',
				'Enter your 12V battery capacity or the load you plan to run.',
				'Set DoD — 50% for lead-acid, 80–100% for 12V lithium.',
				'Read runtime, required Ah, and stored energy in Wh.',
			),
			'faq'              => array(
				array(
					'q' => 'How many watts is a 12V 100Ah battery?',
					'a' => 'It stores 12 × 100 = 1,200Wh. Usable energy depends on chemistry: ≈600Wh at 50% DoD (lead-acid), ≈1,080Wh at 90% (LiFePO4). Peak power is limited by the battery\'s max discharge current and the inverter rating.',
				),
				array(
					'q' => 'Can I run a fridge off a 12V battery?',
					'a' => 'A modern 12V DC compressor fridge uses 40–60W average. A 12V 100Ah LiFePO4 battery runs it for roughly 18–24 hours; lead-acid at 50% DoD about half that.',
				),
			),
			'related'          => array( '24v-48v-battery-calculator', 'battery-runtime-calculator', 'ah-to-wh-calculator' ),
		),

		/* 8 — 24V / 48V Battery Calculator */
		array(
			'slug'             => '24v-48v-battery-calculator',
			'name'             => __( '24V / 48V Battery Calculator', 'batterysizing' ),
			'tagline'          => __( 'Runtime & capacity for 24V and 48V banks.', 'batterysizing' ),
			'icon'             => 'battery24',
			'engine'           => 'runtime_capacity',
			'defaults'         => array( 'voltage' => 24, 'capacity' => 100, 'load' => 500, 'hours' => 4, 'dod' => 80, 'efficiency' => 90 ),
			'voltage_options'  => array( 24, 48 ),
			'meta_title'       => '24V & 48V Battery Calculator — Runtime and Capacity',
			'meta_description' => 'Calculate runtime, required Ah and stored Wh for 24V and 48V battery banks — solar storage, inverters, EV conversions and server rooms, with DoD and efficiency included.',
			'intro'            => '<p>24V and 48V banks halve (or quarter) the current for the same power — the standard for solar storage and whole-home inverters. Use the Runtime tab to see how long a bank lasts, or the Capacity tab to size the bank for a target backup time.</p>',
			'formula'          => 'Runtime (h) = ( V × Ah × DoD × η ) ÷ W   •   Ah = ( W × h ) ÷ ( V × DoD × η )',
			'steps'            => array(
				'Choose 24V or 48V.',
				'Pick Runtime or Capacity mode.',
				'Enter bank Ah, load watts and chemistry DoD.',
				'Read hours of backup or the Ah your bank needs.',
			),
			'faq'              => array(
				array(
					'q' => 'Is 48V better than 24V?',
					'a' => 'For the same energy, 48V runs at half the current: smaller cables, lower losses, and most hybrid inverters are 48V-native. Below ~5kWh of storage, 24V is usually cheaper and simpler.',
				),
				array(
					'q' => 'How long does a 48V 100Ah battery last at 1kW?',
					'a' => '48 × 100 = 4,800Wh. At 80% DoD and 90% efficiency: 4,800 × 0.8 × 0.9 ÷ 1,000 ≈ 3.5 hours.',
				),
			),
			'related'          => array( '12v-battery-calculator', 'battery-bank-calculator', 'solar-battery-calculator' ),
		),

		/* 9 — LiFePO4 Battery Calculator */
		array(
			'slug'             => 'lifepo4-battery-calculator',
			'name'             => __( 'LiFePO4 Battery Calculator', 'batterysizing' ),
			'tagline'          => __( 'Lithium iron phosphate runtime & sizing.', 'batterysizing' ),
			'icon'             => 'lithium',
			'engine'           => 'runtime_capacity',
			'defaults'         => array( 'voltage' => 12, 'capacity' => 100, 'load' => 100, 'hours' => 6, 'dod' => 90, 'efficiency' => 95 ),
			'voltage_options'  => array( 12, 24, 48, 12.8, 25.6, 51.2 ),
			'meta_title'       => 'LiFePO4 Battery Calculator — Runtime & Capacity (Lithium)',
			'meta_description' => 'LiFePO4 calculator with realistic lithium settings: 90–100% usable depth of discharge, 12.8/25.6/51.2V nominal options. Calculate runtime or required Ah instantly.',
			'intro'            => '<p>LiFePO4 (lithium iron phosphate) batteries safely discharge to 90–100% and lose almost nothing to heat, so a "100Ah" lithium battery delivers roughly twice the usable energy of a 100Ah lead-acid. This calculator defaults to real lithium numbers — 90% DoD, 95% efficiency — and supports both marketing (12V) and true nominal (12.8V) voltages.</p>',
			'formula'          => 'Runtime (h) = ( V × Ah × 0.9 × η ) ÷ W   •   Ah = ( W × h ) ÷ ( V × 0.9 × η )',
			'steps'            => array(
				'Choose nominal voltage (12.8V = a "12V" LiFePO4).',
				'Enter capacity in Ah and your load in watts.',
				'Keep DoD at 90% (or push to 100% for quality BMS packs).',
				'Read runtime or required capacity with lithium-grade efficiency.',
			),
			'faq'              => array(
				array(
					'q' => 'Can I really use 100% of a LiFePO4 battery?',
					'a' => 'Quality packs with a good BMS tolerate 100% DoD, and cycle-life impact is modest. Sizing at 90% leaves headroom for BMS cut-off tolerance and cold-weather derating — a sensible default.',
				),
				array(
					'q' => 'How many cycles does LiFePO4 last?',
					'a' => 'Typically 3,000–6,000 cycles to 80% remaining capacity at 80–100% DoD — roughly 10+ years of daily cycling, versus 300–600 cycles for flooded lead-acid.',
				),
			),
			'related'          => array( '12v-battery-calculator', 'battery-runtime-calculator', 'solar-battery-calculator' ),
		),

		/* 10 — Ah to Wh */
		array(
			'slug'             => 'ah-to-wh-calculator',
			'name'             => __( 'Ah to Wh Calculator', 'batterysizing' ),
			'tagline'          => __( 'Convert amp-hours to watt-hours.', 'batterysizing' ),
			'icon'             => 'convert',
			'engine'           => 'ah_to_wh',
			'defaults'         => array( 'capacity' => 100, 'voltage' => 12 ),
			'meta_title'       => 'Ah to Wh Calculator — Amp-Hours to Watt-Hours Converter',
			'meta_description' => 'Convert amp-hours (Ah) to watt-hours (Wh) for any voltage, with a quick-reference table for 12V, 24V and 48V batteries and worked examples.',
			'intro'            => '<p>Watt-hours measure actual stored energy, which is why Wh — not Ah — is the honest way to compare batteries at different voltages. Enter capacity in Ah and the system voltage; the converter shows Wh, kWh and a quick-reference table.</p>',
			'formula'          => 'Wh = Ah × V   •   kWh = Wh ÷ 1000',
			'steps'            => array(
				'Enter the capacity in amp-hours (Ah).',
				'Pick the battery voltage.',
				'Read watt-hours (Wh) and kilowatt-hours (kWh) instantly.',
				'Use the reference table for common 12/24/48V batteries.',
			),
			'faq'              => array(
				array(
					'q' => 'Why can\'t I compare batteries by Ah alone?',
					'a' => 'Ah ignores voltage. A 24V 50Ah battery (1,200Wh) stores the same energy as a 12V 100Ah battery — half the Ah, double the voltage. Convert both to Wh to compare fairly.',
				),
			),
			'related'          => array( 'wh-to-ah-calculator', 'battery-capacity-calculator', '12v-battery-calculator' ),
		),

		/* 11 — Wh to Ah */
		array(
			'slug'             => 'wh-to-ah-calculator',
			'name'             => __( 'Wh to Ah Calculator', 'batterysizing' ),
			'tagline'          => __( 'Convert watt-hours to amp-hours.', 'batterysizing' ),
			'icon'             => 'convert2',
			'engine'           => 'wh_to_ah',
			'defaults'         => array( 'energy' => 1200, 'voltage' => 12 ),
			'meta_title'       => 'Wh to Ah Calculator — Watt-Hours to Amp-Hours Converter',
			'meta_description' => 'Convert watt-hours (Wh) to amp-hours (Ah) at any voltage. Handy for comparing power-station Wh ratings with battery Ah ratings — instant results and examples.',
			'intro'            => '<p>Portable power stations advertise Wh while batteries advertise Ah. Enter the energy in Wh and your system voltage to get the equivalent Ah — the reverse of the classic Wh = V × Ah.</p>',
			'formula'          => 'Ah = Wh ÷ V',
			'steps'            => array(
				'Enter the energy in watt-hours (Wh).',
				'Pick the battery voltage.',
				'Read the equivalent capacity in Ah.',
				'Compare like-for-like against Ah-rated batteries.',
			),
			'faq'              => array(
				array(
					'q' => 'A 500Wh power station — how many Ah is that at 12V?',
					'a' => '500 ÷ 12 ≈ 41.7Ah. At 24V the same energy is ≈ 20.8Ah — the Wh figure stays the constant.',
				),
			),
			'related'          => array( 'ah-to-wh-calculator', 'battery-capacity-calculator', 'battery-runtime-calculator' ),
		),
	);

	/* 0 — The flagship "Battery Calculator" (first in hub, hero CTA target). */
	array_unshift( $calculators, array(
		'slug'             => 'battery-calculator',
		'name'             => __( 'Battery Calculator', 'batterysizing' ),
		'tagline'          => __( 'All-in-one runtime, capacity & energy calculator.', 'batterysizing' ),
		'icon'             => 'battery',
		'engine'           => 'runtime_capacity',
		'featured'         => true,
		'defaults'         => array( 'voltage' => 12, 'capacity' => 100, 'load' => 100, 'hours' => 4, 'dod' => 80, 'efficiency' => 85 ),
		'meta_title'       => 'Battery Calculator — Runtime, Capacity & Watt-Hours',
		'meta_description' => 'Free all-in-one battery calculator: work out runtime from V/Ah and load watts, size the capacity you need for a backup time, and convert between Ah and Wh instantly.',
		'intro'            => '<p>The one calculator that covers both questions: <em>"how long will my battery last?"</em> (Runtime tab) and <em>"what battery do I need?"</em> (Capacity tab). All results factor in depth of discharge and inverter efficiency — the two things label numbers ignore.</p>',
		'formula'          => 'Runtime (h) = ( V × Ah × DoD × η ) ÷ W   •   Capacity (Ah) = ( W × h ) ÷ ( V × DoD × η )',
		'steps'            => array(
				'Choose the Runtime tab or the Capacity tab.',
				'Enter voltage, capacity (Ah) and load (W) — or load and desired hours.',
				'Set depth of discharge for your chemistry and inverter efficiency.',
				'Read runtime in h:mm, usable Wh, or the Ah your bank needs.',
		),
		'faq'              => array(
			array(
				'q' => 'What does a battery calculator actually compute?',
				'a' => 'It converts between the four core quantities — voltage (V), capacity (Ah), energy (Wh) and power (W) — over time. Runtime = usable energy ÷ load; capacity = load × time ÷ usable fraction.',
			),
			array(
				'q' => 'Why do you multiply by depth of discharge?',
				'a' => 'Usable energy is less than stored energy. Emptying lead-acid batteries below ~50% destroys them quickly, so only half the label Ah is really available. Lithium allows 80–100%.',
			),
		),
		'related'          => array( 'battery-runtime-calculator', 'battery-capacity-calculator', 'battery-bank-calculator' ),
	) );

	/**
	 * Filters the calculator registry.
	 *
	 * @param array $calculators Calculator definitions.
	 */
	return apply_filters( 'batterysizing_calculators', $calculators );
}

/**
 * Get one calculator definition by slug.
 */
function batterysizing_get_calculator( $slug ) {
	foreach ( batterysizing_get_calculators() as $calc ) {
		if ( $calc['slug'] === $slug ) {
			return $calc;
		}
	}
	return null;
}

/**
 * Calculators without the flagship (homepage grid shows 12 cards).
 */
function batterysizing_get_grid_calculators() {
	return array_values( array_filter(
		batterysizing_get_calculators(),
		function ( $c ) {
			return empty( $c['featured'] );
		}
	) );
}

/**
 * Page URL for a calculator slug.
 */
function batterysizing_calculator_url( $slug ) {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return get_permalink( $page );
	}
	return home_url( '/' . $slug . '/' );
}

/**
 * Page URL for a guide slug (guides are children of /guides/).
 */
function batterysizing_guide_url( $slug ) {
	$page = get_page_by_path( 'guides/' . $slug );
	if ( $page ) {
		return get_permalink( $page );
	}
	$hub = get_page_by_path( 'guides' );
	if ( $hub ) {
		return trailingslashit( get_permalink( $hub ) ) . $slug . '/';
	}
	return home_url( '/guides/' . $slug . '/' );
}

/**
 * Build the full runtime/capacity field set for a calculator definition.
 * Returned array is JSON-encoded into the widget's data attribute.
 */
function batterysizing_calculator_config( $calc ) {
	$defaults = isset( $calc['defaults'] ) ? $calc['defaults'] : array();
	$d = function ( $k, $fallback ) use ( $defaults ) {
		return isset( $defaults[ $k ] ) ? $defaults[ $k ] : $fallback;
	};

	$voltage_options = isset( $calc['voltage_options'] ) ? $calc['voltage_options'] : null;

	$fields = array();
	switch ( $calc['engine'] ) {
		case 'runtime':
			$fields = array(
				batterysizing_voltage_field( $d( 'voltage', 12 ), $voltage_options ),
				batterysizing_field( 'capacity', __( 'Battery capacity', 'batterysizing' ), $d( 'capacity', 100 ), array( 'unit' => 'Ah', 'min' => 0.1 ) ),
				batterysizing_field( 'load', __( 'Total load', 'batterysizing' ), $d( 'load', 100 ), array( 'unit' => 'W', 'min' => 0.1 ) ),
				batterysizing_dod_field( $d( 'dod', 80 ) ),
				batterysizing_eff_field( $d( 'efficiency', 85 ) ),
			);
			break;

		case 'capacity':
			$fields = array(
				batterysizing_field( 'load', __( 'Total load', 'batterysizing' ), $d( 'load', 300 ), array( 'unit' => 'W', 'min' => 0.1 ) ),
				batterysizing_field( 'hours', __( 'Backup time needed', 'batterysizing' ), $d( 'hours', 4 ), array( 'unit' => 'h', 'min' => 0.1 ) ),
				batterysizing_voltage_field( $d( 'voltage', 12 ), $voltage_options ),
				batterysizing_dod_field( $d( 'dod', 80 ) ),
				batterysizing_eff_field( $d( 'efficiency', 85 ) ),
			);
			break;

		case 'runtime_capacity':
			$fields = array(
				batterysizing_voltage_field( $d( 'voltage', 12 ), $voltage_options ),
				batterysizing_field( 'capacity', __( 'Battery capacity', 'batterysizing' ), $d( 'capacity', 100 ), array( 'unit' => 'Ah', 'min' => 0.1, 'modes' => array( 'runtime' ) ) ),
				batterysizing_field( 'load', __( 'Total load', 'batterysizing' ), $d( 'load', 100 ), array( 'unit' => 'W', 'min' => 0.1 ) ),
				batterysizing_field( 'hours', __( 'Backup time needed', 'batterysizing' ), $d( 'hours', 4 ), array( 'unit' => 'h', 'min' => 0.1, 'modes' => array( 'capacity' ) ) ),
				batterysizing_dod_field( $d( 'dod', 80 ) ),
				batterysizing_eff_field( $d( 'efficiency', 85 ) ),
			);
			break;

		case 'bank':
			$fields = array(
				batterysizing_field( 'target_voltage', __( 'Target bank voltage', 'batterysizing' ), $d( 'target_voltage', 24 ), array(
					'type'    => 'select',
					'options' => array(
						array( 'value' => 12, 'label' => '12V' ),
						array( 'value' => 24, 'label' => '24V' ),
						array( 'value' => 48, 'label' => '48V' ),
					),
				) ),
				batterysizing_field( 'target_capacity', __( 'Target bank capacity', 'batterysizing' ), $d( 'target_capacity', 200 ), array( 'unit' => 'Ah', 'min' => 1 ) ),
				batterysizing_field( 'battery_voltage', __( 'Single battery voltage', 'batterysizing' ), $d( 'battery_voltage', 12 ), array(
					'type'    => 'select',
					'options' => array(
						array( 'value' => 2, 'label' => '2V cell' ),
						array( 'value' => 6, 'label' => '6V' ),
						array( 'value' => 12, 'label' => '12V' ),
					),
				) ),
				batterysizing_field( 'battery_capacity', __( 'Single battery capacity', 'batterysizing' ), $d( 'battery_capacity', 100 ), array( 'unit' => 'Ah', 'min' => 1 ) ),
			);
			break;

		case 'solar':
			$fields = array(
				batterysizing_field( 'daily_wh', __( 'Daily consumption', 'batterysizing' ), $d( 'daily_wh', 3000 ), array( 'unit' => 'Wh/day', 'min' => 1, 'help' => __( 'Use the homepage load estimator if you are not sure.', 'batterysizing' ) ) ),
				batterysizing_field( 'autonomy', __( 'Days of autonomy', 'batterysizing' ), $d( 'autonomy', 1 ), array( 'unit' => 'days', 'min' => 0.5, 'step' => 0.5, 'help' => __( 'Consecutive days with no sun the bank must cover.', 'batterysizing' ) ) ),
				batterysizing_field( 'sun_hours', __( 'Peak sun hours', 'batterysizing' ), $d( 'sun_hours', 5 ), array( 'unit' => 'h', 'min' => 1, 'max' => 8, 'step' => 0.5, 'help' => __( 'Bangladesh ≈ 4.5–5.5, US south ≈ 5–6, UK ≈ 2.5–4.', 'batterysizing' ) ) ),
				batterysizing_voltage_field( $d( 'voltage', 24 ), array( 12, 24, 48 ) ),
				batterysizing_dod_field( $d( 'dod', 80 ) ),
			);
			break;

		case 'ups':
			$fields = array(
				batterysizing_field( 'load', __( 'Connected load', 'batterysizing' ), $d( 'load', 400 ), array( 'unit' => 'W', 'min' => 1 ) ),
				batterysizing_field( 'power_factor', __( 'UPS power factor', 'batterysizing' ), $d( 'power_factor', 0.8 ), array( 'min' => 0.5, 'max' => 1, 'step' => 0.05, 'help' => __( 'From the UPS label: W ÷ VA. Typical 0.6–0.9.', 'batterysizing' ) ) ),
				batterysizing_field( 'hours', __( 'Backup time needed', 'batterysizing' ), $d( 'hours', 1 ), array( 'unit' => 'h', 'min' => 0.1, 'step' => 0.1 ) ),
				batterysizing_voltage_field( $d( 'voltage', 12 ), array( 12, 24, 48 ) ),
				batterysizing_field( 'battery_ah', __( 'Battery size in use', 'batterysizing' ), $d( 'battery_ah', 100 ), array( 'unit' => 'Ah', 'min' => 1, 'help' => __( 'Ah of each 12V battery you plan to install.', 'batterysizing' ) ) ),
				batterysizing_dod_field( $d( 'dod', 50 ) ),
				batterysizing_eff_field( $d( 'efficiency', 90 ) ),
			);
			break;

		case 'ah_to_wh':
			$fields = array(
				batterysizing_field( 'capacity', __( 'Capacity', 'batterysizing' ), $d( 'capacity', 100 ), array( 'unit' => 'Ah', 'min' => 0.01 ) ),
				batterysizing_voltage_field( $d( 'voltage', 12 ), array( 3.2, 3.7, 6, 12, 12.8, 24, 25.6, 36, 48, 51.2 ) ),
			);
			break;

		case 'wh_to_ah':
			$fields = array(
				batterysizing_field( 'energy', __( 'Energy', 'batterysizing' ), $d( 'energy', 1200 ), array( 'unit' => 'Wh', 'min' => 0.1 ) ),
				batterysizing_voltage_field( $d( 'voltage', 12 ), array( 3.2, 3.7, 6, 12, 12.8, 24, 25.6, 36, 48, 51.2 ) ),
			);
			break;
	}

	$config = array(
		'engine'   => $calc['engine'],
		'fields'   => $fields,
		'defaults' => $defaults,
		'labels'   => array(
			'runtimeTab'  => __( 'Runtime', 'batterysizing' ),
			'capacityTab' => __( 'Capacity needed', 'batterysizing' ),
			'calculate'   => __( 'Calculate', 'batterysizing' ),
		),
	);

	return $config;
}

/**
 * True when the current request renders a calculator widget.
 */
function batterysizing_page_has_calculator() {
	if ( is_admin() ) {
		return false;
	}
	if ( is_page_template( 'page-templates/template-calculator.php' ) ) {
		return true;
	}
	if ( is_singular() ) {
		$content = (string) get_post_field( 'post_content', get_the_ID() );
		if ( false !== strpos( $content, '[battery_calculator' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Battery presets used by the What-If comparison widget.
 */
function batterysizing_get_battery_presets() {
	return array(
		array( 'name' => '12V 100Ah Lead-acid', 'voltage' => 12, 'capacity' => 100, 'dod' => 50, 'chemistry' => 'lead' ),
		array( 'name' => '12V 200Ah Lead-acid', 'voltage' => 12, 'capacity' => 200, 'dod' => 50, 'chemistry' => 'lead' ),
		array( 'name' => '12V 100Ah LiFePO4', 'voltage' => 12.8, 'capacity' => 100, 'dod' => 90, 'chemistry' => 'lithium' ),
		array( 'name' => '24V 100Ah LiFePO4', 'voltage' => 25.6, 'capacity' => 100, 'dod' => 90, 'chemistry' => 'lithium' ),
		array( 'name' => '48V 50Ah LiFePO4', 'voltage' => 51.2, 'capacity' => 50, 'dod' => 90, 'chemistry' => 'lithium' ),
		array( 'name' => '48V 100Ah LiFePO4', 'voltage' => 51.2, 'capacity' => 100, 'dod' => 90, 'chemistry' => 'lithium' ),
	);
}

/**
 * Common appliance presets for the load estimator.
 */
function batterysizing_get_appliance_presets() {
	return array(
		array( 'name' => 'LED bulb', 'watts' => 10 ),
		array( 'name' => 'Ceiling fan', 'watts' => 75 ),
		array( 'name' => 'Refrigerator (average)', 'watts' => 150 ),
		array( 'name' => 'Freezer (average)', 'watts' => 200 ),
		array( 'name' => 'LED TV 32–55"', 'watts' => 80 ),
		array( 'name' => 'Laptop', 'watts' => 65 ),
		array( 'name' => 'Desktop PC + monitor', 'watts' => 250 ),
		array( 'name' => 'WiFi router', 'watts' => 10 ),
		array( 'name' => 'Phone charger', 'watts' => 10 ),
		array( 'name' => 'Washing machine', 'watts' => 500 ),
		array( 'name' => 'Rice cooker', 'watts' => 700 ),
		array( 'name' => 'Microwave', 'watts' => 1200 ),
		array( 'name' => 'Electric iron', 'watts' => 1000 ),
		array( 'name' => 'Water pump (0.5 HP)', 'watts' => 400 ),
		array( 'name' => 'AC 1 ton (inverter, avg)', 'watts' => 800 ),
		array( 'name' => 'AC 1.5 ton (inverter, avg)', 'watts' => 1200 ),
	);
}
