<template>
  <g :id="headID" :class="[headID, isActive ? '': 'inactive-viz' ]" :transform="transform">
    <g class="node-data-container">
      <foreignObject width="300px" height="100%">
        <div class="node-data" :style="{background: color }" xmlns="http://www.w3.org/1999/xhtml">
          <h3>
            <span>{{lastClickedNode.id.replace(this.delimiter, " ")}}:</span>
            <a :href="lastClickedNode.url" target="_blank">{{titleText}}</a>
          </h3>
          <div class="node-data-block" :style="{background: color }">
            <div class="node-image-container" v-if="lastClickedNode.image">
              <a :href="lastClickedNode.url" target="_blank">
                <img :src="lastClickedNode.image" alt />
              </a>
            </div>
            <div class="connects-with-message">
              <span>Connects with:</span>
              <strong>{{lastClickedNode.degree}}</strong>
            </div>
            <div v-if="lastClickedNode.excerpt" v-html="lastClickedNode.excerpt"></div>
            <button
              class="data-viz-button"
              v-if="lastClickedNode.url"
              target="_blank"
              @click.prevent="open_link"
              :style="{background: color }"
            >See</button>
          </div>
        </div>
      </foreignObject>
    </g>
  </g>
</template>

<script>
import $ from "jquery";
import * as arm from "../helper-functions";
import { mapState, mapGetters } from "vuex";

export default {
  name: "node-data-viz",
  data() {
    return {
      headID: "node-data-viz",
      nodeTransform: { x: 0, y: 0 },
      titleText: "Node Data Viz",
      isActive: false,
      lastClickedID: "",
      lastClickedNode: { id: "0" },
      status: "unselected", //{ unselected, selectedNoViz, selectedWithViz }
      color: ""
    };
  },

  computed: {
    transform: function() {
      let Dx = 0;
      let Dy = 0;
      let translate =
        "translate(" +
        (this.nodeTransform.x + Dx) +
        "," +
        (this.nodeTransform.y + Dy) +
        ")";
      return translate;
    },
    ...mapState(["graph", "delimiter", "typesData"])
  },

  mounted() {
    $(".node-element").on("click", this.node_clicked);
  },

  methods: {
    node_clicked(event) {
      let d3node = this.get_clicked_d3_node(event);

      if (d3node.id !== this.lastClickedID) {
        this.unselect_graph();
      }

      switch (this.status) {
        case "unselected":
          this.select_node(d3node.id, false);
          break;
        case "selectedNoViz":
          this.select_node(d3node.id, true);
          break;
        case "selectedWithViz":
          this.unselect_graph();
          break;

        default:
          this.unselect_graph();
          break;
      }
      // Should only set the current clicked as the last at the end
      this.set_last_clicked_node_data(d3node);
    },

    /**
     * Selects a node and activates the visualization
     *
     * @param {string} id node being selected
     * @param {boolean} viz whether the visualization should be activated
     */
    select_node(id, viz = false) {
      this.$store.commit("select_node", id);
      if (viz) {
        this.status = "selectedWithViz";
        this.isActive = true;
      } else {
        this.status = "selectedNoViz";
      }
    },

    unselect_graph() {
      this.$store.commit("unselect_graph");
      this.isActive = false;
      this.status = "unselected";
    },

    set_last_clicked_node_data(d3node) {
      this.nodeTransform = arm.parse_translate_string(d3node.attr("transform"));
      this.lastClickedNode = this.graph.get_node_by_id(d3node.id);
      this.titleText = this.lastClickedNode.label;
      this.lastClickedID = d3node.id;
      this.color = this.typesData[this.lastClickedNode.type].color + "C5";
    },

    get_clicked_d3_node(event) {
      let target = $(event.target);
      let d3node = target.closest(".node-element");
      d3node.id = d3node.attr("id");
      return d3node;
    },

    open_link() {
      if (this.lastClickedNode.url) {
        window.open(this.lastClickedNode.url);
      }
    }
  }
};
</script>

<style scoped>
.inactive-viz {
  visibility: hidden;
}
.node-data {
  font-size: 15px;
  border: 1px solid black;
  background-color: rgba(29, 200, 216, 0.8);
}
.node-data h3 {
  margin: 5px 10px;
}
.node-data h3 a {
  color: rgb(7, 7, 46);
  text-decoration: none;
}

.node-data h3 a:hover {
  text-decoration: underline;
}

.node-data h3 span {
  font-size: 0.8em;
  font-weight: 300;
}
.node-data-block {
  height: 100%;
  border-top: 1px solid black;
  background-color: rgba(78, 170, 179, 0.8);
  padding: 5px;
  /* opacity: 0.6; */
  color: white;
}
.node-data-block img {
  width: 50%;
  margin-top: 5px;
}
.node-data-block .connects-with-message {
  margin-bottom: 8px;
}
.node-data-block .connects-with-message span {
  font-size: 0.9em;
  border-bottom: 0.5px solid white;
}
.node-data-block .connects-with-message strong {
  font-size: 1.1em;
}

.node-data-block button {
  background-color: rgba(11, 108, 117, 0.8);
  margin-bottom: 5px;
  padding: 10px 15px;
  opacity: 0.8;
  color: black;
  margin-top: 5px;
  border-radius: 5px;
  cursor: pointer;
}
.node-data-block button:hover {
  box-shadow: 2px 2px black;
}
</style>
