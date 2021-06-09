<template>
	<div>
		<div class="mainUnit">Welcome to mainUnit page</div>
		<b-btn variant="primary" v-b-modal.main-unit-modal>create</b-btn>
		<b-row class="mt-3" cols="3">
			<b-col v-for="mainUnit in mainUnits" :key="mainUnit.id" class="mb-4">
				<b-card header-bg-variant="primary" header-text-variant="white" header-class="py-1" body-class="p-0">
					<template #header>
						<div class="d-flex justify-content-between align-items-center unit">
							<h6 class="mb-0">{{ mainUnit.name }} ({{ mainUnit.short_name }})</h6>
							<div class="unit-btns">
								<b-btn variant="warning" size="sm" @click="createSubUnit(mainUnit)">
									<i class="fa fa-plus"></i>
								</b-btn>
								<b-btn variant="success" size="sm" class="mx-2" v-b-modal.main-unit-modal @click="editMainUnit(mainUnit)">
									<i class="fa fa-edit"></i>
								</b-btn>
								<b-btn variant="danger" size="sm" @click="removeMainUnit(mainUnit)">
									<i class="fa fa-trash-alt"></i>
								</b-btn>
							</div>
						</div>
					</template>
					<ul class="mb-0 pl-4">
						<li
							v-for="subUnit in mainUnit.sub_units"
							:key="subUnit.id"
							class="d-flex align-items-center justify-content-between unit p-2"
						>
							<div>{{ subUnit.value }} {{ subUnit.name }} ({{ subUnit.short_name }})</div>
							<div class="unit-btns">
								<EditIcon @click="editSubUnit(mainUnit, subUnit)" />
								<DeleteIcon @click="removeSubUnit(subUnit)" />
							</div>
						</li>
					</ul>
				</b-card>
			</b-col>
		</b-row>
		<MainUnitForm :oldItem="mainUnitUpdate" @closed="mainUnitUpdate = {}" />
		<SubUnitForm :mainUnit="mainUnit" :oldItem="subUnitUpdate" @closed="subUnitUpdate = {}" />
	</div>
</template>

<script>
	import MainUnitForm from "@/components/MainUnitForm.vue";
	import SubUnitForm from "@/components/SubUnitForm.vue";
	import { mapActions, mapState } from "vuex";
	export default {
		name: "MainUnit",

		components: { MainUnitForm, SubUnitForm },

		data: () => ({
			headers: ["name", "short_name", "actions"],
			mainUnitUpdate: {},
			subUnitUpdate: {},
			mainUnit: {}
		}),

		mounted() {
			this.getMainUnits();
		},

		computed: {
			...mapState({
				mainUnits: (state) => state.MainUnit.all.docs
			})
		},

		methods: {
			...mapActions({
				getMainUnits: "MainUnit/all",
				removeMainUnit: "MainUnit/remove",
				removeSubUnit: "MainUnit/removeSubUnit"
			}),

			createSubUnit(mainUnit) {
				this.mainUnit = { ...mainUnit };
				this.$nextTick(() => {
					this.$bvModal.show("sub-unit-modal");
				});
			},

			editSubUnit(mainUnit, subUnit) {
				this.mainUnit = { ...mainUnit };
				this.subUnitUpdate = { ...subUnit };
				this.$nextTick(() => {
					this.$bvModal.show("sub-unit-modal");
				});
			},

			createMainUnit(mainUnit) {
				this.mainUnitUpdate = mainUnit;
			},

			editMainUnit(mainUnit) {
				this.mainUnitUpdate = mainUnit;
			},

			closeModal() {
				this.mainUnitUpdate = {};
			}
		}
	};
</script>

<style lang="scss" scoped>
	.unit {
		& > .unit-btns {
			transition: 0.1s ease-in-out;
			opacity: 0;
		}
		&:hover {
			& > .unit-btns {
				opacity: 1;
			}
		}
	}
	.card {
		.card-header {
			.btn {
				padding: 2px 8px;
			}
		}
	}
</style>
