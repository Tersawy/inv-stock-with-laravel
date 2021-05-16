<template>
	<div id="app">
		<Navbar v-if="isAuth" />
		<router-view />
	</div>
</template>

<script>
	import Navbar from "@/components/layout/Navbar.vue";
	export default {
		name: "App",
		components: { Navbar },
		mounted() {
			this.$store.commit("Auth/setAuth");
		}
	};
</script>

<style lang="scss">
	.alert-msg {
		position: absolute !important;
		top: 72px;
		left: -100%;
		transition: 0.2s ease-in-out;
		&.show {
			left: 16px;
		}
	}
</style>
