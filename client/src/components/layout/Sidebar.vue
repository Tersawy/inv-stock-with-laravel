<template>
	<div class="navigation">
		<VuePerfectScrollbar class="scroll-area" v-once :settings="{ suppressScrollX: true, wheelPropagation: false }">
			<ul>
				<li>
					<router-link to="/" class="bg-transparent">
						<span class="icon logo">
							<img src="../../assets/logo.png" />
						</span>
						<h2 class="title">Brand Name</h2>
					</router-link>
				</li>
				<li v-for="(item, i) in items" :key="i">
					<router-link :to="item.to" :exact="i == 0">
						<span class="icon"><b-icon :icon="item.icon" scale="1.5"></b-icon></span>
						<span class="title">{{ item.title }}</span>
					</router-link>
				</li>
			</ul>
		</VuePerfectScrollbar>
	</div>
</template>

<script>
	import VuePerfectScrollbar from "vue-perfect-scrollbar";
	export default {
		components: { VuePerfectScrollbar },

		data() {
			return {
				items: [
					{ title: "Dashboard", icon: "grid1x2", to: "/" },
					{ title: "Warehouses", icon: "archive", to: "/warehouse" },
					{ title: "Categories", icon: "layout-three-columns", to: "/category" },
					{ title: "Products", icon: "box-seam", to: "/product" },
					{ title: "Sales", icon: "cart3", to: "/purchase/create" },
					{ title: "Sales Return", icon: "arrow-return-left", to: "/purchase/create" },
					{ title: "Purchases", icon: "gift", to: "/purchase/create" },
					{ title: "Purchases Return", icon: "arrow-return-left", to: "/purchase/create" },
					{ title: "Expenses", icon: "wallet2", to: "/brand" },
					{ title: "Adjustment", icon: "pen", to: "/brand" },
					{ title: "Quotations", icon: "minecart-loaded", to: "/brand" },
					{ title: "Transfer", icon: "arrow-repeat", to: "/brand" },
					{ title: "Customers", icon: "person-plus", to: "/customer" },
					{ title: "Suppliers", icon: "people", to: "/supplier" },
					{ title: "Brands", icon: "tags", to: "/brand" },
					{ title: "Units", icon: "diagram3", to: "/unit" }
				]
			};
		}
	};
</script>

<style lang="scss">
	.navigation {
		position: fixed;
		width: 60px;
		height: 100%;
		background: var(--secondary);
		transition: 0.2s;
		overflow: auto;
		overflow-x: hidden;
		z-index: 90;
		&.active {
			width: 300px;
		}
		.scroll-area {
			position: relative;
			margin: auto;
			width: 300px;
			height: 100%;
		}
		ul {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			padding: 0;
			li {
				position: relative;
				width: 100%;
				list-style: none;
				&:nth-child(1) {
					margin-bottom: 20px;
				}
				&:nth-child(1):hover {
					background: transparent;
				}
				a {
					position: relative;
					display: flex;
					width: 100%;
					text-decoration: none;
					color: #fff;
					&.router-link-active,
					&:hover {
						background: var(--primary);
					}
					.icon {
						position: relative;
						display: block;
						min-width: 60px;
						line-height: 60px;
						text-align: center;
						&.logo {
							display: flex;
							align-items: center;
							justify-content: center;
							border-radius: 50%;
							img {
								width: 40px;
								height: 40px;
							}
						}
					}
					.title {
						position: relative;
						display: block;
						padding: 0 10px;
						height: 60px;
						line-height: 60px;
						white-space: nowrap;
					}
				}
			}
		}
	}
</style>
