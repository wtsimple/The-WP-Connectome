<template>
  <g :id="headID"></g>
</template>

<script>
// The Graph Component handles all the UI (visualization and user inputs) for the connectome graph
// To set the nodes and links on a force-directed layout, it uses a d3 force simulation
import { mapState, mapGetters } from "vuex";
import { d3 } from "../globals";
import * as arm from "../helper-functions";
import D3Simulation from "../classes/D3Simulation";

export default {
  name: "graph",

  data() {
    return {
      headID: "connectome-graph",
      container: {},
      simulation: {},
      nodes: {},
      links: {},
      size: { width: 900, height: 750 }
    };
  },

  computed: {
    ...mapState(["graph", "delimiter", "typesData"])
  },

  mounted() {
    this.container = d3.select("#" + this.headID);
    this.build_links();
    this.build_nodes();
    this.simulation = new D3Simulation(
      this.size.width,
      this.size.height,
      arm.collide_radius
    );
    this.simulation.set_elements(this.nodes, this.links, this.graph);
  },

  methods: {
    build_links() {
      this.links = this.container
        .append("g")
        .attr("class", "links")
        .selectAll("line")
        .data(this.graph.links)
        .join("line")
        .attr("class", "unselected-graph-link")
        .attr("id", d => d.source + "-" + d.target)
        .attr("stroke-width", function(d) {
          return Math.sqrt(d.value);
        });
    },

    build_nodes() {
      // Create the node elements on the svg
      this.nodes = this.container
        .append("g")
        .attr("class", "nodes")
        .selectAll("g")
        .data(this.graph.nodes)
        .join("g")
        .attr("class", "node-element unselected-graph-node")
        .attr("id", function(d) {
          return d.id;
        });

      // Add a circle to each node
      // and define the dragging behavior
      this.nodes
        .append("circle")
        .attr("r", arm.node_radius)
        .attr("fill", this.node_color);
      /*         .call(
          d3
            .drag()
            .on("start", this.drag_started)
            .on("drag", this.dragged)
            .on("end", this.drag_ended)
        ); */

      // Add a label text to each node
      this.nodes
        .append("text")
        .text(this.node_text())
        .attr("x", -3)
        .attr("y", 3);

      // Add a title to each node
      this.nodes.append("title").text(function(d) {
        return d.label;
      });
    },

    node_text() {
      var delimiter = this.delimiter;
      return d => d.id.replace(delimiter, " ");
    },

    node_color(d) {
      return this.typesData[d.type].color;
      if (d.type === "term") return "red";
      if (d.type === "post") return "blue";
      if (d.type === "user") return "green";
      return "yellow";
    },

    drag_started(d) {
      if (!d3.event.active) {
        this.simulation.d3sim.alphaTarget(0.3).restart();
      }
      d.fx = d.x;
      d.fy = d.y;
    },
    dragged(d) {
      d.fx = d3.event.x;
      d.fy = d3.event.y;
    },
    drag_ended(d) {
      if (!d3.event.active) {
        this.simulation.d3sim.alphaTarget(0);
      }
      d.fx = null;
      d.fy = null;
    }
  }
};
</script>

<style>
.nodes circle {
  cursor: move;
  stroke-width: 1.5px;
  stroke: #555;
}
.links line {
  stroke: #999;
}

/* Unselected Graph (when nothing is selected) */
.links line.unselected-graph-link {
  stroke-opacity: 0.6;
}
.nodes .unselected-graph-node circle {
  stroke: rgba(85, 85, 85, 0.746);
}

/* Unselected (non selected elements when something is selected) */
.links line.unselected-link {
  stroke-opacity: 0.2;
  stroke: rgb(189, 178, 178);
}
.nodes .unselected-node circle {
  opacity: 0.35;
}

/* Selected */
.links line.selected-link {
  stroke-opacity: 1;
}
.nodes .selected-node circle {
  stroke: rgb(17, 17, 17);
  stroke-width: 3px;
  opacity: 1;
}

/* Selected Neighbor */
.nodes .selected-neighbor-node circle {
  stroke: rgb(41, 39, 39);
}
</style>
