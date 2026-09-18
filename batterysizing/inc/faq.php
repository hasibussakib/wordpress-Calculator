<?php
/**
 * FAQ items used on the homepage teaser and the dedicated /faq/ page.
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

function batterysizing_get_faq() {
	$items = array(
		array(
			'q'       => 'How long will a 12V 100Ah battery last?',
			'a'       => 'It depends on the load and the chemistry. Usable energy is 12 × 100 × DoD. At 100W through an 85% inverter: lead-acid (50% DoD) lasts about 5.1 hours; LiFePO4 (90% DoD) lasts about 9.2 hours. Use the Battery Runtime Calculator for your exact numbers.',
			'home'    => true,
			'related' => 'battery-runtime-calculator',
		),
		array(
			'q'       => 'Ah vs Wh — which should I shop by?',
			'a'       => 'Watt-hours. Ah ignores voltage, so a 24V 50Ah battery (1,200Wh) stores the same energy as a 12V 100Ah. Convert both to Wh before you compare, or use the Ah → Wh converter.',
			'home'    => true,
			'related' => 'ah-to-wh-calculator',
		),
		array(
			'q'       => 'How many Ah do I need for a 4-hour backup?',
			'a'       => 'Required Ah = (watts × hours) ÷ (voltage × DoD × efficiency). A 300W load at 12V, 50% DoD, 85% inverter needs about 235Ah. Lithium at 90% DoD needs about 131Ah for the same job.',
			'home'    => true,
			'related' => 'battery-capacity-calculator',
		),
		array(
			'q'       => 'What depth of discharge should I use?',
			'a'       => 'Flooded lead-acid: 50%. AGM: 50–80%. Gel: 50–60%. LiFePO4: 80–100% (90% is a sensible default). Deeper cycles buy a little extra runtime and cost a lot of cycle life on lead-acid.',
			'home'    => true,
			'related' => null,
		),
		array(
			'q'       => 'Is a 100Ah lithium battery really twice a 100Ah lead-acid?',
			'a'       => 'In usable energy, yes — roughly. Lead-acid gives you ~50% of the label; LiFePO4 gives you 90–100%, at higher round-trip efficiency. Same Ah on the sticker, about 2× the hours in the wall.',
			'home'    => true,
			'related' => 'lifepo4-battery-calculator',
		),
		array(
			'q'       => '12V, 24V or 48V — which system voltage?',
			'a'       => '12V for small systems (cars, RVs, <1 kWh). 24V for mid-size solar and boats. 48V for whole-home inverters and anything above ~5 kWh — half the current, thinner cables, happier inverters.',
			'home'    => true,
			'related' => '24v-48v-battery-calculator',
		),
		array(
			'q'    => 'Why is my real runtime shorter than the calculator?',
			'a'    => 'The formula assumes rated capacity at a slow discharge. High current (Peukert), cold weather, an ageing battery, a cheap inverter and extra standby loads all shave the number. For lead-acid under heavy inverter loads, plan on 10–25% less.',
			'home' => false,
		),
		array(
			'q'    => 'How do I wire batteries in series vs parallel?',
			'a'    => 'Series adds voltage, Ah stays the same. Parallel adds Ah, voltage stays the same. Total energy (Wh) grows either way. All batteries in a bank should be the same model, age and capacity. The Battery Bank Calculator prints the series/parallel counts for you.',
			'home' => false,
		),
		array(
			'q'    => 'How many solar panels do I need to recharge the bank?',
			'a'    => 'Array watts ≈ daily Wh ÷ (peak sun hours × 0.75). 3,000Wh/day at 5 sun hours needs about 800W of panels. The 0.75 factor covers dirt, heat and controller losses. Size the bank first (days of autonomy), then the array to refill it.',
			'home' => false,
		),
		array(
			'q'    => 'Can I mix lithium and lead-acid in one bank?',
			'a'    => 'No. Different voltages, charge curves and internal resistances mean the weaker chemistry does all the work — and dies. Pick one chemistry per bank.',
			'home' => false,
		),
		array(
			'q'    => 'What inverter size do I need?',
			'a'    => 'Continuous rating ≥ your expected running watts, surge rating ≥ the largest motor start (fridge, pump, AC). A 1,000W inverter on a 12V 100Ah battery is fine electrically but will empty the battery fast — size the battery for hours, the inverter for peak watts.',
			'home' => false,
		),
		array(
			'q'    => 'Are these calculators free to use?',
			'a'    => 'Yes. Every calculator, guide and the homepage estimator on BatterySizing.xyz is free, with no account and no paywall. The WordPress theme that powers a self-hosted copy is GPL-2.0.',
			'home' => false,
		),
	);

	return apply_filters( 'batterysizing_faq', $items );
}

function batterysizing_get_home_faq() {
	return array_values( array_filter(
		batterysizing_get_faq(),
		function ( $item ) {
			return ! empty( $item['home'] );
		}
	) );
}
