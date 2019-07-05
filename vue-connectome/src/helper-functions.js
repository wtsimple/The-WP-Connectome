/**
 * (arm) Returns the radius of a node in the simulation
 * @param {object} d
 */
export function node_radius(d) {
	return 10 + d.degree;
}

/**
 * (arm) Radius of the collision force of a node
 * @param {object} d
 */
export function collide_radius(d) {
	return node_radius(d) + 1;
}

export function parse_translate_string(string) {
	let content = string.match(/translate\((\d+\.\d*,\d+\.\d*)\)/)[1];
	let x = parseFloat(content.split(",")[0]);
	let y = parseFloat(content.split(",")[1]);
	return { x: x, y: y };
}

// /**
//  * Sets the width and height of an svg (d3) object
//  * Returns the resulting transformed object
//  *
//  * @param {object} svg
//  * @param {number} width
//  * @param {number} height
//  */
// export function set_svg_size(svg, width, height) {
// 	return svg.attr("height", height).attr("width", width);
// }
