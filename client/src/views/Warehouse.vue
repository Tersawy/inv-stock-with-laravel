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
						<router-link
							:to="{ name: 'WarehouseUpdate', params: { warehouseId: warehouse.id, warehouse } }"
							custom
							v-slot="{ navigate }"
						>
							<b-btn @click="navigate" @keypress.enter="navigate" role="link" variant="success">edit</b-btn>
						</router-link>
						<b-btn variant="danger" @click="moveToTrash(warehouse)">Trash</b-btn>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
	import { mapActions, mapState } from "vuex";
	export default {
		name: "Warehouse",

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
				getWarehouses: "Warehouse/all",
				moveToTrash: "Warehouse/moveToTrash"
			})
		}
	};
</script>
