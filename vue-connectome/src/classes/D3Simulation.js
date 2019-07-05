import { d3 } from "../globals";

/**
 * (arm) Abstracts a d3 Force simulation for directed force layout
 */
export default class Simulation {
	/**
	 *
	 * @param {Number} width
	 * @param {Number} height
	 * @param {function} radiusFunction
	 */
	constructor(width, height, radiusFunction) {
		this.d3sim = d3
			.forceSimulation()
			.force(
				"link",
				d3.forceLink().id(function(d) {
					return d.id;
				})
			)
			.force("charge", d3.forceManyBody().strength(-150))
			.force("center", d3.forceCenter(width / 2, height / 2))
			.force("collision", d3.forceCollide(radiusFunction));
	}

	set_elements(nodes, links, graph) {
		this.graph = graph;
		this.nodes = nodes;
		this.links = links;
		this.d3sim.nodes(this.graph.nodes).on("tick", this.ticked());
		this.d3sim.force("link").links(this.graph.links);
	}

	ticked() {
		var d3links = this.links;
		var d3nodes = this.nodes;
		return function() {
			d3links
				.attr("x1", function(d) {
					return d.source.x;
				})
				.attr("y1", function(d) {
					return d.source.y;
				})
				.attr("x2", function(d) {
					return d.target.x;
				})
				.attr("y2", function(d) {
					return d.target.y;
				});

			d3nodes.attr("transform", function(d) {
				return "translate(" + d.x + "," + d.y + ")";
			});
		};
	}

	// drag_started(d) {
	// 	if (!d3.event.active) {
	// 		this.simulation.alphaTarget(0.3).restart();
	// 	}
	// 	d.fx = d.x;
	// 	d.fy = d.y;
	// }
	// dragged(d) {
	// 	d.fx = d3.event.x;
	// 	d.fy = d3.event.y;
	// }
	// drag_ended(d) {
	// 	if (!d3.event.active) {
	// 		this.simulation.alphaTarget(0);
	// 	}
	// 	d.fx = null;
	// 	d.fy = null;
	// }
}
