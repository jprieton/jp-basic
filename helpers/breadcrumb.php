<?php
/**
 * 
 * @return type
 */
function the_breadcrumb() {
	if (is_home()) return;

	$items = array('Inicio' => get_option('home'));

	if (is_singular()) {

		$post_ancestors = array_reverse((array) get_post_ancestors(get_the_ID()));

		$post_type = get_post_type(get_the_ID());
		$post_type_object = get_post_type_object($post_type);

		if ($post_type_object->has_archive) {
			$items[$post_type_object->labels->name] = get_post_type_archive_link($post_type);
		}

		foreach ($post_ancestors as $ancestor_id) {
			$post_ancestor = get_post($ancestor_id);
			$items[$post_ancestor->post_title] = get_permalink($post_ancestor->ID);
		}
		$active = sprintf('<li class="active">%s</li>', get_the_title());
	} elseif (is_post_type_archive()) {

		$post_type = get_post_type();
		$post_type_object = get_post_type_object($post_type);
		$active = sprintf('<li class="active">%s</li>', $post_type_object->labels->name);
	} elseif (is_tax()) {

		if (!is_taxonomy_hierarchical(get_query_var('taxonomy'))) {
			$term_object = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
			$active = sprintf('<li class="active">%s</li>', $term_object->name);
		} else {
			$taxonomy = get_query_var('taxonomy');
			$term_object = get_term_by('slug', get_query_var('term'), $taxonomy);
			$active = sprintf('<li class="active">%s</li>', $term_object->name);

			$hierarchy_terms = array();
			$term_ancestor_id = (int) $term_object->parent;
			while ($term_ancestor_id != 0) {
				$_term = get_term($term_ancestor_id, $taxonomy);
				$hierarchy_terms[] = $_term;
				$term_ancestor_id = (int) $_term->parent;
			}
			$hierarchy_terms = array_reverse((array) $hierarchy_terms);

			foreach ($hierarchy_terms as $term) {
				$items[$term->name] = get_term_link($term);
			}
		}
	}

	$breadcrumb = '<ol class="breadcrumb">';
	foreach ($items as $item => $value) {
		$breadcrumb .= sprintf('<li><a href="%s">%s</a></li>', $value, $item);
	}
	$breadcrumb .= $active;
	$breadcrumb .= '</ol>';

	echo $breadcrumb;
}

