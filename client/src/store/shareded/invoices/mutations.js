const payments = (state, res) => {
	let invoice = state.all.docs.find((invoice) => state.one.id == invoice.id);

	if (invoice) {
		invoice.payments = res.data;
		state.one = invoice;
	}
};

const setOldPayment = (state, payment) => (state.oldPayment = payment);

const removePayment = (state, id) => {
	let invoice = state.all.docs.find((invoice) => state.one.id == invoice.id);

	if (invoice) {
		invoice.payments = invoice.payments.filter((payment) => payment.id != id);

		state.one = invoice;
	}
};

export default { payments, setOldPayment, removePayment };
