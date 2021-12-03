<template>
	<div id="app" :class="{ 'sidebar-active': sidebarOpen, 'sidebar-signed': isAuth }">
		<Sidebar v-if="isAuth" />
		<div class="main">
			<Navbar v-if="isAuth" />
			<router-view />
		</div>
	</div>
</template>

<script>
	import Navbar from "@/components/layout/Navbar.vue";
	import Sidebar from "@/components/layout/Sidebar.vue";
	export default {
		name: "App",
		components: { Navbar, Sidebar },
		computed: {
			sidebarOpen: {
				set(v) {
					this.$store.commit("setSidebar", v);
				},
				get() {
					return this.$store.state.sidebarOpen;
				}
			}
		}
	};
</script>

<style lang="scss">
	#app {
		.main {
			position: relative;
			width: 100%;
			min-height: 100vh;
			background: #ebeef0;
			.main-content {
				position: relative;
				min-height: calc(100vh - 60px);
				width: calc(100% - 60px);
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
		.main-content,
		.sidebar-custom,
		.navbar-custom {
			transition: 0.5s;
		}
		&.sidebar-signed {
			.main-content {
				width: calc(100% - 60px);
				left: 60px;
			}
		}
		&.sidebar-active {
			.navbar-custom {
				width: calc(100% - 300px);
				left: 300px;
			}
			.main-content {
				width: calc(100% - 300px);
				left: 300px;
			}
			.sidebar-custom {
				width: 300px;
				left: 0;
			}
		}
	}
</style>
