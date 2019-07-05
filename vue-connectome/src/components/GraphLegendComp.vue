<template>
  <div id="connectome-graph-legend">
    <h3>Legend</h3>
    <div class="legend-element" v-for="(val, key) in typesData" :key="key">
      <span class="legend-element-color" :style="{ background: val.color }"></span>
      <span class="legend-element-text">{{ val.plural }}</span>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters } from "vuex";

export default {
  name: "graph-legend",

  data() {
    return {};
  },

  computed: {
    ...mapState(["typesData"])
  }
};
</script>

<style scoped>
div.legend-element {
  display: inline-block;
  margin-right: 10px;
}
.legend-element-color {
  display: inline-block;
  height: 40px;
  width: 40px;
  margin-right: 4px;
  border-radius: 6px;
  border: 1px solid rgba(87, 79, 79, 0.452);
}
.legend-element-text {
  display: inline-block;
  position: relative;
  top: -13px;
}
</style>
