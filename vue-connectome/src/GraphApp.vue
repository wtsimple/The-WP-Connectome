<!-- This component represents the connectome graph -->

<template>
  <div :id="topHeadID" :class="topHeadID">
    <graph-viz></graph-viz>
    <control-panel></control-panel>
    <graph-legend></graph-legend>
  </div>
</template>


<script>
import { mapState, mapGetters } from "vuex";
import ControlPanel from "./components/ControlPanelComp";
import GraphLegend from "./components/GraphLegendComp";
import GraphViz from "./components/GraphVizComp";
import * as arm from "./helper-functions";
import { d3 } from "./globals";
import D3Simulation from "./classes/D3Simulation";

export default {
  name: "graph-app",
  components: {
    GraphViz,
    ControlPanel,
    GraphLegend
  },

  data() {
    return {
      color: null,
      simulation: {}
    };
  },

  computed: {
    ...mapState(["topHeadID"])
  },

  //Life-cycle functions
  mounted() {
    //d3.scaleOrdinal(d3.schemeCategory20); //Color category scale
    this.$store.commit("init");
  },
  //Components methods
  methods: {
    init() {
      // var zoom = d3.zoom().scaleExtent([1, 8]).on("zoom", zoomed);
      // // container.call( d3.zoom()
      // //   .scaleExtent([0.1,8]).on("zoom",
      // //     () => {
      // //       container.attr("transform", d3.event.transform)
      // // } ));
      // function zoomed() {
      //     svg.attr("transform",
      //       d3.event.transform
      //         // "translate(" + zoom.translate() + ")" +
      //         // "scale(" + zoom.scale() + ")"
      //     );
      // }
      // function interpolateZoom (translate, scale) {
      //     var self = this;
      //     return d3.transition().duration(350).tween("zoom", function () {
      //         var iTranslate = d3.interpolate(zoom.translate(), translate),
      //             iScale = d3.interpolate(zoom.scale(), scale);
      //         return function (t) {
      //             zoom
      //                 .scale(iScale(t))
      //                 .translate(iTranslate(t));
      //             zoomed();
      //         };
      //     });
      // }
      // function zoomClick() {
      //     var clicked = d3.event.target,
      //         direction = 1,
      //         factor = 0.2,
      //         target_zoom = 1,
      //         center = [960 / 2, 480 / 2],
      //         extent = zoom.scaleExtent(),
      //         translate = zoom.translate(),
      //         translate0 = [],
      //         l = [],
      //         view = {x: translate[0], y: translate[1], k: zoom.scale()};
      //     d3.event.preventDefault();
      //     direction = (this.id === 'connectome_zoom_in') ? 1 : -1;
      //     target_zoom = zoom.scale() * (1 + factor * direction);
      //     if (target_zoom < extent[0] || target_zoom > extent[1]) { return false; }
      //     translate0 = [(center[0] - view.x) / view.k, (center[1] - view.y) / view.k];
      //     view.k = target_zoom;
      //     l = [translate0[0] * view.k + view.x, translate0[1] * view.k + view.y];
      //     view.x += center[0] - l[0];
      //     view.y += center[1] - l[1];
      //     interpolateZoom([view.x, view.y], view.k);
      // }
      // d3.selectAll('button').on('click', zoomClick);
    }
  }
};
</script>


<style>
</style>