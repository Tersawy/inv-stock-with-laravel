<template>
	<div id="app">
		<Sidebar v-if="isAuth" />
		<div class="main">
			<Navbar v-if="isAuth" />
			<div class="main-content">
				<b-breadcrumb class="mb-0">
					<b-breadcrumb-item href="#home">
						<b-icon icon="house-fill" scale="1.25" shift-v="1.25" aria-hidden="true"></b-icon>
						Home
					</b-breadcrumb-item>
					<b-breadcrumb-item href="#foo">Foo</b-breadcrumb-item>
					<b-breadcrumb-item href="#bar">Bar</b-breadcrumb-item>
					<b-breadcrumb-item active>Baz</b-breadcrumb-item>
				</b-breadcrumb>
				<router-view />
			</div>
		</div>
	</div>
</template>

<script>
	import Navbar from "@/components/layout/Navbar.vue";
	import Sidebar from "@/components/layout/Sidebar.vue";
	export default {
		name: "App",
		components: { Navbar, Sidebar }
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
		.main-content .breadcrumb-item a {
			color: var(--secondary);
		}
	}
</style>
