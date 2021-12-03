<template>
	<div class="main-content px-3" @click="$store.commit('setSidebar', false)">
		<b-container fluid>
			<b-card class="my-4 rounded shadow-sm" no-body>
				<b-breadcrumb class="mb-0 bg-transparent" v-if="isAuth">
					<b-breadcrumb-item v-for="(bread, i) in breads" :key="i" :active="true">
						<b-icon v-if="breads.length === 1" icon="house-fill" scale="1.25" shift-v="1.25" class="mr-2"></b-icon>
						<span v-if="!bread.link">{{ bread.title }}</span>
						<router-link v-else :to="bread.link">
							<b-icon v-if="i == 0" icon="house-fill" scale="1.25" shift-v="1.25" class="mr-2"></b-icon>
							<span>{{ bread.title }}</span>
						</router-link>
					</b-breadcrumb-item>
				</b-breadcrumb>
			</b-card>
			<slot name="default"></slot>
		</b-container>
	</div>
</template>

<script>
	export default {
		props: {
			breads: {
				type: Array,
				default: () => []
			}
		}
	};
</script>
