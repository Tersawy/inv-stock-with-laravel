import router from "@/router";

export default {
	login(state, { data }) {
		state.user = data.user;
		state.token = data.token;
		sessionStorage.setItem("user", JSON.stringify(data.user));
		sessionStorage.setItem("token", data.token);
		if ([0, 1, 2].includes(data.user.type)) {
			return router.push("/");
		}

		return router.push({ name: "CustomerDashboard" });
	},

	me(state, { data }) {
		state.user = data.user;
		sessionStorage.setItem("user", JSON.stringify(data.user));
	},

	setAuth(state) {
		state.token = sessionStorage.getItem("token");
		state.user = sessionStorage.getItem("user") ? JSON.parse(sessionStorage.getItem("user")) : {};
	},

	unAuth(state) {
		state.user = {};
		state.token = null;
		sessionStorage.removeItem("user");
		sessionStorage.removeItem("token");
	},

	logout(state) {
		state.user = {};
		state.token = null;
		sessionStorage.removeItem("user");
		sessionStorage.removeItem("token");
		router.push("/login");
	},

	resetPassword(state, data) {
		console.log(data);
	},

	verifiyToken(state, data) {
		console.log(data);
	},

	newPassword(state, data) {
		console.log(data);
	},

	updateProfile(state, data) {
		console.log(data);
	}
};
