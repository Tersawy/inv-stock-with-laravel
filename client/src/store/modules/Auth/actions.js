import api from "@/plugins/api";

export const login = async ({ commit }, payload) => {
	api("post", "login", payload, (err, res) => {
		if (err) return;
		commit("login", res.data);
	});
};

export const logout = ({ commit }) => {
	api("post", "logout", {}, (err, res) => {
		if (err) return;
		commit("logout", res.data);
	});
};

export const resetPassword = ({ commit }, data) => {
	let response = "This where is api action go";
	console.log(data);
	commit("resetPassword", response);
};

export const verifiyToken = ({ commit }, data) => {
	let response = "This where is api action go";
	console.log(data);
	commit("verifiyToken", response);
};

export const newPassword = ({ commit }, data) => {
	let response = "This where is api action go";
	console.log(data);
	commit("newPassword", response);
};

export const updateProfile = ({ commit }, data) => {
	let response = "This where is api action go";
	console.log(data);
	commit("updateProfile", response);
};
