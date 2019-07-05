import Vue from "vue";
import GraphApp from "./GraphApp.vue";
import store from "./vuex/store.js";
// import vueJquery from "vue-jquery";
// Vue.use(vueJquery);

new Vue({
	el: "#connectome-graph-container",
	store,
	render: h => h(GraphApp)
});
