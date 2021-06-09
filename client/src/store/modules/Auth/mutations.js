import router from "@/router";

export const login = (state, data) => {
	state.user = data.user;
	state.token = data.token;
	sessionStorage.setItem("user", JSON.stringify(data.user));
	sessionStorage.setItem("token", data.token);

	if ([0, 1, 2].includes(data.user.type)) {
		return router.push("/");
	}

	return router.push({ name: "CustomerDashboard" });
};

export const setAuth = (state) => {
	state.token = sessionStorage.getItem("token");
	state.user = sessionStorage.getItem("user") ? JSON.parse(sessionStorage.getItem("user")) : {};
};

export const unAuth = (state) => {
	state.user = {};
	state.token = null;
	sessionStorage.removeItem("user");
	sessionStorage.removeItem("token");
};

export const logout = (state) => {
	state.user = {};
	state.token = null;
	sessionStorage.removeItem("user");
	sessionStorage.removeItem("token");
	router.push("/login");
};

export const resetPassword = (state, data) => {
	console.log(data);
};

export const verifiyToken = (state, data) => {
	console.log(data);
};

export const newPassword = (state, data) => {
	console.log(data);
};

export const updateProfile = (state, data) => {
	console.log(data);
};
