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
		components: { Navbar }
	};
</script>

<style lang="scss">
	.close-btn {
		cursor: pointer;
	}
	.action-btn {
		transition: 0.1s ease-in-out;
		cursor: pointer;
		opacity: 0.7;
		&:hover {
			opacity: 1;
		}
	}
</style>
