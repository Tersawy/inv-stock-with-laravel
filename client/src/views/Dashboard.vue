<template>
	<div class="dashboard">
		<b-container fluid>
			<div class="dashboard-card-box py-4">
				<report-card name="Today Sales" icon="cart-plus" number="1780.00 $" />
				<report-card name="Income" icon="fas fa-hand-holding-usd" awesome number="2300.00 $" />
				<report-card name="Expenses" icon="far fa-money-bill-alt" awesome number="1888.00 $" />
				<report-card name="Profits" icon="fas fa-coins" awesome number="412.00 $" />
			</div>
			<div class="details">
				<div class="recent-sales shadow-sm bg-white px-3 pb-3 rounded">
					<div class="recent-header py-3 d-flex justify-content-between align-items-center">
						<h3 class="mb-0">Recent Sales</h3>
						<div><b-btn to="/sale">View All</b-btn></div>
					</div>
					<table class="table table-hover mb-0">
						<thead>
							<tr>
								<th>Reference</th>
								<th>Customer</th>
								<th>Status</th>
								<th>Total Price</th>
								<th>Paid</th>
								<th>Due</th>
								<th>Payment Status</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>SL_1111</td>
								<td>Star Refigerator</td>
								<td>
									<b-badge variant="outline-success">Completed</b-badge>
								</td>
								<td>1736.00</td>
								<td>1736.00</td>
								<td>0.00</td>
								<td>
									<b-badge variant="outline-success">Paid</b-badge>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="recent-customers shadow-sm bg-white px-3 pb-3 rounded">
					<div class="recent-header py-3 d-flex justify-content-between align-items-center">
						<h3 class="mb-0">Recent Customers</h3>
						<div><b-btn to="/sale">View All</b-btn></div>
					</div>
					<table class="table table-hover mb-0">
						<tbody>
							<tr v-for="(customer, i) in customers" :key="i">
								<td width="60px">
									<div class="customer-img">
										<img :src="require(`@/assets/${customer.img}`)" alt="" />
									</div>
								</td>
								<td>
									<h4>
										{{ customer.name }}
										<br />
										<span>{{ customer.country }}</span>
									</h4>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</b-container>
	</div>
</template>
<script>
	import ReportCard from "@/components/ui/ReportCard";
	export default {
		name: "Dashboard",
		components: { ReportCard },
		data() {
			return {
				customers: [
					{ name: "David", country: "Italy", img: "user1.jpg" },
					{ name: "Muhammad", country: "Egypt", img: "user2.jpg" },
					{ name: "Amelia", country: "France", img: "user3.jpg" },
					{ name: "Olivia", country: "USA", img: "user4.jpg" },
					{ name: "Amit", country: "Jaban", img: "user5.jpg" },
					{ name: "Ashraf", country: "India", img: "user6.jpg" },
					{ name: "Diana", country: "Malaysia", img: "user7.jpg" },
					{ name: "Yasser", country: "Egypt", img: "user8.jpg" }
				],
				sales: [
					{
						reference: "SL_1111",
						customer: { id: 1, name: "Star Refigerator" },
						status: 0,
						grand_total: 74,
						paid: 74,
						due: 0,
						payment_status: 0
					},
					{
						reference: "SL_1112",
						customer: { id: 2, name: "Mohamed Ashraf" },
						status: 1,
						grand_total: 1595,
						paid: 1595,
						due: 0,
						payment_status: 1
					}
				]
			};
		}
	};
</script>

<style scoped lang="scss">
	.dashboard {
		.dashboard-card-box {
			position: relative;
			width: 100%;
			display: grid;
			grid-template-columns: repeat(4, 1fr);
			grid-gap: 20px;
			.dashboard-card {
				position: relative;
				background: #fff;
				padding: 20px;
				display: flex;
				justify-content: space-between;
				cursor: pointer;
				.dashboard-card-content {
					.dashboard-card-numbers {
						position: relative;
						font-size: 2em;
						font-weight: 500;
					}
					.dashboard-card-name {
						color: #999;
					}
				}
				.dashboard-card-icon {
					font-size: 2.5em;
					color: var(--secondary);
				}
			}
		}
		.details {
			display: grid;
			grid-template-columns: 2fr 1fr;
			grid-gap: 20px;
			.recent-customers {
				position: relative;
				min-height: 530px;
				background-color: #fff;
				.customer-img {
					position: relative;
					width: 40px;
					height: 40px;
					border-radius: 50%;
					overflow: hidden;
					img {
						width: 100%;
						height: 100%;
						object-fit: cover;
					}
				}
				table {
					tr {
						&:hover,
						&:hover td h4 span {
							background: var(--secondary);
							color: #fff;
						}
						td {
							padding: 12px 10px;
							h4 {
								font-size: 16px;
								font-weight: 500;
								line-height: 1.2em;
								span {
									font-size: 14px;
									color: #999;
								}
							}
						}
					}
				}
			}
			// table {
			// 	tbody tr {
			// 		&:hover,
			// 		&:hover td h4 span {
			// 			background: var(--secondary);
			// 			color: #fff;
			// 		}
			// 	}
			// }
		}
	}
</style>
