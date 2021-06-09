<template>
	<div>
		<div class="warehouse">Welcome to warehouse page</div>
		<table class="table mt-5">
			<thead>
				<th v-for="header in headers" :key="header">{{ header }}</th>
			</thead>
			<tbody>
				<tr v-for="warehouse in warehouses" :key="warehouse.id">
					<td>{{ warehouse.name }}</td>
					<td>{{ warehouse.city }}</td>
					<td>{{ warehouse.country }}</td>
					<td>{{ warehouse.email }}</td>
					<td>{{ warehouse.phone }}</td>
					<td>{{ warehouse.zip_code }}</td>
					<td>{{ warehouse.created_at }}</td>
					<td>{{ warehouse.deleted }}</td>
					<td>
						<b-btn variant="success" @click="restore(warehouse)">restore</b-btn>
						<b-btn variant="danger" @click="remove(warehouse)">delete</b-btn>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
	import { mapActions, mapState } from "vuex";
	export default {
		name: "WarehouseTrashed",
		data: () => ({
			headers: ["name", "city", "country", "email", "phone", "zip_code", "created_at", "deleted", "actions"]
		}),

		mounted() {
			this.getWarehouses();
		},

		computed: {
			...mapState({
				warehouses: (state) => state.Warehouse.all.docs
			})
		},

		methods: {
			...mapActions({
				getWarehouses: "Warehouse/trashed",
				restore: "Warehouse/restore",
				remove: "Warehouse/remove"
			})
		}
	};
</script>
