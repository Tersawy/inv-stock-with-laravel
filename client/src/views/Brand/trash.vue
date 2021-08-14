<template>
	<div>
		<div class="brand">Welcome to brand page</div>
		<table class="table mt-5">
			<thead>
				<th v-for="header in headers" :key="header">{{ header }}</th>
			</thead>
			<tbody>
				<tr v-for="brand in categories" :key="brand.id">
					<td>{{ brand.image }}</td>
					<td>{{ brand.name }}</td>
					<td>{{ brand.description }}</td>
					<td>{{ brand.created_at }}</td>
					<td>
						<b-btn variant="success" @click="restore(brand)">restore</b-btn>
						<b-btn variant="danger" @click="remove(brand)">delete</b-btn>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
	import { mapActions, mapState } from "vuex";
	export default {
		name: "BrandTrashed",
		data: () => ({
			headers: ["image", "name", "description", "created_at", "actions"]
		}),

		mounted() {
			this.getBrands();
		},

		computed: {
			...mapState({
				categories: (state) => state.Brand.all.docs
			})
		},

		methods: {
			...mapActions({
				getBrands: "Brand/trashed",
				restore: "Brand/restore",
				remove: "Brand/remove"
			})
		}
	};
</script>
