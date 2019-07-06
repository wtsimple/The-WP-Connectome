// import NodeList from "./GraphSubclasses/NodeList";
// import LinkList from "./GraphSubclasses/LinkList";
// import NodeElement from "./GraphSubclasses/NodeElement";
// import LinkElement from "./GraphSubclasses/LinkElement";

/**
 * Handles the Internal Logic and algorithms for the Graph entity
 *
 * There are several lower level classes like NodeList, LinkList, NodeElement and LinkElement
 * this class relies on.
 */
export default class Graph {
	constructor(graphArray) {
		this.nodes = graphArray.nodes;
		this.links = graphArray.links;
		this.lastSelectedNodeID = "";
	}

	/**
	 * Returns the node with the given ID
	 * @param {string} nodeID
	 */
	get_node_by_id(nodeID) {
		return this.nodes.find(function(element) {
			return element.id == nodeID;
		});
	}

	/**
	 * Returns the links connected to the given node
	 * @param {string} nodeID
	 */
	get_connected_links(nodeID) {
		let links = this.links.filter(function(link) {
			return link.source.id === nodeID || link.target.id === nodeID;
		});
		return links;
	}

	/**
	 * Returns the IDs of the links connected to the given node
	 * @param {string} nodeID
	 */
	get_connected_links_ids(nodeID) {
		let links = this.get_connected_links(nodeID);
		let ids = links.map(link => link.source.id + "-" + link.target.id);
		return ids;
	}

	/**
	 * Returns the neighbor nodes of the given node
	 * @param {string} nodeID
	 */
	get_neighbors(nodeID) {
		// Find the links that contain the nodeID as source or target
		let connectedLinks = this.get_connected_links(nodeID);
		let neighbors = [];
		for (let link of connectedLinks) {
			// If the source is the selected node, then
			// the neighbor is the target, and viceversa
			if (link.source.id == nodeID) {
				neighbors.push(this.get_node_by_id(link.target.id));
			} else {
				neighbors.push(this.get_node_by_id(link.source.id));
			}
		}
		return neighbors;
	}

	/**
	 * Returns the IDs of the given node's neighbors
	 * @param {string} nodeID
	 */
	get_neighbors_ids(nodeID) {
		let nodes = this.get_neighbors(nodeID);
		let ids = nodes.map(node => node.id);
		return ids;
	}
}
