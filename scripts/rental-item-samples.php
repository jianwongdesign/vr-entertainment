<?php
/**
 * Creates the three current rental activities — VR Free Roam, Floor Is Lava,
 * XR Party Game — as published Rental Items, so the cards on /event-rental/
 * open real pages from the moment the feature goes live.
 *
 * Every fact is taken from what the site already says: player counts, ages
 * and game counts from the live game pages; space needs from the rental
 * page's own FAQ. Nothing is invented (no headset counts, no floor sizes, no
 * prices) — those are for the client to add.
 *
 * Idempotent: does nothing if any rental item already exists, in any status.
 * Cover photos are the same attachments the rental cards show today
 * (ow_seo_activity_image), so the page looks identical before and after.
 *
 * Run through wp-cli:
 *   wp eval-file rental-item-samples.php
 *
 * @package Overworld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! post_type_exists( 'rental_item' ) ) {
	WP_CLI::warning( 'The rental_item content type is not registered — is overworld-rental-items.php deployed?' );
	return;
}

$existing = get_posts(
	array(
		'post_type'   => 'rental_item',
		'post_status' => 'any',
		'numberposts' => 1,
		'fields'      => 'ids',
	)
);
if ( $existing ) {
	WP_CLI::log( sprintf( 'Rental items already exist (%d…) — leaving them alone.', (int) $existing[0] ) );
	return;
}

$space_venues = 'Function rooms, hotel ballrooms, mall atriums, school halls and outdoor tentage';

$items = array(
	array(
		'title'   => 'VR Free Roam',
		'slug'    => 'vr-free-roam',
		'order'   => 1,
		'seo_key' => 'VR Free Roam',
		'seo_url' => '/vr-free-roam/',
		'content' => "VR Free Roam is untethered virtual reality: guests put on a headset, step into the arena and walk, duck and fight together inside the game. Every move they make in the real world happens in the virtual one — there are no controllers telling them where to go.\n\nAt an event it works as a shared multiplayer experience. Teams of two to six play each round together, co-op or head-to-head, choosing from more than twenty games — zombies, co-op missions, tower defense, racing and casual titles. Our crew briefs every group before they start and runs each round, so guests who have never tried VR are playing within minutes.",
		'meta'    => array(
			'ri_badge'    => 'Multiplayer VR',
			'ri_tagline'  => 'Untethered VR for events: guests walk, duck and play together inside a shared arena — a multiplayer VR rental that works for teams and crowds.',
			'ri_hl_1_icon' => 'operators', 'ri_hl_1_text' => '2–6 Players Per Round',
			'ri_hl_2_icon' => 'age',       'ri_hl_2_text' => 'Ages 7+',
			'ri_hl_3_icon' => 'gamepad',   'ri_hl_3_text' => '20+ Games',
			'ri_hl_4_icon' => 'vr',        'ri_hl_4_text' => 'Free-Roam VR',
			'ri_pack_1_label' => 'Free-roam VR headsets for every player in the round',
			'ri_pack_1_note'  => 'Untethered — players move freely around the arena.',
			'ri_pack_2_label' => 'A play arena set out at your venue',
			'ri_pack_2_note'  => 'Marked, cleared and tested by our crew before guests arrive.',
			'ri_pack_3_label' => '20+ games to choose from',
			'ri_pack_3_note'  => 'Zombies, co-op missions, tower defense, racing and casual play.',
			'ri_pack_4_label' => 'Co-op and head-to-head modes',
			'ri_pack_4_note'  => 'Teams play together or against each other.',
			'ri_pack_5_label' => 'A VR briefing for every group',
			'ri_pack_5_note'  => 'No experience needed — the crew gets each group playing in minutes.',
			'ri_spec_1_label' => 'Play area', 'ri_spec_1_value' => 'A clear, level area with no obstacles. Send us your floor plan and we will confirm the layout.',
			'ri_spec_2_label' => 'Suitable venues', 'ri_spec_2_value' => $space_venues,
			'ri_spec_3_label' => 'Players', 'ri_spec_3_value' => '2–6 per round; rounds keep the queue moving',
			'ri_spec_4_label' => 'Age', 'ri_spec_4_value' => '7 and above. Some titles are 13+ or 16+ — we pick games to suit your crowd.',
			'ri_faq_1_q' => 'Is VR Free Roam suitable for people who have never tried VR?',
			'ri_faq_1_a' => 'Yes. Every group is briefed by our crew before they start, and the games are chosen to suit the crowd — easy, casual titles for first-timers, tougher co-op missions for groups who want a challenge.',
			'ri_faq_2_q' => 'Which games can our guests play?',
			'ri_faq_2_a' => 'There are more than twenty, from zombies and co-op missions to tower defense, racing and casual games. Some titles are rated 13+ or 16+ (horror and zombie games), so tell us your audience and we will set the line-up to match.',
		),
	),
	array(
		'title'   => 'Floor Is Lava',
		'slug'    => 'floor-is-lava',
		'order'   => 2,
		'seo_key' => 'Floor Is Lava',
		'seo_url' => '/floor-is-lava/',
		'content' => "Floor Is Lava turns the childhood game into a real-life game show on an interactive LED floor. Tiles light up, the floor shifts and players jump, balance and race to stay alive — and everybody watching wants the next turn.\n\nIt is the crowd-puller for family days, school carnivals and roadshows. There are more than thirty game modes, each with five difficulty levels, so the same floor works for young children and for competitive adults. In team mode the squad shares its lives, so one wrong step costs everyone.",
		'meta'    => array(
			'ri_badge'    => 'Crowd-Puller',
			'ri_tagline'  => 'A real-life Floor Is Lava game show on an interactive LED floor. Jump, balance and survive — the crowd-puller for family days, carnivals and roadshows.',
			'ri_hl_1_icon' => 'operators', 'ri_hl_1_text' => '2–8 Players Per Round',
			'ri_hl_2_icon' => 'age',       'ri_hl_2_text' => 'Ages 5+',
			'ri_hl_3_icon' => 'gamepad',   'ri_hl_3_text' => '30+ Game Modes',
			'ri_hl_4_icon' => 'bolt',      'ri_hl_4_text' => '5 Difficulty Levels',
			'ri_pack_1_label' => 'An interactive LED floor',
			'ri_pack_1_note'  => 'Modular — sized to the space you have.',
			'ri_pack_2_label' => '30+ game modes',
			'ri_pack_2_note'  => 'Each with 5 difficulty levels, from gentle to fast.',
			'ri_pack_3_label' => 'Team mode with shared lives',
			'ri_pack_3_note'  => 'One wrong step costs the whole squad.',
			'ri_pack_4_label' => 'Difficulty set to your crowd',
			'ri_pack_4_note'  => 'Slow for ages 5+, medium for 8+, fast for 12+.',
			'ri_spec_1_label' => 'Floor', 'ri_spec_1_value' => 'A modular LED floor that scales to the space you have. Send us your venue dimensions and we will size it.',
			'ri_spec_2_label' => 'Suitable venues', 'ri_spec_2_value' => $space_venues,
			'ri_spec_3_label' => 'Players', 'ri_spec_3_value' => '2–8 per round',
			'ri_spec_4_label' => 'Age', 'ri_spec_4_value' => '5 and above — difficulty is set to match the players',
			'ri_faq_1_q' => 'Is Floor Is Lava safe for young children?',
			'ri_faq_1_a' => 'Yes. The slowest difficulty is suitable from age 5, and our crew sets the level to match each group and supervises every round.',
			'ri_faq_2_q' => 'How big is the floor?',
			'ri_faq_2_a' => 'The floor is modular, so it scales to the space you have. Send us your venue dimensions or a floor plan and we will recommend the size and layout.',
		),
	),
	array(
		'title'   => 'XR Party Game',
		'slug'    => 'xr-party-game',
		'order'   => 3,
		'seo_key' => 'XR Party Game',
		'seo_url' => '/xr-party-game/',
		'content' => "XR Party Game is a mixed-reality party game on the big screen, controlled by players' real movements. There are no headsets and no joysticks — guests run, jump and dodge, and the game follows them.\n\nBecause there is nothing to put on, it is the easiest activity to drop into a busy event: anyone can step in, play a short round and hand over to the next group. Six game modes keep it fresh, and the scores give the crowd something to cheer for.",
		'meta'    => array(
			'ri_badge'    => 'No Headset Needed',
			'ri_tagline'  => 'Mixed-reality party games on the big screen, controlled by your real-world moves. No headset, no experience needed — a party game for all ages.',
			'ri_hl_1_icon' => 'operators', 'ri_hl_1_text' => '2–6 Players Per Round',
			'ri_hl_2_icon' => 'age',       'ri_hl_2_text' => 'Ages 5+',
			'ri_hl_3_icon' => 'gamepad',   'ri_hl_3_text' => '6 Game Modes',
			'ri_hl_4_icon' => 'star',      'ri_hl_4_text' => 'No Headset',
			'ri_pack_1_label' => 'The XR game system with a big-screen display',
			'ri_pack_1_note'  => 'Set up and tested by our crew.',
			'ri_pack_2_label' => 'A play zone in front of the screen',
			'ri_pack_2_note'  => 'Players\' real movements control the game.',
			'ri_pack_3_label' => '6 game modes',
			'ri_pack_3_note'  => 'Short, high-energy rounds with a score to beat.',
			'ri_spec_1_label' => 'Space', 'ri_spec_1_value' => 'Room for a screen and a play zone in front of it.',
			'ri_spec_2_label' => 'Suitable venues', 'ri_spec_2_value' => $space_venues,
			'ri_spec_3_label' => 'Players', 'ri_spec_3_value' => '2–6 per round',
			'ri_spec_4_label' => 'Age', 'ri_spec_4_value' => '5 and above',
			'ri_faq_1_q' => 'Do players need to wear anything?',
			'ri_faq_1_a' => 'No. There are no headsets and no controllers — the game follows the players\' own movements, so anyone can step in and play.',
			'ri_faq_2_q' => 'How much space does XR Party Game need?',
			'ri_faq_2_a' => 'Only room for a screen and a play zone in front of it, which makes it the easiest of our activities to fit into a busy venue. Send us your floor plan and we will confirm the layout.',
		),
	),
);

foreach ( $items as $item ) {
	$id = wp_insert_post(
		array(
			'post_type'    => 'rental_item',
			'post_status'  => 'publish',
			'post_title'   => $item['title'],
			'post_name'    => $item['slug'],
			'menu_order'   => $item['order'],
			'post_content' => $item['content'],
			'post_excerpt' => $item['meta']['ri_tagline'],
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( $item['title'] . ': ' . $id->get_error_message() );
		continue;
	}

	foreach ( $item['meta'] as $key => $value ) {
		update_post_meta( $id, $key, $value );
	}
	update_post_meta( $id, 'ri_featured', '1' );

	$cover = 0;
	if ( function_exists( 'ow_seo_activity_image' ) ) {
		$url = (string) ow_seo_activity_image( $item['seo_key'], $item['seo_url'] );
		if ( '' !== $url ) {
			$cover = (int) attachment_url_to_postid( $url );
		}
	}
	if ( $cover ) {
		update_post_meta( $id, 'ri_cover', $cover );
		set_post_thumbnail( $id, $cover );
	}

	WP_CLI::log( sprintf( 'Created %s (#%d) %s — cover %s', $item['title'], $id, get_permalink( $id ), $cover ? '#' . $cover : 'NONE' ) );
}
