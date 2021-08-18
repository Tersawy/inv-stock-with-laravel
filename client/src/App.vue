<template>
	<div id="app">
		<Sidebar v-if="isAuth" />
		<div class="main">
			<Navbar v-if="isAuth" />
			<div class="main-content px-3 pt-3">
				<b-breadcrumb class="mb-0 bg-transparent" v-if="isAuth">
					<b-breadcrumb-item v-for="(bread, i) in breads" :key="i" :active="true">
						<b-icon v-if="bread.isFirst && bread.isLast" icon="house-fill" scale="1.25" shift-v="1.25" class="mr-1"></b-icon>
						<span v-if="bread.isLast">{{ bread.name }}</span>
						<router-link v-else :to="bread.to">
							<b-icon v-if="bread.isFirst" icon="house-fill" scale="1.25" shift-v="1.25" class="mr-1"></b-icon>
							{{ bread.name }}
						</router-link>
					</b-breadcrumb-item>
				</b-breadcrumb>
				<router-view />
			</div>
		</div>
	</div>
</template>

<script>
	import Navbar from "@/components/layout/Navbar.vue";
	import Sidebar from "@/components/layout/Sidebar.vue";
	import { mapState } from "vuex";
	export default {
		name: "App",
		components: { Navbar, Sidebar },

		computed: {
			...mapState({
				breads: (state) => state.breads
			})
		}
		// mounted() {
		// 	let main = document.querySelector("#app > .main");

		// 	if (this.isAuth) {
		// 		main.style.left = "60px";
		// 		main.style.width = "calc(100% - 60px)";
		// 	} else {
		// 		main.style.left = "0";
		// 		main.style.width = "100%";
		// 	}
		// }
	};
</script>

<style lang="scss">
	.main {
		position: relative;
		width: calc(100% - 60px);
		left: 60px;
		min-height: 100vh;
		background: #ebeef0;
		transition: 0.5s;
		&.active {
			width: calc(100% - 300px);
			left: 300px;
		}
		.main-content {
			min-height: calc(100vh - 60px);
			.breadcrumb-item {
				font-size: 18px;
				span.active {
					color: var(--primary);
					cursor: pointer;
					&:hover {
						text-decoration: underline;
					}
				}
			}
		}
	}
</style>
