<template>
	<v-app>
		<Navbar v-if="isAuth" />
		<Sidebar v-if="isAuth" />
		<v-main>
			<router-view />
		</v-main>
	</v-app>
</template>

<script>
	import Navbar from "@/components/layout/Navbar.vue";
	import Sidebar from "@/components/layout/Sidebar.vue";
	export default {
		name: "App",
		components: { Navbar, Sidebar },

		data: () => ({
			//
		})
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
