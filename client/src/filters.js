import Vue from "vue";

Vue.filter("relation", function (value, key) {
	if (!value) return "Unknown";

	if (typeof value === "string") return value;

	if (typeof value === "object") {
		if (typeof key !== "undefined" && value[key] !== "undefined") {
			return value[key];
		} else {
			return value.name;
		}
	}
});

Vue.filter("floating", function (value, num = 2) {
	value = value ? value : 0;

	return value.toFixed(num);
});
