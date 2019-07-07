# The WP Connectome

A WordPress plugin to show all the elements in a site, like users, terms or posts (both default and custom)
and also the connections among them in a single visualization. It also provides an alternative navigation
and a way of gaining insight over the site's content.

The backend was made on PHP and only requires only WordPress. The front end is made with VUEJS
and D3JS. VUE handles all the logic of the JS app and D3 only the force-directed graph layout.

This is the development version, for the ready to use version go to WordPress-url.

To develop on the front end side you need node and npm.
First install all dependencies by moving to vue-connectome and running:

`npm install`

To run the development server (pointing to localhost:8080), still in the vue-connectome folder, run:

`npm run dev`

To compile and bundle all the JS needed to run the graph app, use:

`npm run build`

I also advice you to use the Vue DevTools extension for either Chrome or Firefox,
although if you are a VUE developer you probably know this.
