<template>
  <div :id="headID">
    <svg>
      <graph-comp />
      <node-data-viz />
    </svg>
  </div>
</template>

<script>
import NodeDataViz from "./NodeDataVizComp";
import GraphComp from "./GraphComp";
import { d3 } from "../globals";

export default {
  name: "graph-viz",

  components: {
    NodeDataViz,
    GraphComp
  },

  data() {
    return {
      headID: "connectome-graph-viz",
      width: 960,
      height: 800,
      svg: {}
    };
  },

  // Life cycle functions
  mounted() {
    // Set the size of the svg element
    this.svg = d3
      .select("#" + this.headID + "> svg")
      .attr("width", this.width)
      .attr("height", this.height);
  },

  methods: {}
};
</script>

<style>
svg {
  border: 1px solid black;
}

text {
  font-family: sans-serif;
  font-size: 10px;
}
</style>
