import Vue from "vue";
import App from "@/App.vue";
import router from "@/router";

import { BootstrapVue } from "bootstrap-vue";
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";
Vue.use(BootstrapVue);

import store from "@/store";
Vue.prototype.$store = store;

import userMixin from "./mixins/userMixin";
Vue.mixin(userMixin);

import $axios from "@/plugins/api";
Vue.prototype.$axios = $axios;

Vue.config.productionTip = false;

router.beforeEach(async (to, from, next) => {
	let user = store.state.user;

	user = user ? user : sessionStorage.getItem("user");

	user = user ? user : {};

	user = typeof user === "object" ? user : JSON.parse(user);

	let isUser = Object.keys(user).length > 1;

	if (!isUser && to.meta.auth) return next({ name: "Login" });

	if (isUser && (!to.meta.auth || (to.meta.auth && !to.meta.userType.includes(+user.type)))) {
		if ([0, 1, 2].includes(+user.type)) return next({ name: "Dashboard" });
		if ([3].includes(+user.type)) return next({ name: "CustomerDashboard" });
	}

	// If !isUser the user will be next to Login page
	// if isUser will complete his route
	next();
});

new Vue({
	store,
	router,
	render: (h) => h(App)
}).$mount("#app");
