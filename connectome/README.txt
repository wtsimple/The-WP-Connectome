=== The Connectome ===
Contributors: armandorivero
Tags: data visualization, graphs, alternative navigation
Requires at least: 4.0.1
Tested up to: 5.2.2
Requires PHP: 5.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: none

The Connectome shows you all of your site in a single visualization. It allows you to see the connections among users, posts and taxonomy terms.

== Description ==

A WP site is made with several elements like posts, users and taxonomy terms.
The Connectome tries to show all of them and their connections in a single
interactive visualization. Also, we calculate some measure of importance
for the nodes and make it obvious through the node's size.
The Connectome, then, should provide insight about the structure
of your site and a different navigation, mostly interesting for the
site admin or editor but perhaps also good to give your visitors to play with.

I think that The Connectome could be of particular use to novice WP
site creators because at the beginning you'll still be wrapping
your head around what's WP about. The Connectome will help you
to learn all the essential elements you have in the site and
how they connect to each other.

The "importance measure" used so far is the simplest, just the degree centrality,
that is, how many connections does the node has.

This is the first release of my first plugin so I'm very exited and scared, but also quite
open to suggestions, requests or criticism. I would be especially happy if more
experienced developers inspected my code and gave me their opinions.

## Road map

I have a long list of improvements to include in future releases, among them:

- Responsiveness and more flexible behavior for the visualization
- Search functionality to find nodes in the graph
- Having several graphs with different configurations to show in different parts of your site
- Tell the admin about problematic nodes like posts without featured image or excerpt text, taxonomy terms without description, etc.
- More interesting importance measures like the eigenvalue or the betweeness centralities, perhaps allowing the user to pick which one to use
- Multilingual support, right now it's only English. Besides making it translation ready, this will also imply to make the plugin compatible with multilingual plugins like Polylang or WPML

If you think one of them is more urgent thant others, you can also tell me.

== Installation ==

1. Upload `connectome.zip` to the `/wp-content/plugins/` directory and extract it
2. Activate `The Connectome` through the 'Plugins' menu in WordPress
3. Go to the plugin page (under settings in the admin) to build the graph
4. Add the shortcode `[connectome-graph]` in your pages or posts to see the widget
5. You can also insert `<?php echo apply_filters('the_content', '[connectome-graph]');?>` in your PHP code

== Frequently Asked Questions ==

= Can I decide which elements go in the graph? =

Yes, you can decide in the settings page exactly which elements will be included.
Next to each type of element there is a button to show a foldable
panel where you can select each element individually. Right now is a bit cumbersome
if you have many elements of some type. I'll try to make this panel more
functional in future releases.

= What if I want to show only certain amount for each type of element? =

You can set a max amount of elements for each type. The first "most important"
elements will be kept while the others discarded. The metrics by now is
the degree centrality. More central nodes are considered more important.

== Screenshots ==

1. Unselected graph display
2. Node selected without data visualization
3. Node data visualization
4. Options (the panel to select individual elements is foldable)

== Changelog ==

= 1.0.0 =

- First version

== Upgrade Notice ===
This is the first released version

== Performance ==

The graph is created by first including all the elements (but those manually disabled)
and then connecting them to calculate the centrality and evaluate which are the most important.
Only then the amount of elements are truncated to the numbers given in the options using
the centrality is the criterion.
That means a very big site (with many users, posts and terms) will require a lot of resources and time
to create the graph, even if you set it to have few elements, unless you manually disable all the unwanted
elements. If you have a very big site you should have a server with ample resources, but if
you don't, then probably is not a good idea to use The Connectome, at least as it's implemented by now.
