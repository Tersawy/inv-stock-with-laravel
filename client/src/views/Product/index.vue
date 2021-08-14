<template>
	<div class="products-list py-3">
		<b-container fluid>
			<b-row>
				<b-col cols="4">
					<b-form-group class="mb-0">
						<b-input-group>
							<b-form-input placeholder="Search in products by name or code" v-model="search" />
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
						<b-icon icon="file-earmark" scale="1" class="mr-2"></b-icon>
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
					<router-link to="/product/create" custom v-slot="{ navigate }">
						<b-btn @click="navigate" @keypress.enter="navigate" role="link" variant="primary" class="d-inline-flex align-items-center">
							<b-icon icon="plus" scale="1.3" class="mr-1"></b-icon>
							Create
						</b-btn>
					</router-link>
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
				<template #cell(image)="row">
					<b-avatar :src="APP_PRODUCTS_URL + row.value" class="shadow-sm" rounded="lg"></b-avatar>
				</template>

				<template #cell(price)="row">
					<span>$ {{ row.value | floating }} </span>
				</template>

				<template #cell(instock)="row">
					<span> {{ row.value | floating }} </span>
				</template>

				<template #cell(actions)="row">
					<router-link :to="{ name: 'ProductUpdate', params: { productId: row.item.id } }">
						<b-icon icon="eye" scale="1.5" variant="info" class="c-pointer"></b-icon>
					</router-link>

					<router-link :to="{ name: 'ProductUpdate', params: { productId: row.item.id } }">
						<b-icon icon="pencil-square" scale="1.5" variant="success" class="c-pointer mx-3"></b-icon>
					</router-link>

					<router-link :to="{ name: 'ProductUpdate', params: { productId: row.item.id } }">
						<b-icon icon="trash" scale="1.5" variant="danger" class="c-pointer"></b-icon>
					</router-link>
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
		name: "Product",

		mixins: [dataTableMixin],

		data: () => ({
			namespace: "Product",
			fields: [
				{ key: "image", label: "Image", sortable: true },
				{ key: "name", label: "Name", sortable: true },
				{ key: "code", label: "Code", sortable: true },
				{ key: "category", label: "Category", sortable: true },
				{ key: "brand", label: "Brand", sortable: true },
				{ key: "price", label: "Price", sortable: true },
				{ key: "unit", label: "Unit", sortable: true },
				{ key: "instock", label: "Stock", sortable: true },
				{ key: "actions", label: "Actions" }
			],
			filterationFields: { name: "", code: "", category: "", brand: "" }
		})
	};
	/*
		Code 128 	=> 8
		Code 39 	=> 8
		EAN8 			=> 7
		EAN13 		=> 12
		UPC 			=> 11
	*/
</script>
