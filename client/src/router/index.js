import Vue from "vue";
import VueRouter from "vue-router";
import Login from "@/views/Login.vue";
import Dashboard from "@/views/Dashboard.vue";
import CustomerDashboard from "@/views/Customer/Dashboard.vue";

Vue.use(VueRouter);

const routes = [
	{
		path: "/login",
		name: "Login",
		component: Login,
		meta: { auth: false }
	},
	{
		path: "/",
		name: "Dashboard",
		component: Dashboard,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/customer",
		name: "CustomerDashboard",
		component: CustomerDashboard,
		meta: { auth: true, userType: [3] }
	}
];

const router = new VueRouter({
	mode: "history",
	base: process.env.BASE_URL,
	routes
});

export default router;
