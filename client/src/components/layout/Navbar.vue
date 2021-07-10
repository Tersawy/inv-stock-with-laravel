<template>
	<v-app-bar app color="primary" dark>
		<div class="d-flex align-center">
			<v-app-bar-nav-icon @click.stop="setDrawer()"></v-app-bar-nav-icon>
			<v-img
				alt="Vuetify Logo"
				class="shrink mr-2"
				contain
				src="https://cdn.vuetifyjs.com/images/logos/vuetify-logo-dark.png"
				transition="scale-transition"
				width="40"
			/>

			<v-img
				alt="Vuetify Name"
				class="shrink mt-1 hidden-sm-and-down"
				contain
				min-width="100"
				src="https://cdn.vuetifyjs.com/images/logos/vuetify-name-dark.png"
				width="100"
			/>
		</div>

		<v-spacer></v-spacer>

		<v-btn @click="logout" text>
			<v-icon>mdi-logout</v-icon>
		</v-btn>
		<!-- <b-btn @click="logout" variant="danger">logout</b-btn> -->
	</v-app-bar>
</template>

<script>
	import { mapActions, mapMutations, mapState } from "vuex";
	export default {
		methods: {
			...mapActions({
				logout: "Auth/logout"
			}),
			...mapMutations(["setDrawer"])
		}
	};
</script>

<style lang="scss"></style>
