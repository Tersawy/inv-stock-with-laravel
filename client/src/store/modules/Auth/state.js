const user = sessionStorage.getItem("user");

const state = {
	user: user ? JSON.parse(user) : {},
	token: sessionStorage.getItem("token"),
	permission: {}
};

export default state;
