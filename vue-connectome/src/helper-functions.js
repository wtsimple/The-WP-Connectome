/**
 * Returns the radius of a node in the simulation
 * @param {object} d the node data
 */
export function node_radius(d) {
	return 8 + 0.5 * Math.min(d.degree, 20) + 3 * Math.log(d.degree + 1);
}

/**
 * Radius of the collision force of a node
 * @param {object} d the node data
 */
export function collide_radius(d) {
	return node_radius(d) + 1;
}

/**
 * Returns an object with the numeric data of the translation
 * from a translation string
 *
 * @param {string} translation formatted like 'translate(5.33,4.99)'
 */
export function parse_translate_string(translation) {
	let content = translation.match(/translate\((\d+\.\d*,\d+\.\d*)\)/)[1];
	let x = parseFloat(content.split(",")[0]);
	let y = parseFloat(content.split(",")[1]);
	return { x: x, y: y };
}
