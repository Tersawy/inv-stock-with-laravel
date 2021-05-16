<template>
	<div id="nav">
		<router-link to="/">Home</router-link> |
		<router-link to="/login">Login</router-link>
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
