<template>
	<div class="navbar-custom px-2">
		<div class="toggle" @click="sidebarOpen = !sidebarOpen">
			<b-icon icon="menu-button-wide" scale="1.6"></b-icon>
		</div>
		<div class="content">
			<b-dropdown variant="link" toggle-class="text-decoration-none" no-caret right size="lg" lazy menu-class="py-0">
				<template #button-content>
					<div class="user">
						<img src="../../assets/default-profile.png" alt="" />
					</div>
				</template>
				<b-dd-header> Mohamed Tersawy </b-dd-header>
				<b-dropdown-item link-class="py-2" href="#">Profile</b-dropdown-item>
				<b-dropdown-item link-class="py-2" href="#">Settings</b-dropdown-item>
				<hr class="m-0" />
				<b-dropdown-item-btn variant="danger" @click="logout" button-class="py-2">
					<span class="pr-2">
						<b-icon icon="box-arrow-in-left" scale="1.5" />
					</span>
					Logout
				</b-dropdown-item-btn>
			</b-dropdown>
		</div>
	</div>
</template>

<script>
	import { mapActions } from "vuex";
	export default {
		computed: {
			sidebarOpen: {
				set(v) {
					this.$store.commit("setSidebar", v);
				},
				get() {
					return this.$store.state.sidebarOpen;
				}
			}
		},

		methods: {
			...mapActions({ logout: "Auth/logout" })
		}
	};
</script>

<style lang="scss">
	.navbar-custom {
		position: relative;
		width: calc(100% - 60px);
		left: 60px;
		background: #fff;
		height: 60px;
		display: flex;
		justify-content: space-between;
		align-items: center;
		.toggle {
			position: relative;
			width: 60px;
			height: 60px;
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		.content {
			.user {
				position: relative;
				display: flex;
				align-items: center;
				justify-content: center;
				width: 40px;
				height: 40px;
				border-radius: 50%;
				border: 1px solid var(--primary);
				overflow: hidden;
				cursor: pointer;
				margin: 0 10px;
				img {
					border-radius: 50%;
					width: 32px;
					height: 32px;
					object-fit: cover;
				}
			}
		}
	}
</style>
