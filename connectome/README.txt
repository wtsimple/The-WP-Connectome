=== The Connectome ===
Contributors: (this should be a list of wordpress.org userid's)
Tags: data visualization, alternative navigation
Requires at least: 3.0.1
Tested up to: 5.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Connectome shows you all of your site in a single visualization. It allows you to see the connections among users, posts and taxonomy terms.

== Description ==

This is the long description. No limit, and you can use Markdown (as well as in the following sections).

For backwards compatibility, if this section is missing, the full length of the short description will be used, and
Markdown parsed.

A few notes about the sections above:

- "Contributors" is a comma separated list of wp.org/wp-plugins.org usernames
- "Tags" is a comma separated list of tags that apply to the plugin
- "Requires at least" is the lowest version that the plugin will work on
- "Tested up to" is the highest version that you've _successfully used to test the plugin_. Note that it might work on
  higher versions... this is just the highest one you've verified.
- Stable tag should indicate the Subversion "tag" of the latest stable version, or "trunk," if you use `/trunk/` for
  stable.

      Note that the `readme.txt` of the stable tag is the one that is considered the defining one for the plugin, so

  if the `/trunk/readme.txt` file says that the stable tag is `4.3`, then it is `/tags/4.3/readme.txt` that'll be used
  for displaying information about the plugin. In this situation, the only thing considered from the trunk `readme.txt`
  is the stable tag pointer. Thus, if you develop in trunk, you can update the trunk `readme.txt` to reflect changes in
  your in-development version, without having that information incorrectly disclosed about the current stable version
  that lacks those changes -- as long as the trunk's `readme.txt` points to the correct stable tag.

      If no stable tag is provided, it is assumed that trunk is stable, but you should specify "trunk" if that's where

  you put the stable version, in order to eliminate any doubt.

== Installation ==

1. Upload `connectome.zip` to the `/wp-content/plugins/` directory and extract it
2. Activate `The Connectome` through the 'Plugins' menu in WordPress
3. Go to the plugin page (under settings in the admin) to build the graph
4. Add the shortcode `[connectome-graph]` in your pages or posts to see the widget
5. You can also insert `<?php echo apply_filters('the_content', '[connectome-graph]');?>` in your PHP code

== Frequently Asked Questions ==

= Can I decide which posts, users or terms to show in the graph? =

Yes, you can decide in the settings page exactly which elements will be included
in the graph. Next to each type of element there is a button to show a foldable
panel where you can select each of the elements individually.

= What happens if I want to show certain amount for each type of element? =

You can set a max amount of elements for each type. The first "most important"
elements will be kept while the others discarded. The metrics by now is
the degree centrality. More central nodes are considered more important.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
   the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
   directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
   (or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0.0 =

- First version

== Performance ==

The graph is created by first including all the elements (but those manually disabled)
and then connecting them to calculate the centrality and evaluate which are the most important.
Only then the amount of elements are truncated to the numbers given in the options using
the centrality is the criterion.
That means a very big site (with many users, posts and terms) will require a lot of resources and time
to create the graph, even if you set it to have few elements, unless you manually disable all the unwanted
elements. If you have a very big site you should have a server with ample resources, but if
you don't, then probably is not a good idea to use The Connectome, at least as it's implemented by now.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

- something
- something else
- third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:

> Asterisks for _emphasis_. Double it up for **strong**.

`<?php code(); // goes in backticks ?>`
