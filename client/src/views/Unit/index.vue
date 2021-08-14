<template>
	<div class="units-list py-3">
		<b-container fluid>
			<b-row>
				<b-col cols="4">
					<b-form-group class="mb-0">
						<b-input-group>
							<b-form-input placeholder="Search in units by name" v-model="search" />
							<b-input-group-append>
								<b-btn variant="primary">
									<i class="fas fa-filter"></i>
									Filter
								</b-btn>
							</b-input-group-append>
						</b-input-group>
					</b-form-group>
				</b-col>
				<b-col class="text-right">
					<b-btn variant="danger" class="d-inline-flex align-items-center mr-3">
						<span class="mr-2 d-flex" style="font-size: 17px">
							<i class="far fa-file-pdf fa-fw"></i>
						</span>
						PDF
					</b-btn>
					<b-btn variant="success" class="d-inline-flex align-items-center mr-3">
						<b-icon icon="file-earmark-excel" scale="1" class="mr-2"></b-icon>
						EXCEL
					</b-btn>
					<b-btn variant="info" class="d-inline-flex align-items-center mr-3">
						<b-icon icon="cloud-upload" scale="1" class="mr-2"></b-icon>
						Import
					</b-btn>
					<b-btn variant="primary" class="d-inline-flex align-items-center" v-b-modal.supplierFormModal>
						<b-icon icon="plus" scale="1.3" class="mr-1"></b-icon>
						Create
					</b-btn>
				</b-col>
			</b-row>

			<b-table
				show-empty
				stacked="md"
				responsive
				hover
				sort-icon-left
				:busy="tableIsBusy"
				:items="items"
				:fields="fields"
				:current-page="1"
				:per-page="perPage"
				:sort-by.sync="sortBy"
				:sort-desc.sync="sortDesc"
				@context-changed="contextChanged"
				:filter="search"
				:filter-function="() => items"
				class="bg-white shadow-sm mt-3 mb-0"
			>
				<template #cell(actions)="row">
					<b-icon @click="edit(row.item)" icon="pencil-square" scale="1.5" variant="success" class="c-pointer mx-3"></b-icon>
					<b-icon @click="moveToTrash(row.item)" icon="trash" scale="1.5" variant="danger" class="c-pointer"></b-icon>
				</template>
			</b-table>

			<b-row class="pt-3">
				<b-col sm="6" md="6" lg="4">
					<div class="d-flex align-items-center">
						<b-card body-class="d-flex align-items-center py-0 px-3" class="rounded-pill">
							<span class="text-muted">Rows per page: </span>
							<b-form-group class="mb-0">
								<b-form-select v-model="perPage" :options="perPageOptions" class="bg-transparent border-0 shadow-none"></b-form-select>
							</b-form-group>
						</b-card>
					</div>
				</b-col>
				<b-col sm="6" md="6" lg="4" class="ml-auto">
					<b-pagination v-model="page" :total-rows="docsCount" :per-page="perPage" align="fill" size="md" class="pagination" pills></b-pagination>
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script>
	import dataTableMixin from "@/mixins/dataTableMixin";
	export default {
		name: "Unit",

		mixins: [dataTableMixin],

		data: () => ({
			namespace: "Unit",
			fields: [
				{ key: "name", label: "Name", sortable: true },
				{ key: "short_name", label: "Short name", sortable: true },
				{ key: "value", label: "Value", sortable: true },
				{ key: "operator", label: "Operator", sortable: true },
				{ key: "main_unit", label: "Main unit", sortable: true },
				{ key: "actions", label: "Actions" }
			],
			filterationFields: { name: "", short_name: "", value: "", operator: "", main_unit_id: "" }
		})
	};
</script>
