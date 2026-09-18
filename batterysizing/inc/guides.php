<?php
/**
 * Guide registry — 8 educational pages matching the original /guides/ routes.
 *
 * @package Batterysizing
 */

defined( 'ABSPATH' ) || exit;

/**
 * All guides, in hub display order.
 */
function batterysizing_get_guides() {
	$guides = array(

		array(
			'slug'             => 'ah-vs-wh',
			'name'             => __( 'Ah vs Wh: Which Number Actually Matters?', 'batterysizing' ),
			'tagline'          => __( 'Amp-hours measure charge. Watt-hours measure energy. Only one of them is comparable across voltages.', 'batterysizing' ),
			'read_time'        => '6 min',
			'meta_title'       => 'Ah vs Wh — Amp-Hours vs Watt-Hours Explained',
			'meta_description' => 'Ah vs Wh explained with examples. Learn why watt-hours (not amp-hours) are the honest way to compare 12V, 24V and 48V batteries, plus the conversion formula.',
			'content'          => '
				<p>Every battery label prints two numbers that look similar and mean completely different things: <strong>Ah</strong> (amp-hours) and, less often, <strong>Wh</strong> (watt-hours). Mixing them up is the #1 reason people buy a battery that is half the size they thought.</p>
				<h2>The one-line difference</h2>
				<ul>
					<li><strong>Ah</strong> = how much <em>charge</em> the battery can push. It ignores voltage.</li>
					<li><strong>Wh</strong> = how much <em>energy</em> the battery stores. Energy is what actually runs your loads.</li>
				</ul>
				<p>The conversion is just multiplication:</p>
				<pre><code>Wh  =  Ah  ×  V
Ah  =  Wh  ÷  V</code></pre>
				<h2>Why Ah lies across voltages</h2>
				<p>A 12V 100Ah battery and a 24V 50Ah battery both store <strong>1,200Wh</strong>. Same energy, same runtime at the same load — half the Ah on the 24V pack. If you shop by Ah alone you will think the 24V battery is “half as big.”</p>
				<table>
					<thead><tr><th>Battery</th><th>Ah</th><th>Voltage</th><th>Energy (Wh)</th></tr></thead>
					<tbody>
						<tr><td>12V lead-acid</td><td>100</td><td>12</td><td>1,200</td></tr>
						<tr><td>24V lead-acid</td><td>50</td><td>24</td><td>1,200</td></tr>
						<tr><td>48V LiFePO4</td><td>25</td><td>48</td><td>1,200</td></tr>
						<tr><td>12.8V LiFePO4</td><td>100</td><td>12.8</td><td>1,280</td></tr>
					</tbody>
				</table>
				<p>Portable power stations almost always advertise Wh for this reason. Battery shops advertise Ah because that is the number on the case. Convert everything to Wh before you compare.</p>
				<h2>Usable energy is still less</h2>
				<p>Label Wh is <em>stored</em> energy. Usable energy = stored × depth of discharge × inverter efficiency. A 1,200Wh lead-acid bank at 50% DoD and 85% inverter efficiency delivers about 510Wh to your AC loads — not 1,200.</p>
				<p>Use the <a href="/ah-to-wh-calculator/">Ah → Wh converter</a> and the <a href="/wh-to-ah-calculator/">Wh → Ah converter</a> whenever you are matching a power-station rating to a battery, or comparing packs at different voltages.</p>
			',
		),

		array(
			'slug'             => 'calculate-battery-runtime',
			'name'             => __( 'How to Calculate Battery Runtime', 'batterysizing' ),
			'tagline'          => __( 'The exact formula, a worked example, and the three things that make real runtime shorter than the math.', 'batterysizing' ),
			'read_time'        => '7 min',
			'meta_title'       => 'How to Calculate Battery Runtime (Formula + Examples)',
			'meta_description' => 'Step-by-step battery runtime formula: hours = (V × Ah × DoD × efficiency) ÷ load watts. Worked examples for 12V, lithium and inverter loads.',
			'content'          => '
				<p>Runtime is “how many hours will this battery run this load?” It is the most searched battery question, and the formula is short:</p>
				<pre><code>Runtime (hours)  =  (Voltage × Ah × DoD ÷ 100 × Efficiency ÷ 100)  ÷  Load (W)</code></pre>
				<h2>Worked example</h2>
				<p>A 12V 100Ah lead-acid battery running a 100W fridge through an 85% efficient inverter, discharged to 50%:</p>
				<pre><code>Usable Wh  =  12 × 100 × 0.50 × 0.85  =  510 Wh
Runtime    =  510 ÷ 100               =  5.1 hours  (5h 06m)</code></pre>
				<p>Swap the same capacity to LiFePO4 at 90% DoD and 95% efficiency:</p>
				<pre><code>Usable Wh  =  12 × 100 × 0.90 × 0.95  =  1,026 Wh
Runtime    =  1,026 ÷ 100             =  10.3 hours</code></pre>
				<p>Same “100Ah” on the label, roughly twice the runtime. Chemistry is not a rounding error.</p>
				<h2>Three things the formula does not capture</h2>
				<ol>
					<li><strong>Peukert’s law.</strong> Lead-acid capacity is rated at a slow 20-hour discharge. Pulling high current (inverter surge, water pump) delivers less Ah than the label. Lithium is almost immune.</li>
					<li><strong>Temperature.</strong> Below 10°C a lead-acid battery can lose 20–40% of its capacity. LiFePO4 charging is often blocked below 0°C by the BMS.</li>
					<li><strong>Age.</strong> After a few hundred cycles a flooded battery may be at 70–80% of nameplate. Size with 20% headroom.</li>
				</ol>
				<p>For a live answer, use the <a href="/battery-runtime-calculator/">Battery Runtime Calculator</a>. If you already know the hours you need and want the battery instead, flip to the <a href="/battery-capacity-calculator/">Capacity Calculator</a>.</p>
			',
		),

		array(
			'slug'             => 'how-many-ah-do-i-need',
			'name'             => __( 'How Many Ah Do I Need?', 'batterysizing' ),
			'tagline'          => __( 'Work backwards from the load and the hours you want — the honest way to size a battery.', 'batterysizing' ),
			'read_time'        => '6 min',
			'meta_title'       => 'How Many Ah Do I Need? Battery Sizing Guide',
			'meta_description' => 'Find out how many amp-hours you need for any load and backup time. Includes the capacity formula, a sizing table, and chemistry-specific DoD advice.',
			'content'          => '
				<p>Sizing from Ah on a shop page is guessing. Size from the load and the hours you actually need:</p>
				<pre><code>Required Ah  =  (Load watts × Hours)  ÷  (Voltage × DoD ÷ 100 × Efficiency ÷ 100)</code></pre>
				<h2>Quick table — 12V system, 85% inverter, 4 hours backup</h2>
				<table>
					<thead><tr><th>Load</th><th>Lead-acid 50% DoD</th><th>LiFePO4 90% DoD</th></tr></thead>
					<tbody>
						<tr><td>100W (lights + router + laptop)</td><td>78 Ah</td><td>44 Ah</td></tr>
						<tr><td>300W (fridge + lights + fan)</td><td>235 Ah</td><td>131 Ah</td></tr>
						<tr><td>800W (small AC or microwave bursts)</td><td>627 Ah</td><td>348 Ah</td></tr>
						<tr><td>1,500W (several kitchen loads)</td><td>1,176 Ah</td><td>654 Ah</td></tr>
					</tbody>
				</table>
				<p>Always round up to the next common size (100, 150, 200, 280 Ah) and add ~20% for age and Peukert. Two 12V 200Ah batteries in parallel is usually cheaper and healthier than one oversized 400Ah unit.</p>
				<h2>A better starting point: watt-hours</h2>
				<p>Compute energy first (<code>Wh = watts × hours</code>), divide by usable fraction, then convert to Ah at your voltage. The homepage <strong>Appliance Load Estimator</strong> totals watts and daily Wh for you — paste that number into the <a href="/battery-capacity-calculator/">Capacity Calculator</a> or the <a href="/solar-battery-calculator/">Solar Battery Calculator</a>.</p>
			',
		),

		array(
			'slug'             => 'depth-of-discharge',
			'name'             => __( 'Depth of Discharge (DoD), Explained', 'batterysizing' ),
			'tagline'          => __( 'The percentage of the battery you actually use — and the reason a 100Ah lithium battery outruns a 100Ah lead-acid.', 'batterysizing' ),
			'read_time'        => '5 min',
			'meta_title'       => 'Depth of Discharge (DoD) — What It Means for Battery Life',
			'meta_description' => 'Depth of discharge explained: recommended DoD by chemistry (lead-acid 50%, AGM 80%, LiFePO4 90–100%) and how it changes usable energy and cycle life.',
			'content'          => '
				<p><strong>Depth of discharge</strong> is the fraction of a battery’s capacity you use on each cycle. 50% DoD means you stop at half empty; 100% DoD means you run it dry.</p>
				<h2>Recommended DoD by chemistry</h2>
				<table>
					<thead><tr><th>Chemistry</th><th>Safe DoD</th><th>Usable Wh from a 12V 100Ah</th><th>Typical cycle life</th></tr></thead>
					<tbody>
						<tr><td>Flooded lead-acid</td><td>50%</td><td>600 Wh</td><td>300–500</td></tr>
						<tr><td>AGM / Gel</td><td>50–80%</td><td>600–960 Wh</td><td>400–800</td></tr>
						<tr><td>LiFePO4</td><td>80–100%</td><td>960–1,200 Wh</td><td>3,000–6,000</td></tr>
					</tbody>
				</table>
				<p>Going deeper than the chemistry likes trades a little extra runtime today for a lot of lost cycles tomorrow. Lead-acid cycled to 80% DoD often dies in a year; the same bank at 50% lasts three.</p>
				<h2>DoD is not the same as state of charge</h2>
				<p>State of charge (SoC) is how full the battery is right now. DoD is how empty you plan to let it get. A battery sitting at 40% SoC has been discharged 60% — that is a 60% DoD cycle.</p>
				<p>Every calculator on this site multiplies capacity by DoD so the runtime you see is the <em>usable</em> runtime, not the label fantasy. Change the DoD dropdown and watch hours jump — that is the chemistry tax, visualised.</p>
			',
		),

		array(
			'slug'             => 'battery-chemistry',
			'name'             => __( 'Battery Chemistry Comparison', 'batterysizing' ),
			'tagline'          => __( 'Flooded, AGM, gel, LiFePO4 and NMC — which one belongs in your system.', 'batterysizing' ),
			'read_time'        => '8 min',
			'meta_title'       => 'Battery Chemistry Comparison: Lead-Acid vs AGM vs LiFePO4',
			'meta_description' => 'Compare flooded lead-acid, AGM, gel, LiFePO4 and NMC on usable energy, cycle life, cost per kWh, temperature and safety — pick the right chemistry for solar, RV, UPS or marine.',
			'content'          => '
				<p>The chemistry inside the case decides usable energy, lifespan, charging rules and (usually) whether the battery is worth the extra money. Here is the field, stripped of brochure language.</p>
				<h2>At a glance</h2>
				<table>
					<thead><tr><th></th><th>Flooded</th><th>AGM</th><th>Gel</th><th>LiFePO4</th></tr></thead>
					<tbody>
						<tr><td>Usable DoD</td><td>50%</td><td>50–80%</td><td>50–60%</td><td>90–100%</td></tr>
						<tr><td>Cycle life</td><td>300–500</td><td>400–800</td><td>500–1,000</td><td>3,000–6,000</td></tr>
						<tr><td>Round-trip efficiency</td><td>~80%</td><td>~85%</td><td>~85%</td><td>~95%</td></tr>
						<tr><td>Weight (12V 100Ah)</td><td>~30 kg</td><td>~28 kg</td><td>~30 kg</td><td>~12 kg</td></tr>
						<tr><td>Upfront cost</td><td>Lowest</td><td>Medium</td><td>Medium</td><td>Highest</td></tr>
						<tr><td>Cost per kWh-cycle</td><td>Highest</td><td>High</td><td>High</td><td>Lowest</td></tr>
						<tr><td>Maintenance</td><td>Water, vent</td><td>None</td><td>None</td><td>None (BMS)</td></tr>
						<tr><td>Cold charging</td><td>OK</td><td>OK</td><td>OK</td><td>Blocked &lt; 0°C</td></tr>
					</tbody>
				</table>
				<h2>When to pick what</h2>
				<ul>
					<li><strong>Flooded lead-acid</strong> — cheap standby UPS, off-grid cabins where weight and watering are fine.</li>
					<li><strong>AGM</strong> — sealed backup, mobility scooters, starting + cycling hybrids. Better than flooded, still heavy.</li>
					<li><strong>Gel</strong> — slow, deep discharges in hot rooms. Poor at high current (inverters).</li>
					<li><strong>LiFePO4</strong> — daily cycling: solar, RV, marine, home backup. Safest lithium, flat voltage curve, worth it if you cycle more than ~200 times.</li>
					<li><strong>NMC / NCA</strong> — power stations and EVs. Higher energy density, shorter life, stricter thermal limits. Not a great stationary choice.</li>
				</ul>
				<p>A 12V 100Ah LiFePO4 costs more than a 12V 100Ah AGM, but it delivers nearly twice the usable Wh and ten times the cycles. Over a decade it is usually the cheaper battery. Run the numbers in the <a href="/lifepo4-battery-calculator/">LiFePO4 calculator</a>.</p>
			',
		),

		array(
			'slug'             => 'battery-formulas',
			'name'             => __( 'Battery Formulas Cheat Sheet', 'batterysizing' ),
			'tagline'          => __( 'Every formula the calculators on this site use, in one place.', 'batterysizing' ),
			'read_time'        => '5 min',
			'meta_title'       => 'Battery Formulas Cheat Sheet (Wh, Ah, Runtime, Banks)',
			'meta_description' => 'Copy-ready battery formulas: Wh = V × Ah, runtime, required capacity, series/parallel bank counts, solar array sizing and UPS VA. All of them, one page.',
			'content'          => '
				<h2>Energy and charge</h2>
				<pre><code>Wh          =  V  ×  Ah
kWh         =  Wh  ÷  1,000
Ah          =  Wh  ÷  V
Usable Wh   =  V  ×  Ah  ×  (DoD ÷ 100)</code></pre>
				<h2>Runtime and capacity</h2>
				<pre><code>Runtime (h)     =  (V × Ah × DoD/100 × η/100)  ÷  Load(W)
Required Ah     =  (Load(W) × Hours)  ÷  (V × DoD/100 × η/100)
Required Wh     =  Load(W) × Hours  ÷  (DoD/100 × η/100)</code></pre>
				<p>η is inverter (or round-trip) efficiency in percent. Use 100 for pure DC loads.</p>
				<h2>Current</h2>
				<pre><code>Amps from a DC load     =  Watts  ÷  Voltage
Amps through inverter   =  Watts  ÷  (Voltage × η/100)
Cable sizing            =  use amps + length + 3% voltage-drop tables</code></pre>
				<h2>Battery banks</h2>
				<pre><code>Series count       =  Target V  ÷  Battery V
Parallel strings   =  Target Ah ÷  Battery Ah   (round up)
Total batteries    =  Series × Parallel
Bank Wh            =  Target V × Target Ah</code></pre>
				<p>Never mix series and parallel in the same string without identical batteries (same brand, age, capacity). A weak cell becomes a heater.</p>
				<h2>Solar</h2>
				<pre><code>Bank Wh            =  Daily Wh  ×  Autonomy days  ÷  (DoD/100)
Bank Ah            =  Bank Wh  ÷  System V
Array watts        =  Daily Wh  ÷  (Peak sun hours × 0.75)</code></pre>
				<p>The 0.75 derate covers dirt, heat, charge-controller and wiring losses so the array actually refills the bank on an average day.</p>
				<h2>UPS</h2>
				<pre><code>UPS rating (VA)    =  Load(W)  ÷  Power factor
Required Ah        =  (Load(W) × Hours)  ÷  (V × DoD/100 × η/100)
12V battery count  =  ceil(Required Ah ÷ Battery Ah)  ×  (Bus V ÷ 12)</code></pre>
				<p>Every calculator on this site is just these lines with a nicer input. If you want the derivation in context, the <a href="/guides/calculate-battery-runtime/">runtime guide</a> and <a href="/guides/how-many-ah-do-i-need/">Ah sizing guide</a> walk through them with numbers.</p>
			',
		),

		array(
			'slug'             => 'examples',
			'name'             => __( 'Worked Battery Sizing Examples', 'batterysizing' ),
			'tagline'          => __( 'Four real setups — apartment UPS, RV fridge, off-grid cabin, shop backup — sized end to end.', 'batterysizing' ),
			'read_time'        => '9 min',
			'meta_title'       => 'Battery Sizing Examples: UPS, RV, Solar Cabin, Shop',
			'meta_description' => 'Four worked battery sizing examples with real appliance lists, DoD, inverter efficiency and the final Ah/Wh recommendation for each.',
			'content'          => '
				<h2>1. Apartment UPS — lights, router, laptop, fan</h2>
				<p>Load ≈ 10W lights + 10W router + 65W laptop + 75W fan = <strong>160W</strong>. Want 3 hours. 12V lead-acid, 50% DoD, 85% inverter.</p>
				<pre><code>Ah  =  (160 × 3) ÷ (12 × 0.50 × 0.85)  ≈  94 Ah
Buy =  one 12V 100Ah tubular / AGM (or a 12V 100Ah LiFePO4 for ~2× the runtime)</code></pre>
				<h2>2. RV — compressor fridge overnight</h2>
				<p>12V DC fridge, 50W average, 12 hours, LiFePO4 at 90% DoD, no inverter (DC).</p>
				<pre><code>Ah  =  (50 × 12) ÷ (12.8 × 0.90 × 1.00)  ≈  52 Ah
Buy =  12V 100Ah LiFePO4 (covers fridge + phones + lights with margin)</code></pre>
				<h2>3. Off-grid cabin — 3 kWh/day, 2 days autonomy</h2>
				<p>24V LiFePO4, 90% DoD, 5 peak sun hours.</p>
				<pre><code>Bank Wh   =  3,000 × 2 ÷ 0.90          =  6,667 Wh
Bank Ah   =  6,667 ÷ 24                ≈  278 Ah   →  300 Ah at 24V
Array W   =  3,000 ÷ (5 × 0.75)        =  800 W of panels</code></pre>
				<p>A 24V 300Ah LiFePO4 is two 12V 300Ah in series, or six 12V 100Ah (2S3P). Use the <a href="/solar-battery-calculator/">solar calculator</a> and the <a href="/battery-bank-calculator/">bank calculator</a> to try layouts.</p>
				<h2>4. Workshop backup — 1,200W for 2 hours</h2>
				<p>48V LiFePO4, 90% DoD, 90% hybrid inverter.</p>
				<pre><code>Ah  =  (1,200 × 2) ÷ (48 × 0.90 × 0.90)  ≈  62 Ah
Buy =  48V 100Ah (or 16 × 3.2V 100Ah cells) — extra headroom for tool surge</code></pre>
				<p>Open the matching calculator and change one number at a time. The point of these examples is not the exact Ah — it is the habit of starting from watts and hours, not from a battery you already wanted to buy.</p>
			',
		),
	);

	return apply_filters( 'batterysizing_guides', $guides );
}

function batterysizing_get_guide( $slug ) {
	foreach ( batterysizing_get_guides() as $guide ) {
		if ( $guide['slug'] === $slug ) {
			return $guide;
		}
	}
	return null;
}

function batterysizing_get_guide_by_page() {
	if ( ! is_page() ) {
		return null;
	}
	$slug = get_post_field( 'post_name', get_the_ID() );
	return batterysizing_get_guide( $slug );
}
