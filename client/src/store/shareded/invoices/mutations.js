const payments = (state, res) => {
	let invoice = state.all.docs.find((invoice) => res.data.id == invoice.id);

	if (invoice) {
		invoice.payments = res.data.payments;
	}
};

const removePayment = (state, res) => {
	let invoice = state.all.docs.find((invoice) => state.one.id == invoice.id);

	if (invoice) {
		invoice.payments = invoice.payments.filter((payment) => payment.id != res.data);
	}
};

export default { payments, removePayment };
