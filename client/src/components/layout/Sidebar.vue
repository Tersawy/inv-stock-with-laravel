<template>
	<v-navigation-drawer app permanent :mini-variant.sync="drawer">
		<v-list-item class="px-2">
			<v-list-item-avatar>
				<v-img src="https://randomuser.me/api/portraits/men/85.jpg"></v-img>
			</v-list-item-avatar>

			<v-list-item-title>{{ me.username }}</v-list-item-title>

			<v-btn icon @click.stop="setDrawer(!drawer)">
				<v-icon>mdi-chevron-left</v-icon>
			</v-btn>
		</v-list-item>
		<v-list dense nav>
			<v-list-item v-for="item in items" :key="item.title" link :to="item.to">
				<v-list-item-icon>
					<v-icon>{{ item.icon }}</v-icon>
				</v-list-item-icon>

				<v-list-item-content>
					<v-list-item-title>{{ item.title }}</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
		</v-list>
	</v-navigation-drawer>
	<!-- <router-link to="/">Home</router-link> | <router-link to="/login">Login</router-link> |
		<router-link to="/warehouse">warehouse</router-link> | <router-link to="/warehouse/create">create a warehouse</router-link> |
		<router-link to="/warehouse/trashed">warehouse trashed</router-link> | <router-link to="/category">category</router-link> |
		<router-link to="/category/trashed">category trashed</router-link> | <router-link to="/brand">brand</router-link> |
		<router-link to="/brand/trashed">brand trashed</router-link> | <router-link to="/main-unit">main-unit</router-link> |
		<router-link to="/product">products</router-link> | <router-link to="/product/create">create a product</router-link> |
		<router-link to="/supplier">supplier</router-link> | <router-link to="/supplier/create">create a supplier</router-link> |
		<router-link to="/supplier/trashed">supplier trashed</router-link>
		| <router-link to="/purchase/create">create a purchase</router-link> |
		<b-btn @click="logout" variant="danger">logout</b-btn> -->
</template>

<script>
	import { mapMutations, mapState } from "vuex";
	export default {
		data() {
			return {
				items: [
					{ title: "Dashboard", icon: "mdi-view-dashboard", to: "/" },
					{ title: "Warehouses", icon: "mdi-image", to: "/warehouse" },
					{ title: "Categories", icon: "mdi-archive", to: "/category" },
					{ title: "Brands", icon: "mdi-help-box", to: "/brand" },
					{ title: "Products", icon: "mdi-help-box", to: "/product" },
					{ title: "Suppliers", icon: "mdi-help-box", to: "/supplier" },
					{ title: "Units", icon: "mdi-help-box", to: "/main-unit" },
					{ title: "Purchases", icon: "mdi-help-box", to: "/purchase/create" }
				]
			};
		},

		computed: {
			...mapState({
				getDrawer: (state) => state.drawer
			}),
			drawer: {
				get: function () {
					return this.getDrawer;
				},
				set: function (v) {
					this.setDrawer(v);
				}
			}
		},

		methods: {
			...mapMutations(["setDrawer"])
		}
	};
</script>

<style lang="scss"></style>
