<template>
	<b-modal id="paymentForm" @hidden="$emit('reset-modal')" hide-footer @ok="handleSave">
		<template #modal-header="{ close }">
			<div class="d-flex align-items-center justify-content-between w-100">
				<div v-if="invoice" class="d-flex align-items-center">
					<h6 class="mb-0">Create Payment:&nbsp;</h6>
					<b-badge variant="outline-primary" class="font-weight-500">{{ invoice.reference }}</b-badge>
				</div>
				<b-button size="sm" variant="outline-danger" @click="close()"> Close </b-button>
			</div>
		</template>
		<template #default="{ ok }">
			<b-form @submit.prevent="handleSave" v-if="invoice">
				<b-row cols="1">
					<!-- -------------Date------------- -->
					<b-col>
						<date-input :object="payment" />
						<input-error :vuelidate="$v.payment.date" field="date" :namespace="namespace" />
					</b-col>

					<!-- -------------Amount------------- -->
					<b-col>
						<amount-input :object="payment" />
						<input-error :vuelidate="$v.payment.amount" field="amount" :namespace="namespace" />
					</b-col>

					<!-- -------------Note------------- -->
					<b-col>
						<note-input :object="payment" />
						<input-error :vuelidate="$v.payment.note" field="note" :namespace="namespace" />
					</b-col>
				</b-row>
				<div class="text-right">
					<b-btn @click="ok()" variant="outline-primary">Done</b-btn>
				</div>
			</b-form>
		</template>
	</b-modal>
</template>

<script>
	import DateInput from "@/components/ui/inputs/DateInput";
	import AmountInput from "@/components/ui/inputs/AmountInput";
	import NoteInput from "@/components/ui/inputs/NoteInput";

	import { required, numeric, maxLength, minValue } from "vuelidate/lib/validators";
	export default {
		props: ["namespace"],

		components: { DateInput, AmountInput, NoteInput },

		data() {
			let zeroFill = (v) => (+v < 10 ? `0${v}` : v);

			let [m, d, y] = new Date().toLocaleDateString().split("/");

			const today = `${y}-${zeroFill(m)}-${zeroFill(d)}`;

			return {
				payment: {
					amount: 0,
					date: today,
					note: null
				}
			};
		},

		validations() {
			return {
				payment: {
					amount: { numeric, minValue: minValue(0) },
					note: { maxLength: maxLength(255) },
					date: { required }
				}
			};
		},

		computed: {
			invoice() {
				return this.$store.state[this.namespace].one;
			}
		},

		methods: {
			handleSave() {}
		}
	};
</script>
