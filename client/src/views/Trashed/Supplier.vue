<template>
	<div>
		<div class="supplier">Welcome to supplier page</div>
		<table class="table mt-5">
			<thead>
				<th v-for="header in headers" :key="header">{{ header }}</th>
			</thead>
			<tbody>
				<tr v-for="supplier in suppliers" :key="supplier.id">
					<td>{{ supplier.name }}</td>
					<td>{{ supplier.city }}</td>
					<td>{{ supplier.country }}</td>
					<td>{{ supplier.email }}</td>
					<td>{{ supplier.phone }}</td>
					<td>{{ supplier.address }}</td>
					<td>
						<b-btn variant="success" @click="restore(supplier)">restore</b-btn>
						<b-btn variant="danger" @click="remove(supplier)">delete</b-btn>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
	import { mapActions, mapState } from "vuex";
	export default {
		name: "SupplierTrashed",
		data: () => ({
			headers: ["name", "city", "country", "email", "phone", "address", "actions"]
		}),

		mounted() {
			this.getSuppliers();
		},

		computed: {
			...mapState({
				suppliers: (state) => state.Supplier.all.docs
			})
		},

		methods: {
			...mapActions({
				getSuppliers: "Supplier/trashed",
				restore: "Supplier/restore",
				remove: "Supplier/remove"
			})
		}
	};
</script>
