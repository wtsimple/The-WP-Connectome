/* eslint-disable no-undef */
import Vue from "vue";
import Vuex from "vuex";
import Graph from "../classes/Graph";

Vue.use(Vuex);

export default new Vuex.Store({
	state: {
		// eslint-disable-next-line no-undef
		graph: new Graph(vueData.graph) /* the vueData comes from wp_localize */,
		topHeadID: "connectome-viz", //The ID slug for the root element of the app
		delimiter: "_connectome_",
		// eslint-disable-next-line no-undef
		typesData: vueData.typesData
	},

	mutations: {
		/**
		 * This is the initialization for the vuex store
		 *
		 * @param {object} state
		 * @param {object} payload
		 */
		init(/* state, payload */) {},
		/**
		 * Marks a node as selected, with several other changes.
		 *
		 * The neighbors are marked as selected-neighbor
		 * The links are marked as selected-link
		 * the other nodes are marked as unselected
		 * @param {object} state
		 * @param {object} nodeID
		 */
		select_node(state, nodeID) {
			// Remove unselected-graph classes
			jQuery(".node-element")
				.removeClass()
				.addClass("node-element");
			jQuery(".links line").removeClass();
			// Apply unselected classes
			jQuery(".node-element").addClass("unselected-node");
			jQuery(".links line").addClass("unselected-link");

			// Apply selected classes
			jQuery("#" + nodeID).addClass("selected-node");
			let selectedLinks = state.graph.get_connected_links_ids(nodeID);
			for (let linkID of selectedLinks) {
				jQuery("#" + linkID)
					.removeClass("unselected-link")
					.addClass("selected-link");
			}
			// Apply selected-neighbor classes
			let neighborNodes = state.graph.get_neighbors_ids(nodeID);
			for (let neighborID of neighborNodes) {
				jQuery("#" + neighborID)
					.removeClass("unselected-node")
					.addClass("selected-neighbor-node");
			}
		},
		unselect_graph(/* state */) {
			// Remove selected-graph classes
			jQuery(".node-element")
				.removeClass()
				.addClass("node-element");
			jQuery(".links line").removeClass();
			// Apply unselected-graph classes
			jQuery(".node-element").addClass("unselected-graph-node");
			jQuery(".links line").addClass("unselected-graph-link");
		}
	},

	getters: {}
});
