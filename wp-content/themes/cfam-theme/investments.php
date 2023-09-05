<?php
$context = Timber::context();
$post = new Timber\Post();
$post_cat = $post->get_terms('category');
$post_cat = $post_cat[0]->ID;
$context['post'] = $post;

$sidebar_context = array();
$sidebar_context['investments'] = Timber::get_posts('cat='.$post_cat);
// $context['sidebar'] = Timber::get_sidebar('sidebar-related.twig', $sidebar_context);
Timber::render('single.twig', $context);