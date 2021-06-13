<template>
	<div id="nav">
		<router-link to="/">Home</router-link> | <router-link to="/login">Login</router-link> |
		<router-link to="/warehouse">warehouse</router-link> | <router-link to="/warehouse/create">create a warehouse</router-link> |
		<router-link to="/warehouse/trashed">warehouse trashed</router-link> | <router-link to="/category">category</router-link> |
		<router-link to="/category/trashed">category trashed</router-link> | <router-link to="/brand">brand</router-link> |
		<router-link to="/brand/trashed">brand trashed</router-link> | <router-link to="/main-unit">main-unit</router-link> |
		<router-link to="/product">products</router-link> | <router-link to="/product/create">create a product</router-link> |
		<router-link to="/supplier">supplier</router-link> | <router-link to="/supplier/create">create a supplier</router-link> |
		<router-link to="/supplier/trashed">supplier trashed</router-link>
		| <router-link to="/purchase/create">create a purchase</router-link> |
		<b-btn @click="logout" variant="danger">logout</b-btn>
	</div>
</template>

<script>
	import { mapActions } from "vuex";
	export default {
		methods: {
			...mapActions({
				logout: "Auth/logout"
			})
		}
	};
</script>

<style lang="scss"></style>
