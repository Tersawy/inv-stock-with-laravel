<template>
	<div class="purchases-list py-3">
		<b-container fluid>
			<b-row>
				<b-col cols="4">
					<b-form-group class="mb-0">
						<b-input-group>
							<b-form-input placeholder="Search in purchases by name" v-model="search" />
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
					<router-link :to="{ name: 'PurchaseCreate' }">
						<b-btn variant="primary" class="d-inline-flex align-items-center">
							<b-icon icon="plus" scale="1.3" class="mr-1"></b-icon>
							Create
						</b-btn>
					</router-link>
				</b-col>
			</b-row>

			<b-table
				show-empty
				stacked="lg"
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
					<InvoiceActions :invoice="row.item" :namespace="namespace" invoiceName="Purchase" />
				</template>

				<template #cell(supplier)="row">
					<span> {{ row.value | relation }} </span>
				</template>
				<template #cell(warehouse)="row">
					<span> {{ row.value | relation }} </span>
				</template>
				<template #cell(total_price)="row">
					<span class="text-primary font-weight-500">$ {{ row.value | floating }} </span>
				</template>
				<template #cell(paid)="row">
					<span>$ {{ row.value | floating }} </span>
				</template>
				<template #cell(due)="row">
					<span>$ {{ row.value | floating }} </span>
				</template>
				<template #cell(payment_status)="row">
					<span v-payment-status="row.value"> </span>
				</template>
				<template #cell(status)="row">
					<span v-purchase-status="row.value"> </span>
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
		<PaymentForm :namespace="namespace" />
		<Payments :namespace="namespace" />
	</div>
</template>

<script>
	import dataTableMixin from "@/mixins/dataTableMixin";
	import invoicePaymentsMixin from "@/mixins/invoicePaymentsMixin";
	export default {
		name: "Purchase",

		mixins: [dataTableMixin, invoicePaymentsMixin],

		data: () => ({
			namespace: "Purchase",
			fields: [
				{ key: "date", label: "Date", sortable: true },
				{ key: "reference", label: "Reference", sortable: true },
				{ key: "supplier", label: "Supplier", sortable: true },
				{ key: "warehouse", label: "Warehouse", sortable: true },
				{ key: "status", label: "Status", sortable: true },
				{ key: "total_price", label: "Total", sortable: true },
				{ key: "paid", label: "Paid", sortable: true },
				{ key: "due", label: "Due", sortable: true },
				{ key: "payment_status", label: "Payment Status", sortable: true },
				{ key: "actions", label: "Actions" }
			],
			filterationFields: { date: "", reference: "", supplier: "", warehouse: "", status: "", payment_status: "" }
		})
	};
</script>
