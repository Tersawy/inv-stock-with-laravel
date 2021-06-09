import axios from "axios";
import router from "@/router";
import store from "@/store";

axios.defaults.baseURL = process.env.VUE_APP_API_URL;

let api = async (method, route, data, callback, formData = false) => {
	let params, config, isCallback;

	config = {
		headers: {
			"Content-Type": formData ? "multipart/form-data" : "application/json",
			Authorization: `Bearer ${store.state.Auth.token}`
		}
	};

	params = method == "get" ? [route] : [route, data];

	callback = typeof data === "function" ? data : callback;

	isCallback = typeof callback === "function";

	store.commit("removeErrors");

	try {
		let res = await axios[method](...params, config);

		if (res.data.message) store.commit("setSuccess", res.data.message);

		isCallback ? callback(null, res.data) : null;
	} catch (err) {
		if (!err.response) return;

		const {
			response: { status, data }
		} = err;

		store.commit("setErrors", data);

		if (status === 401) {
			store.commit("Auth/unAuth");
			if (router.history.current.name != "Login") router.push("/login");
		}

		isCallback ? callback(data) : null;
	}
};

export default api;

// let api = (method, route, data) => {
// 	let $api, params, headers;

// 	headers = {
// 		headers: {
// 			"Content-Type": "application/json",
// 			Authorization: `Bearer ${store.state.Auth.token}`
// 		}
// 	};

// 	params = method == "get" ? [route] : [route, data];

// 	$api = axios[method](...params, headers);

// 	return $api;
// };

// export let getApi = async (route, callback) => {
// 	let isCallback = typeof callback === "function";

// 	try {
// 		let res = await api("get", route);

// 		isCallback ? callback(null, res.data) : null;
// 	} catch ({ response: { status, data } }) {
// 		store.commit("setErrors", data);

// 		if (status === 401) {
// 			store.commit("Auth/unAuth");
// 			if (router.history.current.name != "Login") router.push("/login");
// 		}

// 		isCallback ? callback(data) : null;
// 	}
// };

// export let postApi = async (route, data, callback) => {
// 	let isCallback = typeof callback === "function";

// 	try {
// 		let res = await api("post", route, data);

// 		isCallback ? callback(null, res.data) : null;
// 	} catch ({ response: { status, data } }) {
// 		store.commit("setErrors", data);

// 		if (status === 401) {
// 			store.commit("Auth/unAuth");
// 			if (router.history.current.name != "Login") router.push("/login");
// 		}

// 		isCallback ? callback(data) : null;
// 	}
// };

// export default axios;
